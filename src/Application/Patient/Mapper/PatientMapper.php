<?php
declare(strict_types=1);

namespace App\Application\Patient\Mapper;

use App\Domain\Patient\Patient;
use DateTimeImmutable;

class PatientMapper
{
    public static function toEntity(array $row): Patient
    {
        $patient = (new \ReflectionClass(Patient::class))->newInstanceWithoutConstructor();
        
        $set = function($property, $value) use ($patient) {
            $p = new \ReflectionProperty(Patient::class, $property);
            $p->setValue($patient, $value);
        };

        $set('id', $row['id'] ? (int)$row['id'] : null);
        $set('uuid', $row['uuid']);
        $set('doctorId', (int)$row['doctor_id']);
        $set('firstName', $row['first_name']);
        $set('lastName', $row['last_name']);
        $set('middleName', $row['middle_name'] ?? null);
        $set('dateOfBirth', $row['date_of_birth'] ?? null);
        $set('sex', $row['sex'] ?? null);
        $set('phone', $row['phone'] ?? null);
        $set('email', $row['email'] ?? null);
        $set('addressLine', $row['address_line'] ?? null);
        $set('emergencyContactName', $row['emergency_contact_name'] ?? null);
        $set('emergencyContactPhone', $row['emergency_contact_phone'] ?? null);
        $set('bloodGroup', $row['blood_group'] ?? null);
        $set('allergies', $row['allergies'] ?? null);
        $set('chronicConditions', $row['chronic_conditions'] ?? null);
        
        $set('archivedAt', $row['archived_at'] ? new DateTimeImmutable($row['archived_at']) : null);
        $set('createdAt', new DateTimeImmutable($row['created_at']));
        $set('updatedAt', new DateTimeImmutable($row['updated_at']));

        return $patient;
    }

    public static function toDatabase(Patient $patient): array
    {
        return [
            'uuid' => $patient->getUuid(),
            'doctor_id' => $patient->getDoctorId(),
            'first_name' => $patient->getFirstName(),
            'last_name' => $patient->getLastName(),
            'middle_name' => $patient->getMiddleName(),
            'date_of_birth' => $patient->getDateOfBirth(),
            'sex' => $patient->getSex(),
            'phone' => $patient->getPhone(),
            'email' => $patient->getEmail(),
            'address_line' => $patient->getAddressLine(),
            'emergency_contact_name' => $patient->getEmergencyContactName(),
            'emergency_contact_phone' => $patient->getEmergencyContactPhone(),
            'blood_group' => $patient->getBloodGroup(),
            'allergies' => $patient->getAllergies(),
            'chronic_conditions' => $patient->getChronicConditions(),
            'archived_at' => $patient->getArchivedAt(),
            'created_at' => $patient->getCreatedAt(),
            'updated_at' => $patient->getUpdatedAt()
        ];
    }
}
