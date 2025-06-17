-- Switch to edoc database
USE edoc;

-- Check patient table entry
SELECT pid, pemail, pname, ppassword FROM patient WHERE pemail = 'patient1@gmail.com'; 