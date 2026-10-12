<?php
require_once __DIR__ . '/../partials/header.php';
$topbarLinks = [
    ['label' => 'Active Patients', 'url' => '/patients', 'active' => false],
    ['label' => 'Register Patient', 'url' => '/patient/create', 'active' => false],
    ['label' => 'Archived Patients', 'url' => '/patient/archived', 'active' => true]
];
require_once __DIR__ . '/../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Archived Patient Records</div>
    <div class="card-body">
        <form action="/patient/archived" method="GET" class="search-bar">
            <select name="field" id="archiveSearchField" class="form-control">
                <option value="all">View All Archived</option>
                <option value="first_name">First Name</option>
                <option value="last_name">Last Name</option>
                <option value="full_name">Full Name</option>
                <option value="phone">Phone Number</option>
                <option value="email">Email Address</option>
                <option value="uuid">System Reference (UUID)</option>
            </select>
            <input type="text" name="value" id="archiveSearchValue" class="form-control" disabled>
            <button type="submit" class="btn btn-primary">Search Archives</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Date of Birth</th>
                    <th>Archived Timestamp</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($patients)): ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient->getLastName() . ', ' . $patient->getFirstName()); ?></td>
                            <td><?php echo htmlspecialchars($patient->getDateOfBirth() ?? 'Not Provided'); ?></td>
                            <td><?php echo htmlspecialchars($patient->getArchivedAt()); ?></td>
                            <td>
                                <form action="/patient/restore" method="POST" class="require-confirm inline-form"
                                    data-confirm-message="Restore this patient record to active status?">
                                    <input type="hidden" name="csrf_token"
                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="patient_uuid"
                                        value="<?php echo htmlspecialchars($patient->getUuid()); ?>">
                                    <a href="/patient/show?uuid=<?php echo htmlspecialchars($patient->getUuid()); ?>"
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

<?php require_once __DIR__ . '/../partials/footer.php'; ?>