<?php
use PHPMailer\PHPMailer\PHPMailer;
session_start();
$_SESSION["currentpage"] = $_SERVER['REQUEST_URI'];

if (!isset($_SESSION["package"])) {
    header("Location:index.php");
    exit;
}
include "config/connect.php";
$name = $email = $phone = $passanger = $paymentId = $payerId = $paidAmount = '';
$errors = ['name' => '', 'email' => '', 'phone' => '', 'passanger' => '', 'paymentid' => '', 'payerid' => '', 'paidamount' => '', 'payment' => ''];
$errorInForm = ['formError' => ''];
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
    // Check passanger
    if (isset($_POST["passanger"]) === "--Select passanger--") {
        $errors['passanger'] = "Please select the number of passanger";
    }

    // Check payment
    if (!isset($_POST["payment"])) {
        $errors['payment'] = "Please select the payment option";
    }

    if (isset($_POST["payment"]) === "payPal") {
        // Check paypal payment id
        if (empty($_POST["paymentId"])) {
            $errors["paymentid"] = "payment process is incomplete";
        }

        // Check paypal payer id
        if (empty($_POST["payerId"])) {
            $errors["payerid"] = "payment process is incomplete";
        }

        // Check paypal payment amount
        if (empty($_POST["paidAmount"])) {
            $errors["paidamount"] = "payment process is incomplete";
        }
    }

    if (array_filter($errors)) {
        $errorInForm["formError"] = "<p class='formInvalidMsg'><i class='fas fa-exclamation-triangle'></i>One or more fields are missing or invalid</p>";
    } else {

        // Prepare and bind customer
        if (isset($_SESSION["user"])) {
            $stmt_user = $conn->prepare("SELECT customer_id FROM customer_tbl WHERE email = ?");
            $stmt_user->bind_param("s", $_SESSION["uemail"]);
            $stmt_user->execute();
            $stmt_user->store_result();
            $stmt_user->bind_result($customerID);
            $stmt_user->fetch();
            $_SESSION["customerId"] = $customerID;
        } else {
            // $stmt_userGuest = $conn->prepare("SELECT * FROM customer_tbl WHERE email = ?");
            // $stmt_userGuest->bind_param("s", $_POST["email"]);
            // $stmt_userGuest->execute();
            // $stmt_userGuest->store_result();
            // $stmt_userGuest->bind_result($guestID, $guestName, $guestEmail, $guestPass, $guestPhone);
            // $stmt_userGuest->fetch();

            // if ($stmt_userGuest->num_rows > 0 && is_null($guestPass)) {
            //     $stmt_update = $conn->prepare("UPDATE customer_tbl SET name = ?, phone = ? WHERE customer_id = ?");
            //     $stmt_update->bind_param("ssi", $guestName, $guestPhone, $guestID);
            // }

            $customer = $conn->prepare("INSERT INTO customer_tbl(name, email, phone, type) VALUES(?, ?, ?, ?)");
            $customer->bind_param("ssss", $name, $email, $codeAndPhone, $type);

            // Set parameters to customer table
            $name = ucwords($_POST["name"]);
            $email = $_POST["email"];
            $codeAndPhone = $_POST["code"] . "-" . $_POST["phone"];
            $type = "guest";
            // Execute query
            $customer->execute();
            // Retrieve last customer id
            $_SESSION["customerId"] = $conn->insert_id;

        }

        // Prepare and bind booking
        $booking = $conn->prepare("INSERT INTO booking_tbl(customer_id, pickup_address, dropoff_address, journey_date, journey_time, customer_note) VALUES(?, ?, ?, ?, ?, ?)");
        $booking->bind_param("ssssss", $_SESSION["customerId"], $pickup, $dropoff, $date, $time, $note);

        // Set parameters to booking table
        $pickup = $_POST["pickup"];
        $dropoff = $_POST["dropoff"];
        $date = $_POST["date"];
        $time = $_POST["time"];
        $note = $_POST["note"];
        // Execute query
        $booking->execute();
        // Retrieve last booking id
        $book_id = $conn->insert_id;
        $_SESSION["bookid"] = $book_id;

        // Prepare and bind vehicle
        $vehicle = $conn->prepare("INSERT INTO vehicle_tbl(booking_id, vehicle_type, passanger,luggage) VALUES(?, ?, ?, ?)");
        $vehicle->bind_param("ssss", $book_id, $carType, $passanger, $luggage);

        // Set parameters to vehicle table
        $carType = $_POST["car"];
        $passanger = $_POST["passanger"];
        $luggage = $_POST["luggage"];
        // Execute query
        $vehicle->execute();

        // Prepare and bind payment
        $payment = $conn->prepare("INSERT INTO payment_tbl(customer_id, booking_id, payment_method, amount) VALUES(?, ?, ?, ?)");
        $payment->bind_param("ssss", $_SESSION["customerId"], $book_id, $paymentMethod, $fare);

        // Set parameters to payment table
        $paymentMethod = $_POST["payment"];
        (int) $fare = $_POST["fare"];
        // Execute query
        $payment->execute();
        // Retrieve last payment id
        $pay_id = $conn->insert_id;

        // Get paypal details
        if (isset($_POST["paymentId"]) && !empty($_POST["paymentId"])) {
            // Prepare and bind paypal
            $paypalPayment = $conn->prepare("INSERT INTO paypal_payment_tbl(paypal_payment_id, payment_id, payer_id, payer_name, paid_amount, paid_at) VALUES(?, ?, ?, ?, ?, ?)");
            $paypalPayment->bind_param("ssssss", $paymentId, $pay_id, $payerId, $payerName, $paidAmount, $paidAt);

            // Set parameters to paypal table
            $paymentId = $_POST["paymentId"];
            $payerId = $_POST["payerId"];
            $payerName = $_POST["payerName"];
            $paidAmount = $_POST["paidAmount"];
            $paidAt = $_POST["paidAt"];
            // Execute query
            $paypalPayment->execute();
        }

        $amount = 40;
        require_once "vendor/autoload.php";
        $mail = new PHPMailer;
        $mail->isSMTP(); //Set PHPMailer to use SMTP.
        $mail->Host = "smtp.gmail.com"; //Set SMTP host name
        $mail->SMTPAuth = true; //Set this to true if SMTP host requires authentication to send email
        $mail->Username = "jamunatharan@gmail.com"; //Provide username and password
        $mail->Password = "musebh9Hj";
        $mail->SMTPSecure = "tls"; //If SMTP requires TLS encryption then set it
        $mail->Port = 587; //Set TCP port to connect to
        $mail->From = "jamunatharan@gmail.com";
        $mail->FromName = "Heathrow Drive";
        $mail->addAddress($_SESSION["uemail"], $_SESSION["uname"]);
        $mail->isHTML(true);
        $mail->Subject = "Travel booking confirmation";
        $mail->Body = '<div style="font-family: sans-serif;">
        <div style="background-color:#ffffff;box-sizing: border-box;">
            <a href="http://localhost:8080/taxi-booking/index.php"><img
                    src="https://lh3.googleusercontent.com/V33zpBqH4h5OdkijxGLFjarpm13vN6qUYGlJdPAHbJzAMpt8Ui5u0nO4I5ImaKQDoqY6V--2HIdip-X-NCbvnK7QjekEelXdSk1h2vAgIk1Z-oH9FHVOJznk7IWTV3nUx4bZM60kvA=w2400"
                    width="150" height="auto" /></a>
            <div style="background-color:#ffffff;border:10px solid #f3fbfe;padding:5px;color:#162a3a;">
                <div style="background-color:#ffffff;">
                    <p>Hi <strong>' . $_SESSION["user"] . ',</strong></p>
                    <p>Your booking number: <strong>' . $_SESSION["bookid"] . '</strong></p>
                    <p>Date: <strong>' . date("l, d F Y") . '</strong></p>
                    <p>Thank you for booking your travel with Heathrow Drive.</p>
                    <p>Please check your booking details below,</p>
                </div>
                <table style="background-color:#ffffff;margin:0 auto;width:100%;">
                    <tr style="border-color:#dedef8;">
                        <th style="padding:10px 0px;background-color:#dedef8;">Journey Details</th>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e5e5e5;padding:5px 0px;">
                            Pickup Address: <strong>' . $_POST["pickup"] . '</strong></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e5e5e5;padding:5px 0px;">Dropoff Address: <strong>' .
            $_POST["dropoff"] . '</strong></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e5e5e5;padding:5px 0px;">Pickup Date: <strong>' .
            $_POST["date"] . '</strong></td>
                    </tr>
                    <tr>
                        <td style="padding:5px 0px;">Pickup Time: <strong>' . $_POST["time"] . '</strong></td>
                    </tr>
                    <tr>
                        <th style="padding:10px 0px;background-color:#dedef8;">Vehicle Details</th>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e5e5e5;padding:5px 0px;">Car Type: <strong>' .
            $_POST["car"] .
            '</strong></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e5e5e5;padding:5px 0px;">Number of Passangers: <strong>' .
            $_POST["passanger"] . '</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px 0px;">Number of Luggages: <strong>' . $_POST["luggage"] . '</strong>
                        </td>
                    </tr>
                    <tr>
                        <th style="padding:10px 0px;background-color:#dedef8;">Payment Details</th>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e5e5e5;padding:5px 0px;">Payment Method: <strong>' .
            $_POST["payment"] . '</strong></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px solid #e5e5e5;padding:5px 0px;">Total Payment: <span
                        style="font-weight:bolder;font-size:20px;float:right;">£' .
            $_POST["fare"] . '.00</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>';
        $mail->AltBody = "This is the plain text version of the email content";
        if ($mail->send()) {
            unset($_SESSION['currentpage']);
            unset($_SESSION['package']);
            unset($_SESSION['uname']);
            unset($_SESSION['user']);
            unset($_SESSION['code']);
            unset($_SESSION['mobile']);
            unset($_SESSION['customerId']);
            header("Location: confirm.php");
            exit;
        } else {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    }
    $conn->close();
}

?>

<!DOCTYPE html>
<html>

<head>
    <?php include "templates/head.php";?>
    <!-- Booking page css -->
    <link rel="stylesheet" href="stylesheets/booking.css" />
    <!-- Header css -->
    <link rel="stylesheet" href="stylesheets/header.css" />
</head>

<body>
    <div id="loader" class="loader"></div>
    <div id="loadBackground" class="loadBackground">
        <h3>Your booking is being processed...</h3>
        <div class="loaderSubmit"></div>
    </div>
    <section>
        <article>
            <?php include "templates/header.php";?>
        </article>
    </section>
    <section class="checkMain">
        <div class="checkMainLine">
            <div class="checkStatus">
                <div class="checkCircle"><i class="fas fa-check"></i></div>
                <div class="checkLine"></div>
                <p>Trip</p>
            </div>
            <div class="checkStatus">
                <div class="checkCircle" id="activePersonalCircle">2</div>
                <div class="checkLine" id="activePersonalLine"></div>
                <p>Personal</p>
            </div>
            <div class="checkStatus">
                <div class="checkCircle" id="activePaymentCircle">3</div>
                <div class="checkLine" id="activePaymentLine"></div>
                <p>Payment</p>
            </div>
            <div class="checkStatus">
                <div class="checkCircle" id="activeConfirmCircle">4</div>
                <div class="checkLine" id="activeConfirmLine"></div>
                <p>Complete</p>
            </div>
        </div>
    </section>
    <main class="bookingMain">
        <div></div>
        <section class="journeyInfo">
            <div class="heading">
                <h2>Journey Details</h2>
                <div class="edit">
                    <a href="index.php"> <i class="far fa-edit"></i>EDIT</a>
                </div>
            </div>
            <div class="journeyInfoDetail">
                <label>From</label>
                <div id="pickAddress"></div>
            </div>
            <div class="journeyInfoDetail">
                <label>To</label>
                <div id="dropAddress"></div>
            </div>
            <div class="journeyInfoDetail">
                <label>Date</label>
                <div id="date"></div>
            </div>
            <div class="journeyInfoDetail">
                <label>Time</label>
                <div id="time"></div>
            </div>
            <div class="journeyInfoDetail">
                <label>Fare</label>
                <div id="fare"></div>
            </div>
            <div class="journeyInfoDetail">
                <h2>Vehicle Details</h2>
                <div class="edit">
                    <a href="index.php#journeyDetailsDisplayId"> <i class="far fa-edit"></i>EDIT</a>
                </div>
            </div>
            <div class="journeyInfoDetail">
                <label>Car Type</label>
                <div id="car"></div>
            </div>
            <div class="journeyInfoDetail">
                <label>Passangers</label>
                <div id="people"></div>
            </div>
            <div class="journeyInfoDetail">
                <label>Luggage</label>
                <div id="luggage"></div>
            </div>
        </section>
        <section class="userInfo">

            <div class="heading">
                <h2>Passanger Details</h2>
            </div>
            <div><?php echo $errorInForm["formError"]; ?></div>
            <form action="booking.php" method="POST" id="bookingForm">

                <div class="inputContainer">
                    <div>
                        <label>Full Name</label>
                    </div>
                    <input type="text" class="userInputBook" id="name" name="name"
                        value="<?php echo $_SESSION["uname"] ?? null; ?>" placeholder="e.g: Joe Smith" />
                    <div class="errorMsg">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p></p>
                    </div>
                </div>

                <div class="inputContainer">
                    <div>
                        <label>E-mail</label>
                    </div>
                    <input type="text" class="userInputBook" id="email" name="email"
                        value="<?php echo $_SESSION["uemail"] ?? null; ?>" placeholder="e.g: joe@gmail.com" />
                    <div class="errorMsg">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p></p>
                    </div>
                </div>

                <div class="inputContainer">
                    <div>
                        <label>Phone Number</label>
                    </div>
                    <input type="text" list="codes" class="countryCode" id="code"
                        value="<?php echo $_SESSION["code"] ?? "+44"; ?>" name="code" />
                    <datalist id="codes">
                    </datalist>
                    <input type="text" class="userInputBook" id="phone" name="phone"
                        value="<?php echo $_SESSION["mobile"] ?? null; ?>" placeholder="e.g: 7466775500" />
                    <div class="errorMsg">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p></p>
                    </div>
                </div>

                <div class="inputContainer">
                    <div>
                        <label>Passanger</label>
                    </div>
                    <select type="text" class="userInputBook" id="passanger" name="passanger">
                        <option>--Select passanger--</option>
                        <option>1 Passanger</option>
                    </select>
                    <div class="errorMsg">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p></p>
                    </div>
                </div>

                <div class="inputContainer">
                    <div>
                        <label>Notes (optional)</label>
                    </div>
                    <textarea id="note" name="note" placeholder="e.g: Car seat for a toddler"></textarea>
                </div>

                <div class="inputContainer" id="payment">

                    <div>
                        <h2>Payment Methods</h2>
                    </div>

                    <div class="selectMethod">
                        <input type="radio" name="payment" id="cash" value="cash" />
                        <label class="label" for="cash">Cash</label>
                        <i class="fas fa-plus" id="plus"></i>
                    </div>
                    <div class="panel">
                        <p>Please pay to the driver</p>
                    </div>
                    <div class="selectMethod">
                        <input type="radio" name="payment" id="creditCard" value="creditCard" />
                        <label class="label" for="creditCard">Credit Card</label>
                        <i class="fas fa-plus" id="plus"></i>
                    </div>
                    <div class="panel">
                        <p>We do not currently accept card payment. Please pay by cash.</p>
                    </div>
                    <div class="selectMethod">
                        <input type="radio" name="payment" id="payPal" value="payPal" />
                        <label class="label" for="payPal">Paypal</label>
                        <i class="fas fa-plus" id="plus"></i>
                    </div>
                    <div class="panel">
                        <!-- <p>We do not currently accept Paypal payment. Please pay by cash.</p> -->
                        <div id="paypal-button-container"></div>
                    </div>
                    <div class="errorMsg" id="payErrorMsg">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p></p>
                    </div>
                </div>

                <!-- Journey details -->
                <input type="hidden" name="pickup" id="pickUp" />
                <input type="hidden" name="dropoff" id="dropOff" />
                <input type="hidden" name="date" id="pickDate" />
                <input type="hidden" name="time" id="pickTime" />
                <input type="hidden" name="car" id="carType" />
                <input type="hidden" name="fare" id="tripFare" />
                <input type="hidden" name="luggage" id="luggageNo" />
                <!-- PayPal payment details -->
                <input type="hidden" name="paymentId" id="paymentId" />
                <input type="hidden" name="payerId" id="payerId" />
                <input type="hidden" name="payerName" id="payerName" />
                <input type="hidden" name="paidAmount" id="paidAmount" />
                <input type="hidden" name="paidAt" id="paidAt" />

                <input type="submit" name="submit" class="bookNowBtn" value="BOOK NOW" />
            </form>

        </section>
    </main>
    <!-- <script
        src="https://www.paypal.com/sdk/js?client-id=Aep41JbvpBA_v9G6_2LYxSoMACL6BsjsfIBa4e6_nhCFAPYTOVUGCu84t9ieniCT9mWq3eyxRYJeCaCL&currency=GBP">

    </script> -->
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
    <script src="scripts/booking.js"></script>
</body>

</html>