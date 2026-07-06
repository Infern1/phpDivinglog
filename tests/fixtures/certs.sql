CREATE TABLE IF NOT EXISTS DL_Brevets (
  BrevetID INTEGER PRIMARY KEY,
  Org TEXT,
  Brevet TEXT,
  CertDate TEXT,
  Number TEXT,
  Instructor TEXT,
  Scan1Path TEXT,
  Scan2Path TEXT
);

INSERT INTO DL_Brevets (BrevetID, Org, Brevet, CertDate, Number, Instructor, Scan1Path, Scan2Path)
VALUES
  (1, 'PADI', 'Divemaster', '2007-05-26', 'DM-491969', NULL, 'cert-divemaster-front.jpg', 'cert-divemaster-back.jpg'),
  (2, 'PADI', 'Rescue Diver', '2006-09-16', '0609E8304', 'Fiona Pullens', 'cert-rescue-front.jpg', NULL),
  (3, 'PADI', 'EFR', '2006-09-16', NULL, 'Fiona Pullens', 'cert-efr-front.jpg', 'cert-efr-back.jpg');
