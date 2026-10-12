<?php
require_once __DIR__ . '/../../partials/header.php';
$topbarLinks = [
    ['label' => 'Active Users', 'url' => '/admin/users', 'active' => false],
    ['label' => 'Provision New User', 'url' => '/admin/users/create', 'active' => true],
    ['label' => 'Deactivated Users', 'url' => '/admin/users/inactive', 'active' => false]
];
require_once __DIR__ . '/../../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Provision New Account</div>
    <div class="card-body">
        <form action="/admin/users/store" method="POST"> <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

            <div class="grid-2">
                <div class="form-group"><label>First Name</label><input type="text" name="first_name"
                        class="form-control" required></div>
                <div class="form-group"><label>Last Name</label><input type="text" name="last_name" class="form-control"
                        required></div>
            </div>

            <div class="grid-2">
                <div class="form-group"><label>Middle Name</label><input type="text" name="middle_name"
                        class="form-control"></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
            </div>

            <div class="grid-2">
                <div class="form-group"><label>Email Address</label><input type="email" name="email"
                        class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Temporary Password</label>
                    <div class="form-inline">
                        <label><input type="radio" name="password_mode" value="generate" checked> Generate
                            password</label>
                        <label class="radio-label"><input type="radio" name="password_mode" value="manual"> Enter
                            manually</label>

                        <input type="text" name="temp_password" id="tempPasswordInput" class="form-control"
                            placeholder="Enter temporary password" disabled>
                        <small class="text-muted">Choose "Generate password" to let the system create a temporary
                            password.</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>System Role</label>
                <select name="role" class="form-control" required>
                    <option value="doctor">Doctor</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create User</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>