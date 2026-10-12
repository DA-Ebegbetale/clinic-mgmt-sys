<?php
require_once __DIR__ . '/../../partials/header.php';
$topbarLinks = [
    ['label' => 'Admin Actions', 'url' => '/admin/logs', 'active' => false],
    ['label' => 'Doctor Actions', 'url' => '/doctor/audit', 'active' => false],
    ['label' => 'Security Events', 'url' => '/security/audit', 'active' => true]
];
require_once __DIR__ . '/../../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Security Audit Trail</div>
    <div class="card-body">
        <form action="/security/audit" method="GET" class="search-bar">
            <input type="date" name="date_from" class="form-control"
                value="<?php echo htmlspecialchars($dateFrom ?? date('Y-01-01')); ?>" placeholder="From Date">
            <input type="date" name="date_to" class="form-control"
                value="<?php echo htmlspecialchars($dateTo ?? date('Y-m-d')); ?>" placeholder="To Date">
            <button type="submit" class="btn btn-primary">Filter by Date</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>Event Type</th>
                    <th>User</th>
                    <th>Agent Information</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['event_type']); ?></td>
                            <td><?php echo htmlspecialchars($log['user_uuid'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($log['device_info'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($log['details'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No security events recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>