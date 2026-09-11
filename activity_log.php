<?php
require_once 'db.php';

try {
    // جلب سجل النشاطات مع عنوان المهمة المرتبطة بها
    $stmt = $pdo->query("SELECT activity_log.*, tasks.title AS task_title 
                         FROM activity_log 
                         LEFT JOIN tasks ON activity_log.task_id = tasks.id 
                         ORDER BY activity_log.id DESC");
    $logs = $stmt->fetchAll();
} catch (PDOException $e) {
    die("خطأ في جلب سجل النشاط: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل النشاط - Activity Log</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="dashboard-container">
        <!-- القائمة الجانبية -->
        <div class="sidebar">
            <h2>لوحة التحكم</h2>
            <ul>
                <li><a href="projects.php">المشاريع</a></li>
                <li><a href="tasks.php">المهام</a></li>
                <li><a href="kanban.php">لوحة كانبان</a></li>
                <li><a href="activity_log.php" class="active">سجل النشاط</a></li>
                <li><a href="report.php">تقارير المشروع</a></li>
            </ul>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <header>
                <h1>سجل النشاط (Activity Log)</h1>
            </header>

            <section class="table-section">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المهمة</th>
                            <th>الحالة السابقة</th>
                            <th>الحالة الجديدة</th>
                            <th>تاريخ ووقت التغيير</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">لا يوجد نشاطات مسجلة حتى الآن.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['id']); ?></td>
                                    <td><?php echo htmlspecialchars($log['task_title'] ?? 'مهمة محذوفة'); ?></td>
                                    <td><span class="badge <?php echo htmlspecialchars($log['old_status']); ?>"><?php echo htmlspecialchars($log['old_status']); ?></span></td>
                                    <td><span class="badge <?php echo htmlspecialchars($log['new_status']); ?>"><?php echo htmlspecialchars($log['new_status']); ?></span></td>
                                    <td><?php echo htmlspecialchars($log['changed_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</body>
</html>