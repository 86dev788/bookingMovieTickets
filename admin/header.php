<?php

// Load Bootstrap 5 CSS and icons for admin pages (keeps admin styles isolated)
echo "<link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">\n";
echo "<link href=\"https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css\" rel=\"stylesheet\">\n";

// Small admin layout helper overrides to fix spacing/background collisions
echo "<style>
.admin-container{display:flex;gap:18px}
.admin-section{flex:1}
.admin-section-header{display:flex;justify-content:space-between;align-items:center;padding:18px 24px;background:linear-gradient(90deg,#071026,#0f2a4a);border-radius:10px;margin-bottom:18px}
.admin-section-panel{background:#fff;border-radius:8px;padding:12px}
.admin-logo{font-weight:700;color:#fff;padding:8px 12px;background:rgba(255,255,255,0.05);border-radius:6px}

/* Sidebar visual polish */
.admin-sidebar{border-radius:10px;padding:10px}
.admin-sidebar .nav-link{padding:10px 12px;margin:4px 4px}
.admin-sidebar .nav-link.active, .admin-sidebar .nav-link.show{background:linear-gradient(90deg,rgba(93,131,255,0.12),rgba(93,131,255,0.06));color:#eaf4ff}
.admin-sidebar .nav .nav-link{transition:all .15s ease}
.admin-sidebar .nav .nav-link:hover{transform:translateX(4px);background:rgba(255,255,255,0.02)}
.admin-sidebar .collapse .nav-link{padding-left:2.4rem;border-radius:6px}
.admin-sidebar hr{border-color:rgba(255,255,255,0.04)}

</style>\n";

// logout
if (isset($_POST['but_logout'])) {
    session_destroy();
    header('Location: index.php');
}
?>
<?php
// Load Bootstrap JS for admin interactivity
echo "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>\n";
?>
?>
<div class="admin-section-header">
    <div class="admin-logo">
        Movie Ticket Booking
    </div>
    <div class="admin-login-info">
        <div style="padding: 0 20px;">
            <h2><a href="#" class="styleAnchor">Admin Panel</a></h2>
        </div>
        <form method='post' action="">
            <input type="submit" value="Logout" class="btn btn-outline-warning" name="but_logout">
        </form>
        <img class="admin-user-avatar" src="../img/avatar.png" alt="">
    </div>
</div>