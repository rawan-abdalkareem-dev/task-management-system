<?php
require_once 'db.php';

try {
    // 1. جلب كافة المهام مع بيانات المشروع والمستخدم المسند إليه
    $stmt = $pdo->query("SELECT tasks.*, 
                                projects.name AS project_name, 
                                users.name AS user_name 
                         FROM tasks 
                         LEFT JOIN projects ON tasks.project_id = projects.id 
                         LEFT JOIN users ON tasks.assigned_to = users.id 
                         ORDER BY tasks.id DESC");
    $tasks = $stmt->fetchAll();

    // 2. جلب المشاريع لقائمة الاختيار
    $projects = $pdo->query("SELECT id, name FROM projects ORDER BY name ASC")->fetchAll();

    // 3. جلب المستخدمين لقائمة الاختيار
    $users = $pdo->query("SELECT id, name FROM users ORDER BY name ASC")->fetchAll();

} catch (PDOException $e) {
    die("خطأ في جلب البيانات: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المهام - Task Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="dashboard-container">
        <!-- القائمة الجانبية -->
        <div class="sidebar">
            <h2>لوحة التحكم</h2>
            <ul>
                <li><a href="projects.php">المشاريع</a></li>
                <li><a href="tasks.php" class="active">المهام</a></li>
            </ul>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <header>
                <h1>إدارة المهام</h1>
                <button class="btn-primary" onclick="openTaskModal()">+ إضافة مهمة جديدة</button>
            </header>

            <!-- جدول عرض المهام -->
            <section class="table-section">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان المهمة</th>
                            <th>المشروع</th>
                            <th>المسند إليه</th>
                            <th>الوصف</th>
                            <th>الأولوية</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tasks)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">لا يوجد مهام مضافة حتى الآن.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['id']); ?></td>
                                    <td><?php echo htmlspecialchars($task['title']); ?></td>
                                    <td><?php echo htmlspecialchars($task['project_name'] ?? 'غير محدد'); ?></td>
                                    <td><?php echo htmlspecialchars($task['user_name'] ?? 'غير مسند'); ?></td>
                                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                                    <td><span class="badge <?php echo htmlspecialchars($task['priority']); ?>"><?php echo htmlspecialchars($task['priority']); ?></span></td>
                                    <td><span class="badge <?php echo htmlspecialchars($task['status']); ?>"><?php echo htmlspecialchars($task['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <!-- نموذج إضافة مهمة جديدة -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeTaskModal()">&times;</span>
            <h2>إضافة مهمة جديدة</h2>
            <form action="add_task.php" method="POST">
                <div class="form-group">
                    <label for="project_id">المشروع التابع له (project_id):</label>
                    <select id="project_id" name="project_id" required>
                        <option value="">-- اختر المشروع --</option>
                        <?php foreach ($projects as $proj): ?>
                            <option value="<?php echo $proj['id']; ?>"><?php echo htmlspecialchars($proj['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">عنوان المهمة (title):</label>
                    <input type="text" id="title" name="title" placeholder="أدخل عنوان المهمة" required>
                </div>
                
                <div class="form-group">
                    <label for="description">وصف المهمة (description):</label>
                    <textarea id="description" name="description" rows="3" placeholder="تفاصيل المهمة..."></textarea>
                </div>

                <div class="form-group">
                    <label for="assigned_to">إسناد إلى مستخدم (assigned_to):</label>
                    <select id="assigned_to" name="assigned_to">
                        <option value="">-- بدون إسناد --</option>
                        <?php foreach ($users as $usr): ?>
                            <option value="<?php echo $usr['id']; ?>"><?php echo htmlspecialchars($usr['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">الأولوية (priority):</label>
                    <select id="priority" name="priority">
                        <option value="high">عالية (high)</option>
                        <option value="medium" selected>متوسطة (medium)</option>
                        <option value="low">منخفضة (low)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">الحالة (status):</label>
                    <select id="status" name="status">
                        <option value="new" selected>جديد (new)</option>
                        <option value="in_progress">قيد العمل (in_progress)</option>
                        <option value="completed">مكتمل (completed)</option>
                    </select>
                </div>

                <button type="submit" class="btn">حفظ المهمة</button>
            </form>
        </div>
    </div>

    <script>
        function openTaskModal() {
            document.getElementById('taskModal').style.display = 'flex';
        }
        function closeTaskModal() {
            document.getElementById('taskModal').style.display = 'none';
        }
    </script>
</body>
</html>