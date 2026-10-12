<?php
require_once __DIR__ . '/../partials/header.php';
$topbarLinks = [
    ['label' => 'Active Patients', 'url' => '/patients', 'active' => true],
];
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'doctor') {
    $topbarLinks[] = ['label' => 'Register Patient', 'url' => '/patient/create', 'active' => false];
}
$topbarLinks[] = ['label' => 'Archived Patients', 'url' => '/patient/archived', 'active' => false];
require_once __DIR__ . '/../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Active Patient Roster</div>
    <div class="card-body">
        <form action="/patients" method="GET" class="search-bar">
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
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($patients)): ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient->getLastName() . ', ' . $patient->getFirstName()); ?></td>
                            <td><?php echo htmlspecialchars($patient->getDateOfBirth() ?? 'Not Provided'); ?></td>
                            <td><?php echo htmlspecialchars($patient->getPhone() ?? 'Not Provided'); ?></td>
                            <td>
                                <a href="/patient/show?uuid=<?php echo htmlspecialchars($patient->getUuid()); ?>"
                                    class="btn btn-primary">View</a>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'doctor'): ?>
                                    <a href="/patient/edit?uuid=<?php echo htmlspecialchars($patient->getUuid()); ?>"
                                        class="btn btn-primary">Edit</a>
                                <?php endif; ?>
                                <form action="/patient/archive" method="POST" class="require-confirm inline-form"
                                    data-confirm-message="Archive this patient record?">
                                    <input type="hidden" name="csrf_token"
                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <input type="hidden" name="patient_uuid"
                                        value="<?php echo htmlspecialchars($patient->getUuid()); ?>">
                                    <button type="submit" class="btn btn-danger">Archive</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No active patients found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>