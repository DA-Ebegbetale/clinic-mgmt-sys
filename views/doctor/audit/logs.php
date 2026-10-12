<?php
require_once __DIR__ . '/../../partials/header.php';
$topbarLinks = [
    ['label' => 'Admin Actions', 'url' => '/admin/logs', 'active' => false],
    ['label' => 'Doctor Actions', 'url' => '/doctor/audit', 'active' => true],
    ['label' => 'Security Events', 'url' => '/security/audit', 'active' => false]
];
require_once __DIR__ . '/../../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Doctor Action Audit Trail</div>
    <div class="card-body">
        <form action="/doctor/audit" method="GET" class="search-bar">
            <input type="date" name="date_from" class="form-control"
                value="<?php echo htmlspecialchars($dateFrom ?? date('Y-01-01')); ?>" placeholder="From Date">
            <input type="date" name="date_to" class="form-control"
                value="<?php echo htmlspecialchars($dateTo ?? date('Y-m-d')); ?>" placeholder="To Date">
            <button type="submit" class="btn btn-primary">Filter by Date</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>Created At</th>
                    <th>Action</th>
                    <th>Target Type</th>
                    <th>Target UUID</th>
                    <th>Doctor ID</th>
                    <th>Meta Data</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                            <td><?php echo htmlspecialchars($log['target_type']); ?></td>
                            <td><?php echo htmlspecialchars($log['target_uuid'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($log['doctor_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['meta'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No audit logs recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>