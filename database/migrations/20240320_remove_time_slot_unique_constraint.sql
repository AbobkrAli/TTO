-- Remove the unique constraint on department, day, and hour
ALTER TABLE subjects DROP INDEX uk_department_day_hour; 