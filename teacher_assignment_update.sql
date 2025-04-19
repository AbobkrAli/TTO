-- Database update to add teacher assignment capability
USE education_management;
-- Add teacher_id column to subjects table
ALTER TABLE subjects ADD COLUMN teacher_id INT NULL COMMENT 'Teacher assigned to this subject',
ADD CONSTRAINT fk_subjects_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
ADD INDEX idx_subject_teacher (teacher_id);
