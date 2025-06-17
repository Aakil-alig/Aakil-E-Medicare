-- Switch to the edoc database
USE edoc;

-- Create doctor_ratings table
CREATE TABLE IF NOT EXISTS doctor_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    docid INT NOT NULL,
    pid INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add sample ratings (only if the table is empty)
INSERT INTO doctor_ratings (docid, pid, rating, review)
SELECT * FROM (
    SELECT 1, 1, 5, 'Excellent doctor, very professional and caring' UNION ALL
    SELECT 1, 2, 4, 'Good experience overall' UNION ALL
    SELECT 2, 1, 5, 'Very knowledgeable and helpful' UNION ALL
    SELECT 2, 2, 4, 'Professional service' UNION ALL
    SELECT 3, 1, 5, 'Great experience' UNION ALL
    SELECT 3, 2, 4, 'Very thorough examination'
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM doctor_ratings LIMIT 1); 