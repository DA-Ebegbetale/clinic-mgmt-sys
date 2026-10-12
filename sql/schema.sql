CREATE DATABASE IF NOT EXISTS clinic_management_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE clinic_management_db;

-- USERS (Doctors)
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  first_name VARCHAR(120) NOT NULL,
  last_name VARCHAR(120) NOT NULL,
  middle_name VARCHAR(120) NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  phone VARCHAR(25) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('doctor','admin') NOT NULL DEFAULT 'doctor',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_phone (phone),
  INDEX idx_users_is_active (is_active)
) ENGINE=InnoDB;

-- PATIENTS
CREATE TABLE IF NOT EXISTS patients (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  doctor_id BIGINT UNSIGNED NOT NULL,
  first_name VARCHAR(120) NOT NULL,
  last_name VARCHAR(120) NOT NULL,
  middle_name VARCHAR(120) NULL,
  date_of_birth DATE NULL,
  sex ENUM('male','female','other') NULL,
  phone VARCHAR(25) NULL,
  email VARCHAR(120) NULL,
  address_line VARCHAR(255) NULL,
  emergency_contact_name VARCHAR(120) NULL,
  emergency_contact_phone VARCHAR(25) NULL,
  blood_group VARCHAR(5) NULL,
  allergies TEXT NULL,
  chronic_conditions TEXT NULL,
  archived_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_patients_doctor
    FOREIGN KEY (doctor_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_patients_doctor_id (doctor_id),
  INDEX idx_patients_first_name (first_name),
  INDEX idx_patients_last_name (last_name),
  INDEX idx_patients_name (last_name, first_name),
  INDEX idx_patients_phone (phone),
  INDEX idx_patients_email (email)
) ENGINE=InnoDB;

-- CONSULTATIONS (Structured notes)
CREATE TABLE IF NOT EXISTS consultations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  patient_id BIGINT UNSIGNED NOT NULL,
  doctor_id BIGINT UNSIGNED NOT NULL,
  visit_datetime DATETIME NOT NULL,
  chief_complaint TEXT NOT NULL,
  history_of_present_illness TEXT NULL,
  vitals JSON NULL,
  assessment TEXT NULL,
  diagnosis TEXT NULL,
  plan TEXT NULL,
  prescription TEXT NULL,
  follow_up_date DATE NULL,
  additional_notes TEXT NULL,
  archived_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_consultations_patient
    FOREIGN KEY (patient_id) REFERENCES patients(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_consultations_doctor
    FOREIGN KEY (doctor_id) REFERENCES users(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_consultations_doctor_id (doctor_id),
  INDEX idx_consultations_visit_datetime (visit_datetime),
  INDEX idx_consultations_patient_visit (patient_id, visit_datetime)
) ENGINE=InnoDB;

-- ADMIN ACTION AUDIT LOGS
CREATE TABLE IF NOT EXISTS admin_action_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  action VARCHAR(64) NOT NULL,
  target_type VARCHAR(32) NOT NULL,
  target_id BIGINT UNSIGNED NULL,
  target_uuid CHAR(36) NULL,
  admin_id BIGINT UNSIGNED NOT NULL,
  reason TEXT NULL,
  meta JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_admin_action_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT,
  INDEX idx_admin_action (action),
  INDEX idx_admin_target (target_type, target_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- PRACTITIONER'S ACTION LOG
CREATE TABLE IF NOT EXISTS practioner_action_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(50) NOT NULL,
    target_type VARCHAR(50) NOT NULL,
    target_uuid VARCHAR(36),
    doctor_id BIGINT UNSIGNED NOT NULL,
    meta JSON,
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_doctor_action_doctor FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_created_at (created_at)
);

-- SECURITY EVENT LOGS
CREATE TABLE IF NOT EXISTS security_event_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    user_uuid CHAR(36),
    ip_address VARCHAR(45),
    user_agent TEXT,
    device_info VARCHAR(500),
    details TEXT,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at)
);