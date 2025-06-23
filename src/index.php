<?php
require_once 'functions.php';

if (isset($_POST['task-name'])) {
    $name = trim($_POST['task-name']);
    if ($name !== '') addTask($name);
    header("Location: index.php");
    exit;
}

if (isset($_POST['complete-task-id'])) {
    markTaskAsCompleted($_POST['complete-task-id'], $_POST['status'] === '1');
    header("Location: index.php");
    exit;
}

if (isset($_POST['delete-task-id'])) {
    deleteTask($_POST['delete-task-id']);
    header("Location: index.php");
    exit;
}

if (isset($_POST['email'])) {
    subscribeEmail(trim($_POST['email']));
    header("Location: index.php");
    exit;
}

$tasks = getAllTasks();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Task Scheduler</title>
</head>
<body>
    <!-- Add Task Form -->
    <form method="POST">
        <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
        <button type="submit" id="add-task">Add Task</button>
    </form>

    <!-- Tasks List -->
    <ul class="tasks-list" id="tasks-list">
        <?php foreach ($tasks as $task): ?>
            <li class="task-item<?= $task['completed'] ? ' completed' : '' ?>">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="complete-task-id" value="<?= $task['id'] ?>">
                    <input type="hidden" name="status" value="<?= $task['completed'] ? '0' : '1' ?>">
                    <input type="checkbox" class="task-status" onchange="this.form.submit();" <?= $task['completed'] ? 'checked' : '' ?> >
                </form>
                <?= htmlspecialchars($task['name']) ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete-task-id" value="<?= $task['id'] ?>">
                    <button type="submit" class="delete-task">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Subscription Form -->
    <form method="POST">
        <input type="email" name="email" required />
        <button type="submit" id="submit-email">Subscribe</button>
    </form>
</body>
</html>