<?php
require_once __DIR__ . '/../partials/header.php';

$topbarLinks = [
    ['label' => 'Back to Record', 'url' => '/consultation/show?uuid=' . htmlspecialchars($consultation->getUuid()), 'active' => false],
    ['label' => 'Edit Consultation', 'url' => '/consultation/edit?uuid=' . htmlspecialchars($consultation->getUuid()), 'active' => true]
];
require_once __DIR__ . '/../partials/topbar.php';
$vitals = $consultation->getVitals() ?? [];
?>

<div class="card">
    <div class="card-header">Update Clinical Record</div>
    <div class="card-body">
        <form action="/consultation/update" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="consultation_uuid" value="<?php echo htmlspecialchars($consultation->getUuid()); ?>">

            <fieldset class="form-section">
                <legend>Encounter Details</legend>
                <div class="form-group">
                    <label>Chief Complaint *</label>
                    <textarea name="chief_complaint" class="form-control" rows="2" required><?php echo htmlspecialchars($consultation->getChiefComplaint()); ?></textarea>
                </div>
                <div class="form-group">
                    <label>History of Present Illness</label>
                    <textarea name="history_of_present_illness" class="form-control" rows="4"><?php echo htmlspecialchars($consultation->getHistoryOfPresentIllness() ?? ''); ?></textarea>
                </div>
            </fieldset>

            <fieldset class="form-section mt-4">
                <legend>Vitals</legend>
                <div class="grid-2">
                    <div class="form-group"><label>Blood Pressure</label><input type="text" name="vitals[blood_pressure]" class="form-control" value="<?php echo htmlspecialchars($vitals['blood_pressure'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Heart Rate</label><input type="number" name="vitals[heart_rate]" class="form-control" value="<?php echo htmlspecialchars($vitals['heart_rate'] ?? ''); ?>"></div>
                </div>
                <div class="grid-2">
                    <div class="form-group"><label>Temperature (°C)</label><input type="number" step="0.1" name="vitals[temperature]" class="form-control" value="<?php echo htmlspecialchars($vitals['temperature'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Weight (kg)</label><input type="number" step="0.1" name="vitals[weight]" class="form-control" value="<?php echo htmlspecialchars($vitals['weight'] ?? ''); ?>"></div>
                </div>
            </fieldset>

            <fieldset class="form-section mt-4">
                <legend>Clinical Assessment & Plan</legend>
                <div class="form-group"><label>Assessment</label><textarea name="assessment" class="form-control" rows="3"><?php echo htmlspecialchars($consultation->getAssessment() ?? ''); ?></textarea></div>
                <div class="form-group"><label>Diagnosis</label><input type="text" name="diagnosis" class="form-control" value="<?php echo htmlspecialchars($consultation->getDiagnosis() ?? ''); ?>"></div>
                <div class="form-group"><label>Treatment Plan</label><textarea name="plan" class="form-control" rows="3"><?php echo htmlspecialchars($consultation->getPlan() ?? ''); ?></textarea></div>
                <div class="form-group"><label>Prescription / Medications</label><textarea name="prescription" class="form-control" rows="3"><?php echo htmlspecialchars($consultation->getPrescription() ?? ''); ?></textarea></div>
            </fieldset>

            <fieldset class="form-section mt-4">
                <legend>Follow-Up & Notes</legend>
                <div class="grid-2">
                    <div class="form-group"><label>Follow-Up Date</label><input type="date" name="follow_up_date" class="form-control" value="<?php echo htmlspecialchars(\App\Application\Shared\Formatter\DateTimeFormatter::toFormDate($consultation->getFollowUpDate())); ?>">
                </div>
                <div class="form-group"><label>Additional Notes</label><textarea name="additional_notes" class="form-control" rows="2"><?php echo htmlspecialchars($consultation->getAdditionalNotes() ?? ''); ?></textarea></div>
            </fieldset>

            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">Update Record</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>