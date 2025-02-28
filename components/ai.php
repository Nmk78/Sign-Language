<?php

$apiUrl = "https://api.aimlapi.com/chat/completions";
$apiKey = "e504ab0e5d...362a15c7ec";

$data = [
    "model" => "gpt-4o",
    "messages" => [
        [
            "role" => "user",
            "content" => "What kind of model are you?"
        ]
    ],
    "max_tokens" => 512,
    "stream" => false
];

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($curl);
curl_close($curl);

if ($response === false) {
    echo "Error: " . curl_error($curl);
} else {
    // Decode and flatten the output to get the chatbot's response
    $responseData = json_decode($response, true);
    $botResponse = $responseData['choices'][0]['message']['content'] ?? "No response found";
    echo $botResponse;
}