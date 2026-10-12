<?php
require_once __DIR__ . '/icons.php';

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$userRole = $_SESSION['user_role'] ?? 'doctor';

$isActive = static function (array $paths) use ($currentPath): string {
    return in_array($currentPath, $paths, true) ? 'sidebar-link active' : 'sidebar-link';
};
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <span class="brand-line brand-name">Clinic</span>
        <span class="brand-line brand-os">OS</span>

        <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle sidebar" type="button">⟨</button>
    </div>
    <nav class="sidebar-nav">
        <a href="/dashboard" class="<?php echo $isActive(['/dashboard', '/admin/dashboard', '/doctor/dashboard']); ?>">
            <?php echo render_icon('dashboard'); ?>
            <span class="link-text">Dashboard</span>
        </a>

        <?php if ($userRole === 'admin'): ?>
            <a href="/admin/users"
                class="<?php echo $isActive(['/admin/users', '/admin/users/create', '/admin/users/inactive', '/admin/users/show']); ?>">
                <?php echo render_icon('users'); ?>
                <span class="link-text">User Management</span>
            </a>
            <a href="/admin/patients/index" class="<?php echo $isActive(['/admin/patients/index']); ?>">
                <?php echo render_icon('patients') . render_icon('archive'); ?>
                <span class="link-text">Patient Management</span>
            </a>
            <a href="/admin/logs" class="<?php echo $isActive(['/admin/logs', '/doctor/audit', '/security/audit']); ?>">
                <?php echo render_icon('logs'); ?>
                <span class="link-text">Audit Logs</span>
            </a>
        <?php else: ?>
            <a href="/patients"
                class="<?php echo $isActive(['/patients', '/patient/create', '/patient/edit', '/patient/show', '/patient/archived']); ?>">
                <?php echo render_icon('patients'); ?>
                <span class="link-text">My Patients</span>
            </a>
            <a href="/consultations"
                class="<?php echo $isActive(['/consultations', '/consultation/create', '/consultation/edit', '/consultation/show', '/consultations/archived']); ?>">
                <?php echo render_icon('consultations'); ?>
                <span class="link-text">Consultations</span>
            </a>
        <?php endif; ?>

        <a href="/profile" class="<?php echo $isActive(['/profile', '/profile/edit', '/profile/security']); ?>">
            <?php echo render_icon('profile'); ?>
            <span class="link-text">My Profile</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <form action="/logout" method="POST" class="require-confirm"
            data-confirm-message="Are you sure you want to log out of the system?">
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <button type="submit" class="btn-logout">
                <?php echo render_icon('logout'); ?>
                <span class="link-text">Log Out</span>
            </button>
        </form>
    </div>
</aside>