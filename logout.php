<?php
session_start();
unset($_SESSION["user"]);
unset($_SESSION["uname"]);
unset($_SESSION["uemail"]);
unset($_SESSION["uphone"]);
unset($_SESSION["code"]);
unset($_SESSION["mobile"]);
//header("Location: $_SESSION['currentpage']");
header("Location: " . $_SESSION['currentpage']);