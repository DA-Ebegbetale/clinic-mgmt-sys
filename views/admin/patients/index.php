<?php
require_once __DIR__ . '/../../partials/header.php';
$topbarLinks = [
    ['label' => 'Active Patients', 'url' => '/patients', 'active' => false],
    ['label' => 'Archived Patients', 'url' => '/admin/patients/index', 'active' => true]
];
require_once __DIR__ . '/../../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">System-Wide Archived Patients</div>
    <div class="card-body">
        <form action="/admin/patients/index" method="GET" class="search-bar">
            <select name="field" id="searchField" class="form-control">
                <option value="all">View All Records</option>
                <option value="first_name">First Name</option>
                <option value="last_name">Last Name</option>
                <option value="full_name">Full Name</option>
                <option value="phone">Phone Number</option>
                <option value="email">Email Address</option>
                <option value="uuid">System Reference (UUID)</option>
            </select>
            <input type="text" name="value" id="searchValue" class="form-control" disabled>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>Patient UUID</th>
                    <th>Name</th>
                    <th>Archived Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($patients)): ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient->getUuid()); ?></td>
                            <td><?php echo htmlspecialchars($patient->getLastName() . ', ' . $patient->getFirstName()); ?></td>
                            <td><?php echo htmlspecialchars($patient->getArchivedAt()); ?></td>
                            <td>
                                <form action="/patient/restore" method="POST" class="require-confirm"
                                    data-confirm-message="Are you sure you want to restore this patient record?">
                                    <input type="hidden" name="csrf_token"
                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="patient_uuid" value="<?php echo $patient->getUuid(); ?>">
                                    <a href="/patient/show?uuid=<?php echo htmlspecialchars($patient->getUuid()); ?>&from=admin"
                                        class="btn btn-primary">View Full Profile</a>
                                    <button type="submit" class="btn btn-success">Restore Record</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No archived patients found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../partials/footer.php'; ?>