<?php
require_once 'db.php';

try {
    // 1. حساب إجمالي المهام والمهام المكتملة (SUB-5.2.1)
    $total_tasks_stmt = $pdo->query("SELECT COUNT(*) AS total FROM tasks");
    $total_tasks = $total_tasks_stmt->fetch()['total'] ?? 0;

    $completed_tasks_stmt = $pdo->query("SELECT COUNT(*) AS completed FROM tasks WHERE status = 'completed'");
    $completed_tasks = $completed_tasks_stmt->fetch()['completed'] ?? 0;

    // 2. حساب نسبة إنجاز المشروع الكلية (SUB-5.2.3)
    $completion_percentage = $total_tasks > 0 ? round(($completed_tasks / $total_tasks) * 100, 2) : 0;

    // 3. حساب عدد المهام المكتملة لكل عضو (SUB-5.2.2)
    $member_stats_stmt = $pdo->query("SELECT assigned_to, COUNT(*) AS completed_count 
                                      FROM tasks 
                                      WHERE status = 'completed' AND assigned_to IS NOT NULL AND assigned_to != '' 
                                      GROUP BY assigned_to");
    $member_stats = $member_stats_stmt->fetchAll();

} catch (PDOException $e) {
    die("خطأ في جلب بيانات التقرير: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقارير المشروع - Project Report</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-card h3 {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
        }
    </style>
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
                <li><a href="activity_log.php">سجل النشاط</a></li>
                <li><a href="report.php" class="active">تقارير المشروع</a></li>
            </ul>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <header>
                <h1>تقرير وإحصائيات المشروع (Project Report)</h1>
            </header>

            <!-- بطاقات الإحصائيات العامة -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>إجمالي المهام</h3>
                    <div class="number"><?php echo $total_tasks; ?></div>
                </div>
                <div class="stat-card">
                    <h3>المهام المكتملة</h3>
                    <div class="number" style="color: #28a745;"><?php echo $completed_tasks; ?></div>
                </div>
                <div class="stat-card">
                    <h3>نسبة الإنجاز الكلية</h3>
                    <div class="number" style="color: #17a2b8;"><?php echo $completion_percentage; ?>%</div>
                </div>
            </div>

            <!-- جدول المهام المكتملة لكل عضو -->
            <section class="table-section">
                <h2>المهام المكتملة حسب أعضاء الفريق</h2>
                <br>
                <table>
                    <thead>
                        <tr>
                            <th>عضو الفريق</th>
                            <th>عدد المهام المكتملة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($member_stats)): ?>
                            <tr>
                                <td colspan="2" style="text-align: center;">لا توجد مهام مكتملة مسندة لأعضاء حتى الآن.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($member_stats as $stat): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($stat['assigned_to']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($stat['completed_count']); ?></strong> مهام</td>
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