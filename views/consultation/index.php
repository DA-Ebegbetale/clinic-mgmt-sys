<?php 
require_once __DIR__ . '/../partials/header.php'; 
$topbarLinks = [
    ['label' => 'Active Consultations', 'url' => '/consultations', 'active' => true],
    ['label' => 'Archived Records', 'url' => '/consultations/archived', 'active' => false]
];
require_once __DIR__ . '/../partials/topbar.php'; 
?>

<div class="card">
    <div class="card-header">Active Clinical Records</div>
    <div class="card-body">
        <form action="/consultations" method="GET" class="search-bar">
            <select name="field" id="searchField" class="form-control">
                <option value="all">View All Records</option>
                <option value="chief_complaint">Chief Complaint</option>
                <option value="diagnosis">Diagnosis</option>
                <option value="uuid">Record Reference (UUID)</option>
            </select>
            <input type="text" name="value" id="searchValue" class="form-control" disabled>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Visit Date/Time</th>
                    <th>Chief Complaint</th>
                    <th>Diagnosis</th>
                    <th>Follow-Up Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($consultations)): ?>
                    <?php foreach ($consultations as $consultation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($consultation->getVisitDatetime()); ?></td>
                            <td><?php echo htmlspecialchars(substr($consultation->getChiefComplaint(), 0, 40)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars(substr($consultation->getDiagnosis() ?? 'Pending', 0, 30)); ?></td>
                            <td><?php echo htmlspecialchars($consultation->getFollowUpDate() ?? 'None'); ?></td>
                            <td>
                                <a href="/consultation/show?uuid=<?php echo htmlspecialchars($consultation->getUuid()); ?>" class="btn btn-primary">View</a>
                                <a href="/consultation/edit?uuid=<?php echo htmlspecialchars($consultation->getUuid()); ?>" class="btn btn-primary">Edit</a>
                                <form action="/consultations/archive" method="POST" class="require-confirm inline-form" data-confirm-message="Archive this clinical note?">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="consultation_uuid" value="<?php echo htmlspecialchars($consultation->getUuid()); ?>">
                                    <button type="submit" class="btn btn-danger">Archive</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No active consultations found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>