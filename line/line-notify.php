<?php

require_once __DIR__ . '/../config/line-config.php';

/* =========================
   PUSH TEXT MESSAGE
========================= */
function sendLinePush($userId, $message) {
  $data = [
    "to" => $userId,
    "messages" => [
      ["type" => "text", "text" => $message]
    ]
  ];

  sendLineRequest($data);
}

/* =========================
   PUSH IMAGE MESSAGE
========================= */
function sendLinePushImage($userId, $imageUrl) {
  $data = [
    "to" => $userId,
    "messages" => [
      [
        "type" => "image",
        "originalContentUrl" => $imageUrl,
        "previewImageUrl"  => $imageUrl
      ]
    ]
  ];

  sendLineRequest($data);
}

/* =========================
   BROADCAST MESSAGE (ร้าน)
========================= */
function sendLineMessage($message) {
  $data = [
    "messages" => [
      ["type" => "text", "text" => $message]
    ]
  ];

  sendLineRequest($data, true);
}

/* =========================
   CURL REQUEST
========================= */
function sendLineRequest($data, $broadcast = false) {
  $url = $broadcast
    ? "https://api.line.me/v2/bot/message/broadcast"
    : "https://api.line.me/v2/bot/message/push";

  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
      "Content-Type: application/json",
      "Authorization: Bearer " . LINE_CHANNEL_ACCESS_TOKEN
    ],
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_RETURNTRANSFER => true
  ]);

  curl_exec($ch);
  curl_close($ch);
}
