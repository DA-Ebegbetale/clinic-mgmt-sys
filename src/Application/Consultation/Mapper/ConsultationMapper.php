<?php
declare(strict_types=1);

namespace App\Application\Consultation\Mapper;

use App\Domain\Consultation\Consultation;
use DateTimeImmutable;

class ConsultationMapper
{
    public static function toEntity(array $row): Consultation
    {
        $consultation = (new \ReflectionClass(Consultation::class))->newInstanceWithoutConstructor();
        
        $set = function($property, $value) use ($consultation) {
            $p = new \ReflectionProperty(Consultation::class, $property);
            $p->setValue($consultation, $value);
        };

        $set('id', $row['id'] ? (int)$row['id'] : null);
        $set('uuid', $row['uuid']);
        $set('patientId', (int)$row['patient_id']);
        $set('doctorId', (int)$row['doctor_id']);
        $set('visitDatetime', $row['visit_datetime']);
        $set('chiefComplaint', $row['chief_complaint']);
        $set('historyOfPresentIllness', $row['history_of_present_illness'] ?? null);
        
        // Handle JSON column parsing safely
        $vitals = null;
        if (!empty($row['vitals'])) {
            $decoded = json_decode($row['vitals'], true);
            $vitals = is_array($decoded) ? $decoded : null;
        }
        $set('vitals', $vitals);

        $set('assessment', $row['assessment'] ?? null);
        $set('diagnosis', $row['diagnosis'] ?? null);
        $set('plan', $row['plan'] ?? null);
        $set('prescription', $row['prescription'] ?? null);
        $set('followUpDate', $row['follow_up_date'] ? $row['follow_up_date'] : null);
        $set('additionalNotes', $row['additional_notes'] ?? null);
        
        $set('archivedAt', $row['archived_at'] ? new DateTimeImmutable($row['archived_at']) : null);
        $set('createdAt', new DateTimeImmutable($row['created_at']));
        $set('updatedAt', new DateTimeImmutable($row['updated_at']));

        return $consultation;
    }

    public static function toDatabase(Consultation $consultation): array
    {
        return [
            'uuid' => $consultation->getUuid(),
            'patient_id' => $consultation->getPatientId(),
            'doctor_id' => $consultation->getDoctorId(),
            'visit_datetime' => $consultation->getVisitDatetime(),
            'chief_complaint' => $consultation->getChiefComplaint(),
            'history_of_present_illness' => $consultation->getHistoryOfPresentIllness(),
            // Encode array back to JSON string for the database
            'vitals' => $consultation->getVitals() ? json_encode($consultation->getVitals()) : null,
            'assessment' => $consultation->getAssessment(),
            'diagnosis' => $consultation->getDiagnosis(),
            'plan' => $consultation->getPlan(),
            'prescription' => $consultation->getPrescription(),
            'follow_up_date' => $consultation->getFollowUpDate(),
            'additional_notes' => $consultation->getAdditionalNotes(),
            'archived_at' => $consultation->getArchivedAt(),
            'created_at' => $consultation->getCreatedAt(),
            'updated_at' => $consultation->getUpdatedAt()
        ];
    }
}