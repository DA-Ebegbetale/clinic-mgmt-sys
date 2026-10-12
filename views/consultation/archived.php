<?php
require_once __DIR__ . '/../partials/header.php';
$topbarLinks = [
    ['label' => 'Active Consultations', 'url' => '/consultations', 'active' => false],
    ['label' => 'Archived Records', 'url' => '/consultations/archived', 'active' => true]
];
require_once __DIR__ . '/../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Archived Clinical Records</div>
    <div class="card-body">
        <form action="/consultations/archived" method="GET" class="search-bar">
            <select name="field" id="archiveSearchField" class="form-control">
                <option value="all">View All Archived</option>
                <option value="chief_complaint">Chief Complaint</option>
                <option value="diagnosis">Diagnosis</option>
                <option value="uuid">Record Reference (UUID)</option>
            </select>
            <input type="text" name="value" id="archiveSearchValue" class="form-control" disabled>
            <button type="submit" class="btn btn-primary">Search Archives</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>Visit Date/Time</th>
                    <th>Diagnosis</th>
                    <th>Archived Timestamp</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($consultations)): ?>
                    <?php foreach ($consultations as $consultation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($consultation->getVisitDatetime()); ?></td>
                            <td><?php echo htmlspecialchars($consultation->getDiagnosis() ?? 'Pending'); ?></td>
                            <td><?php echo htmlspecialchars($consultation->getArchivedAt()); ?></td>
                            <td>
                                <form action="/consultations/restore" method="POST" class="require-confirm inline-form"
                                    data-confirm-message="Restore this clinical record to active status?">
                                    <input type="hidden" name="csrf_token"
                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="consultation_uuid"
                                        value="<?php echo htmlspecialchars($consultation->getUuid()); ?>">
                                    <a href="/consultation/show?uuid=<?php echo htmlspecialchars($consultation->getUuid()); ?>"
                                        class="btn btn-primary">View Note</a>
                                    <button type="submit" class="btn btn-success">Restore Record</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No archived consultations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>