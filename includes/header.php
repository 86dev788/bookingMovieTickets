<header class="main-header">

    <div class="navbar-wrapper">

        <!-- Brand Name -->
        <div class="navbar-brand">
            <a href="index.php" class="brand-link">
                <h1 class="navbar-heading">Movie Ticket</h1>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="navbar">
            <ul class="navbar-menu">

                <li><a href="index.php">Home</a></li>

                <li><a href="movies.php">Movies</a></li>

                <li><a href="schedule.php">Schedule</a></li>

                <li><a href="contact-us.php">Contact</a></li>

                <?php if (isset($_SESSION['customer_id'])): ?>

                    <li><a href="my-bookings.php">My Bookings</a></li>

                    <li>
                        <a href="logout.php" class="logout-btn">
                            Logout
                        </a>
                    </li>

                <?php else: ?>

                    <li><a href="login.php">Login</a></li>

                    <li>
                        <a href="register.php" class="register-btn">
                            Register
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </nav>

    </div>

</header>