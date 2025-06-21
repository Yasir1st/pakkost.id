<?php
session_start();
session_unset();
session_destroy();
header("Location: /klp1/index.php");
exit();
?>