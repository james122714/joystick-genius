<?php
session_start();
session_destroy();
header("Location: http://localhost/joystick genius/controlador/login.php");
exit();
?>