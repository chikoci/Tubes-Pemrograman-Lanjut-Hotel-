    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Hotel Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="<?php echo asset('js/script.js'); ?>"></script>
</body>
</html>
<?php
// Clear old input and errors
clearOld();
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
}
?>
