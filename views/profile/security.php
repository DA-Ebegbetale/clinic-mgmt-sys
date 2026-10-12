<?php
require_once __DIR__ . '/../partials/header.php';

$topbarLinks = [
    ['label' => 'Profile Details', 'url' => '/profile', 'active' => false],
    ['label' => 'Security', 'url' => '/profile/security', 'active' => true]
];
require_once __DIR__ . '/../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Change Password</div>
    <div class="card-body">
        <form action="/profile/password" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>