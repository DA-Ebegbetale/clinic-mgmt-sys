<?php
declare(strict_types=1);

namespace App\Application\Admin\DTOs;

class RegisterUserDTO
{
    public readonly string $adminUuid;
    public readonly string $firstName;
    public readonly string $lastName;
    public readonly ?string $middleName;
    public readonly string $email;
    public readonly ?string $phone;
    public readonly string $tempPassword;
    public readonly string $role;

    public function __construct(
        string $adminUuid,
        string $firstName,
        string $lastName,
        ?string $middleName,
        string $email,
        ?string $phone,
        string $tempPassword,
        string $role
    ) {
        $this->adminUuid = $adminUuid;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->email = $email;
        $this->phone = $phone;
        $this->tempPassword = $tempPassword;
        $this->role = $role;
    }
}