<?php
declare(strict_types=1);

namespace App\Domain\Consultation;

use DateTimeImmutable;

class Consultation
{
    private ?int $id;
    private string $uuid;
    private int $patientId;
    private int $doctorId;
    private string $visitDatetime; 
    private string $chiefComplaint;
    private ?string $historyOfPresentIllness;
    private ?array $vitals; // For the JSON column
    private ?string $assessment;
    private ?string $diagnosis;
    private ?string $plan;
    private ?string $prescription;
    private ?string $followUpDate;
    private ?string $additionalNotes;
    private ?DateTimeImmutable $archivedAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function recordNewConsultation(
        string $uuid,
        int $patientId,
        int $doctorId,
        string $visitDatetime,
        string $chiefComplaint,
        ?string $historyOfPresentIllness = null,
        ?array $vitals = null,
        ?string $assessment = null,
        ?string $diagnosis = null,
        ?string $plan = null,
        ?string $prescription = null,
        ?string $followUpDate = null,
        ?string $additionalNotes = null
    ): self {
        $consultation = new self();
        $consultation->id = null;
        $consultation->uuid = $uuid;
        $consultation->patientId = $patientId;
        $consultation->doctorId = $doctorId;
        $consultation->visitDatetime = $visitDatetime;
        $consultation->chiefComplaint = $chiefComplaint;
        $consultation->historyOfPresentIllness = $historyOfPresentIllness;
        $consultation->vitals = $vitals;
        $consultation->assessment = $assessment;
        $consultation->diagnosis = $diagnosis;
        $consultation->plan = $plan;
        $consultation->prescription = $prescription;
        $consultation->followUpDate = $followUpDate;
        $consultation->additionalNotes = $additionalNotes;
        $consultation->archivedAt = null;
        $consultation->createdAt = new DateTimeImmutable();
        $consultation->updatedAt = new DateTimeImmutable();
        return $consultation;
    }

    public function updateNotes(
        string $chiefComplaint,
        ?string $historyOfPresentIllness,
        ?array $vitals,
        ?string $assessment,
        ?string $diagnosis,
        ?string $plan,
        ?string $prescription,
        ?string $followUpDate,
        ?string $additionalNotes
    ): void {
        $this->chiefComplaint = $chiefComplaint;
        $this->historyOfPresentIllness = $historyOfPresentIllness;
        $this->vitals = $vitals;
        $this->assessment = $assessment;
        $this->diagnosis = $diagnosis;
        $this->plan = $plan;
        $this->prescription = $prescription;
        $this->followUpDate = $followUpDate;
        $this->additionalNotes = $additionalNotes;
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
    public function getPatientId(): int { return $this->patientId; }
    public function getDoctorId(): int { return $this->doctorId; }
    public function getVisitDatetime(): string { return $this->visitDatetime; }
    public function getChiefComplaint(): string { return $this->chiefComplaint; }
    public function getHistoryOfPresentIllness(): ?string { return $this->historyOfPresentIllness; }
    public function getVitals(): ?array { return $this->vitals; }
    public function getAssessment(): ?string { return $this->assessment; }
    public function getDiagnosis(): ?string { return $this->diagnosis; }
    public function getPlan(): ?string { return $this->plan; }
    public function getPrescription(): ?string { return $this->prescription; }
    public function getFollowUpDate(): ?string { return $this->followUpDate; }
    public function getAdditionalNotes(): ?string { return $this->additionalNotes; }
    public function getArchivedAt(): ?string { return $this->archivedAt ? $this->archivedAt->format('Y-m-d H:i:s') : null; }
    public function getCreatedAt(): string { return $this->createdAt->format('Y-m-d H:i:s'); }
    public function getUpdatedAt(): string { return $this->updatedAt->format('Y-m-d H:i:s'); }
}