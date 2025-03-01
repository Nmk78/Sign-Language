<?php
session_start();

$apiUrl = "https://api.aimlapi.com/chat/completions";
$apiKeys = [
    "a85337536d384bda8674be80259d73b0",
    "e81209f5615d4746afc95ddc97dd3470",
    "e504ab0e5df14852b1d9ea362a15c7ec",
    "3fe5f4b992bc41239b25d2742bbe538c",
    "8039dd75b5d745a69bf0b3bba15efb46"
];

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
                "Authorization: Bearer $currentApiKey",
                "Content-Type: application/json"
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response !== false && $httpCode === 200) {
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
                </form>
            </div>
            <form method="POST">
                <input type="text" name="user_message" placeholder="Type a message..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
    
    <script>
        const chatIcon = document.getElementById("chatIcon");
        const chatBox = document.getElementById("chatBox");
        const chatMessages = document.getElementById("chatMessages");
        const chatForm = document.getElementById("chatForm");
        const userMessageInput = document.getElementById("userMessage");
        
        chatIcon.addEventListener("click", () => {
            chatBox.classList.toggle("hidden");
            if (!chatBox.classList.contains("hidden")) {
                setTimeout(() => {
                    chatBox.classList.add("scale-100", "opacity-100");
                    chatBox.classList.remove("scale-95", "opacity-0");
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 10);
            } else {
                chatBox.classList.add("scale-95", "opacity-0");
                chatBox.classList.remove("scale-100", "opacity-100");
            }
        });

        chatForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const userMessage = userMessageInput.value.trim();
            if (userMessage) {
                appendMessage("user", userMessage);
                userMessageInput.value = "";
                
                const response = await fetch("", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `user_message=${encodeURIComponent(userMessage)}`,
                });
                
                const text = await response.text();
                const botResponse = extractBotResponse(text);
                appendMessage("assistant", botResponse);
            }
        });

        function appendMessage(role, content) {
            const messageDiv = document.createElement("div");
            messageDiv.className = `flex ${role === "user" ? "justify-end" : "justify-start"}`;
            messageDiv.innerHTML = `
                <div class="max-w-3/4 p-3 rounded-lg ${role === "user" ? "bg-blue-100 text-blue-800" : "bg-gray-100 text-gray-800"}">
                    <p class="text-sm">${content}</p>
                </div>
            `;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function extractBotResponse(html) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const lastMessage = doc.querySelector("#chatMessages > div:last-child p");
            return lastMessage ? lastMessage.textContent : "No response found";
        }
    </script>
</body>

</html>