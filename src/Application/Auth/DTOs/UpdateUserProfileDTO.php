<?php
declare(strict_types=1);

namespace App\Application\Auth\DTOs;

class UpdateUserProfileDTO
{
    public readonly string $userUuid;
    public readonly string $firstName;
    public readonly string $lastName;
    public readonly ?string $middleName;
    public readonly string $email;
    public readonly ?string $phone;

    public function __construct(
        string $userUuid,
        string $firstName,
        string $lastName,
        ?string $middleName,
        string $email,
        ?string $phone
    ) {
        $this->userUuid = $userUuid;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->email = $email;
        $this->phone = $phone;
    }
}
