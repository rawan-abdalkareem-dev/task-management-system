<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id  = !empty($_POST['project_id']) ? $_POST['project_id'] : null;
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority    = $_POST['priority'] ?? 'medium';
    $status      = $_POST['status'] ?? 'new';
    $assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;

    if ($project_id && !empty($title)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (project_id, title, description, priority, status, assigned_to) 
                                   VALUES (:project_id, :title, :description, :priority, :status, :assigned_to)");
            
            $stmt->execute([
                ':project_id'  => $project_id,
                ':title'       => $title,
                ':description' => $description,
                ':priority'    => $priority,
                ':status'      => $status,
                ':assigned_to' => $assigned_to
            ]);

            header("Location: tasks.php?success=1");
            exit;

        } catch (PDOException $e) {
            die("فشلت عملية حفظ المهمة: " . $e->getMessage());
        }
    } else {
        die("يرجى اختيار المشروع وتعبئة عنوان المهمة!");
    }
} else {
    header("Location: tasks.php");
    exit;
}
?>