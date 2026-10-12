<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Patient\Patient;
use App\Domain\Patient\PatientRepositoryInterface;
use App\Application\Patient\Mapper\PatientMapper;
use App\Infrastructure\Database\PDOConnection;
use PDO;

class MySQLPatientRepository implements PatientRepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save(Patient $patient): void
    {
        $data = PatientMapper::toDatabase($patient);
        $stmt = $this->db->prepare("
            INSERT INTO patients (
                uuid, doctor_id, first_name, last_name, middle_name, date_of_birth, sex, 
                phone, email, address_line, emergency_contact_name, emergency_contact_phone, 
                blood_group, allergies, chronic_conditions, archived_at, created_at, updated_at
            ) VALUES (
                :uuid, :doctor_id, :first_name, :last_name, :middle_name, :date_of_birth, :sex, 
                :phone, :email, :address_line, :emergency_contact_name, :emergency_contact_phone, 
                :blood_group, :allergies, :chronic_conditions, :archived_at, :created_at, :updated_at
            )
        ");
        $stmt->execute($data);
    }

    public function update(Patient $patient): void
    {
        $data = PatientMapper::toDatabase($patient);
        $stmt = $this->db->prepare("
            UPDATE patients SET 
                first_name = :first_name, last_name = :last_name, middle_name = :middle_name, 
                date_of_birth = :date_of_birth, sex = :sex, phone = :phone, email = :email, 
                address_line = :address_line, emergency_contact_name = :emergency_contact_name, 
                emergency_contact_phone = :emergency_contact_phone, blood_group = :blood_group, 
                allergies = :allergies, chronic_conditions = :chronic_conditions, 
                archived_at = :archived_at, updated_at = :updated_at 
            WHERE uuid = :uuid
        ");
        
        // Remove immutable properties for update
        unset($data['doctor_id'], $data['created_at']);
        $stmt->execute($data);
    }

    public function findById(int $id): ?Patient
    {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? PatientMapper::toEntity($row) : null;
    }

    public function findByUuid(string $uuid): ?Patient
    {
        $stmt = $this->db->prepare("SELECT * FROM patients WHERE uuid = :uuid LIMIT 1");
        $stmt->execute(['uuid' => $uuid]);
        $row = $stmt->fetch();
        return $row ? PatientMapper::toEntity($row) : null;
    }

    public function search(\App\Application\Patient\DTOs\SearchPatientDTO $dto): array
    {
        // Ensure isolation to get the doctor's internal ID based on their UUID
        // protects the database structure from UUID injection attacks crossing scopes)
        $docStmt = $this->db->prepare("SELECT id, role FROM users WHERE uuid = :uuid LIMIT 1");
        $docStmt->execute(['uuid' => $dto->doctorUuid]);
        $userContext = $docStmt->fetch();
        
        if (!$userContext) return ['patients' => [], 'total' => 0];

        $whereClause = "1=1";
        $params = [];

        // Scope Enforcement for Admins see global, Doctors see their own
        if ($userContext['role'] !== 'admin') {
            $whereClause .= " AND doctor_id = :doc_id";
            $params['doc_id'] = $userContext['id'];
        }

        // Archive Filter Enforcement
        if ($dto->isArchived === true) {
            $whereClause .= " AND archived_at IS NOT NULL";
        } elseif ($dto->isArchived === false) {
            $whereClause .= " AND archived_at IS NULL";
        }

        // Dropdown filter Logic
        if (!empty($dto->searchValue)) {
            switch ($dto->searchField) {
                case 'uuid':
                    $whereClause .= " AND uuid = :val";
                    $params['val'] = $dto->searchValue;
                    break;
                case 'first_name':
                    $whereClause .= " AND first_name LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;
                case 'last_name':
                    $whereClause .= " AND last_name LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;
                case 'phone':
                    $whereClause .= " AND phone LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;
                case 'email':
                    $whereClause .= " AND email = :val";
                    $params['val'] = $dto->searchValue;
                    break;
                case 'full_name':
                    $whereClause .= " AND CONCAT_WS(' ', first_name, middle_name, last_name) LIKE :val";
                    $params['val'] = '%' . $dto->searchValue . '%';
                    break;
            }
        }

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM patients WHERE " . $whereClause);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $limit = $dto->limit;
        $offset = $dto->getOffset();
        $sql = "SELECT * FROM patients WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $patients = [];
        while ($row = $stmt->fetch()) {
            $patients[] = PatientMapper::toEntity($row);
        }

        return ['patients' => $patients, 'total' => $total];
    }
}