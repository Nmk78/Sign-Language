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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="fixed bottom-6 right-6">
        <div class="relative">
            <button id="chatIcon" class="w-16 h-16 bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-300">
                💬
            </button>
            <div id="chatBox" class="hidden absolute bottom-20 right-0 w-80 bg-white shadow-lg rounded-lg p-4 transition-all duration-500 transform scale-95 opacity-0">
                <h4 class="text-lg font-bold mb-2">AI Chatbot</h4>
                <div class="max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 space-y-2 p-2 border rounded-lg">
                    <?php foreach ($_SESSION['chat_history'] as $message): ?>
                        <div class="p-2 rounded-md text-sm <?php echo $message['role'] === 'user' ? 'bg-green-100 text-right' : 'bg-gray-200 text-left'; ?>">
                            <strong><?php echo ucfirst($message['role']); ?>:</strong> <?php echo htmlspecialchars($message['content']); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form method="POST" class="mt-3 flex space-x-2">
                    <input type="text" name="user_message" class="flex-1 border p-2 rounded-lg" placeholder="Type a message..." required>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Send</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const chatIcon = document.getElementById("chatIcon");
        const chatBox = document.getElementById("chatBox");
        
        chatIcon.addEventListener("click", () => {
            chatBox.classList.toggle("hidden");
            if (!chatBox.classList.contains("hidden")) {
                chatBox.classList.add("scale-100", "opacity-100");
                chatBox.classList.remove("scale-95", "opacity-0");
            } else {
                chatBox.classList.add("scale-95", "opacity-0");
                chatBox.classList.remove("scale-100", "opacity-100");
            }
        });
    </script>
</body>
</html>