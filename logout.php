<?php
session_start();

/* ลบตัวแปร session */
$_SESSION = [];

/* ทำลาย session */
session_destroy();

/* กลับหน้าแรก */
header("Location: /project/login.php");
exit;
