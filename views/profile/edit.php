<?php
require_once __DIR__ . '/../partials/header.php';

$topbarLinks = [
    ['label' => 'Profile Details', 'url' => '/profile', 'active' => true],
    ['label' => 'Security', 'url' => '/profile/security', 'active' => false]
];
require_once __DIR__ . '/../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">My Profile</div>
    <div class="card-body">
        <form action="/profile/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

            <div class="grid-2">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user->getFirstName() ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user->getLastName() ?? ''); ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="<?php echo htmlspecialchars($user->getMiddleName() ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user->getEmail() ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user->getPhone() ?? ''); ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>