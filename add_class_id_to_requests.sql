-- Add class_id column to requests table
ALTER TABLE requests
ADD COLUMN class_id INT NULL,
ADD CONSTRAINT fk_requests_class
FOREIGN KEY (class_id) REFERENCES classes(id)
ON DELETE SET NULL
ON UPDATE CASCADE; 