USE edoc;

CREATE TABLE IF NOT EXISTS patient_reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT,
    doctor_id INT,
    appointment_id INT,
    disease VARCHAR(255),
    symptoms TEXT,
    diagnosis TEXT,
    treatment TEXT,
    prescription TEXT,
    report_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patient(pid),
    FOREIGN KEY (doctor_id) REFERENCES doctor(docid),
    FOREIGN KEY (appointment_id) REFERENCES appointment(appoid)
); 