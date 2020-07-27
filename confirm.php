<?php
session_start();
if (!isset($_SESSION['bookid'])) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>

<head>
    <?php include "templates/head.php";?>
    <!-- Confirm page css -->
    <link rel="stylesheet" href="stylesheets/confirm.css" />
    <!-- Header css -->
    <link rel="stylesheet" href="stylesheets/header.css" />
</head>

<body>
    <section>
        <?php include "templates/header.php";?>
    </section>
    <section class="confirmMain">
        <h1>Thank you!</h1>
        <i class="fas fa-check"></i>
        <p>Your booking number: <?php echo $_SESSION["bookid"] ?? null; ?></p>
        <p>Thank you for booking your travel with us.</p>
        <p>You will receive an email confirmation shortly at
            <strong><?php echo $_SESSION["uemail"] ?? null; ?></strong>.</p>
        <a href="index.php">GO BACK TO HOMEPAGE</a>
    </section>
    <script>
    localStorage.clear();
    </script>
</body>

</html>