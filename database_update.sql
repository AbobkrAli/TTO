-- Database schema update for office hours functionality

USE education_management;

-- Add is_office_hour and request_id columns to subjects table
ALTER TABLE subjects 
  ADD COLUMN is_office_hour TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Flag indicating if this is an office hour (1) or regular subject (0)',
  ADD COLUMN request_id INT NULL COMMENT 'Reference to the request that created this office hour',
  ADD CONSTRAINT fk_subjects_request FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD INDEX idx_subject_type (is_office_hour),
  ADD INDEX idx_subject_request (request_id);

-- Update requests table to ensure consistency (renamed 'rejected' to 'declined')
ALTER TABLE requests 
  MODIFY COLUMN status ENUM('pending', 'approved', 'declined') NOT NULL DEFAULT 'pending';

-- Add an index on the subjects table for better performance when filtering by day
ALTER TABLE subjects 
  ADD INDEX idx_subject_day (day); 