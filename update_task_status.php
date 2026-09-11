<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'] ?? null;
    $status  = $_POST['status'] ?? null;

    if ($task_id && $status) {
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :task_id");
            $stmt->execute([
                ':status'  => $status,
                ':task_id' => $task_id
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
