<?php require_once __DIR__ . '/../partials/auth-header.php'; ?>

<div class="card auth-card">
    <div class="card-header">System Login</div>
    <div class="card-body">
        <form action="/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Log In</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/auth-footer.php'; ?>