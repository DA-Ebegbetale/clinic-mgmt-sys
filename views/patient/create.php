<?php 
require_once __DIR__ . '/../partials/header.php'; 
$topbarLinks = [
    ['label' => 'Active Patients', 'url' => '/patients', 'active' => false],
    ['label' => 'Register Patient', 'url' => '/patient/create', 'active' => true],
    ['label' => 'Archived Patients', 'url' => '/patient/archived', 'active' => false]
];
require_once __DIR__ . '/../partials/topbar.php'; 
?>

<div class="card">
    <div class="card-header">Register New Patient</div>
    <div class="card-body">
        <form action="/patient/create" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

            <fieldset class="form-section">
                <legend>Core Identification</legend>
                <div class="grid-2">
                    <div class="form-group"><label>First Name *</label><input type="text" name="first_name" class="form-control" required></div>
                    <div class="form-group"><label>Last Name *</label><input type="text" name="last_name" class="form-control" required></div>
                </div>
                <div class="grid-2">
                    <div class="form-group"><label>Middle Name</label><input type="text" name="middle_name" class="form-control"></div>
                    <div class="form-group"><label>Date of Birth</label><input type="date" name="date_of_birth" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label>Sex</label>
                    <select name="sex" class="form-control">
                        <option value="">Select...</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </fieldset>

            <fieldset class="form-section mt-4">
                <legend>Contact Information</legend>
                <div class="grid-2">
                    <div class="form-group"><label>Phone Number</label><input type="text" name="phone" class="form-control"></div>
                    <div class="form-group"><label>Email Address</label><input type="email" name="email" class="form-control"></div>
                </div>
                <div class="form-group"><label>Physical Address Line</label><input type="text" name="address_line" class="form-control"></div>
                <div class="grid-2">
                    <div class="form-group"><label>Emergency Contact Name</label><input type="text" name="emergency_contact_name" class="form-control"></div>
                    <div class="form-group"><label>Emergency Contact Phone</label><input type="text" name="emergency_contact_phone" class="form-control"></div>
                </div>
            </fieldset>

            <fieldset class="form-section mt-4">
                <legend>Medical Baseline</legend>
                <div class="form-group"><label>Blood Group</label><input type="text" name="blood_group" class="form-control" maxlength="5"></div>
                <div class="form-group"><label>Allergies</label><textarea name="allergies" class="form-control" rows="3"></textarea></div>
                <div class="form-group"><label>Chronic Conditions</label><textarea name="chronic_conditions" class="form-control" rows="3"></textarea></div>
            </fieldset>

            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">Register Patient</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>