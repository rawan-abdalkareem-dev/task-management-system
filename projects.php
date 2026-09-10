<?php
// جلب الاتصال بقاعدة البيانات
require_once 'db.php';

try {
    // جلب كافة المشاريع من قاعدة البيانات من الأحدث للأقدم
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
        <!-- القائمة الجانبية -->
        <div class="sidebar">
            <h2>لوحة التحكم</h2>
            <ul>
                <li><a href="projects.php" class="active">المشاريع</a></li>
                <!-- تم التعديل إلى tasks.php -->
                <li><a href="tasks.php">المهام</a></li>
            </ul>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <header>
                <h1>إدارة المشاريع</h1>
                <button class="btn-primary" onclick="openProjectModal()">+ إضافة مشروع جديد</button>
            </header>

            <!-- جدول عرض المشاريع -->
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
                                        <button class="btn-action edit" onclick="openProjectModal()">تعديل</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <!-- نموذج إنشاء/تعديل المشروع -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProjectModal()">&times;</span>
            <h2>إضافة مشروع جديد</h2>
            <form action="add_project.php" method="POST">
                <div class="form-group">
                    <label for="name">اسم المشروع (name):</label>
                    <input type="text" id="name" name="name" placeholder="أدخل اسم المشروع" required>
                </div>
                
                <div class="form-group">
                    <label for="description">الوصف (description):</label>
                    <textarea id="description" name="description" rows="3" placeholder="تفاصيل المشروع..."></textarea>
                </div>

                <div class="form-group">
                    <label for="start_date">تاريخ البداية (start_date):</label>
                    <input type="date" id="start_date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="expected_end_date">تاريخ النهاية المتوقع (expected_end_date):</label>
                    <input type="date" id="expected_end_date" name="expected_end_date" required>
                </div>

                <button type="submit" class="btn">حفظ البيانات</button>
            </form>
        </div>
    </div>

    <script>
        function openProjectModal() {
            document.getElementById('projectModal').style.display = 'flex';
        }
        function closeProjectModal() {
            document.getElementById('projectModal').style.display = 'none';
        }
    </script>
</body>
</html>