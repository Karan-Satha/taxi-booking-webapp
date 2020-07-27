<header>
    <nav>
        <!-- Logo  -->
        <div class="navMainContainer">
            <a href="index.php"><img src="./images/logo.jpg" /></a>
        </div>
        <!-- Login and register link -->
        <div class="userLink">
            <?php if (isset($_SESSION["user"])) {?>
            <p>Hi, <strong><?php echo $_SESSION["user"]; ?></strong></p>
            <?php }?>
            <?php if (isset($_SESSION["user"])) {?>
            <a class="logout" href="logout.php">(Sign out)</a>
            <?php } else {?>
            <a href="register.php">Signup</a>
            <a href="login.php">Login</a>
            <?php }?>
            <i class="fas fa-user"></i>
        </div>
        <!-- Toggle button  -->
        <div class="toggleNavContainer">
            <div class="navInnerContainer" id="zoomOutDiv">
                <div class="navIconContainer">
                    <div class="barContainer" id="navToggle">
                        <div class="line1"></div>
                        <div class="line2"></div>
                        <div class="line3"></div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>