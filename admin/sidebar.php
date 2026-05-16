<?php
// Sidebar: left column only (include inside .admin-container so content flows on the right)
$cur_page = basename($_SERVER['PHP_SELF']);
function isActive($name) { global $cur_page; return $cur_page === $name ? 'active' : ''; }
function submenuShow(array $names) { global $cur_page; foreach ($names as $n) if ($cur_page === $n) return 'show'; return ''; }
?>

<aside class="admin-sidebar-col">
    <div class="admin-sidebar bg-dark px-sm-2 px-0">
        <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
            <a href="../admin.php" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                <span class="fs-5 d-none d-sm-inline">Menu</span>
            </a>
            <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                <li class="nav-item">
                    <a href="admin.php" class="nav-link align-middle px-0 text-white <?php echo isActive('admin.php'); ?>">
                        <i class="fs-4 bi-speedometer2"></i> <span class="ms-1 d-none d-sm-inline">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="view.php" class="nav-link px-0 align-middle text-white <?php echo isActive('view.php'); ?>">
                        <i class="fs-4 bi-table"></i> <span class="ms-1 d-none d-sm-inline">Bookings</span></a>
                </li>
                <li>
                    <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle text-white <?php echo submenuShow(['addmovie.php','showmovie.php','add-show.php','show-schedule.php']); ?>">
                        <i class="fs-4 bi-bootstrap"></i> <span class="ms-1 d-none d-sm-inline">Movies</span></a>
                    <ul class="collapse nav flex-column ms-1 <?php echo submenuShow(['addmovie.php','showmovie.php','add-show.php','show-schedule.php']); ?>" id="submenu2" data-bs-parent="#menu">
                        <li class="w-100">
                            <a href="addmovie.php" class="nav-link px-0 text-white <?php echo isActive('addmovie.php'); ?>"> <span class="d-none d-sm-inline">Add Movie</span></a>
                        </li>
                        <li>
                            <a href="showmovie.php" class="nav-link px-0 text-white <?php echo isActive('showmovie.php'); ?>"> <span class="d-none d-sm-inline">Show Movies</span></a>
                        </li>
                        <li>
                            <a href="add-show.php" class="nav-link px-0 text-white <?php echo isActive('add-show.php'); ?>"> <span class="d-none d-sm-inline">Add Show</span></a>
                        </li>
                        <li>
                            <a href="show-schedule.php" class="nav-link px-0 text-white <?php echo isActive('show-schedule.php'); ?>"> <span class="d-none d-sm-inline">Show Schedule</span></a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#submenu3" data-bs-toggle="collapse" class="nav-link px-0 align-middle text-white <?php echo submenuShow(['cinemas.php','screens.php','seats.php']); ?>">
                        <i class="fs-4 bi-grid"></i> <span class="ms-1 d-none d-sm-inline">Cinemas</span> </a>
                        <ul class="collapse nav flex-column ms-1 <?php echo submenuShow(['cinemas.php','screens.php','seats.php']); ?>" id="submenu3" data-bs-parent="#menu">
                        <li class="w-100">
                            <a href="cinemas.php" class="nav-link px-0 text-white <?php echo isActive('cinemas.php'); ?>"> <span class="d-none d-sm-inline">Manage Cinemas</span></a>
                        </li>
                        <li>
                            <a href="screens.php" class="nav-link px-0 text-white <?php echo isActive('screens.php'); ?>"> <span class="d-none d-sm-inline">Manage Screens</span></a>
                        </li>
                        <li>
                            <a href="seats.php" class="nav-link px-0 text-white <?php echo isActive('seats.php'); ?>"> <span class="d-none d-sm-inline">Manage Seats</span></a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="contactus.php" class="nav-link px-0 align-middle text-white <?php echo isActive('contactus.php'); ?>">
                        <i class="fs-4 bi-people"></i> <span class="ms-1 d-none d-sm-inline">Customers</span> </a>
                </li>
            </ul>
            <hr class="text-secondary w-100">
            <div class="dropdown pb-4 w-100">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="../img/screenshot/images.jfif" alt="user" width="30" height="30" class="rounded-circle">
                    <span class="d-none d-sm-inline mx-1"><?php echo htmlspecialchars($_SESSION['uname'] ?? 'Admin'); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                    <li><a class="dropdown-item" href="#">New project...</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </div>
</aside>

<style>
/* Small additions to match dark theme and spacing */
.bg-dark{background:#0b1220 !important}
.nav-link{border-radius:8px}
.nav-link:hover{background:rgba(255,255,255,0.03)}
.dropdown-menu-dark{background:#0f1724}
.text-white{color:#e6eef8 !important}

/* Ensure sidebar stays left and content flows right */
.admin-sidebar-col{order:0;flex:0 0 220px}
.admin-sidebar{position:sticky;top:0;height:100vh}
.admin-section{order:1;flex:1}
</style>

<!-- Note: this sidebar uses Bootstrap's collapse & dropdown. Ensure Bootstrap 5 CSS/JS and Bootstrap Icons (or FontAwesome) are loaded in your admin layout. -->
