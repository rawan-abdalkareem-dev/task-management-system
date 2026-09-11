<?php
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
    $projects = $stmt->fetchAll();
} catch (PDOException $e) {
    die("خطأ في جلب المشاريع: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المشاريع - Task Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="dashboard-container">
        <div class="sidebar">
            <h2>لوحة التحكم</h2>
            <ul>
                <li><a href="projects.php" class="active">المشاريع</a></li>
                <li><a href="tasks.php">المهام</a></li>
                <li><a href="kanban.php">لوحة كانبان</a></li>
                <li><a href="activity_log.php">سجل النشاط</a></li>
                <li><a href="report.php">تقارير المشروع</a></li>
            </ul>
        </div>

        <div class="main-content">
            <header>
                <h1>إدارة المشاريع</h1>
                <button class="btn-primary" onclick="openAddProjectModal()">+ إضافة مشروع جديد</button>
            </header>

            <section class="table-section">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم المشروع</th>
                            <th>الوصف</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية المتوقع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($projects)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">لا يوجد مشاريع مضافة حتى الآن.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($projects as $project): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($project['id']); ?></td>
                                    <td><?php echo htmlspecialchars($project['name']); ?></td>
                                    <td><?php echo htmlspecialchars($project['description']); ?></td>
                                    <td><?php echo htmlspecialchars($project['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($project['expected_end_date']); ?></td>
                                    <td>
                                        <button class="btn-action edit" onclick='openEditProjectModal(<?php echo json_encode($project); ?>)'>تعديل</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <!-- نافذة الإضافة والتعديل -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProjectModal()">&times;</span>
            <h2 id="modalTitle">إضافة مشروع جديد</h2>
            <form action="add_project.php" method="POST">
                <input type="hidden" id="project_id" name="project_id" value="">

                <div class="form-group">
                    <label for="name">اسم المشروع:</label>
                    <input type="text" id="name" name="name" placeholder="أدخل اسم المشروع" required>
                </div>
                
                <div class="form-group">
                    <label for="description">الوصف:</label>
                    <textarea id="description" name="description" rows="3" placeholder="تفاصيل المشروع..."></textarea>
                </div>

                <div class="form-group">
                    <label for="start_date">تاريخ البداية:</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="expected_end_date">تاريخ النهاية المتوقع:</label>
                    <input type="date" id="expected_end_date" name="expected_end_date" required>
                </div>

                <button type="submit" class="btn" id="submitBtn">حفظ البيانات</button>
            </form>
        </div>
    </div>

    <script>
        function openAddProjectModal() {
            document.getElementById('modalTitle').innerText = 'إضافة مشروع جديد';
            document.getElementById('submitBtn').innerText = 'حفظ البيانات';
            document.getElementById('project_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('expected_end_date').value = '';
            document.getElementById('projectModal').style.display = 'flex';
        }

        function openEditProjectModal(project) {
            document.getElementById('modalTitle').innerText = 'تعديل المشروع';
            document.getElementById('submitBtn').innerText = 'تحديث البيانات';
            document.getElementById('project_id').value = project.id;
            document.getElementById('name').value = project.name || '';
            document.getElementById('description').value = project.description || '';
            document.getElementById('start_date').value = project.start_date || '';
            document.getElementById('expected_end_date').value = project.expected_end_date || '';
            document.getElementById('projectModal').style.display = 'flex';
        }

        function closeProjectModal() {
            document.getElementById('projectModal').style.display = 'none';
        }
    </script>
</body>
</html>