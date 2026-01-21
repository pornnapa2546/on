<?php
session_start();

$state = bin2hex(random_bytes(16));
$_SESSION['line_state'] = $state;

$loginUrl =
  "https://access.line.me/oauth2/v2.1/authorize?"
  . http_build_query([
      'response_type' => 'code',
      'client_id'     => 2008817982,
      'redirect_uri'  => 'https://b49005e06d39.ngrok-free.app/project/line/line-callback.php',
      'state'         => $state,
      'scope'         => 'profile openid',
  ]);

header("Location: $loginUrl");
exit;
