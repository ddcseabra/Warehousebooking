<?php

// ตรวจสอบว่ามีการกดปุ่ม 'register' และส่งข้อมูลมาแบบ POST
if (isset($_POST['register'])) {

    // --- 1. รับข้อมูลจากฟอร์ม ---
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmpassword'];
    $name = $_POST['name'];
    $companyName = $_POST['company_name'];
    $tel = $_POST['tel'];

    // --- 2. ตรวจสอบข้อมูล (Validation) ---
    if ($password !== $confirmPassword) {
        die("Error: Passwords do not match. Please go back and try again.");
    }
    if (strlen($password) < 8) {
        die("Error: Password must be at least 8 characters long.");
    }
    // (สามารถเพิ่มการตรวจสอบอื่นๆ ได้ เช่น email ซ้ำ)

    // --- 3. เข้ารหัสรหัสผ่าน (สำคัญมาก!) ---
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // --- 4. เตรียมข้อมูลสำหรับบันทึกลง JSON ---
    $newUser = [
        'id' => uniqid(), // สร้าง ID ที่ไม่ซ้ำกัน
        'email' => $email,
        'username' => $username,
        'password' => $hashedPassword, // เก็บรหัสผ่านที่เข้ารหัสแล้ว
        'name' => $name,
        'company_name' => $companyName,
        'tel' => $tel,
        'registration_date' => date('Y-m-d H:i:s')
    ];

    // --- 5. อ่าน, อัปเดต, และบันทึกไฟล์ JSON ---
    $jsonFile = 'data/user.json';
    
    // อ่านข้อมูลเดิมจากไฟล์
    $users = [];
    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $users = json_decode($jsonContent, true);
    }

    // เพิ่มผู้ใช้ใหม่เข้าไปใน array
    $users[] = $newUser;

    // แปลงกลับเป็น JSON และบันทึกไฟล์
    // JSON_PRETTY_PRINT ให้อ่านง่าย, JSON_UNESCAPED_UNICODE ให้รองรับภาษาไทย
    $newJsonContent = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($jsonFile, $newJsonContent, LOCK_EX);

    // --- 6. ส่งผู้ใช้กลับไปที่หน้า index.html ---
    header('Location: index.html');
    exit(); // จบการทำงานของสคริปต์ทันทีหลัง redirect
}
?>
