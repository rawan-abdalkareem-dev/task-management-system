<?php
require_once 'db.php';

try {
    // 1. جلب كافة المهام من قاعدة البيانات مع أسماء المشاريع
    $stmt = $pdo->query("SELECT tasks.*, 
                                projects.name AS project_name 
                         FROM tasks 
                         LEFT JOIN projects ON tasks.project_id = projects.id 
                         ORDER BY tasks.id DESC");
    $tasks = $stmt->fetchAll();

    // 2. تصنيف المهام ديناميكياً حسب الحالة (status)
    $todo_tasks        = array_filter($tasks, fn($t) => $t['status'] === 'new');
    $in_progress_tasks = array_filter($tasks, fn($t) => $t['status'] === 'in_progress');
    $done_tasks        = array_filter($tasks, fn($t) => $t['status'] === 'completed');

} catch (PDOException $e) {
    die("خطأ في جلب البيانات: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة كانبان - Kanban Board</title>
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
                <li><a href="kanban.php" class="active">لوحة كانبان</a></li>
            </ul>
        </div>

        <!-- المحتوى الرئيسي -->
        <div class="main-content">
            <header>
                <h1>لوحة متابعة المهام (Kanban Board)</h1>
            </header>

            <!-- أعمدة كانبان ديناميكية -->
            <div class="kanban-board">
                
                <!-- العمود الأول: To Do -->
                <div class="kanban-column" ondragover="allowDrop(event)" ondrop="drop(event, 'new')">
                    <div class="column-header todo-header">
                        <h3>To Do (<?php echo count($todo_tasks); ?>)</h3>
                    </div>
                    <div class="column-body">
                        <?php foreach ($todo_tasks as $task): ?>
                            <div class="kanban-card" id="task-<?php echo $task['id']; ?>" data-id="<?php echo $task['id']; ?>" draggable="true" ondragstart="drag(event)">
                                <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                                <p><?php echo htmlspecialchars($task['description']); ?></p>
                                <span class="badge <?php echo htmlspecialchars($task['priority']); ?>"><?php echo htmlspecialchars($task['priority']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- العمود الثاني: In Progress -->
                <div class="kanban-column" ondragover="allowDrop(event)" ondrop="drop(event, 'in_progress')">
                    <div class="column-header progress-header">
                        <h3>In Progress (<?php echo count($in_progress_tasks); ?>)</h3>
                    </div>
                    <div class="column-body">
                        <?php foreach ($in_progress_tasks as $task): ?>
                            <div class="kanban-card" id="task-<?php echo $task['id']; ?>" data-id="<?php echo $task['id']; ?>" draggable="true" ondragstart="drag(event)">
                                <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                                <p><?php echo htmlspecialchars($task['description']); ?></p>
                                <span class="badge <?php echo htmlspecialchars($task['priority']); ?>"><?php echo htmlspecialchars($task['priority']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- العمود الثالث: Done -->
                <div class="kanban-column" ondragover="allowDrop(event)" ondrop="drop(event, 'completed')">
                    <div class="column-header done-header">
                        <h3>Done (<?php echo count($done_tasks); ?>)</h3>
                    </div>
                    <div class="column-body">
                        <?php foreach ($done_tasks as $task): ?>
                            <div class="kanban-card" id="task-<?php echo $task['id']; ?>" data-id="<?php echo $task['id']; ?>" draggable="true" ondragstart="drag(event)">
                                <h4><?php echo htmlspecialchars($task['title']); ?></h4>
                                <p><?php echo htmlspecialchars($task['description']); ?></p>
                                <span class="badge <?php echo htmlspecialchars($task['priority']); ?>"><?php echo htmlspecialchars($task['priority']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("text", ev.target.id);
        }

        function drop(ev, newStatus) {
            ev.preventDefault();
            var data = ev.dataTransfer.getData("text");
            var cardElement = document.getElementById(data);
            var columnBody = ev.currentTarget.querySelector('.column-body');
            
            if (cardElement && columnBody) {
                columnBody.appendChild(cardElement);

                // تحديث حالة المهمة في قاعدة البيانات تلقائياً عبر Fetch API
                var taskId = cardElement.getAttribute('data-id');
                updateTaskStatus(taskId, newStatus);
            }
        }

        function updateTaskStatus(taskId, status) {
            var formData = new FormData();
            formData.append('task_id', taskId);
            formData.append('status', status);

            fetch('update_task_status.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json())
              .then(data => {
                  if(!data.success) {
                      alert('حدث خطأ أثناء تحديث حالة المهمة في قاعدة البيانات.');
                  }
              }).catch(err => console.error(err));
        }
    </script>
</body>
</html>