<div class="admin-section admin-section1">
    <ul>
        <li><i class="fas fa-sliders-h"></i><a href="admin.php">Dashboard </a><i class="fas admin-dropdown fa-chevron-right"></i></li>
        <li><i class="fas fa-ticket-alt"></i><a href="view.php">Bookings</a> <i class="fas admin-dropdown fa-chevron-right"></i></li>
        
        <li class="has-submenu">
            <div class="submenu-head" onclick="toggleSubmenu(this)">
                <span><i class="fas fa-film"></i> Movies</span>
                <i class="fas admin-dropdown fa-chevron-right"></i>
            </div>
            <ul>
                <li><a href="addmovie.php"><i class="fas fa-plus-circle"></i> Add Movie</a></li>
                <li><a href="showmovie.php"><i class="fas fa-eye"></i> Show Movies</a></li>
                <li><a href="add-show.php"><i class="fas fa-calendar-plus"></i> Add Show</a></li>
                <li><a href="show-schedule.php"><i class="fas fa-list"></i> Show Schedule</a></li>
            </ul>
        </li>
        <li><i class="fas fa-plus-circle"></i><a href="add.php">Add entry</a> <i class="fas admin-dropdown fa-chevron-right"></i></a></li>
        <li><i class="fas fa-id-card"></i><a href="contactus.php">User Feedback</a> <i class="fas admin-dropdown fa-chevron-right"></i></a></li>
        <li><i class="fa fa-check-circle"></i><a href="../TxnStatus.php" target="_blank">Check Status</a> <i class="fas admin-dropdown fa-chevron-right"></i></a></li>
    </ul>
</div>

<script>
function toggleSubmenu(element) {
    const submenu = element.nextElementSibling;
    const icon = element.querySelector('.admin-dropdown');
    const isOpen = submenu.style.display === 'block';
    
    if (isOpen) {
        submenu.style.display = 'none';
        element.classList.remove('active');
        icon.style.transform = 'rotate(0deg)';
    } else {
        submenu.style.display = 'block';
        element.classList.add('active');
        icon.style.transform = 'rotate(90deg)';
    }
}
</script>
