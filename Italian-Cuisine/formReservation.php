<?php
require "functions.php"; // استدعاء ملف الاتصال بقاعدة البيانات
session_start(); // بدء الجلسة لتخزين الرسائل

$success_message = '';
$error_message = '';
$show_login_msg = false;

try {
    $pdo = getPDOConnection(); // إنشاء الاتصال بقاعدة البيانات

    // تنفيذ الكود فقط إذا تم إرسال النموذج
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // جلب البيانات من النموذج
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $party_size = isset($_POST['party_size']) ? (int)$_POST['party_size'] : 0;

        // تحويل الوقت إلى صيغة 24 ساعة
        $time_24 = $time ? date("H:i:s", strtotime($time)) : '';

        // التحقق مما إذا كان التاريخ والوقت في المستقبل
        $is_future = false;
        if ($date && $time_24) {
            $reservation_datetime = strtotime("$date $time_24");
            if ($reservation_datetime > time()) {
                $is_future = true;
            }
        }

        // جلب client_id إذا كان المستخدم مسجل الدخول
        $client_id = null;
        if (isset($_SESSION['gmail'])) {
            $gmail = $_SESSION['gmail'];

            // جلب معلومات الحساب من جدول account
            $stmt = $pdo->prepare("SELECT Id, AccountType FROM account WHERE LOWER(Gmail) = LOWER(?)");
            $stmt->execute([$gmail]);
            $account = $stmt->fetch();

            // التأكد أن نوع الحساب هو Client
            if ($account && strtolower($account['AccountType']) === 'client') {
                $accountId = $account['Id'];

                // التحقق مما إذا كان موجوداً مسبقًا في جدول client
                $stmt = $pdo->prepare("SELECT 1 FROM client WHERE AccountId = ?");
                $stmt->execute([$accountId]);
                $exists = $stmt->fetch();

                // إذا لم يكن موجوداً، أضفه إلى جدول client
                if (!$exists) {
                    $stmt = $pdo->prepare("INSERT INTO client (AccountId) VALUES (?)");
                    $stmt->execute([$accountId]);
                }

                // تحديد معرف العميل لاستخدامه في الحجز
                $client_id = $accountId;
            }
        }

        // تنفيذ الحجز إذا كانت جميع الشروط صحيحة
        if ($date && $time_24 && $party_size > 0 && $client_id && $is_future) {
            $stmt = $pdo->prepare("
                INSERT INTO reservations (date, time, party_size, client_id) 
                VALUES (?, ?, ?, ?)
            ");
            if ($stmt->execute([$date, $time_24, $party_size, $client_id])) {
                $success_message = "✅ the reservation has been successfully made.";
                 echo '<script>
                setTimeout(function(){
                    window.history.back();
                }, 3000);
            </script>';
            } else {
                $error_message = "❌ Failed to make the reservation."; echo '<script>
                setTimeout(function(){
                    window.history.back();
                }, 3000);
            </script>';
            }
        } elseif (!$is_future) {
            $error_message = "❌ Please select a date and time in the future."; echo '<script>
                setTimeout(function(){
                    window.history.back();
                }, 3000);
            </script>';
        } else {
            $error_message = "❌ Please ensure all fields are filled out or logged in as a client."; echo '<script>
                setTimeout(function(){
                    window.history.back();
                }, 3000);
            </script>';
        }
    }
} catch (PDOException $e) {
    $error_message = "❌ Failed to connect to the database: " . $e->getMessage(); echo '<script>
                setTimeout(function(){
                    window.history.back();
                }, 3000);
            </script>';
}

// عرض رسالة تسجيل الدخول إذا لم يكن المستخدم مسجل الدخول
if (!isset($_SESSION['gmail'])) {
    $show_login_msg = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservation Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; direction: rtl; }
        input, select, button { margin-top: 10px; padding: 5px; width: 200px; }
        .message { font-weight: bold; margin-bottom: 15px; }
        .success { color: green; }
        .error { color: red; }
        label { display: block; margin-top: 15px; }
    </style>
</head>
<body>

<h2>Reservation Form</h2>

<!-- رسالة تطلب من المستخدم تسجيل الدخول إذا لم يكن -->
<?php if ($show_login_msg): ?>
    <div class="message error">
        Please login as a client to proceed with the reservation.
    </div>
<?php endif; ?>

<!-- عرض رسالة النجاح -->
<?php if (!empty($success_message)): ?>
    <div class="message success"><?php echo $success_message; ?></div>
<?php endif; ?>

<!-- عرض رسالة الخطأ -->
<?php if (!empty($error_message)): ?>
    <div class="message error"><?php echo $error_message; ?></div>
<?php endif; ?>

<?php
// جلب معلومات الحجوزات مع اسم العميل
$stmt = $pdo->prepare("
    SELECT r.*, a.FirstName, a.LastName 
    FROM reservations r
    JOIN client c ON r.client_id = c.AccountId
    JOIN account a ON c.AccountId = a.Id
");
$stmt->execute();
$reservations = $stmt->fetchAll();

?>

</body>
</html>