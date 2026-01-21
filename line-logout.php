<?php
session_start();

/* ลบ session ทั้งหมด */
$_SESSION = [];
session_destroy();

/* กลับหน้าแรก */
header("Location: /project/index.php");
exit;
