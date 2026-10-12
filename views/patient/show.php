<?php
require_once __DIR__ . '/../partials/header.php';
$backUrl = '/patients';
$backLabel = 'Back to Roster';
if (!empty($_GET['from']) && $_GET['from'] === 'admin') {
    $backUrl = '/admin/patients/index';
    $backLabel = 'Back to Patient Management';
}
$topbarLinks = [
    ['label' => $backLabel, 'url' => $backUrl, 'active' => false],
    ['label' => 'Patient Profile', 'url' => '/patient/show?uuid=' . htmlspecialchars($patient->getUuid()), 'active' => true]
];
require_once __DIR__ . '/../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Full Patient Demographics</div>
    <div class="card-body">
        <h2 class="profile-title">
            <?php echo htmlspecialchars($patient->getFirstName() . ' ' . ($patient->getMiddleName() ?? '') . ' ' . $patient->getLastName()); ?>
        </h2>

        <div class="grid-2">
            <div>
                <p><strong>System UUID:</strong> <?php echo htmlspecialchars($patient->getUuid()); ?></p>
                <p><strong>Assigned Doctor:</strong>
                    <?php echo htmlspecialchars($doctor->getLastName() . ', ' . $doctor->getFirstName()); ?></p>
                <p><strong>Doctor Reference:</strong> <?php echo htmlspecialchars($doctor->getUuid()); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($patient->getDateOfBirth() ?? 'N/A'); ?>
                </p>
                <p><strong>Sex:</strong> <?php echo htmlspecialchars(ucfirst($patient->getSex() ?? 'N/A')); ?></p>
                <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($patient->getBloodGroup() ?? 'N/A'); ?></p>
            </div>
            <div>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient->getPhone() ?? 'N/A'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($patient->getEmail() ?? 'N/A'); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($patient->getAddressLine() ?? 'N/A'); ?></p>
                <p><strong>Emergency Contact:</strong>
                    <?php echo htmlspecialchars($patient->getEmergencyContactName() ?? 'N/A'); ?>
                    (<?php echo htmlspecialchars($patient->getEmergencyContactPhone() ?? 'N/A'); ?>)</p>
            </div>
        </div>

        <div class="full-width-section mt-4">
            <p><strong>Allergies:</strong><br>
                <?php echo nl2br(htmlspecialchars($patient->getAllergies() ?? 'None recorded.')); ?></p>
            <p><strong>Chronic Conditions:</strong><br>
                <?php echo nl2br(htmlspecialchars($patient->getChronicConditions() ?? 'None recorded.')); ?></p>
        </div>

        <div class="system-meta-section mt-4 text-muted">
            <p><small><strong>Record Created At:</strong>
                    <?php echo htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($patient->getCreatedAt())); ?></small>
            </p>
            <p><small><strong>Record Updated At:</strong>
                    <?php echo htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($patient->getUpdatedAt())); ?></small>
            </p>
        </div>

        <div class="action-bar mt-4">
            <?php
            $isDoctor = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'doctor');
            $isArchived = !empty($patient->getArchivedAt());
            if ($isDoctor && !$isArchived): ?>
                <a href="/consultation/create?patient_uuid=<?php echo htmlspecialchars($patient->getUuid()); ?>"
                    class="btn btn-primary">Start New Consultation</a>
                <a href="/patient/edit?uuid=<?php echo htmlspecialchars($patient->getUuid()); ?>"
                    class="btn btn-primary">Edit Patient</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">Consultation History</div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Visit Date/Time</th>
                    <th>Chief Complaint</th>
                    <th>Follow-Up Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($consultation)): ?>
                    <?php foreach ($consultation as $visit): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($visit->getVisitDatetime()); ?></td>
                            <td><?php echo htmlspecialchars(substr($visit->getChiefComplaint(), 0, 60)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($visit->getFollowUpDate() ?? 'None'); ?></td>
                            <td>
                                <a href="/consultation/show?uuid=<?php echo htmlspecialchars($visit->getUuid()); ?>"
                                    class="btn btn-primary">View Notes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No past consultations on record.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>