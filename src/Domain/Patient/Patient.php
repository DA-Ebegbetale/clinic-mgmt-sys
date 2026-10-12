<?php
declare(strict_types=1);

namespace App\Domain\Patient;

use DateTimeImmutable;

class Patient
{
    private ?int $id;
    private string $uuid;
    private int $doctorId; // Foreign Key to Users table
    private string $firstName;
    private string $lastName;
    private ?string $middleName;
    private ?string $dateOfBirth;
    private ?string $sex;
    private ?string $phone;
    private ?string $email;
    private ?string $addressLine;
    private ?string $emergencyContactName;
    private ?string $emergencyContactPhone;
    private ?string $bloodGroup;
    private ?string $allergies;
    private ?string $chronicConditions;
    private ?DateTimeImmutable $archivedAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function registerNewPatient(
        string $uuid,
        int $doctorId,
        string $firstName,
        string $lastName,
        ?string $middleName = null,
        ?string $dateOfBirth = null,
        ?string $sex = null,
        ?string $phone = null,
        ?string $email = null,
        ?string $addressLine = null,
        ?string $emergencyContactName = null,
        ?string $emergencyContactPhone = null,
        ?string $bloodGroup = null,
        ?string $allergies = null,
        ?string $chronicConditions = null
    ): self {
        $patient = new self();
        $patient->id = null;
        $patient->uuid = $uuid;
        $patient->doctorId = $doctorId;
        $patient->firstName = $firstName;
        $patient->lastName = $lastName;
        $patient->middleName = $middleName;
        $patient->dateOfBirth = $dateOfBirth;
        $patient->sex = $sex;
        $patient->phone = $phone;
        $patient->email = $email;
        $patient->addressLine = $addressLine;
        $patient->emergencyContactName = $emergencyContactName;
        $patient->emergencyContactPhone = $emergencyContactPhone;
        $patient->bloodGroup = $bloodGroup;
        $patient->allergies = $allergies;
        $patient->chronicConditions = $chronicConditions;
        $patient->archivedAt = null;
        $patient->createdAt = new DateTimeImmutable();
        $patient->updatedAt = new DateTimeImmutable();
        
        return $patient;
    }

    // Behavioral Methods
    public function updateProfile(
        string $firstName, string $lastName, ?string $middleName, ?string $dateOfBirth,
        ?string $sex, ?string $phone, ?string $email, ?string $addressLine,
        ?string $emergencyContactName, ?string $emergencyContactPhone,
        ?string $bloodGroup, ?string $allergies, ?string $chronicConditions
    ): void {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->dateOfBirth = $dateOfBirth;
        $this->sex = $sex;
        $this->phone = $phone;
        $this->email = $email;
        $this->addressLine = $addressLine;
        $this->emergencyContactName = $emergencyContactName;
        $this->emergencyContactPhone = $emergencyContactPhone;
        $this->bloodGroup = $bloodGroup;
        $this->allergies = $allergies;
        $this->chronicConditions = $chronicConditions;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function archive(): void
    {
        $this->archivedAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function restore(): void
    {
        $this->archivedAt = null;
        $this->updatedAt = new DateTimeImmutable();
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUuid(): string { return $this->uuid; }
    public function getDoctorId(): int { return $this->doctorId; }
    public function getFirstName(): string { return $this->firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function getMiddleName(): ?string { return $this->middleName; }
    public function getDateOfBirth(): ?string { return $this->dateOfBirth; }
    public function getSex(): ?string { return $this->sex; }
    public function getPhone(): ?string { return $this->phone; }
    public function getEmail(): ?string { return $this->email; }
    public function getAddressLine(): ?string { return $this->addressLine; }
    public function getEmergencyContactName(): ?string { return $this->emergencyContactName; }
    public function getEmergencyContactPhone(): ?string { return $this->emergencyContactPhone; }
    public function getBloodGroup(): ?string { return $this->bloodGroup; }
    public function getAllergies(): ?string { return $this->allergies; }
    public function getChronicConditions(): ?string { return $this->chronicConditions; }
    
    // Formatted Date Getters
    public function getArchivedAt(): ?string { return $this->archivedAt ? $this->archivedAt->format('Y-m-d H:i:s') : null; }
    public function getCreatedAt(): string { return $this->createdAt->format('Y-m-d H:i:s'); }
    public function getUpdatedAt(): string { return $this->updatedAt->format('Y-m-d H:i:s'); }
}