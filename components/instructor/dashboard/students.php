<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "root"; 
$dbname = "sign_language"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all users
$teacher_id = $_SESSION['user']['user_id']; // Get teacher ID from session

$sql = "SELECT DISTINCT u.*
        FROM users u
        JOIN course_enrollments ce ON u.id = ce.user_id
        JOIN courses c ON ce.course_id = c.id
        WHERE u.role = 'student' AND c.created_by = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result(); // Fetch the result


?>

<html>

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
        rel="stylesheet"
        as="style"
        onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&amp;family=Lexend%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />
    <title>Galileo Design</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

</head>

<body class="">
    <!-- <div class="flex flex-wrap justify-between gap-3 p-4">
            <p class="text-[#0e161b] tracking-light text-[32px] font-bold leading-tight min-w-72">Student Analytics</p>
        </div> -->
        <!-- TODO remove grade -->
    <!-- <div class="flex gap-3 p-3 flex-wrap pr-4">
        <div class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e8eef3] pl-4 pr-4">
            <p class="text-[#0e161b] text-sm font-medium leading-normal">1st Grade</p>
        </div>
        <div class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e8eef3] pl-4 pr-4">
            <p class="text-[#0e161b] text-sm font-medium leading-normal">2nd Grade</p>
        </div>
        <div class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e8eef3] pl-4 pr-4">
            <p class="text-[#0e161b] text-sm font-medium leading-normal">3rd Grade</p>
        </div>
        <div class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e8eef3] pl-4 pr-4">
            <p class="text-[#0e161b] text-sm font-medium leading-normal">4th Grade</p>
        </div>
        <div class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e8eef3] pl-4 pr-4">
            <p class="text-[#0e161b] text-sm font-medium leading-normal">5th Grade</p>
        </div>
        <div class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e8eef3] pl-4 pr-4">
            <p class="text-[#0e161b] text-sm font-medium leading-normal">6th Grade</p>
        </div>
        <div class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e8eef3] pl-4 pr-4">
            <p class="text-[#0e161b] text-sm font-medium leading-normal">7th Grade</p>
        </div>
        <div class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-xl bg-[#e8eef3] pl-4 pr-4">
            <p class="text-[#0e161b] text-sm font-medium leading-normal">8th Grade</p>
        </div>
    </div> -->

    <div class="px-1 py-3 @container">
        <div class="relative flex size-full flex-col bg-[#f8fafb] group/design-root overflow-x-hidden" style='font-family: Lexend, "Noto Sans", sans-serif;'>
            <table class="flex-1">
                <thead>
                    <tr class="bg-[#f8fafb]">
                        <th class="table-5a24c0bc-ebfc-4bfd-9cd6-04bf15c81ef0-column-56 px-4 py-3 text-left text-[#0e161b] w-14 text-sm font-medium leading-normal">Username</th>
                        <th class="table-5a24c0bc-ebfc-4bfd-9cd6-04bf15c81ef0-column-176 px-4 py-3 text-left text-[#0e161b] w-[400px] text-sm font-medium leading-normal">Email</th>
                        <th class="table-5a24c0bc-ebfc-4bfd-9cd6-04bf15c81ef0-column-296 px-4 py-3 text-left text-[#0e161b] w-60 text-sm font-medium leading-normal">Role</th>
                        <th class="table-5a24c0bc-ebfc-4bfd-9cd6-04bf15c81ef0-column-416 px-4 py-3 text-left text-[#0e161b] w-[400px] text-sm font-medium leading-normal">Profile Image</th>
                        <th class="table-5a24c0bc-ebfc-4bfd-9cd6-04bf15c81ef0-column-536 px-4 py-3 text-left text-[#0e161b] w-[400px] text-sm font-medium leading-normal">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr class='border-t border-t-[#d1dde6]'>";
                            echo "<td class='px-4 py-2'>" . $row["username"] . "</td>";
                            echo "<td class='px-4 py-2'>" . $row["email"] . "</td>";
                            echo "<td class='px-4 py-2'>" . $row["role"] . "</td>";
                            echo "<td class='px-4 py-2'><img src='" . $row["profile"] . "' alt='Profile' class='w-12 h-12 rounded-full'></td>";
                            echo "<td class='px-4 py-2'>" . $row["created_at"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='px-4 py-2 text-center'>No student found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>

</html>
