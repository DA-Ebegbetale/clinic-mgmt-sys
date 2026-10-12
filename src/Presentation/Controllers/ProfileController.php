<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\Auth\DTOs\ChangePasswordDTO;
use App\Application\Auth\DTOs\UpdateUserProfileDTO;

use App\Application\Auth\Services\ChangePasswordService;
use App\Application\Auth\Services\GetCurrentUserService;
use App\Application\Auth\Services\UpdateUserProfileService;
use App\Application\Shared\Validators\Validator;
use App\Presentation\Core\Request;
use App\Presentation\Core\Response;
use App\Presentation\Core\Session;

class ProfileController
{
    public function __construct(
        private GetCurrentUserService $getCurrentUserService,
        private UpdateUserProfileService $updateUserProfileService,
        private ChangePasswordService $changePasswordService
    ) {}

    public function showProfile(Request $request): void
    {
        $user = $this->getCurrentUserService->execute($_SESSION['user_uuid']);
        Response::view('profile/edit', ['user' => $user]);
    }

    public function showSecurity(Request $request): void
    {
        $user = $this->getCurrentUserService->execute($_SESSION['user_uuid']);
        Response::view('profile/security', ['user' => $user]);
    }

    public function updateProfile(Request $request): void
    {
        try {
            $data = $request->getBody();
            $userUuid = $_SESSION['user_uuid'];

            $firstName = Validator::validateRequiredString($data['first_name'] ?? '', 'First Name');
            $lastName = Validator::validateRequiredString($data['last_name'] ?? '', 'Last Name');
            $email = Validator::validateEmail($data['email'] ?? '');
            $phone = !empty($data['phone']) ? Validator::validateRequiredString($data['phone'], 'Phone') : null;
            $middleName = !empty($data['middle_name']) ? Validator::validateRequiredString($data['middle_name'], 'Middle Name') : null;

            $dto = new UpdateUserProfileDTO($userUuid, $firstName, $lastName, $middleName, $email, $phone);
            $this->updateUserProfileService->execute($dto);

            Session::setFlash('success', 'Profile updated successfully.');
            Response::redirect('/profile');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/profile');
        }
    }

    public function changePassword(Request $request): void
    {
        try {
            $data = $request->getBody();
            $userUuid = $_SESSION['user_uuid'];

            $currentPassword = Validator::validateRequiredString($data['current_password'] ?? '', 'Current Password');
            $newPassword = Validator::validateRequiredString($data['new_password'] ?? '', 'New Password');

            $dto = new ChangePasswordDTO($userUuid, $currentPassword, $newPassword);
            $this->changePasswordService->execute($dto);

            Session::setFlash('success', 'Password changed successfully.');
            Response::redirect('/profile/security');
        } catch (\Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Response::redirect('/profile/security');
        }
    }
}