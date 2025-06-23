<?php
// src/functions.php

function loadJsonFile($filePath, $default) {
    if (!file_exists($filePath)) return $default;
    $data = file_get_contents($filePath);
    return json_decode($data, true) ?? $default;
}

function saveJsonFile($filePath, $data): bool {
    return file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT)) !== false;
}

function addTask(string $task_name): bool {
    $file = __DIR__ . '/tasks.txt';
    $tasks = loadJsonFile($file, []);
    foreach ($tasks as $task) {
        if ($task['name'] === $task_name) return false;
    }
    $tasks[] = ["id" => uniqid(), "name" => $task_name, "completed" => false];
    return saveJsonFile($file, $tasks);
}

function getAllTasks(): array {
    return loadJsonFile(__DIR__ . '/tasks.txt', []);
}

function markTaskAsCompleted(string $task_id, bool $is_completed): bool {
    $file = __DIR__ . '/tasks.txt';
    $tasks = loadJsonFile($file, []);
    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
        }
    }
    return saveJsonFile($file, $tasks);
}

function deleteTask(string $task_id): bool {
    $file = __DIR__ . '/tasks.txt';
    $tasks = loadJsonFile($file, []);
    $tasks = array_filter($tasks, fn($task) => $task['id'] !== $task_id);
    return saveJsonFile($file, array_values($tasks));
}

function generateVerificationCode(): string {
    return strval(rand(100000, 999999));
}

function subscribeEmail(string $email): bool {
    $file = __DIR__ . '/pending_subscriptions.txt';
    $pending = loadJsonFile($file, []);
    if (isset($pending[$email])) return false;
    $code = generateVerificationCode();
    $pending[$email] = ["code" => $code, "timestamp" => time()];
    saveJsonFile($file, $pending);
    $verification_link = "http://localhost/verify.php?email=" . urlencode($email) . "&code=$code";
    $subject = "Verify subscription to Task Planner";
    $message = "<p>Click the link below to verify your subscription to Task Planner:</p><p><a id='verification-link' href='$verification_link'>Verify Subscription</a></p>";
    $headers = "Content-type:text/html;charset=UTF-8\r\nFrom: no-reply@example.com";
    return mail($email, $subject, $message, $headers);
}

function verifySubscription(string $email, string $code): bool {
    $pending_file = __DIR__ . '/pending_subscriptions.txt';
    $subscribers_file = __DIR__ . '/subscribers.txt';
    $pending = loadJsonFile($pending_file, []);
    if (!isset($pending[$email]) || $pending[$email]['code'] !== $code) return false;
    unset($pending[$email]);
    $subs = loadJsonFile($subscribers_file, []);
    if (!in_array($email, $subs)) $subs[] = $email;
    saveJsonFile($pending_file, $pending);
    return saveJsonFile($subscribers_file, $subs);
}

function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/subscribers.txt';
    $subs = loadJsonFile($file, []);
    $subs = array_filter($subs, fn($e) => $e !== $email);
    return saveJsonFile($file, array_values($subs));
}

function sendTaskReminders(): void {
    $subscribers = loadJsonFile(__DIR__ . '/subscribers.txt', []);
    $tasks = loadJsonFile(__DIR__ . '/tasks.txt', []);
    $pending_tasks = array_filter($tasks, fn($task) => !$task['completed']);
    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
    }
}

function sendTaskEmail(string $email, array $pending_tasks): bool {
    $subject = "Task Planner - Pending Tasks Reminder";
    $message = "<h2>Pending Tasks Reminder</h2><p>Here are the current pending tasks:</p><ul>";
    foreach ($pending_tasks as $task) {
        $message .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }
    $unsubscribe_link = "http://localhost/unsubscribe.php?email=" . urlencode($email);
    $message .= "</ul><p><a id='unsubscribe-link' href='$unsubscribe_link'>Unsubscribe from notifications</a></p>";
    $headers = "Content-type:text/html;charset=UTF-8\r\nFrom: no-reply@example.com";
    return mail($email, $subject, $message, $headers);
}
