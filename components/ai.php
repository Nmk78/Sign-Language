<?php
session_start(); // Start the session to store chat history

$apiUrl = "https://api.aimlapi.com/chat/completions";
$apiKey = "a85337536d384bda8674be80259d73b0";

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_message'])) {
    $userMessage = trim($_POST['user_message']);
    
    if (!empty($userMessage)) {
        $_SESSION['chat_history'][] = ["role" => "user", "content" => $userMessage];
        
        $data = [
            "model" => "gpt-4o",
            "messages" => $_SESSION['chat_history'],
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

        if ($response !== false) {
            $responseData = json_decode($response, true);
            $botResponse = $responseData['choices'][0]['message']['content'] ?? "No response found";
            
            $_SESSION['chat_history'][] = ["role" => "assistant", "content" => $botResponse];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .chat-widget { position: fixed; bottom: 20px; right: 20px; }
        .chat-icon { width: 60px; height: 60px; background-color: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: white; font-size: 24px; }
        .chat-box { display: none; position: fixed; bottom: 90px; right: 20px; width: 300px; border: 1px solid #ccc; background: white; box-shadow: 0px 4px 6px rgba(0,0,0,0.1); padding: 10px; border-radius: 10px; }
        .chat-history { max-height: 200px; overflow-y: auto; }
        .chat-message { margin-bottom: 10px; padding: 10px; border-radius: 5px; }
        .user { background-color: #e1ffc7; text-align: right; }
        .assistant { background-color: #f1f1f1; text-align: left; }
    </style>
    <script>
        function toggleChat() {
            let chatBox = document.getElementById("chatBox");
            chatBox.style.display = (chatBox.style.display === "none" || chatBox.style.display === "") ? "block" : "none";
        }
    </script>
</head>
<body>
    <div class="chat-widget">
        <div class="chat-icon" onclick="toggleChat()">💬</div>
        <div class="chat-box" id="chatBox">
            <h4>AI Chatbot</h4>
            <div class="chat-history">
                <?php foreach ($_SESSION['chat_history'] as $message): ?>
                    <div class="chat-message <?= $message['role'] ?>">
                        <strong><?= ucfirst($message['role']) ?>:</strong> <?= htmlspecialchars($message['content']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="POST">
                <input type="text" name="user_message" placeholder="Type a message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</body>
</html>
