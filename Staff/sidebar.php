<div class="sidebar">
    <img src="avatar.avif" alt="Poblacion 1 Logo" style="width: 100px; height: auto; display: block; margin: 0 auto 20px; border-radius: 55%;">
    <h2>Poblacion 1</h2>


    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>

    <a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i> DASHBOARD
    </a>
    <a href="profile.php" class="<?= $currentPage == 'profile.php' ? 'active' : '' ?>">
        <i class="fas fa-user"></i> PROFILE
    </a>
    <a href="announcement.php" class="<?= $currentPage == 'announcement.php' ? 'active' : '' ?>">
        <i class="fas fa-bullhorn"></i> ANNOUNCEMENT
    </a>
    <a href="pickup.php" class="<?= $currentPage == 'pickup.php' ? 'active' : '' ?>">
        <i class="fas fa-truck-pickup"></i> PICKED-UP LIST
    </a>    
    <a href="request.php" class="<?= $currentPage == 'request.php' ? 'active' : '' ?>"><i class="fas fa-clipboard"></i> REQUEST LIST</a>

    
  

    <a href="logout.php" class="logout-btn">LOGOUT</a>
</div>
