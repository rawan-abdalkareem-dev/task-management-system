<?php
require_once 'db.php';

try {
    // 1. جلب المهام مع اسم المشروع واسم المهمة الرئيسية التابعة لها
    $stmt = $pdo->query("SELECT tasks.*, 
                                projects.name AS project_name, 
                                parent_tasks.title AS parent_title
                         FROM tasks 
                         LEFT JOIN projects ON tasks.project_id = projects.id 
                         LEFT JOIN tasks AS parent_tasks ON tasks.parent_id = parent_tasks.id
                         ORDER BY tasks.id DESC");
    $tasks = $stmt->fetchAll();

    // 2. جلب البيانات للقوائم المنسدلة
    $projects = $pdo->query("SELECT id, name FROM projects ORDER BY name ASC")->fetchAll();
    $parent_task_options = $pdo->query("SELECT id, title FROM tasks WHERE parent_id IS NULL ORDER BY title ASC")->fetchAll();

    // 3. جلب الأعضاء ديناميكياً من جدول users (SUB-4.1.1)
    $team_members = $pdo->query("SELECT id, name FROM users ORDER BY name ASC")->fetchAll();

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
                <li><a href="kanban.php">لوحة كانبان</a></li>
            </ul>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <header>
                <h1>إدارة المهام</h1>
                <button class="btn-primary" onclick="openAddTaskModal()">+ إضافة مهمة جديدة</button>
            </header>

            <section class="table-section">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان المهمة</th>
                            <th>نوع المهمة</th>
                            <th>المشروع</th>
                            <th>المسند إليه</th>
                            <th>الأولوية</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tasks)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">لا يوجد مهام مضافة حتى الآن.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($task['id']); ?></td>
                                    <td>
                                        <?php if (!empty($task['parent_id'])): ?>
                                            <span style="color: #888;">↳ </span>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($task['title']); ?>
                                    </td>
                                    <td>
                                        <?php if (empty($task['parent_id'])): ?>
                                            <span style="font-weight: bold; color: #2c3e50;">رئيسية</span>
                                        <?php else: ?>
                                            <span>فرعية للمهمة الرئيسية: <strong><?php echo htmlspecialchars($task['parent_title'] ?? ''); ?></strong></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($task['project_name'] ?? 'غير محدد'); ?></td>
                                    <td><?php echo htmlspecialchars($task['assigned_to'] ?? 'غير مسند'); ?></td>
                                    <td><span class="badge <?php echo htmlspecialchars($task['priority']); ?>"><?php echo htmlspecialchars($task['priority']); ?></span></td>
                                    <td><span class="badge <?php echo htmlspecialchars($task['status']); ?>"><?php echo htmlspecialchars($task['status']); ?></span></td>
                                    <td>
                                        <button class="btn-action edit" onclick='openEditTaskModal(<?php echo json_encode($task); ?>)'>تعديل</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <!-- النافذة المنبثقة (Modal) للإضافة والتعديل -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeTaskModal()">&times;</span>
            <h2 id="modalTitle">إضافة مهمة جديدة</h2>
            <form action="save_task.php" method="POST">
                <input type="hidden" id="task_id" name="task_id" value="">

                <div class="form-group">
                    <label for="project_id">المشروع التابع له:</label>
                    <select id="project_id" name="project_id" required>
                        <option value="">-- اختر المشروع --</option>
                        <?php foreach ($projects as $proj): ?>
                            <option value="<?php echo $proj['id']; ?>"><?php echo htmlspecialchars($proj['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- نوع المهمة: رئيسية أو فرعية -->
                <div class="form-group">
                    <label for="task_type">نوع المهمة:</label>
                    <select id="task_type" onchange="toggleParentTaskSelect()">
                        <option value="main">رئيسية</option>
                        <option value="sub">فرعية</option>
                    </select>
                </div>

                <!-- قائمة التحديد للمهمة الرئيسية (تظهر عند اختيار "فرعية" فقط) -->
                <div class="form-group" id="parent_task_group" style="display: none;">
                    <label for="parent_id">حدد المهمة الرئيسية التابعة لها:</label>
                    <select id="parent_id" name="parent_id">
                        <option value="">-- اختر المهمة الرئيسية --</option>
                        <?php foreach ($parent_task_options as $p_task): ?>
                            <option value="<?php echo $p_task['id']; ?>"><?php echo htmlspecialchars($p_task['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="title">عنوان المهمة:</label>
                    <input type="text" id="title" name="title" placeholder="أدخل عنوان المهمة" required>
                </div>
                
                <div class="form-group">
                    <label for="description">وصف المهمة:</label>
                    <textarea id="description" name="description" rows="3" placeholder="تفاصيل المهمة..."></textarea>
                </div>

                <!-- قائمة المسند إليه الديناميكية من جدول users -->
                <div class="form-group">
                    <label for="assigned_to">إسناد / توزيع إلى عضو الفريق:</label>
                    <select id="assigned_to" name="assigned_to">
                        <option value="">-- بدون إسناد --</option>
                        <?php foreach ($team_members as $member): ?>
                            <option value="<?php echo htmlspecialchars($member['name']); ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priority">الأولوية:</label>
                    <select id="priority" name="priority">
                        <option value="high">عالية (high)</option>
                        <option value="medium" selected>متوسطة (medium)</option>
                        <option value="low">منخفضة (low)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">الحالة:</label>
                    <select id="status" name="status">
                        <option value="new" selected>جديد (new)</option>
                        <option value="in_progress">قيد العمل (in_progress)</option>
                        <option value="completed">مكتمل (completed)</option>
                    </select>
                </div>

                <button type="submit" class="btn" id="submitBtn">حفظ المهمة</button>
            </form>
        </div>
    </div>

    <script>
        function toggleParentTaskSelect() {
            var taskType = document.getElementById('task_type').value;
            var parentGroup = document.getElementById('parent_task_group');
            if (taskType === 'sub') {
                parentGroup.style.display = 'block';
            } else {
                parentGroup.style.display = 'none';
                document.getElementById('parent_id').value = '';
            }
        }

        function openAddTaskModal() {
            document.getElementById('modalTitle').innerText = 'إضافة مهمة جديدة';
            document.getElementById('submitBtn').innerText = 'حفظ المهمة';
            document.getElementById('task_id').value = '';
            document.getElementById('project_id').value = '';
            document.getElementById('task_type').value = 'main';
            document.getElementById('parent_id').value = '';
            document.getElementById('title').value = '';
            document.getElementById('description').value = '';
            document.getElementById('assigned_to').value = '';
            document.getElementById('priority').value = 'medium';
            document.getElementById('status').value = 'new';
            toggleParentTaskSelect();
            document.getElementById('taskModal').style.display = 'flex';
        }

        function openEditTaskModal(task) {
            document.getElementById('modalTitle').innerText = 'تعديل المهمة';
            document.getElementById('submitBtn').innerText = 'تحديث المهمة';
            document.getElementById('task_id').value = task.id;
            document.getElementById('project_id').value = task.project_id || '';
            
            if (task.parent_id) {
                document.getElementById('task_type').value = 'sub';
                document.getElementById('parent_id').value = task.parent_id;
            } else {
                document.getElementById('task_type').value = 'main';
                document.getElementById('parent_id').value = '';
            }
            toggleParentTaskSelect();

            document.getElementById('title').value = task.title || '';
            document.getElementById('description').value = task.description || '';
            document.getElementById('assigned_to').value = task.assigned_to || '';
            document.getElementById('priority').value = task.priority || 'medium';
            document.getElementById('status').value = task.status || 'new';
            document.getElementById('taskModal').style.display = 'flex';
        }

        function closeTaskModal() {
            document.getElementById('taskModal').style.display = 'none';
        }
    </script>
</body>
</html>