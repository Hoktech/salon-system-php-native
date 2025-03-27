<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and permission files
include_once '../config/database.php';
include_once '../config/helpers.php';
include_once '../auth/permissions.php';

// Initialize response array
$response = [
    "status" => false,
    "message" => "",
    "data" => null
];

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }
    
    // Check user permissions
    $user = checkUserPermission(['admin', 'manager', 'cashier']);
    
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (
        !isset($data->customer_id) || 
        !isset($data->employee_id) || 
        !isset($data->service_id) || 
        !isset($data->appointment_date) || 
        !isset($data->start_time)
    ) {
        throw new Exception("Missing required fields");
    }
    
    // Sanitize and validate input
    $customer_id = (int)$data->customer_id;
    $employee_id = (int)$data->employee_id;
    $service_id = (int)$data->service_id;
    $branch_id = isset($data->branch_id) ? (int)$data->branch_id : $user['branch_id'];
    $appointment_date = sanitizeInput($data->appointment_date);
    $start_time = sanitizeInput($data->start_time);
    $notes = isset($data->notes) ? sanitizeInput($data->notes) : "";
    
    // Validate date format
    if (!validateDate($appointment_date, 'Y-m-d')) {
        throw new Exception("Invalid date format. Use YYYY-MM-DD");
    }
    
    // Validate time format
    if (!validateTime($start_time, 'H:i')) {
        throw new Exception("Invalid time format. Use HH:MM (24-hour format)");
    }
    
    // Check if customer exists
    $stmt = $db->prepare("SELECT id FROM customers WHERE id = :id");
    $stmt->bindParam(":id", $customer_id);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        throw new Exception("Customer not found");
    }
    
    // Check if employee exists and is available
    $stmt = $db->prepare("
        SELECT u.id, u.branch_id 
        FROM users u 
        WHERE u.id = :id AND u.role = 'employee' AND u.status = 'active'
    ");
    $stmt->bindParam(":id", $employee_id);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        throw new Exception("Employee not found or not available");
    }
    
    // Check if branch_id matches employee's branch
    $employeeData = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($employeeData['branch_id'] != $branch_id) {
        throw new Exception("Employee does not belong to the selected branch");
    }
    
    // Check if service exists
    $stmt = $db->prepare("SELECT id, duration FROM services WHERE id = :id AND status = 'active'");
    $stmt->bindParam(":id", $service_id);
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        throw new Exception("Service not found or not active");
    }
    
    // Get service duration to calculate end_time
    $serviceData = $stmt->fetch(PDO::FETCH_ASSOC);
    $duration = $serviceData['duration']; // Duration in minutes
    
    // Calculate end_time
    $startDateTime = new DateTime($appointment_date . ' ' . $start_time);
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('PT' . $duration . 'M'));
    $end_time = $endDateTime->format('H:i');
    
    // Check for scheduling conflicts
    $stmt = $db->prepare("
        SELECT id FROM appointments 
        WHERE employee_id = :employee_id 
        AND appointment_date = :appointment_date 
        AND status IN ('scheduled', 'confirmed')
        AND (
            (start_time <= :start_time AND end_time > :start_time) OR
            (start_time < :end_time AND end_time >= :end_time) OR
            (start_time >= :start_time AND end_time <= :end_time)
        )
    ");
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->bindParam(":appointment_date", $appointment_date);
    $stmt->bindParam(":start_time", $start_time);
    $stmt->bindParam(":end_time", $end_time);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        throw new Exception("Scheduling conflict: The employee already has an appointment during this time");
    }
    
    // Check if the branch is open during the appointment time
    // Note: You would need to implement branch hours logic here
    
    // Insert the appointment
    $query = "
        INSERT INTO appointments (
            customer_id, employee_id, service_id, branch_id, 
            appointment_date, start_time, end_time, 
            status, notes, created_by, created_at
        ) VALUES (
            :customer_id, :employee_id, :service_id, :branch_id,
            :appointment_date, :start_time, :end_time,
            'scheduled', :notes, :created_by, NOW()
        )
    ";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(":customer_id", $customer_id);
    $stmt->bindParam(":employee_id", $employee_id);
    $stmt->bindParam(":service_id", $service_id);
    $stmt->bindParam(":branch_id", $branch_id);
    $stmt->bindParam(":appointment_date", $appointment_date);
    $stmt->bindParam(":start_time", $start_time);
    $stmt->bindParam(":end_time", $end_time);
    $stmt->bindParam(":notes", $notes);
    $stmt->bindParam(":created_by", $user['id']);
    
    if ($stmt->execute()) {
        $appointment_id = $db->lastInsertId();
        
        // Get the appointment details for response
        $query = "
            SELECT a.*, 
                   c.name as customer_name, c.phone as customer_phone,
                   e.name as employee_name,
                   b.name as branch_name,
                   s.name as service_name, s.duration as service_duration, s.price as service_price
            FROM appointments a
            LEFT JOIN customers c ON a.customer_id = c.id
            LEFT JOIN users e ON a.employee_id = e.id
            LEFT JOIN branches b ON a.branch_id = b.id
            LEFT JOIN services s ON a.service_id = s.id
            WHERE a.id = :id
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $appointment_id);
        $stmt->execute();
        $appointmentData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Format appointment data for response
        $appointment = [
            "id" => $appointmentData['id'],
            "customer" => [
                "id" => $appointmentData['customer_id'],
                "name" => $appointmentData['customer_name'],
                "phone" => $appointmentData['customer_phone']
            ],
            "employee" => [
                "id" => $appointmentData['employee_id'],
                "name" => $appointmentData['employee_name']
            ],
            "branch" => [
                "id" => $appointmentData['branch_id'],
                "name" => $appointmentData['branch_name']
            ],
            "service" => [
                "id" => $appointmentData['service_id'],
                "name" => $appointmentData['service_name'],
                "duration" => $appointmentData['service_duration'],
                "price" => $appointmentData['service_price']
            ],
            "appointment_date" => $appointmentData['appointment_date'],
            "start_time" => $appointmentData['start_time'],
            "end_time" => $appointmentData['end_time'],
            "status" => $appointmentData['status'],
            "notes" => $appointmentData['notes'],
            "created_at" => $appointmentData['created_at']
        ];
        
        $response['status'] = true;
        $response['message'] = "Appointment created successfully";
        $response['data'] = $appointment;
    } else {
        throw new Exception("Unable to create appointment");
    }
    
} catch (Exception $e) {
    $response['status'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON response
echo json_encode($response);

/**
 * Helper function to validate date format
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Helper function to validate time format
 */
function validateTime($time, $format = 'H:i') {
    $d = DateTime::createFromFormat($format, $time);
    return $d && $d->format($format) === $time;
}
?>