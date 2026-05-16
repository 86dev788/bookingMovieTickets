<footer class="footer" style="margin-top: 50px;">
    <div class="footer-container">

        <div class="footer-brand">
            <h2>Movie Ticket Booking</h2>
            <p>Book your favorite movies anytime, anywhere.</p>
        </div>

        <div class="footer-links">
            <a href="#">Advertising</a>
            <a href="#">Privacy Policy</a>
            <a href="contact-us.php">Contact</a>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2026 Movie Ticket Booking. Created by Faiqa Malik.</p>
    </div>
</footer>
<!-- Bootstrap JS bundle (required for collapse, dropdowns) -->
<?php
// Load Bootstrap JS only for admin pages
if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) : ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php endif; ?>