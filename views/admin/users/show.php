<?php
require_once __DIR__ . '/../../partials/header.php';
$topbarLinks = [
    ['label' => 'Back to Active Users', 'url' => '/admin/users', 'active' => false],
    ['label' => 'User Security Profile', 'url' => '/admin/users/security/' . $user->getUuid(), 'active' => true]
];
require_once __DIR__ . '/../../partials/topbar.php';
$user = $data['user'];
?>

<div class="card">
    <div class="card-header">Profile Information</div>
    <div class="card-body">
        <div class="profile-details">
            <p><strong>System Reference (UUID):</strong> <?php echo htmlspecialchars($user->getUuid()); ?></p>
            <p><strong>Full Name:</strong>
                <?php echo htmlspecialchars($user->getLastName() . ', ' . $user->getFirstName() . ' ' . ($user->getMiddleName() ?? '')); ?>
            </p>
            <p><strong>Email Address:</strong> <?php echo htmlspecialchars($user->getEmail()); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($user->getPhone() ?? 'N/A'); ?></p>
            <p><strong>System Role:</strong> <span class="text-capitalize"><?php echo htmlspecialchars($user->getRole()); ?></span></p>
            <p><strong>Account Status:</strong> <?php echo $user->getIsActive() === 1 ? 'Active' : 'Deactivated'; ?></p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">System Stamp Metadata</div>
    <div class="card-body">
        <div class="profile-details">
            <p><strong>Last Login Timestamp:</strong>
                <?php echo $user->getLastLoginAt()
                    ? htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($user->getLastLoginAt()))
                    : 'Never logged in'; ?>
            </p>
            <p><strong>Record Created At:</strong>
                <?php echo htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($user->getCreatedAt())); ?>
            </p>
            <p><strong>Record Last Updated At:</strong>
                <?php echo htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($user->getUpdatedAt())); ?>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>