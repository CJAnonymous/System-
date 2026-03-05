<style>
/* global sidebar design */
.sidebar {
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background: #003566;
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    transition: width 0.3s ease;
    z-index: 100;
}
.sidebar img {
    width: 80px;
    border-radius: 50%;
    margin-bottom: 15px;
}
.sidebar h2 {
    font-size: 20px;
    margin-bottom: 25px;
    text-align: center;
}
.sidebar a {
    width: 100%;
    padding: 10px 15px;
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}
.sidebar a i {
    min-width: 20px;
    text-align: center;
}
.sidebar a:hover,
.sidebar a.active {
    background: #0056b3;
}
.logout-btn {
    margin-top: auto;
    width: 100%;
    text-align: center;
    padding: 10px 0;
    background: #dc3545;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
}
.logout-btn:hover {
    background: #a71d2a;
}
@media(max-width:768px) {
    .sidebar {
        width: 0;
        overflow: hidden;
    }
    .sidebar.active {
        width: 250px;
    }
}
</style>

<div class="sidebar">
    <img src="avatar.avif" alt="Poblacion 1 Logo">
    <h2>Poblacion 1</h2>
    
    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>
    <a href="dashboard.php" class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> DASHBOARD</a>
    <a href="profile.php" class="<?= $currentPage == 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> PROFILE</a>
    <a href="announcement.php" class="<?= $currentPage == 'announcement.php' ? 'active' : '' ?>"><i class="fas fa-bullhorn"></i> ANNOUNCEMENT</a>
    <a href="request.php" class="<?= $currentPage == 'request.php' ? 'active' : '' ?>"><i class="fas fa-clipboard"></i> REQUEST</a>
    <a href="about.php" class="<?= $currentPage == 'about.php' ? 'active' : '' ?>"><i class="fas fa-info-circle"></i> ABOUT</a>
    <a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> CONTACT US</a>
    <a href="logout.php" class="logout-btn">LOGOUT</a>
</div>
