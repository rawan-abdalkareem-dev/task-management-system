<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'] ?? null;
    $status  = $_POST['status'] ?? null;

    if ($task_id && $status) {
        try {
            // 1. جلب الحالة القديمة للمهمة قبل التحديث
            $stmt_old = $pdo->prepare("SELECT status FROM tasks WHERE id = :task_id");
            $stmt_old->execute([':task_id' => $task_id]);
            $old_task = $stmt_old->fetch();
            $old_status = $old_task ? $old_task['status'] : null;

            // إذا لم تتغير الحالة فعلياً، لا داعي لتسجيلها
            if ($old_status === $status) {
                echo json_encode(['success' => true]);
                exit;
            }

            // 2. تحديث حالة المهمة في جدول tasks
            $stmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :task_id");
            $stmt->execute([
                ':status'  => $status,
                ':task_id' => $task_id
            ]);

            // 3. تسجيل التغيير في جدول activity_log (تاريخ، وقت، حالة جديدة، حالة قديمة)
            $stmt_log = $pdo->prepare("INSERT INTO activity_log (task_id, old_status, new_status, changed_at) VALUES (:task_id, :old_status, :new_status, NOW())");
            $stmt_log->execute([
                ':task_id'    => $task_id,
                ':old_status' => $old_status,
                ':new_status' => $status
            ]);

            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}
echo json_encode(['success' => false]);
?>