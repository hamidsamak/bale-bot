<?php

define('BOT_ID', '000000000');
define('BOT_TOKEN', '0000000000000000000000000000000000000000');

function bale_send_message($user_id, $text, $bot_id = BOT_ID, $bot_token = BOT_TOKEN)
{
    $post = [
        '$type' => 'Request',
        'body' => [
            '$type' => 'SendMessage',
            'randomId' => time() . rand(100, 999),
            'peer' => [
                '$type' => 'User',
                'accessHash' => rand(),
                'id' => $user_id,
            ],
            'message' => [
                '$type' => 'Text',
                'text' => $text,
            ],
            'quotedMessage' => null,
        ],
        'service' => 'messaging',
        'id' => '0',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://apitest.bale.ai/v1/bots/http/' . $bot_id . ':' . $bot_token);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);

    $data = curl_exec($ch);

    curl_close($ch);

    $json = json_decode($data, true);

    if (empty($json)) {
        return false;
    } else if (isset($json['body']['date']) && $json['body']['date'] > 0) {
        return true;
    }

    return $data;
}

$data = file_get_contents('php://input');

if ($json = json_decode($data, true)) {
    if (isset($json['body']['$type'])) {
        switch ($json['body']['$type']) {
                // case 'BotReceivedUpdate':
                //     $json['body']['peer']['$type']; // User
                //     $json['body']['peer']['id'];  // Id
                //     break;

            case 'Message':
                bale_send_message($json['body']['sender']['id'], 'Your message received: ' . $json['body']['message']['text']);
                break;

                // case 'BotReadUpdate':
                //     $json['body']['peer']['$type']; // User
                //     $json['body']['peer']['id'];  // Id
                //     break;
        }
    }
}
