<?php
declare(strict_types=1); 
ob_start();

// 1. Composer Autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Presentation\Core\ExceptionHandler;
use App\Presentation\Core\Request;
use App\Presentation\Core\Session;
use App\Presentation\Middleware\CsrfMiddleware;
use App\Infrastructure\Container\AppContainer;
use Dotenv\Dotenv;

// 2. Global Exception Handler
set_exception_handler([ExceptionHandler::class, 'handle']);

// 3. Load Environment Variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// 4. Boot Secure Session & Inactivity Timeout
Session::start();

// 5. Initialize the Request
$request = new Request();

// 6. Security Middleware: CSRF Protection
\App\Presentation\Middleware\CsrfMiddleware::generateToken();
CsrfMiddleware::verify($request);

// 7. Initialize Dependency Injection Container
$container = new AppContainer();

// ---------------------------------------------------------
// REPOSITORIES BINDING
// ---------------------------------------------------------
$container->bind(\App\Domain\User\UserRepositoryInterface::class, function() {
    return new \App\Infrastructure\Persistence\MySQLUserRepository();
});

$container->bind(\App\Domain\Admin\AdminAuditRepositoryInterface::class, function() {
    return new \App\Infrastructure\Persistence\MySQLAdminAuditRepository();
});

$container->bind(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class, function () {
        return new \App\Infrastructure\Persistence\MySQLDoctorAuditRepository();
    });

$container->bind(\App\Domain\Security\SecurityAuditRepositoryInterface::class, function() {
    return new \App\Infrastructure\Persistence\MySQLSecurityAuditRepository();
});

$container->bind(\App\Domain\Patient\PatientRepositoryInterface::class, function() {
    return new \App\Infrastructure\Persistence\MySQLPatientRepository();
});

$container->bind(\App\Domain\Consultation\ConsultationRepositoryInterface::class, function() {
    return new \App\Infrastructure\Persistence\MySQLConsultationRepository();
});

// ---------------------------------------------------------
// AUTHENTICATION SERVICE & CONTROLLER BINDINGS
// ---------------------------------------------------------
$container->bind(\App\Application\Auth\Services\LoginService::class, function($c) {
    return new \App\Application\Auth\Services\LoginService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class),
        new \App\Infrastructure\Security\BcryptPasswordHasher(),
        $c->get(\App\Domain\Security\SecurityAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Presentation\Controllers\AuthController::class, function($c) {
    return new \App\Presentation\Controllers\AuthController(
        $c->get(\App\Application\Auth\Services\LoginService::class),
        $c->get(\App\Domain\Security\SecurityAuditRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

// ---------------------------------------------------------
// PROFILE SERVICES & CONTROLLER BINDINGS
// ---------------------------------------------------------
$container->bind(\App\Application\Auth\Services\ChangePasswordService::class, function($c) {
    return new \App\Application\Auth\Services\ChangePasswordService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class),
        new \App\Infrastructure\Security\BcryptPasswordHasher(),
        $c->get(\App\Domain\Security\SecurityAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Auth\Services\UpdateUserProfileService::class, function($c) {
    return new \App\Application\Auth\Services\UpdateUserProfileService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class),
        $c->get(\App\Domain\Security\SecurityAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Auth\Services\GetCurrentUserService::class, function($c) {
    return new \App\Application\Auth\Services\GetCurrentUserService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Presentation\Controllers\ProfileController::class, function($c) {
    return new \App\Presentation\Controllers\ProfileController(
        $c->get(\App\Application\Auth\Services\GetCurrentUserService::class),
        $c->get(\App\Application\Auth\Services\UpdateUserProfileService::class),
        $c->get(\App\Application\Auth\Services\ChangePasswordService::class)
    );
});


// ---------------------------------------------------------
// ADMIN MODULE BINDINGS
// ---------------------------------------------------------
$container->bind(\App\Application\Admin\Services\RegisterUserService::class, function($c) {
    return new \App\Application\Admin\Services\RegisterUserService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class),
        $c->get(\App\Domain\Admin\AdminAuditRepositoryInterface::class),
        new \App\Infrastructure\Security\BcryptPasswordHasher()
    );
});

$container->bind(\App\Application\Admin\Services\DeactivateUserService::class, function($c) {
    return new \App\Application\Admin\Services\DeactivateUserService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class),
        $c->get(\App\Domain\Admin\AdminAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Admin\Services\RestoreUserService::class, function($c) {
    return new \App\Application\Admin\Services\RestoreUserService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class),
        $c->get(\App\Domain\Admin\AdminAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Admin\Services\ResetUserPasswordService::class, function($c) {
    return new \App\Application\Admin\Services\ResetUserPasswordService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class),
        $c->get(\App\Domain\Admin\AdminAuditRepositoryInterface::class),
        new \App\Infrastructure\Security\BcryptPasswordHasher()
    );
});

$container->bind(\App\Application\Admin\Services\SearchUserService::class, function($c) {
    return new \App\Application\Admin\Services\SearchUserService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Admin\Services\GetAuditLogsService::class, function($c) {
    return new \App\Application\Admin\Services\GetAuditLogsService(
        $c->get(\App\Domain\Admin\AdminAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Doctor\Services\GetAuditLogsService::class, function($c) {
    return new \App\Application\Doctor\Services\GetAuditLogsService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Admin\Services\GetUserService::class, function($c) {
    return new \App\Application\Admin\Services\GetUserService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Admin\Services\SearchInactiveUserService::class, function($c) {
    return new \App\Application\Admin\Services\SearchInactiveUserService(
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Doctor\Services\GetAuditLogsService::class, function($c) {
    return new \App\Application\Doctor\Services\GetAuditLogsService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Security\Services\GetAuditLogsService::class, function($c) {
    return new \App\Application\Security\Services\GetAuditLogsService(
        $c->get(\App\Domain\Security\SecurityAuditRepositoryInterface::class)
    );
});

$container->bind(\App\Presentation\Controllers\DoctorAuditController::class, function($c) {
    return new \App\Presentation\Controllers\DoctorAuditController(
        $c->get(\App\Application\Doctor\Services\GetAuditLogsService::class)
    );
});

$container->bind(\App\Presentation\Controllers\SecurityAuditController::class, function($c) {
    return new \App\Presentation\Controllers\SecurityAuditController(
        $c->get(\App\Application\Security\Services\GetAuditLogsService::class)
    );
});

$container->bind(\App\Presentation\Controllers\AdminController::class, function($c) {
    return new \App\Presentation\Controllers\AdminController(
        $c->get(\App\Application\Admin\Services\RegisterUserService::class),
        $c->get(\App\Application\Admin\Services\DeactivateUserService::class),
        $c->get(\App\Application\Admin\Services\RestoreUserService::class),
        $c->get(\App\Application\Admin\Services\ResetUserPasswordService::class),
        $c->get(\App\Application\Admin\Services\SearchUserService::class),
        $c->get(\App\Application\Admin\Services\GetAuditLogsService::class),
        $c->get(\App\Application\Patient\Services\SearchPatientService::class),
        $c->get(\App\Application\Admin\Services\GetUserService::class),
        $c->get(\App\Application\Admin\Services\SearchInactiveUserService::class)
    );
});

// ---------------------------------------------------------
// PATIENT MODULE BINDINGS
// ---------------------------------------------------------
$container->bind(\App\Application\Patient\Services\CreatePatientService::class, function($c) {
    return new \App\Application\Patient\Services\CreatePatientService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Patient\Services\UpdatePatientService::class, function($c) {
    return new \App\Application\Patient\Services\UpdatePatientService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Patient\Services\ArchivePatientService::class, function($c) {
    return new \App\Application\Patient\Services\ArchivePatientService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Patient\Services\RestorePatientService::class, function($c) {
    return new \App\Application\Patient\Services\RestorePatientService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Patient\Services\SearchPatientService::class, function($c) {
    return new \App\Application\Patient\Services\SearchPatientService(
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Patient\Services\GetPatientService::class, function($c) {
    return new \App\Application\Patient\Services\GetPatientService(
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Patient\Services\SearchArchivedPatientService::class, function($c) {
    return new \App\Application\Patient\Services\SearchArchivedPatientService(
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Patient\Services\GetPatientOverviewService::class, function($c) {
    return new \App\Application\Patient\Services\GetPatientOverviewService(
        $c->get(\App\Application\Patient\Services\GetPatientService::class),
        $c->get(\App\Application\Consultation\Services\SearchConsultationService::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Presentation\Controllers\PatientController::class, function($c) {
    return new \App\Presentation\Controllers\PatientController(
        $c->get(\App\Application\Patient\Services\CreatePatientService::class),
        $c->get(\App\Application\Patient\Services\UpdatePatientService::class),
        $c->get(\App\Application\Patient\Services\ArchivePatientService::class),
        $c->get(\App\Application\Patient\Services\RestorePatientService::class),
        $c->get(\App\Application\Patient\Services\SearchPatientService::class),
        $c->get(\App\Application\Patient\Services\SearchArchivedPatientService::class),
        $c->get(\App\Application\Patient\Services\GetPatientService::class),
        $c->get(\App\Application\Patient\Services\GetPatientOverviewService::class)
    );
});

// ---------------------------------------------------------
// CONSULTATION MODULE BINDINGS
// ---------------------------------------------------------
$container->bind(\App\Application\Consultation\Services\RecordConsultationService::class, function($c) {
    return new \App\Application\Consultation\Services\RecordConsultationService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Domain\Consultation\ConsultationRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class),
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Consultation\Services\UpdateConsultationService::class, function($c) {
    return new \App\Application\Consultation\Services\UpdateConsultationService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Domain\Consultation\ConsultationRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Consultation\Services\ArchiveConsultationService::class, function($c) {
    return new \App\Application\Consultation\Services\ArchiveConsultationService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Domain\Consultation\ConsultationRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Consultation\Services\RestoreConsultationService::class, function($c) {
    return new \App\Application\Consultation\Services\RestoreConsultationService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Domain\Consultation\ConsultationRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Consultation\Services\SearchConsultationService::class, function($c) {
    return new \App\Application\Consultation\Services\SearchConsultationService(
        $c->get(\App\Domain\Consultation\ConsultationRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Consultation\Services\GetConsultationService::class, function($c) {
    return new \App\Application\Consultation\Services\GetConsultationService(
        $c->get(\App\Domain\Consultation\ConsultationRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Consultation\Services\SearchArchivedConsultationService::class, function($c) {
    return new \App\Application\Consultation\Services\SearchArchivedConsultationService(
        $c->get(\App\Domain\Consultation\ConsultationRepositoryInterface::class)
    );
});

$container->bind(\App\Application\Consultation\Services\GetConsultationReportService::class, function($c) {
    return new \App\Application\Consultation\Services\GetConsultationReportService(
        $c->get(\App\Application\Consultation\Services\GetConsultationService::class),
        $c->get(\App\Domain\Patient\PatientRepositoryInterface::class),
        $c->get(\App\Domain\User\UserRepositoryInterface::class)
    );
});

$container->bind(\App\Infrastructure\Export\DomPdfConsultationExporter::class, function() {
    return new \App\Infrastructure\Export\DomPdfConsultationExporter();
});

$container->bind(\App\Application\Consultation\Services\ExportConsultationPdfService::class, function($c) {
    return new \App\Application\Consultation\Services\ExportConsultationPdfService(
        $c->get(\App\Domain\Doctor\DoctorAuditRepositoryInterface::class),
        $c->get(\App\Application\Consultation\Services\GetConsultationReportService::class),
        $c->get(\App\Infrastructure\Export\DomPdfConsultationExporter::class)
    );
});

$container->bind(\App\Presentation\Controllers\ConsultationController::class, function($c) {
    return new \App\Presentation\Controllers\ConsultationController(
        $c->get(\App\Application\Consultation\Services\RecordConsultationService::class),
        $c->get(\App\Application\Consultation\Services\UpdateConsultationService::class),
        $c->get(\App\Application\Consultation\Services\ArchiveConsultationService::class),
        $c->get(\App\Application\Consultation\Services\RestoreConsultationService::class),
        $c->get(\App\Application\Consultation\Services\SearchConsultationService::class),
        $c->get(\App\Application\Consultation\Services\SearchArchivedConsultationService::class),
        $c->get(\App\Application\Consultation\Services\GetConsultationService::class),
        $c->get(\App\Application\Consultation\Services\GetConsultationReportService::class),
        $c->get(\App\Application\Consultation\Services\ExportConsultationPdfService::class)
    );
});

// ---------------------------------------------------------
// 8. ROUTING & DISPATCHER
// ---------------------------------------------------------
$routes = require_once __DIR__ . '/../routes/web.php';
$method = $request->getMethod();
$uri = $request->getUri();
$routeKey = "{$method} {$uri}";

$publicRoutes = [
    'GET /',
    'POST /',
    'GET /login',
    'POST /login',
    'GET /health'
];

if (array_key_exists($routeKey, $routes)) {
    if (isset($_SESSION['user_uuid']) && !in_array($routeKey, $publicRoutes, true)) {
        \App\Presentation\Middleware\SessionTimeoutMiddleware::check($request);
    }

    $action = $routes[$routeKey];

    if (is_callable($action)) {
        if ($action instanceof Closure) {
            $reflection = new ReflectionFunction($action);
            $args = [];

            if ($reflection->getNumberOfParameters() >= 1) {
                $args[] = $request;
            }

            if ($reflection->getNumberOfParameters() >= 2) {
                $args[] = $container;
            }

            call_user_func_array($action, $args);
        } else {
            call_user_func($action, $request, $container);
        }
    } elseif (is_array($action)) {
        [$controllerClass, $methodName] = $action;
        $controller = $container->get($controllerClass);
        $controller->$methodName($request);
    }
} else {
    http_response_code(404);
    \App\Presentation\Core\Response::view('errors/404');
}