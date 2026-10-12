<?php
declare(strict_types=1);

$userIsLoggedIn = isset($_SESSION['user_uuid']);
$backLink = $userIsLoggedIn ? '/dashboard' : '/login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>400 - Bad Request</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 150px; background: #f8f9fa; color: #333; }
        h1 { font-size: 50px; margin-bottom: 10px; color: #dc3545; }
        p { font-size: 20px; color: #6c757d; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>400 - Bad Request</h1>
    <p>An unexpected clinical or system issue occurred. The incident has been safely logged for review.</p>
    <a class="btn btn-primary" href="<?php echo htmlspecialchars($backLink); ?>">Return to Dashboard</a>
</body>
</html>