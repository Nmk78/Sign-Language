<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$apiUrl = "https://api.aimlapi.com/chat/completions";
$apiKey = "a85337536d384bda8674be80259d73b0";

// Initialize chat history if not set
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Handle incoming messages
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_message'])) {
    $userMessage = trim($_POST['user_message']);

    if (!empty($userMessage)) {
        $_SESSION['chat_history'][] = ["role" => "user", "content" => $userMessage];

        $data = [
            "model" => "gpt-3.5-turbo",
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

<!-- Chatbox UI -->
<div class="fixed bottom-5 right-5">
    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer text-white text-2xl" onclick="toggleChat()">
        💬
    </div>

    <div id="chat-box" class="hidden fixed bottom-24 right-5 w-80 p-4 bg-white border border-gray-300 rounded-lg shadow-lg">
        <h4 class="text-lg font-semibold mb-3">AI Chatbot</h4>

        <!-- Chat Messages -->
        <div class="max-h-52 overflow-y-auto mb-4">
            <?php foreach ($_SESSION["chat_history"] as $message): ?>
                <div class="mb-2 p-2 rounded-lg <?php echo ($message['role'] === 'user') ? 'bg-green-100 text-right' : 'bg-gray-200 text-left'; ?>">
                    <strong class="font-semibold"><?php echo ucfirst(htmlspecialchars($message['role'])); ?>:</strong>
                    <?php echo htmlspecialchars($message['content']); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Message Form -->
        <form method="POST" class="flex">
            <input type="text" name="user_message" placeholder="Type a message..." required
                class="w-full p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-600" />
            <button type="submit" class="p-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 focus:outline-none">
                Send
            </button>
        </form>
    </div>
</div>

<script>
    function toggleChat() {
        const chatBox = document.getElementById("chat-box");
        chatBox.classList.toggle("hidden");
    }
</script>