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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Function to check URL query parameters and open/close chatbox accordingly
        function checkChatState() {
            const urlParams = new URLSearchParams(window.location.search);
            const chatState = urlParams.get('chat'); // Get 'chat' parameter from URL

            let chatBox = document.getElementById("chat-box");
            if (chatState === 'open') {
                chatBox.classList.add("block"); // Show the chatbox
            } else {
                chatBox.classList.add("hidden"); // Hide the chatbox using Tailwind's 'hidden' class
            }
        }

        // Run the checkChatState function when the page loads
        window.onload = checkChatState;
    </script>
</head>

<body class="font-sans bg-gray-100">
    <div class="chat-widget fixed bottom-5 right-5">
        <div class="chat-icon w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer text-white text-2xl">
            <!-- This link will toggle the state of the chatbox via URL parameter -->
            <a href="<?php echo (isset($_GET['chat']) && $_GET['chat'] === 'open') ? '?' : '?chat=open'; ?>" class="text-white">💬</a>
        </div>
        <div id="chat-box" class="chat-box fixed bottom-24 right-5 w-80 p-4 bg-white border border-gray-300 rounded-lg shadow-lg">
            <h4 class="text-lg font-semibold mb-3">AI Chatbot</h4>
            <div class="chat-history max-h-52 overflow-y-auto mb-4">
                <?php foreach ($_SESSION['chat_history'] as $message): ?>
                    <div class="chat-message mb-2 p-2 rounded-lg <?= $message['role'] === 'user' ? 'bg-green-100 text-right' : 'bg-gray-200 text-left' ?>">
                        <strong class="font-semibold"><?= ucfirst($message['role']) ?>:</strong> <?= htmlspecialchars($message['content']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="POST" class="flex">
                <input type="text" name="user_message" placeholder="Type a message..." required class="w-full p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-600" />
                <button type="submit" class="p-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 focus:outline-none">Send</button>
            </form>
        </div>
    </div>
</body>

</html>