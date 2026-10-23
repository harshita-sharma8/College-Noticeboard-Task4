<?php
header('Content-Type: application/json');

require_once '../config/Database.php';
require_once '../classes/CategoryNotice.php';
require_once '../classes/Notification.php';

try {
    $db = (new Database())->getConnection();
    $noticeManager = new CategoryNotice($db);
    $notificationManager = new Notification($db);

    $category = $_GET['category'] ?? 'All';

    $notices = $noticeManager->getNoticesByCategory($category);
    $notifications = $notificationManager->getLatestNotifications();
    $count = $notificationManager->getNotificationCount();

    echo json_encode([
        'status' => 'success',
        'category' => $category,
        'count' => count($notices),
        'unread_notifications' => $count,
        'notices' => $notices,
        'notifications' => $notifications
    ]);
} catch(Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>