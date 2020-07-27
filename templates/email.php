<!DOCTYPE html>
<html>

<head>
    <title></title>
</head>

<body>
    <div style="font-family: sans-serif;">
        <div style="background-color:#ffffff;box-sizing: border-box;">
            <a href="http://localhost:8080/taxi-booking/index.php"><img
                    src="https://lh3.googleusercontent.com/V33zpBqH4h5OdkijxGLFjarpm13vN6qUYGlJdPAHbJzAMpt8Ui5u0nO4I5ImaKQDoqY6V--2HIdip-X-NCbvnK7QjekEelXdSk1h2vAgIk1Z-oH9FHVOJznk7IWTV3nUx4bZM60kvA=w2400"
                    width="150" height="auto" /></a>
            <div style="background-color:#ffffff;border:10px solid #f3fbfe;padding:5px;color:#162a3a;">
                <div style="background-color:#ffffff;">
                    <p>Hello <strong>' . $_SESSION["user"] . ',</strong></p>
                    <p>Your booking number: <strong>' . $_SESSION["bookid"] .
                            '</strong>
                    </p>
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
    </div>
</body>

</html>