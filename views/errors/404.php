<?php
declare(strict_types=1);

$userIsLoggedIn = isset($_SESSION['user_uuid']);
$backLink = $userIsLoggedIn ? '/dashboard' : '/login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="error-body">
    <main class="error-panel">
        <h1>404</h1>
        <h2>Page Not Found</h2>
        <p>The requested page could not be located.</p>
        <a class="btn btn-primary" href="<?php echo htmlspecialchars($backLink); ?>">Return to Dashboard</a>
    </main>
</body>
</html>