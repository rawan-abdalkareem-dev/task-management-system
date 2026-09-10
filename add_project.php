<?php
// 1. استدعاء ملف الاتصال بقاعدة البيانات
require_once 'db.php';

// 2. التأكد من أن الطلب أُرسل باستخدام POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. استلام البيانات القادمة من النموذج
    $name              = trim($_POST['name'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $start_date        = $_POST['start_date'] ?? '';
    $expected_end_date = $_POST['expected_end_date'] ?? '';

    // 4. التحقق من عدم ترك الحقول المطلوبة فارغة
    if (!empty($name) && !empty($start_date) && !empty($expected_end_date)) {
        try {
            // 5. كتابة استعلام الإدخال إلى جدول projects
            $stmt = $pdo->prepare("INSERT INTO projects (name, description, start_date, expected_end_date) 
                                   VALUES (:name, :description, :start_date, :expected_end_date)");
            
            // 6. تنفيذ الاستعلام مع إرسال القيم
            $stmt->execute([
                ':name'              => $name,
                ':description'       => $description,
                ':start_date'        => $start_date,
                ':expected_end_date' => $expected_end_date
            ]);

            // 7. العودة تلقائياً لصفحة المشاريع PHP بعد النجاح
            header("Location: projects.php?success=1");
            exit;

        } catch (PDOException $e) {
            // في حال وجود خطأ في قاعدة البيانات
            die("فشلت عملية حفظ المشروع: " . $e->getMessage());
        }
    } else {
        die("يرجى تعبئة كافة الحقول المطلوبة!");
    }
} else {
    // إذا حاول أحد فتح الملف مباشرة بدون إرسال Form
    header("Location: projects.php");
    exit;
}
?>