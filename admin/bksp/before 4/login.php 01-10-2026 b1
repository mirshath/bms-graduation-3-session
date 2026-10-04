<?php
session_start();
include '../database/connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Both fields are required!';
    } else {
        $sql = "SELECT * FROM admin WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = 'No account found with that email!';
        } else {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['admin_name'];
                $_SESSION['role'] = $admin['role'];
                header("Location: index");
                exit();
            } else {
                $error = 'Incorrect password!';
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Student Registration System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>



<body class="min-h-screen bg-[url('./img/BG-LOGO.png')]   flex items-center justify-center">

    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full flex items-center justify-center shadow-lg">
                <!-- <i class="fas fa-graduation-cap text-white text-2xl"></i> -->
                <!-- If you intend to use an image as the primary logo, you might want to replace the <i class="fas fa-graduation-cap"></i> tag above with this image. -->
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRGkKtuN4jwLcLnigNTh1sGRqxzyEdf_HGDow&s" alt="Admin Logo" class="h-full w-full object-contain rounded-full p-2">
            </div>
            <h2 class="mt-6 text-3xl font-bold text-gray-900">Admin Portal</h2>
            <p class="mt-2 text-sm text-gray-600">BMS Graduations</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 relative">
            <?php if ($error): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="relative">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-gray-400"></i>Username
                    </label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-gray-50 focus:bg-white"
                        placeholder="Enter your username">
                </div>

                <div class="relative">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-gray-400"></i>Password
                    </label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-gray-50 focus:bg-white"
                        placeholder="Enter your password">

                    <!-- Eye Icon -->
                    <button type="button" id="togglePassword" style="margin-top: 25px;"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>

                <script>
                    const togglePassword = document.getElementById('togglePassword');
                    const passwordInput = document.getElementById('password');
                    const eyeIcon = togglePassword.querySelector('i');

                    togglePassword.addEventListener('click', function() {
                        // Toggle password visibility
                        const type = passwordInput.type === 'password' ? 'text' : 'password';
                        passwordInput.type = type;

                        // Toggle icon
                        eyeIcon.classList.toggle('fa-eye-slash');
                    });
                </script>


                <button type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition duration-200 hover:scale-105 shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>
            </form>

        </div>

        <!-- Footer -->
        <div class="text-center mt-4">
            <p class="text-xs text-gray-500">
                © 2026 BMS Graduation. All rights reserved.
            </p>
        </div>
    </div>



</body>

</html>