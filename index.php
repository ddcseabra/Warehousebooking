<?php

// ตรวจสอบว่ามีการกดปุ่ม 'register' และส่งข้อมูลมาแบบ POST
if (isset($_POST['register'])) {

    // --- 1. รับข้อมูลจากฟอร์ม + Escape ---
    $email          = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8');
    $username       = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
    $password       = $_POST['password'] ?? '';
    $confirmPassword= $_POST['confirmpassword'] ?? '';
    $name           = htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $companyName    = htmlspecialchars($_POST['company_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $tel            = htmlspecialchars($_POST['tel'] ?? '', ENT_QUOTES, 'UTF-8');

    // --- 2. Validation ---
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format.");
    }

    if ($password !== $confirmPassword) {
        die("Error: Passwords do not match. Please go back and try again.");
    }

    if (strlen($password) < 8) {
        die("Error: Password must be at least 8 characters long.");
    }

    // --- 3. เข้ารหัสรหัสผ่าน ---
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // --- 4. เตรียมข้อมูลผู้ใช้ ---
    $newUser = [
        'id' => bin2hex(random_bytes(16)), // Random ID ที่ปลอดภัยกว่า uniqid()
        'email' => $email,
        'username' => $username,
        'password' => $hashedPassword,
        'name' => $name,
        'company_name' => $companyName,
        'tel' => $tel,
        'registration_date' => date('Y-m-d H:i:s')
    ];

    // --- 5. อ่านไฟล์ JSON ---
    $jsonFile = 'data/user.json';
    $users = [];

    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $users = json_decode($jsonContent, true);

        if (!is_array($users)) {
            $users = [];
        }
    }

    // --- 6. ตรวจสอบ email/username ซ้ำ ---
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            die("Error: This email is already registered.");
        }
        if ($user['username'] === $username) {
            die("Error: This username is already taken.");
        }
    }

    // --- 7. บันทึกผู้ใช้ใหม่ลง JSON ---
    $users[] = $newUser;
    $newJsonContent = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    file_put_contents($jsonFile, $newJsonContent, LOCK_EX);

    // --- 8. แสดงผลสำเร็จ + Redirect ---
    echo '
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <title>Registration Successful</title>
            <style>
                body { font-family: sans-serif; text-align: center; padding-top: 50px; }
                .container { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
                h1 { color: #28a745; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Create Account Successful!</h1>
                <p>Your account has been created successfully.</p>
                <p>You will be redirected to the main page in 3 seconds...</p>
            </div>

            <script>
                setTimeout(function() {
                    window.location.href = "https://ddcseabra.github.io/Warehousebooking/index.html";
                }, 3000); // 3 วินาที
            </script>
        </body>
        </html>
    ';

    // เผื่อกรณีที่ JavaScript ถูกปิด
    header("Refresh: 3; URL=https://ddcseabra.github.io/Warehousebooking/index.html");
    exit();

} else {
    // --- 9. กรณีเข้าหน้าตรง ---
    echo '
        <!DOCTYPE html>
        <html lang="th">
        <head>
            <meta charset="UTF-8">
            <title>Error</title>
            <style>
                body { font-family: sans-serif; text-align: center; padding-top: 50px; }
                .container { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
                h1 { color: #dc3545; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Error: Invalid Access</h1>
                <p>This page cannot be accessed directly. Please complete the registration form.</p>
                <a href="WHAPP/registor.html">Go to Registration Page</a>
            </div>
        </body>
        </html>
    ';
}

?>
