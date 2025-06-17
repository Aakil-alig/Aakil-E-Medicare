-- Switch to edoc database
USE edoc;

-- Check for any whitespace in patient email
SELECT CONCAT('|', pemail, '|') as patient_email_check FROM patient WHERE pemail LIKE '%patient1@gmail.com%';

-- Check for any whitespace in webuser email
SELECT CONCAT('|', email, '|') as webuser_email_check FROM webuser WHERE email LIKE '%patient1@gmail.com%'; 