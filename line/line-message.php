<?php

require __DIR__ . '/../config/line-config.php';

function sendLineImage($userId, $imageUrl)
{
    $data = [
        "to" => $userId,
        "messages" => [[
            "type" => "image",
            "originalContentUrl" => $imageUrl,
            "previewImageUrl"    => $imageUrl
        ]]
    ];

    $ch = curl_init("https://api.line.me/v2/bot/message/push");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer " . 'Q1p++cllztVB48/LQO9o9ZC1eq7fBN22HXyQGyWoKhhibAM98T/KRTcUGsZ968OXg8WZ2pjgocQlgMoz2/Vrl1mkR6QMkkWcCYmS76nE7ZtP6L34+yCAirj3ig+NJtZ+Fx9lqGllmfSlXS16MVNQSQdB04t89/1O/w1cDnyilFU='
        ],
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_RETURNTRANSFER => true
    ]);
    curl_exec($ch);
    curl_close($ch);
}

function sendLineMessage($userId, $messages) {

  $url = "https://api.line.me/v2/bot/message/push";

  $data = [
    "to" => $userId,
    "messages" => is_array($messages) ? $messages : [
      ["type"=>"text","text"=>$messages]
    ]
  ];

  $headers = [
    "Content-Type: application/json",
    "Authorization: Bearer " . 'Q1p++cllztVB48/LQO9o9ZC1eq7fBN22HXyQGyWoKhhibAM98T/KRTcUGsZ968OXg8WZ2pjgocQlgMoz2/Vrl1mkR6QMkkWcCYmS76nE7ZtP6L34+yCAirj3ig+NJtZ+Fx9lqGllmfSlXS16MVNQSQdB04t89/1O/w1cDnyilFU='
  ];

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);

  // 🔥 debug ตรงนี้สำคัญ
  file_put_contents(
    __DIR__."/line-log.txt",
    date("Y-m-d H:i:s")." | $httpCode | $response\n",
    FILE_APPEND
  );

  return $httpCode === 200;
}
