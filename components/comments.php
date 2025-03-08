<!-- Comments Section -->
<div class="bg-white p-6 rounded-lg shadow-sm">
    <h3 class="text-lg font-semibold mb-4">Comments</h3>

    <!-- Comment Form -->
    <form id="commentForm" action="/handlers/addComment.php" method="POST">
        <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
        <input type="hidden" name="user_id" id="userId">

        <div class="mb-4">
            <textarea
                name="comment"
                id="commentText"
                rows="3"
                class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-[#4A90E2]"
                placeholder="Add a comment..." required></textarea>
        </div>
        <button
            type="submit"
            id="submitComment"
            class="bg-[#4A90E2] text-white px-4 py-2 rounded-md hover:bg-[#357abd] focus:outline-none focus:ring-2 focus:ring-[#4A90E2] focus:ring-opacity-50">
            Post Comment
        </button>
        <p id="loading" class="text-sm text-gray-500 hidden">Posting...</p>
    </form>

    <!-- Comments List -->
    <div id="commentsList">
        <?php if (empty($comments)): ?>
            <div class="text-center py-4 text-gray-500">
                <p>No comments yet. Be the first to comment!</p>
            </div>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="border-b pb-4 mb-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-700 font-semibold">
                            <?php
                            if (!empty($comment['profile_image'])):
                            ?>
                                <img src="<?php echo htmlspecialchars($comment['profile_image']); ?>" alt="Profile" class="w-10 h-10 rounded-full">
                            <?php else:
                                $username = $comment['username'] ?? 'User' . $comment['user_id'];
                                $initials = strtoupper(substr($username, 0, 1));
                                echo $initials;
                            endif;
                            ?>
                        </div>
                        <div>
                            <p class="font-medium"><?php echo htmlspecialchars($comment['username'] ?? 'User' . $comment['user_id']); ?></p>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($comment['comment']); ?></p>
                            <p class="text-sm text-gray-500"><?php echo formatTimeAgo($comment['created_at']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- JavaScript for AJAX Submission -->
<script>
    document.getElementById("commentForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);
        let submitButton = document.getElementById("submitComment");
        let loadingText = document.getElementById("loading");

        submitButton.disabled = true;
        loadingText.classList.remove("hidden");

        fetch("/handlers/addComment.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let commentsList = document.getElementById("commentsList");

                // Create new comment HTML
                let newComment = `
                    <div class="border-b pb-4 mb-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-700 font-semibold">
                                ${data.profile_image ? `<img src="${data.profile_image}" alt="Profile" class="w-10 h-10 rounded-full">` : data.initials}
                            </div>
                            <div>
                                <p class="font-medium">${data.username}</p>
                                <p class="text-gray-600 mb-2">${data.comment}</p>
                                <p class="text-sm text-gray-500">Just now</p>
                            </div>
                        </div>
                    </div>
                `;

                // Insert new comment at the top
                commentsList.insertAdjacentHTML("afterbegin", newComment);
                document.getElementById("commentText").value = ""; // Clear textarea
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error))
        .finally(() => {
            submitButton.disabled = false;
            loadingText.classList.add("hidden");
        });
    });
</script>
