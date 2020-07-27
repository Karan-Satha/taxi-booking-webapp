<?php
ob_start();
session_start();
if (isset($_SESSION["user"])) {
    header("Location:" . $_SESSION["currentpage"]);
    exit;
}
include "config/connect.php";
$name = $email = $phone = $password = $confirmPassword = "";
$errors = ["name" => "", "email" => "", "phone" => "", "password" => "", "confirmPassword" => ""];
$errorInForm = ["formError" => ""];

if (isset($_POST["submit"])) {

    // Check name
    if (empty($_POST["name"])) {
        $errors['name'] = "Please enter your name";
    } else {
        $name = $_POST["name"];
        if (!preg_match('/^[a-zA-Z-,]+\s[a-zA-Z-,]+(\s?)([a-zA-Z-,]?)+$/', $name)) {
            $errors['name'] = "Name must contain letters and space only";
        }
    }

    // Check email
    if (empty($_POST["email"])) {
        $errors['email'] = "Please enter your email address";
    } else {
        $email = $_POST["email"];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Incorrect email format";
        }
    }

    // Check phone
    if (empty($_POST["phone"])) {
        $errors['phone'] = "Please enter your phone  number";
    } else {
        $phone = $_POST["phone"];
        if (!preg_match('/^\d{9,10}$/', $phone)) {
            $errors['phone'] = "Invalid phone number";
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

    // Check repeat password
    if (empty($_POST["confirmPassword"])) {
        $errors["confirmPassword"] = "Please confirm your password";
    } else {
        if ($_POST["password"] !== $_POST["confirmPassword"]) {
            $errors["confirmPassword"] = "Password doesn't match";
        }
    }

    if (array_filter($errors)) {
        $errorInForm["formError"] = "<p class='formInvalidMsg'><i class='fas fa-exclamation-triangle'></i>One or more fields are missing or invalid</p>";
    } else {
        $email = $_POST["email"];
        // Check whether user already exists
        $stmt_user = $conn->prepare("SELECT email, type FROM customer_tbl WHERE email = ? ORDER BY type DESC");
        $stmt_user->bind_param("s", $email);
        $stmt_user->execute();
        $stmt_user->store_result();
        $stmt_user->bind_result($emailDB, $typeDB);
        $stmt_user->fetch();

        if ($stmt_user->num_rows > 0 && $typeDB === "user") {
            $errorInForm["formError"] = "<p class='formInvalidMsg'><i class='fas fa-exclamation-triangle'></i>Email address already exists</p>";
        } else {
            // Prepare and bind customer
            $customer = $conn->prepare("INSERT INTO customer_tbl(name, email, password, phone, type) VALUES(?, ?, ?, ?, ?)");
            $customer->bind_param("sssss", $name, $email, $hashed_password, $codeAndPhone, $type);

            // Set parameters to customer table
            $name = ucwords($_POST["name"]);
            $email = $_POST["email"];
            $codeAndPhone = $_POST["code"] . "-" . $_POST["phone"];
            $password = $_POST["password"];
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $type = "user";

            // Execute query
            if ($customer->execute()) {
                $fullName = explode(" ", $name);
                $firstName = $fullName[0];
                $_SESSION["user"] = $firstName;
                $_SESSION["uname"] = $name;
                $_SESSION["uemail"] = $email;
                $_SESSION["code"] = $_POST["code"];
                $_SESSION["mobile"] = $_POST["phone"];
            }

            // Redirect to previous page after register
            header("Location:" . $_SESSION["currentpage"]);
            exit;
        }
        $stmt_user->close();
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
    <link rel="stylesheet" href="stylesheets/register.css" />
</head>

<body>
    <div id="loader" class="center"></div>
    <?php include "templates/header.php"?>
    <section class="registerMain">
        <div class="heading">
            <h1>Create Account</h1>
        </div>
        <div><?php echo $errorInForm["formError"]; ?></div>
        <form action="register.php" method="POST" id="registerForm">
            <article>
                <label>Full Name</label>
                <input type="text" placeholder="e.g: John Smith" name="name" class="userInputReg" id="name" />
                <div class="errorMsg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p></p>
                </div>
            </article>
            <article class="phoneContainer">
                <label>Mobile Number</label>
                <input type="text" list="codes" class="countryCode" id="code" value="+44" name="code" />
                <datalist id="codes">
                </datalist>
                <input type="text" placeholder="e.g: 7474567888" name="phone" class="userInputReg" id="phone" />
                <div class="errorMsg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p></p>
                </div>
            </article>
            <article>
                <label>Email</label>
                <input type="text" placeholder="e.g: john@gmail.com" name="email" class="userInputReg" id="email" />
                <div class="errorMsg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p></p>
                </div>
            </article>
            <article>
                <label>Password</label>
                <input type="password" name="password" class="userInputReg" id="password" />
                <div class="errorMsg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p></p>
                </div>
            </article>
            <article>
                <label>Confirm Password</label>
                <input type="password" name="confirmPassword" class="userInputReg" id="rePassword" />
                <div class="errorMsg">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p></p>
                </div>
            </article>
            <input type="submit" name="submit" value="REGISTER" />
            <p class="haveAccount">Have an account with us already? <a href="login.php">Log in</a></p>
        </form>
    </section>
    <script src="scripts/register.js"></script>
</body>

</html>