<?php

// 1. ตรวจสอบว่ามีการส่งข้อมูลมาแบบ POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. ดึงข้อมูลจากฟอร์มและทำความสะอาดข้อมูลเบื้องต้นเพื่อป้องกัน XSS
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password']; // ไม่ต้อง htmlspecialchars เพราะจะนำไป hash
    $confirmPassword = $_POST['confirmpassword'];
    $name = htmlspecialchars($_POST['name']);
    $companyName = htmlspecialchars($_POST['company_name']); // ใช้ชื่อที่ไม่มีเว้นวรรค
    $tel = htmlspecialchars($_POST['tel']);
    
    // ตรวจสอบว่า checkbox ถูกติ๊กหรือไม่
    $agreedToTerms = isset($_POST['checkbox']);

    // 3. ตรวจสอบข้อมูล (Validation)
    $errors = []; // สร้าง array ไว้เก็บข้อผิดพลาด

    if (empty($email) || empty($username) || empty($password) || empty($name) || empty($companyName) || empty($tel)) {
        $errors[] = "กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";
    }

    if (strlen($password) < 8) {
        $errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร";
    }

    if (!$agreedToTerms) {
        $errors[] = "คุณต้องยอมรับข้อตกลงและนโยบายความเป็นส่วนตัว";
    }


    // --- ถ้าข้อมูลถูกต้องทั้งหมด (ไม่มี error) ---
    if (empty($errors)) {
        
        // 4. ***สำคัญมาก: เข้ารหัสผ่านก่อนเก็บลงฐานข้อมูล***
        // ห้ามเก็บรหัสผ่านเป็นข้อความธรรมดาเด็ดขาด!
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 5. เตรียมบันทึกข้อมูลลงฐานข้อมูล (Database)
        // โค้ดส่วนนี้เป็น "ตัวอย่าง" คุณต้องไปเขียนโค้ดเชื่อมต่อฐานข้อมูลของคุณเอง (เช่น MySQLi หรือ PDO)
        /*
        
        $servername = "localhost";
        $db_username = "your_db_username";
        $db_password = "your_db_password";
        $dbname = "your_database_name";

        // สร้างการเชื่อมต่อ
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // เตรียมคำสั่ง SQL เพื่อป้องกัน SQL Injection
        $stmt = $conn->prepare("INSERT INTO users (email, username, password, name, company_name, tel) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $email, $username, $hashed_password, $name, $companyName, $tel);

        // รันคำสั่ง
        if ($stmt->execute()) {
            echo "<h1>ลงทะเบียนสำเร็จ!</h1>";
            echo "<p>ขอบคุณสำหรับการลงทะเบียน, คุณ $name.</p>";
            // อาจจะ redirect ไปหน้า login
            // header('Location: login.html');
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();

        */

        // แสดงผลลัพธ์เบื้องต้น (สำหรับทดสอบ)
        echo "<h1>ลงทะเบียนสำเร็จ!</h1>";
        echo "<p><strong>Email:</strong> " . $email . "</p>";
        echo "<p><strong>Username:</strong> " . $username . "</p>";
        echo "<p><strong>Name:</strong> " . $name . "</p>";
        echo "<p><strong>Company:</strong> " . $companyName . "</p>";
        echo "<p><strong>Tel:</strong> " . $tel . "</p>";
        echo "<p><strong>Hashed Password (สำหรับเก็บใน DB):</strong> " . $hashed_password . "</p>";


    } else {
        // --- ถ้าข้อมูลไม่ถูกต้อง (มี error) ---
        echo "<h1>เกิดข้อผิดพลาด!</h1>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
        echo '<a href="javascript:history.back()">กลับไปแก้ไข</a>';
    }


} else {
    // ถ้าไม่ได้เข้ามาหน้านี้ผ่านการ POST ให้ redirect กลับไปหน้าฟอร์ม
    header('Location: register.html'); // แก้ชื่อไฟล์ให้ตรงกับหน้าฟอร์มของคุณ
    exit();
}
?>
