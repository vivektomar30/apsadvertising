<?php
// Admin Dashboard
require_once '../config/database.php';
require_once '../models/User.php';
require_once '../models/Contact.php';
require_once '../models/Feedback.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$userModel = new User($db);
$contactModel = new Contact($db);
$feedbackModel = new Feedback($db);

// Get statistics
$totalContacts = $contactModel->getCount();
$newContacts = $contactModel->getCount('new');
$totalFeedback = $feedbackModel->getCount();
$totalUsers = $userModel->getCount();

// Get recent contacts
$recentContacts = $contactModel->getAll(1, 10);
$recentFeedback = $feedbackModel->getAll(1, 10, false);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APS Advertising - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-red: #FF003C;
            --accent-red: #FF3366;
            --primary-black: #000000;
            --dark-grey: #1A1A1A;
            --off-white: #F5F5F5;
            --pure-white: #FFFFFF;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--dark-grey);
            color: var(--off-white);
            line-height: 1.6;
        }

        .admin-header {
            background: var(--primary-black);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--primary-red);
        }

        .admin-header h1 {
            color: var(--pure-white);
            font-size: 1.5rem;
        }

        .admin-header h1 i {
            color: var(--primary-red);
            margin-right: 10px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: var(--primary-red);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: var(--accent-red);
        }

        .admin-container {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        .sidebar {
            width: 250px;
            background: var(--primary-black);
            padding: 20px 0;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: var(--off-white);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 0, 60, 0.1);
            color: var(--primary-red);
            border-left-color: var(--primary-red);
        }

        .nav-link i {
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            padding: 30px;
        }

        .dashboard-title {
            color: var(--pure-white);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-red);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-red);
            box-shadow: 0 10px 20px rgba(255, 0, 60, 0.2);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-red);
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--pure-white);
        }

        .stat-card p {
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .section-title {
            color: var(--pure-white);
            margin: 30px 0 20px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-responsive {
            overflow-x: auto;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: rgba(255, 0, 60, 0.2);
            color: var(--primary-red);
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .table td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-new { background: #ffc107; color: #000; }
        .status-replied { background: #28a745; color: white; }
        .status-archived { background: #6c757d; color: white; }
        .status-approved { background: #28a745; color: white; }
        .status-pending { background: #ffc107; color: #000; }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .btn-view { background: #17a2b8; color: white; }
        .btn-edit { background: #ffc107; color: #000; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-approve { background: #28a745; color: white; }

        .btn-group {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <h1><i class="fas fa-cogs"></i> APS Advertising Admin</h1>
        <div class="user-info">
            <span>Welcome, <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></span>
            <form method="POST" action="logout.php">
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </header>

    <!-- Main Container -->
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="contacts.php" class="nav-link">
                        <i class="fas fa-envelope"></i> Contact Messages
                        <?php if ($newContacts > 0): ?>
                        <span style="background: var(--primary-red); color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8rem;">
                            <?php echo $newContacts; ?> new
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="feedback.php" class="nav-link">
                        <i class="fas fa-comments"></i> Feedback
                    </a>
                </li>
                <li class="nav-item">
                    <a href="content.php" class="nav-link">
                        <i class="fas fa-file-alt"></i> Content
                    </a>
                </li>
                <li class="nav-item">
                    <a href="partners.php" class="nav-link">
                        <i class="fas fa-handshake"></i> Partners
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../index.html" class="nav-link" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Website
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h1 class="dashboard-title">Dashboard Overview</h1>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3><?php echo $totalContacts; ?></h3>
                    <p>Total Contact Messages</p>
                    <?php if ($newContacts > 0): ?>
                    <small style="color: var(--primary-red);">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $newContacts; ?> new
                    </small>
                    <?php endif; ?>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3><?php echo $totalFeedback; ?></h3>
                    <p>Feedback Received</p>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>15+</h3>
                    <p>Active Partners</p>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Admin Users</p>
                </div>
            </div>

            <!-- Recent Contact Messages -->
            <h2 class="section-title">
                <i class="fas fa-envelope"></i> Recent Contact Messages
                <a href="contacts.php" style="font-size: 0.9rem; color: var(--primary-red); text-decoration: none; margin-left: auto;">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </h2>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentContacts as $contact): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($contact['name']); ?></td>
                            <td><?php echo htmlspecialchars($contact['email']); ?></td>
                            <td><?php echo htmlspecialchars($contact['service']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $contact['status']; ?>">
                                    <?php echo ucfirst($contact['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($contact['created_at'])); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="contact_view.php?id=<?php echo $contact['id']; ?>" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if ($contact['status'] == 'new'): ?>
                                    <a href="contact_reply.php?id=<?php echo $contact['id']; ?>" class="btn btn-approve">
                                        <i class="fas fa-reply"></i> Reply
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Feedback -->
            <h2 class="section-title">
                <i class="fas fa-comments"></i> Recent Feedback
                <a href="feedback.php" style="font-size: 0.9rem; color: var(--primary-red); text-decoration: none; margin-left: auto;">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </h2>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Service</th>
                            <th>Feedback</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentFeedback as $feedback): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($feedback['name']); ?></td>
                            <td>
                                <?php 
                                echo str_repeat('★', $feedback['rating']);
                                echo str_repeat('☆', 5 - $feedback['rating']);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($feedback['service']); ?></td>
                            <td><?php echo substr(htmlspecialchars($feedback['feedback']), 0, 50); ?>...</td>
                            <td>
                                <span class="status-badge <?php echo $feedback['is_approved'] ? 'status-approved' : 'status-pending'; ?>">
                                    <?php echo $feedback['is_approved'] ? 'Approved' : 'Pending'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="feedback_view.php?id=<?php echo $feedback['id']; ?>" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if (!$feedback['is_approved']): ?>
                                    <a href="feedback_approve.php?id=<?php echo $feedback['id']; ?>" class="btn btn-approve">
                                        <i class="fas fa-check"></i> Approve
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Auto refresh every 60 seconds
        setTimeout(() => {
            window.location.reload();
        }, 60000);
    </script>
</body>
</html>