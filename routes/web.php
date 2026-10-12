<?php
use App\Presentation\Controllers\ProfileController;
use App\Presentation\Controllers\AuthController;
use App\Presentation\Middleware\AuthMiddleware;
use App\Presentation\Middleware\AdminMiddleware;
use App\Presentation\Core\Request;
use App\Presentation\Core\Response;

return [
    'GET /' => [AuthController::class, 'showLoginForm'],
    'POST /' => [AuthController::class, 'login'],
    'GET /login' => [AuthController::class, 'showLoginForm'],
    'POST /login' => [AuthController::class, 'login'],
    'POST /logout' => [AuthController::class, 'logout'],

    'GET /dashboard' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        Response::view('dashboard/index');
    },
    'GET /admin/dashboard' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        Response::redirect('/dashboard');
    },
    'GET /doctor/dashboard' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        Response::redirect('/dashboard');
    },

    'GET /profile' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(ProfileController::class)->showProfile($request);
    },
    'GET /profile/edit' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        Response::redirect('/profile');
    },
    'GET /profile/security' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(ProfileController::class)->showSecurity($request);
    },
    'POST /profile/update' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(ProfileController::class)->updateProfile($request);
    },
    'POST /profile/password' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(ProfileController::class)->changePassword($request);
    },

    'GET /patients' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->searchPatients($request);
    },
    'GET /patient/create' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->showCreateForm($request);
    },
    'GET /patient/edit' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->editPatient($request);
    },
    'GET /patient/show' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->showPatient($request);
    },
    'GET /patient/archived' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->viewArchived($request);
    },

    'GET /consultations' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->searchConsultations($request);
    },
    'GET /consultation/create' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->showCreateForm($request);
    },
    'GET /consultation/edit' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->editConsultation($request);
    },
    'GET /consultation/show' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->showConsultation($request);
    },
    'GET /consultations/archived' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->viewArchived($request);
    },
    'GET /consultations/export' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->exportPdf($request);
    },

    'GET /admin/users' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->searchUsers($request);
    },
    'GET /admin/users/create' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->showCreateForm($request);
    },
    'POST /admin/users/store' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->registerUser($request);
    },
    'GET /admin/users/inactive' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->viewInactiveUsers($request);
    },
    'GET /admin/users/show' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->showUser($request);
    },
    'POST /admin/users/deactivate' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->deactivateUser($request);
    },
    'POST /admin/users/restore' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->restoreUser($request);
    },
    'POST /admin/users/reset-password' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->resetPassword($request);
    },
    'GET /admin/logs' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->viewAuditLogs($request);
    },
    'GET /doctor/audit' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\DoctorAuditController::class)->viewAuditLogs($request);
    },
    'GET /security/audit' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\SecurityAuditController::class)->viewAuditLogs($request);
    },
    'GET /admin/patients/index' => function (Request $request, $container) {
        AdminMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\AdminController::class)->viewGlobalArchivedPatients($request);
    },

    'POST /patient/create' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->createPatient($request);
    },
    'POST /patient/update' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->updatePatient($request);
    },
    'POST /patient/archive' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->archivePatient($request);
    },
    'POST /patient/restore' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\PatientController::class)->restorePatient($request);
    },

    'POST /consultations/create' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->recordConsultation($request);
    },
    'POST /consultation/update' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->updateConsultation($request);
    },
    'POST /consultations/archive' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->archiveConsultation($request);
    },
    'POST /consultations/restore' => function (Request $request, $container) {
        AuthMiddleware::check($request);
        $container->get(\App\Presentation\Controllers\ConsultationController::class)->restoreConsultation($request);
    },

    'GET /health' => function () {
        Response::json(['status' => 'Application is running optimally']);
    }
];