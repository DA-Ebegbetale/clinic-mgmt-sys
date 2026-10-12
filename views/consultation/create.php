<?php
require_once __DIR__ . '/../partials/header.php';
$topbarLinks = [
    ['label' => 'Back to Patient', 'url' => '/patient/show?uuid=' . htmlspecialchars($_GET['patient_uuid'] ?? ''), 'active' => false],
    ['label' => 'New Consultation', 'url' => '/consultations/create', 'active' => true]
];
require_once __DIR__ . '/../partials/topbar.php';
?>

<div class="card">
    <div class="card-header">Record New Consultation</div>
    <div class="card-body">
        <form action="/consultations/create" method="POST">
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            <input type="hidden" name="patient_uuid"
                value="<?php echo htmlspecialchars($_GET['patient_uuid'] ?? ''); ?>">

            <fieldset class="form-section">
                <legend>Encounter Details</legend>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Visit Date & Time *</label>
                        <?php $currentServerTime = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d\TH:i'); ?>
                        <input type="datetime-local" name="visit_datetime" class="form-control"
                            value="<?php echo $currentServerTime; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Chief Complaint *</label>
                    <textarea name="chief_complaint" class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label>History of Present Illness</label>
                    <textarea name="history_of_present_illness" class="form-control" rows="4"></textarea>
                </div>
            </fieldset>

            <fieldset class="form-section mt-4">
                <legend>Vitals (Optional)</legend>
                <div class="grid-2">
                    <div class="form-group"><label>Blood Pressure (mmHg)</label><input type="text"
                            name="vitals[blood_pressure]" class="form-control" placeholder="e.g., 120/80"></div>
                    <div class="form-group"><label>Heart Rate (bpm)</label><input type="number"
                            name="vitals[heart_rate]" class="form-control"></div>
                </div>
                <div class="grid-2">
                    <div class="form-group"><label>Temperature (°C)</label><input type="number" step="0.1"
                            name="vitals[temperature]" class="form-control"></div>
                    <div class="form-group"><label>Weight (kg)</label><input type="number" step="0.1"
                            name="vitals[weight]" class="form-control"></div>
                </div>
            </fieldset>

            <fieldset class="form-section mt-4">
                <legend>Clinical Assessment & Plan</legend>
                <div class="form-group"><label>Assessment</label><textarea name="assessment" class="form-control"
                        rows="3"></textarea></div>
                <div class="form-group"><label>Diagnosis</label><input type="text" name="diagnosis"
                        class="form-control"></div>
                <div class="form-group"><label>Treatment Plan</label><textarea name="plan" class="form-control"
                        rows="3"></textarea></div>
                <div class="form-group"><label>Prescription / Medications</label><textarea name="prescription"
                        class="form-control" rows="3"></textarea></div>
            </fieldset>

            <fieldset class="form-section mt-4">
                <legend>Follow-Up & Notes</legend>
                <div class="grid-2">
                    <div class="form-group"><label>Follow-Up Date</label><input type="date" name="follow_up_date"
                            class="form-control"></div>
                </div>
                <div class="form-group"><label>Additional Notes</label><textarea name="additional_notes"
                        class="form-control" rows="2"></textarea></div>
            </fieldset>

            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">Save Clinical Record</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>