CREATE TABLE IF NOT EXISTS DL_Brevet (
  BrevetID INTEGER PRIMARY KEY,
  Organisation TEXT,
  Brevet TEXT,
  DateBrevet TEXT,
  BrevetNr TEXT,
  Instructor TEXT,
  Picture1 TEXT,
  Picture2 TEXT
);

INSERT INTO DL_Brevet (BrevetID, Organisation, Brevet, DateBrevet, BrevetNr, Instructor, Picture1, Picture2)
VALUES
  (1, 'PADI', 'Divemaster', '2007-05-26', 'DM-491969', NULL, 'cert-divemaster-front.jpg', 'cert-divemaster-back.jpg'),
  (2, 'PADI', 'Rescue Diver', '2006-09-16', '0609E8304', 'Fiona Pullens', 'cert-rescue-front.jpg', NULL),
  (3, 'PADI', 'EFR', '2006-09-16', NULL, 'Fiona Pullens', 'cert-efr-front.jpg', 'cert-efr-back.jpg');
