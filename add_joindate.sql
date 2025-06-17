-- Switch to the edoc database
USE edoc;

-- Add joindate column to patient table
ALTER TABLE patient ADD COLUMN joindate DATE DEFAULT CURRENT_DATE;

-- Update existing records to use their registration date
UPDATE patient SET joindate = CURRENT_DATE WHERE joindate IS NULL; 