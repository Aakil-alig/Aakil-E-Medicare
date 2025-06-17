-- Switch to edoc database
USE edoc;

-- Create webuser table if it doesn't exist
CREATE TABLE IF NOT EXISTS webuser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    usertype CHAR(1) NOT NULL,
    reset_token VARCHAR(64) NULL,
    reset_expiry DATETIME NULL
);

-- Show the current entry in webuser table
SELECT * FROM webuser WHERE email = 'patient1@gmail.com';

-- Update the existing entry to ensure correct usertype
UPDATE webuser SET usertype = 'p' WHERE email = 'patient1@gmail.com';

-- Add missing webuser entry for patient1@gmail.com
INSERT INTO webuser (email, usertype) VALUES ('patient1@gmail.com', 'p'); 