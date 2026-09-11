<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id        = !empty($_POST['project_id']) ? $_POST['project_id'] : null;
    $name              = trim($_POST['name'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $start_date        = $_POST['start_date'] ?? '';
    $expected_end_date = $_POST['expected_end_date'] ?? '';

    if (!empty($name) && !empty($start_date) && !empty($expected_end_date)) {
        try {
            if ($project_id) {
                // تعديل مشروع قائم
                $stmt = $pdo->prepare("UPDATE projects 
                                       SET name = :name, 
                                           description = :description, 
                                           start_date = :start_date, 
                                           expected_end_date = :expected_end_date 
                                       WHERE id = :project_id");
                $stmt->execute([
                    ':name'              => $name,
                    ':description'       => $description,
                    ':start_date'        => $start_date,
                    ':expected_end_date' => $expected_end_date,
                    ':project_id'        => $project_id
                ]);
            } else {
                // إضافة مشروع جديد
                $stmt = $pdo->prepare("INSERT INTO projects (name, description, start_date, expected_end_date) 
                                       VALUES (:name, :description, :start_date, :expected_end_date)");
                $stmt->execute([
                    ':name'              => $name,
                    ':description'       => $description,
                    ':start_date'        => $start_date,
                    ':expected_end_date' => $expected_end_date
                ]);
            }

            header("Location: projects.php?success=1");
            exit;

        } catch (PDOException $e) {
            die("فشلت عملية حفظ المشروع: " . $e->getMessage());
        }
    } else {
        die("يرجى تعبئة كافة الحقول المطلوبة!");
    }
} else {
    header("Location: projects.php");
    exit;
}
?>