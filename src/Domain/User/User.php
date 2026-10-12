<?php
declare(strict_types=1);

namespace App\Domain\User;

use DateTimeImmutable;

class User
{
    private ?int $id;
    private string $uuid;
    private string $firstName;
    private string $lastName;
    private ?string $middleName;
    private string $email;
    private ?string $phone;
    private string $passwordHash;
    private string $role;
    private int $isActive;
    private ?DateTimeImmutable $lastLoginAt;
    private ?DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $updatedAt;

    private function __construct()
    {
    }

    // Named Constructor


    public static function registerNewUser(
        string $uuid,
        string $firstName,
        string $lastName,
        ?string $middleName,
        string $email,
        ?string $phone,
        string $passwordHash,
        string $role = 'doctor'
    ): self {
        $user = new self();
        $user->id = null;
        $user->uuid = $uuid;
        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->middleName = $middleName;
        $user->email = $email;
        $user->phone = $phone;
        $user->passwordHash = $passwordHash;
        $user->role = $role;
        $user->isActive = 1;
        $user->lastLoginAt = null;
        $user->createdAt = new DateTimeImmutable();
        $user->updatedAt = new DateTimeImmutable();

        return $user;
    }

    // Behavioral Methods (Replacing Setters)
    public function recordLogin(): void
    {
        $this->lastLoginAt = new DateTimeImmutable();
    }

    public function deactivate(): void
    {
        $this->isActive = 0;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function activate(): void
    {
        $this->isActive = 1;
        $this->updatedAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUuid(): string
    {
        return $this->uuid;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function getIsActive(): int
    {
        return $this->isActive;
    }

    // Returning formatted strings for the UI
    public function getLastLoginAt(): ?string
    {
        return $this->lastLoginAt ? $this->lastLoginAt->format('Y-m-d H:i:s') : null;
    }
    public function getCreatedAt(): ?string
    {
        return $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null;
    }

    // Behavioral method for changing password
    public function changePassword(string $newPasswordHash): void
    {
        $this->passwordHash = $newPasswordHash;
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Behavioral method for updating profile
    public function updateProfile(
        string $firstName,
        string $lastName,
        ?string $middleName,
        string $email,
        ?string $phone
    ): void {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->email = $email;
        $this->phone = $phone;
        $this->updatedAt = new \DateTimeImmutable();
    }
}