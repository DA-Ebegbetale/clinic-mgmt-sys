<?php
require_once __DIR__ . '/../partials/header.php';
$topbarLinks = [
    ['label' => 'Back to List', 'url' => '/consultations', 'active' => false],
    ['label' => 'Clinical Note', 'url' => '/consultation/show?uuid=' . htmlspecialchars($consultation->getUuid()), 'active' => true]
];
require_once __DIR__ . '/../partials/topbar.php';
$vitals = $consultation->getVitals() ?? [];
?>

<div class="card">
    <div class="card-header">
        <span>Clinical Consultation Note</span>
        <a href="/consultations/export?uuid=<?php echo htmlspecialchars($consultation->getUuid()); ?>"
            class="btn btn-success" target="_blank">Export to PDF</a>
    </div>
    <div class="card-body">

        <div class="grid-2 mb-4">
            <div>
                <p><strong>System UUID:</strong> <?php echo htmlspecialchars($consultation->getUuid()); ?></p>
                <p><strong>Visit Date/Time:</strong>
                    <?php echo htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($consultation->getVisitDatetime())); ?>
                </p>
                <p><strong>Patient:</strong>
                    <?php echo htmlspecialchars($patient->getLastName() . ', ' . $patient->getFirstName()); ?></p>
            </div>
            <div>
                <p><strong>Attending Doctor:</strong> Dr.
                    <?php echo htmlspecialchars($doctor->getLastName() . ', ' . $doctor->getFirstName()); ?>
                </p>
                <p><strong>Doctor Reference:</strong> <?php echo htmlspecialchars($doctor->getUuid()); ?></p>
                <p><strong>Record Created:</strong>
                    <?php echo htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($consultation->getCreatedAt())); ?>
                </p>
                <p><strong>Last Updated:</strong>
                    <?php echo htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($consultation->getUpdatedAt())); ?>
                </p>
            </div>
        </div>

        <div class="full-width-section mt-4">
            <h4>Chief Complaint</h4>
            <p><?php echo nl2br(htmlspecialchars($consultation->getChiefComplaint())); ?></p>
        </div>

        <?php if ($consultation->getHistoryOfPresentIllness()): ?>
            <div class="full-width-section mt-4">
                <h4>History of Present Illness</h4>
                <p><?php echo nl2br(htmlspecialchars($consultation->getHistoryOfPresentIllness())); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($vitals)): ?>
            <div class="full-width-section mt-4">
                <h4>Recorded Vitals</h4>
                <ul>
                    <?php foreach ($vitals as $key => $value): ?>
                        <li><strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?>:</strong>
                            <?php echo htmlspecialchars((string) $value); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid-2 mt-4">
            <div>
                <h4>Assessment</h4>
                <p><?php echo nl2br(htmlspecialchars($consultation->getAssessment() ?? 'None recorded')); ?></p>
            </div>
            <div>
                <h4>Diagnosis</h4>
                <p><?php echo htmlspecialchars($consultation->getDiagnosis() ?? 'Pending'); ?></p>
            </div>
        </div>

        <div class="grid-2 mt-4">
            <div>
                <h4>Treatment Plan</h4>
                <p><?php echo nl2br(htmlspecialchars($consultation->getPlan() ?? 'None recorded')); ?></p>
            </div>
            <div>
                <h4>Prescription</h4>
                <p><?php echo nl2br(htmlspecialchars($consultation->getPrescription() ?? 'None recorded')); ?></p>
            </div>
        </div>

        <div class="full-width-section mt-4">
            <h4>Follow-Up & Notes</h4>
            <p><strong>Follow-Up Date:</strong>
                <?php echo $consultation->getFollowUpDate()
                    ? htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toHuman($consultation->getFollowUpDate()))
                    : 'None scheduled'; ?>
            </p>
            <p><strong>Additional Notes:</strong><br>
                <?php echo nl2br(htmlspecialchars($consultation->getAdditionalNotes() ?? 'None recorded')); ?></p>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>