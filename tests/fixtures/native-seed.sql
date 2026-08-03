-- Small synthetic sample data for native-schema.sql. Not real user data.

INSERT INTO Country (ID, LogID, Country, Gmt, Currency, CurFactor, FlagPath, Comments)
VALUES
  (1, NULL, 'Bahamas', -5, 'USD', 1.0, 'bahamas.png', 'Warm and clear'),
  (2, NULL, 'Egypt', 2, 'EGP', 1.0, 'egypt.png', 'Red Sea diving');

INSERT INTO City (ID, CountryID, City, Type, Comments, MapPath)
VALUES
  (1, 1, 'Nassau', 1, 'Capital city', NULL),
  (2, 1, 'Freeport', 1, 'Northern island hub', NULL);

INSERT INTO Place (ID, CountryID, CityID, Place, Rating, MaxDepth, Lat, Lon, MapPath, Comments, Water, Altitude, WaterName, Difficulty)
VALUES
  (10, 1, 1, 'Blue Hole', 5, 41.0, '25.123', '-80.456', 'blue-hole-map.jpg', 'Steep wall', 1, NULL, 'Ocean', 'Advanced'),
  (11, 1, 2, 'Coral Garden', 4, 22.4, '24.987', '-80.321', 'coral-garden-map.jpg', 'Easy reef', 1, NULL, 'Ocean', 'Easy');

INSERT INTO Shop (ID, ShopName, ShopType, City, Comments, Rating)
VALUES
  (1, 'Ocean Dive Center', 'Resort', 'Nassau', 'Friendly staff', 5),
  (2, 'Coral Pro Shop', 'Day Boat', 'Freeport', 'Great guides', 4);

INSERT INTO Trip (ID, ShopID, CountryID, CityID, TripName, StartDate, EndDate, Comments, Rating)
VALUES
  (1, 1, 1, 1, 'Spring Bahamas', '2026-01-01', '2026-01-07', 'Warm water week', 5),
  (2, 2, 1, 2, 'Reef Weekend', '2026-02-14', '2026-02-16', 'Quick getaway', 4),
  (3, NULL, 2, NULL, 'No Dives Trip', '2026-04-01', '2026-04-03', 'No diving this time', NULL);

INSERT INTO Equipment (ID, Object, Manufacturer, DateP, DateR, DateRN, Comments, PhotoPath)
VALUES
  (1, 'Regulator', 'Apeks', '2024-06-01', '2026-07-15', '2026-06-15', 'Primary set', 'regulator.jpg'),
  (2, 'BCD', 'Scubapro', '2023-03-20', '2026-12-01', '2026-11-01', 'Travel bcd', 'bcd.jpg'),
  (3, 'Computer', 'Shearwater', '2025-01-10', NULL, NULL, 'Spare computer', 'computer.jpg');

INSERT INTO Buddy (ID, FirstName, LastName, Email, Comments, PhotoPath)
VALUES
  (1, 'Alex', 'Reef', 'alex@example.com', 'Steady diver', 'alex.jpg'),
  (2, 'Sam', 'Blue', 'sam@example.com', 'Great with navigation', 'sam.jpg');

INSERT INTO Logbook (
  ID, Number, Divedate, Entrytime, Country, CountryID, City, CityID, Place, PlaceID,
  Divetime, Depth, Buddy, BuddyIDs, Comments, Water, Entry, Divetype, Tanksize, PresS, PresE,
  Airtemp, Watertemp, Weight, Deco, Rep, Profile, ProfileInt, O2, DblTank, SupplyType, ShopID, TripID
)
VALUES
  (1, 1, '2026-01-01', '09:10:00', 'Bahamas', 1, 'Nassau', 1, 'Blue Hole', 10,
   40, 18.0, NULL, '1', 'Great first dive', 1, 1, '3,8', 12.0, 200.0, 70.0,
   27.0, 24.0, NULL, 0, 1, NULL, 60, 32.0, 0, 0, 1, 1),
  (2, 2, '2026-02-01', '10:30:00', 'Bahamas', 1, 'Freeport', 2, 'Coral Garden', 11,
   50, 22.4, NULL, '2', 'Reef dive', 2, 2, '4,5', 11.0, 210.0, 90.0,
   25.0, 22.0, NULL, 1, 1, NULL, 60, 21.0, 1, 1, 2, 2),
  (3, 3, '2026-03-15', '08:15:00', 'Bahamas', 1, 'Nassau', 1, 'Blue Hole', 10,
   65, 41.0, NULL, '1,2', 'Deep wall dive', 3, 1, '6,7', 11.0, 210.0, 80.0,
   24.0, 21.0, NULL, 0, 1, NULL, 60, 21.0, 1, 2, 1, 1);

INSERT INTO Pictures (ID, LogID, Path, Description)
VALUES
  (1, 1, 'dive-1-a.jpg', 'Shark pass'),
  (2, 1, 'dive-1-b.jpg', 'Coral arch'),
  (3, 2, 'dive-2-a.jpg', 'Sunbeams');

INSERT INTO Tank (ID, LogID, TankID, SortOrd, Tanktype, Tanksize, PresS, PresE, PresW, O2, He, DblTank, SupplyType, MinPPO2, MaxPPO2)
VALUES
  (1, 1, 1, 1, 0, 12.0, 200.0, 70.0, NULL, 32.0, NULL, 0, 0, NULL, 1.4),
  (2, 2, 1, 1, 1, 11.0, 210.0, 90.0, NULL, 21.0, NULL, 1, 1, NULL, 1.4),
  (3, 3, 1, 1, 0, 11.0, 210.0, 80.0, NULL, 21.0, NULL, 1, 2, NULL, 1.4);

INSERT INTO Userdefined (ID, LogID, galid, Field2, Field3, Field4, Field5, Field6, Field7, Field8, Field9, Field10)
VALUES
  (1, 1, 'gal-1', '20m visibility', 'Mild current', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
  (2, 2, NULL, 'Great visibility', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO Personal (ID, FirstName, LastName, City, Country, Email, Comments, PhotoPath)
VALUES
  (1, 'Robin', 'Diver', 'Nassau', 'Bahamas', 'robin@example.com', 'Avid diver', 'profile.jpg');

INSERT INTO DBInfo (PrgName, DBVersion)
VALUES
  ('Diving Log', '6.0.22');

INSERT INTO Brevets (ID, Brevet, Org, CertDate, Number, Instructor, Scan1Path, Scan2Path)
VALUES
  (1, 'Open Water Diver', 'PADI', '2020-05-01', '12345', 'Jane Instructor', 'ow-front.jpg', 'ow-back.jpg');
