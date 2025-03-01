<?php
session_start();

$apiUrl = "https://api.aimlapi.com/chat/completions";
$apiKey = "a85337536d384bda8674be80259d73b0";

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

function sendMessage($userMessage) {
    global $apiUrl, $apiKey;

    // Only send the current user message instead of the entire chat history
    $data = [
        "model" => "gpt-4o",
        "messages" => [["role" => "user", "content" => $userMessage]],
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
        return $responseData['choices'][0]['message']['content'] ?? "No response found";
    }

    return "Error: Unable to get a response";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_message'])) {
    $userMessage = trim($_POST['user_message']);
    
    if (!empty($userMessage)) {
        // Still add to chat history for display purposes
        $_SESSION['chat_history'][] = ["role" => "user", "content" => $userMessage];
        $botResponse = sendMessage($userMessage);
        $_SESSION['chat_history'][] = ["role" => "assistant", "content" => $botResponse];
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
    <style>
        /* Custom scrollbar styles */
        .scrollbar-custom::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-custom::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .scrollbar-custom::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="fixed bottom-6 right-6 z-50">
        <div class="relative">
            <button id="chatIcon" class="w-16 h-16 bg-blue-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-blue-600 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </button>
            <div id="chatBox" class="hidden absolute bottom-20 right-0 w-96 bg-white shadow-2xl rounded-lg overflow-hidden transition-all duration-300 transform scale-95 opacity-0">
                <div class="bg-blue-500 text-white p-4">
                    <h4 class="text-lg font-bold">AI Chatbot</h4>
                </div>
                <div class="h-96 overflow-y-auto scrollbar-custom p-4 space-y-4">
                    <?php foreach ($_SESSION['chat_history'] as $message): ?>
                        <div class="flex <?php echo $message['role'] === 'user' ? 'justify-end' : 'justify-start'; ?>">
                            <div class="max-w-3/4 p-3 rounded-lg <?php echo $message['role'] === 'user' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <p class="text-sm"><?php echo htmlspecialchars($message['content']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form method="POST" class="p-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex space-x-2">
                        <input type="text" name="user_message" class="flex-1 border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Type a message..." required>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
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
                setTimeout(() => {
                    chatBox.classList.add("scale-100", "opacity-100");
                    chatBox.classList.remove("scale-95", "opacity-0");
                }, 10);
            } else {
                chatBox.classList.add("scale-95", "opacity-0");
                chatBox.classList.remove("scale-100", "opacity-100");
            }
        });
    </script>
</body>
</html>