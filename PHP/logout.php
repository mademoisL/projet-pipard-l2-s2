<?php
session_start();
session_destroy();
header("Location: ../HTML/accueil.html");
exit();
?>
 