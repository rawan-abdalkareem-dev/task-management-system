<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id     = $_POST['task_id'] ?? null;
    $project_id  = $_POST['project_id'];
    $parent_id   = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
    $title       = $_POST['title'];
    $description = $_POST['description'];
    $assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;
    $priority    = $_POST['priority'];
    $status      = $_POST['status'];

    try {
        if (!empty($task_id)) {
            // تحديث مهمة قائمة
            $stmt = $pdo->prepare("UPDATE tasks SET project_id = :project_id, parent_id = :parent_id, title = :title, description = :description, assigned_to = :assigned_to, priority = :priority, status = :status WHERE id = :id");
            $stmt->execute([
                ':project_id'  => $project_id,
                ':parent_id'   => $parent_id,
                ':title'       => $title,
                ':description' => $description,
                ':assigned_to' => $assigned_to,
                ':priority'    => $priority,
                ':status'      => $status,
                ':id'          => $task_id
            ]);
        } else {
            // إضافة مهمة جديدة
            $stmt = $pdo->prepare("INSERT INTO tasks (project_id, parent_id, title, description, assigned_to, priority, status) VALUES (:project_id, :parent_id, :title, :description, :assigned_to, :priority, :status)");
            $stmt->execute([
                ':project_id'  => $project_id,
                ':parent_id'   => $parent_id,
                ':title'       => $title,
                ':description' => $description,
                ':assigned_to' => $assigned_to,
                ':priority'    => $priority,
                ':status'      => $status
            ]);
        }

        header('Location: tasks.php');
        exit;
    } catch (PDOException $e) {
        die("خطأ أثناء حفظ البيانات: " . $e->getMessage());
    }
}
?>