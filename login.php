<?php
ob_start();
session_start();
if (isset($_SESSION["user"])) {
    header("Location:" . $_SESSION["currentpage"]);
    exit;
}
//echo $_SESSION['currentpage'];

include "config/connect.php";

$email = $password = "";
$errors = ["email" => "", "password" => ""];
$errorInForm = ["formError" => ""];
if (isset($_POST["submit"])) {
// Check email
    if (empty($_POST["email"])) {
        $errors["email"] = "Please enter your email address";
    } else {
        $email = $_POST["email"];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Incorrect email format";
        }
    }
// Check password
    if (empty($_POST["password"])) {
        $errors["password"] = "Please enter your password";
    } else {
        $password = $_POST["password"];
        if (!preg_match('/^(?=.*\d)(?=.*[a-zA-Z]).{8,}$/', $password)) {
            $errors["password"] = "Invalid password";
        }
    }
    if (array_filter($errors)) {
        $errorInForm["formError"] = "<p class='formInvalidMsg'><i class='fas fa-exclamation-triangle'></i>One or more fields are missing or invalid</p>";
    } else {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $stmt_login = $conn->prepare("SELECT email, password, name, phone, type FROM customer_tbl WHERE email = ? ORDER BY type DESC");
        $stmt_login->bind_param("s", $email);
        $stmt_login->execute();
        $stmt_login->store_result();
        $stmt_login->bind_result($emailDB, $passwordDB, $nameDB, $phoneDB, $typeDB);
        $stmt_login->fetch();
        if ($stmt_login->num_rows > 0 && $typeDB === "user" && !is_null($passwordDB)) {
            if (password_verify($_POST["password"], $passwordDB)) {
                $fullName = explode(" ", $nameDB);
                $firstName = $fullName[0];
                $fullPhone = explode("-", $phoneDB);
                $counCode = $fullPhone[0];
                $mobileNo = $fullPhone[1];
                $_SESSION["uname"] = $nameDB;
                $_SESSION["uemail"] = $emailDB;
                $_SESSION["user"] = $firstName;
                $_SESSION["code"] = $counCode;
                $_SESSION["mobile"] = $mobileNo;

                // Redirect to previous page after login
                header("Location:" . $_SESSION["currentpage"]);
                exit;
            } else {
                $errorInForm["formError"] = "<p class='formInvalidMsg'><i class='fas fa-exclamation-triangle'></i>Incorrect password</p>";
            }
        } else {
            $errorInForm["formError"] = "<p class='formInvalidMsg'><i class='fas fa-exclamation-triangle'></i>Email address doesn't exist</p>";
        }
        $stmt_login->close();
    }
    $conn->close();
}
ob_end_flush();
?>

<!DOCTYPE html>
<html>

<head>
    <?php include "templates/head.php";?>
    <link rel="stylesheet" href="stylesheets/header.css" />
    <link rel="stylesheet" href="stylesheets/login.css" />
</head>

<body>
    <div id="loader" class="center"></div>
    <?php include "templates/header.php";?>
    <section class="loginMain">
        <div class="heading">
            <h1>Sign in</h1>
        </div>
        <div><?php echo $errorInForm["formError"]; ?></div>
        <form action="login.php" method="POST" id="loginForm">
            <article>
                <label>Email</label>
                <input type="text" name="email" class="userInputLog" id="email" />
                <div class="errorMsg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p></p>
                </div>
            </article>
            <article>
                <label>Password</label>
                <input type="password" name="password" class="userInputLog" id="password" />
                <div class="errorMsg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p></p>
                </div>
            </article>
            <input type="submit" name="submit" value="LOG IN" />
            <p class="haveAccount">Don't have an account with us? <a href="register.php">Register</a></p>
        </form>
    </section>
    <script src="scripts/login.js"></script>
</body>

</html>