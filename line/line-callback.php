<?php
session_start();
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/line-config.php';

/* =========================
   CHECK CODE ONLY
========================= */
if (!isset($_GET['code'])) {
  exit('No authorization code');
}

$code = $_GET['code'];

/* =========================
   REQUEST TOKEN
========================= */
$token = json_decode(
  file_get_contents(
    'https://api.line.me/oauth2/v2.1/token',
    false,
    stream_context_create([
      'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded",
        'content' => http_build_query([
          'grant_type'    => 'authorization_code',
          'code'          => $code,
          'redirect_uri'  => LINE_LOGIN_REDIRECT_URI,
          'client_id'     => LINE_LOGIN_CHANNEL_ID,
          'client_secret' => LINE_LOGIN_CHANNEL_SECRET
        ])
      ]
    ])
  ),
  true
);

if (empty($token['access_token'])) {
  exit('Token error');
}

/* =========================
   GET PROFILE
========================= */
$profile = json_decode(
  file_get_contents(
    'https://api.line.me/v2/profile',
    false,
    stream_context_create([
      'http' => [
        'header' => "Authorization: Bearer {$token['access_token']}"
      ]
    ])
  ),
  true
);

/* =========================
   SAVE / UPDATE USER
========================= */
$stmt = $conn->prepare("
  INSERT INTO users (line_user_id, name, picture, created_at)
  VALUES (?, ?, ?, NOW())
  ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    picture = VALUES(picture)
");
$stmt->bind_param(
  "sss",
  $profile['userId'],
  $profile['displayName'],
  $profile['pictureUrl']
);
$stmt->execute();

/* =========================
   SET SESSION
========================= */
$_SESSION['line_user_id'] = $profile['userId'];
$_SESSION['name']         = $profile['displayName'];

header("Location: /project/index.php");
exit;
