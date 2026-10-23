<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: security/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    // If any student will try to go in admin_dashboard he/she gets back on index.php
    header("Location: index.php");
    exit();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/Database.php';
require_once 'classes/Notice.php';

$db = (new Database())->getConnection();
$noticeManager = new Notice($db);
$message = "";

// Variables for Edit mode
$editMode = false;
$editNotice = ['id' => '', 'title' => '', 'category' => 'Events', 'content' => ''];

// 1. DELETE ACTION
if (isset($_GET['delete_id'])) {
    if ($noticeManager->deleteNotice((int)$_GET['delete_id'])) {
        $_SESSION['flash_message'] = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4 text-sm'>Notice deleted successfully!</div>";
    } else {
        $_SESSION['flash_message'] = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4 text-sm'>Failed to delete notice.</div>";
    }
    header("Location: admin_dashboard.php");
    exit();
}

// 2. EDIT FETCH ACTION
if (isset($_GET['edit_id'])) {
    $fetched = $noticeManager->getNoticeById((int)$_GET['edit_id']);
    if ($fetched) {
        $editMode = true;
        $editNotice = $fetched;
    }
}

// 3. FORM SUBMISSION (CREATE or UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $noticeManager->setTitle($_POST['title']);
    $noticeManager->setContent($_POST['content']);
    $noticeManager->setCategory($_POST['category']);

  
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $noticeManager->setId((int)$_POST['id']);
        
        if ($noticeManager->updateNotice()) {
            $_SESSION['flash_message'] = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4 text-sm'>Notice updated successfully!</div>";
        } else {
            $_SESSION['flash_message'] = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4 text-sm'>Failed to update notice.</div>";
        }
        
    } else {
        // Create / Publish Action
        if ($noticeManager->createNotice()) {
            $_SESSION['flash_message'] = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4 text-sm'>Notice published successfully!</div>";
        } else {
            $_SESSION['flash_message'] = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4 text-sm'>Failed to publish notice.</div>";
        }
    }

    // CRITICAL PRG REDIRECTION STEP:
    header("Location: admin_dashboard.php");
    exit();

}

$notices = $noticeManager->getAllNotices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Notice Board CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-5xl mx-auto space-y-6">
        
        <!-- Navbar Header -->
        <div class="bg-white p-5 rounded-lg shadow-sm flex justify-between items-center border">
            <div>
                <h1 class="text-xl font-bold text-gray-800">⚙️ Admin Control Panel</h1>
                <p class="text-xs text-gray-500">Manage, post, edit, and purge campus notices</p>
            </div>
            <a href="security/logout.php" class="btn btn-danger bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-md text-xs">Logout</a>
            <a href="index.php" target="_blank" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-md text-xs font-semibold transition">View Student Board ↗</a>
        </div>

        <?php 
        if (isset($_SESSION['flash_message'])) {
            echo $_SESSION['flash_message'];
            unset($_SESSION['flash_message']); 
        } 
        ?>

        <!-- Create / Edit Notice Form -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h2 class="text-md font-bold mb-4 text-gray-800 flex items-center gap-2">
                <?= $editMode ? "✏️ Edit Notice (#" . $editNotice['id'] . ")" : "📌 Publish New Notice" ?>
            </h2>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?= $editMode ? 'update' : 'create' ?>">
                <?php if ($editMode): ?>
                    <input type="hidden" name="id" value="<?= $editNotice['id'] ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($editNotice['title']) ?>" required class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                        <select name="category" required class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <?php foreach (['Events', 'Holidays', 'Exams','Internships', 'Placements'] as $cat): ?>
                                <option value="<?= $cat ?>" <?= $editNotice['category'] === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Content</label>
                    <textarea name="content" rows="3" required class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"><?= htmlspecialchars($editNotice['content']) ?></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium transition">
                        <?= $editMode ? 'Update Notice' : 'Publish Notice' ?>
                    </button>
                    <?php if ($editMode): ?>
                        <a href="admin_dashboard.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm font-medium transition">Cancel Edit</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Read & Actions Table -->
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h2 class="text-md font-bold mb-4 text-gray-800">Manage Existing Notices</h2>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b text-xs text-gray-500 uppercase tracking-wider">
                            <th class="p-3">Category</th>
                            <th class="p-3">Title</th>
                            <th class="p-3">Date</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php if (empty($notices)): ?>
                            <tr><td colspan="4" class="p-4 text-center text-gray-400">No notices posted yet.</td></tr>
                        <?php else: foreach ($notices as $n): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-3">
                                    <span class="bg-blue-50 text-blue-700 text-xs px-2.5 py-1 rounded font-semibold border border-blue-100">
                                        <?= htmlspecialchars($n['category']) ?>
                                    </span>
                                </td>
                                <td class="p-3 font-medium text-gray-800"><?= htmlspecialchars($n['title']) ?></td>
                                <td class="p-3 text-gray-400 text-xs"><?= $n['created_at'] ?></td>
                                <td class="p-3 text-right space-x-3">
                                    <a href="admin_dashboard.php?edit_id=<?= $n['id'] ?>" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Edit</a>
                                    <a href="admin_dashboard.php?delete_id=<?= $n['id'] ?>" onclick="return confirm('Are you sure you want to delete this notice?');" class="text-red-600 hover:text-red-800 text-xs font-semibold">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>