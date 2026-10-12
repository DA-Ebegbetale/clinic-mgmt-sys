<?php
require_once __DIR__ . '/../../partials/header.php';
$topbarLinks = [
    ['label' => 'Active Users', 'url' => '/admin/users', 'active' => false],
    ['label' => 'Provision New User', 'url' => '/admin/users/create', 'active' => false],
    ['label' => 'Deactivated Users', 'url' => '/admin/users/inactive', 'active' => true]
];
require_once __DIR__ . '/../../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Deactivated Users</div>
    <div class="card-body">
        <form action="/admin/users/inactive" method="GET" class="search-bar">
            <select name="field" id="inactiveSearchField" class="form-control">
                <option value="all">View All Inactive</option>
                <option value="first_name">First Name</option>
                <option value="last_name">Last Name</option>
                <option value="full_name">Full Name</option>
                <option value="email">Email Address</option>
                <option value="phone">Phone</option>
                <option value="uuid">System Reference (UUID)</option>
            </select>
            <input type="text" name="value" id="inactiveSearchValue" class="form-control" disabled>
            <button type="submit" class="btn btn-primary">Search Inactive Users</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user->getLastName() . ', ' . $user->getFirstName()); ?></td>
                            <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                            <td><?php echo htmlspecialchars($user->getPhone() ?? 'Not Provided'); ?></td>
                            <td>
                                <form action="/admin/users/restore" method="POST" class="require-confirm"
                                    data-confirm-message="Are you sure you want to restore this user?">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="target_uuid" value="<?php echo $user->getUuid(); ?>">
                                    <button type="submit" class="btn btn-success">Restore User</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No deactivated users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>