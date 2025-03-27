<!-- Footer -->
<footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>حقوق النشر &copy; نظام إدارة صالونات الحلاقة والكوافير <?php echo date('Y'); ?></span>
            </div>
        </div>
    </footer>
    <!-- End of Footer -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Common Modal for Alerts and Confirmations -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">تنبيه</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="alertModalBody">
                    <!-- Alert content will be dynamically inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="alertModalConfirmBtn">تأكيد</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript helpers -->
    <script>
        // Helper function for displaying alerts
        function showAlert(message, title = 'تنبيه', hasConfirm = false, confirmCallback = null) {
            $('#alertModalLabel').text(title);
            $('#alertModalBody').html(message);
            
            if (hasConfirm) {
                $('#alertModalConfirmBtn').show();
                $('#alertModalConfirmBtn').off('click').on('click', function() {
                    if (confirmCallback) confirmCallback();
                    $('#alertModal').modal('hide');
                });
            } else {
                $('#alertModalConfirmBtn').hide();
            }
            
            $('#alertModal').modal('show');
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // Scroll to top button behavior
        $(document).scroll(function() {
            var scrollDistance = $(this).scrollTop();
            if (scrollDistance > 100) {
                $('.scroll-to-top').fadeIn();
            } else {
                $('.scroll-to-top').fadeOut();
            }
        });

        $(document).on('click', 'a.scroll-to-top', function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 'slow');
        });
    </script>
</body>
</html>