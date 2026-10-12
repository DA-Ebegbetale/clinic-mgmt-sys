<?php 
require_once __DIR__ . '/../../partials/header.php'; 
$topbarLinks = [
    ['label' => 'Active Users', 'url' => '/admin/users', 'active' => true],
    ['label' => 'Provision New User', 'url' => '/admin/users/create', 'active' => false],
    ['label' => 'Deactivated Users', 'url' => '/admin/users/inactive', 'active' => false]
];
require_once __DIR__ . '/../../partials/topbar.php'; 
?>

<div class="card">
    <div class="card-header">Active System Users</div>
    <div class="card-body">
        <form action="/admin/users" method="GET" class="search-bar">
            <select name="field" id="searchField" class="form-control">
                <option value="all">View All Records</option>
                <option value="first_name">First Name</option>
                <option value="last_name">Last Name</option>
                <option value="full_name">Full Name</option>
                <option value="email">Email Address</option>
                <option value="phone">Phone</option>
                <option value="uuid">System Reference (UUID)</option>
            </select>
            <input type="text" name="value" id="searchValue" class="form-control" disabled>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['users'])): ?>
                    <?php foreach ($data['users'] as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user->getLastName() . ', ' . $user->getFirstName()); ?></td>
                            <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                            <td class="text-capitalize"><?php echo htmlspecialchars($user->getRole()); ?></td>
                            <td>
                                <form action="/admin/users/reset-password" method="POST" class="require-confirm" data-confirm-message="Are you sure you want to reset the password for this user?">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="target_uuid" value="<?php echo $user->getUuid(); ?>">
                                    <button type="submit" class="btn btn-primary">Reset Pass</button>
                                </form>
                                <form action="/admin/users/deactivate" method="POST" class="require-confirm" data-confirm-message="Are you sure you want to deactivate this user?">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="target_uuid" value="<?php echo $user->getUuid(); ?>">
                                    <button type="submit" class="btn btn-danger">Deactivate</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No active users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>