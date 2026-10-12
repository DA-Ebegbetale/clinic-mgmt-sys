<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Consultation\Consultation;
use App\Domain\Consultation\ConsultationRepositoryInterface;
use App\Application\Consultation\Mapper\ConsultationMapper;
use App\Infrastructure\Database\PDOConnection;
use PDO;

class MySQLConsultationRepository implements ConsultationRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save(Consultation $consultation): void
    {
        $data = ConsultationMapper::toDatabase($consultation);
        $stmt = $this->db->prepare("
            INSERT INTO consultations (
                uuid, patient_id, doctor_id, visit_datetime, chief_complaint, 
                history_of_present_illness, vitals, assessment, diagnosis, plan, 
                prescription, follow_up_date, additional_notes, archived_at, created_at, updated_at
            ) VALUES (
                :uuid, :patient_id, :doctor_id, :visit_datetime, :chief_complaint, 
                :history_of_present_illness, :vitals, :assessment, :diagnosis, :plan, 
                :prescription, :follow_up_date, :additional_notes, :archived_at, :created_at, :updated_at
            )
        ");
        $stmt->execute($data);
    }

    public function update(Consultation $consultation): void
    {
        $data = ConsultationMapper::toDatabase($consultation);
        $stmt = $this->db->prepare("
            UPDATE consultations SET 
                chief_complaint = :chief_complaint, history_of_present_illness = :history_of_present_illness, 
                vitals = :vitals, assessment = :assessment, diagnosis = :diagnosis, plan = :plan, 
                prescription = :prescription, follow_up_date = :follow_up_date, 
                additional_notes = :additional_notes, archived_at = :archived_at, updated_at = :updated_at 
            WHERE uuid = :uuid
        ");

        // Remove immutable fields to prevent SQL parameter errors
        unset($data['patient_id'], $data['doctor_id'], $data['visit_datetime'], $data['created_at']);
        $stmt->execute($data);
    }

    public function findById(int $id): ?Consultation
    {
        $stmt = $this->db->prepare("SELECT * FROM consultations WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? ConsultationMapper::toEntity($row) : null;
    }

    public function findByUuid(string $uuid): ?Consultation
    {
        $stmt = $this->db->prepare("SELECT * FROM consultations WHERE uuid = :uuid LIMIT 1");
        $stmt->execute(['uuid' => $uuid]);
        $row = $stmt->fetch();
        return $row ? ConsultationMapper::toEntity($row) : null;
    }

    public function search(\App\Application\Consultation\DTOs\SearchConsultationDTO $dto): array
    {
        // Resolve User(Doctor) Identity & Scope
        $docStmt = $this->db->prepare("SELECT id, role FROM users WHERE uuid = :uuid LIMIT 1");
        $docStmt->execute(['uuid' => $dto->doctorUuid]);
        $userContext = $docStmt->fetch();

        if (!$userContext)
            return ['consultations' => [], 'total' => 0];

        // We use an alias 'c' for consultations because of the join with patients table, which is aliased as 'p'.
        $whereClause = "1=1";
        $params = [];

        // Scope Enforcement
        if ($userContext['role'] !== 'admin') {
            $whereClause .= " AND c.doctor_id = :doc_id";
            $params['doc_id'] = $userContext['id'];
        }

        // Archive Filter
        if ($dto->isArchived === true) {
            $whereClause .= " AND c.archived_at IS NOT NULL";
        } elseif ($dto->isArchived === false) {
            $whereClause .= " AND c.archived_at IS NULL";
        }

        // Specific Patient Filter (If viewing a single patient's history)
        if ($dto->patientUuid) {
            $whereClause .= " AND p.uuid = :patient_uuid";
            $params['patient_uuid'] = $dto->patientUuid;
        }

        // Text Search Logic
        if (!empty($dto->searchValue)) {
            switch ($dto->searchField) {
                case 'uuid':
                    $whereClause .= " AND c.uuid = :val";
                    $params['val'] = $dto->searchValue;
                    break;
                case 'diagnosis':
                    $whereClause .= " AND c.diagnosis LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;
                case 'chief_complaint':
                    $whereClause .= " AND c.chief_complaint LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;
            }
        }

        // Join patients table to allow filtering by patient_uuid safely
        $joinClause = " LEFT JOIN patients p ON c.patient_id = p.id ";

        $countStmt = $this->db->prepare("SELECT COUNT(c.id) FROM consultations c" . $joinClause . " WHERE " . $whereClause);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $limit = $dto->limit;
        $offset = $dto->getOffset();
        $sql = "SELECT c.* FROM consultations c" . $joinClause . " WHERE {$whereClause} ORDER BY c.visit_datetime DESC LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $consultations = [];
        while ($row = $stmt->fetch()) {
            $consultations[] = ConsultationMapper::toEntity($row);
        }

        return ['consultations' => $consultations, 'total' => $total];
    }
}