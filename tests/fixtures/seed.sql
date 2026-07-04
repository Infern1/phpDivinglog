INSERT INTO DL_Logbook (Number, LogID, PlaceID, ShopID, TripID, Divedate, Divetime, Depth, Profile, ProfileInt)
VALUES
  (1, 100, 10, 1, 1, '2026-01-01', '40', 18.2, '010000000000015000000000020000000000', 60),
  (2, 101, 11, 2, 2, '2026-02-01', '50', 22.4, '012000000000018000000000024000000000', 60);

INSERT INTO DL_Place (PlaceID, Place, CountryID, CityID, Latitude, Longitude, PlaceMap, PlaceComment)
VALUES
  (10, 'Blue Hole', 1, 1, 25.123, -80.456, 'blue-hole-map.jpg', 'Steep wall'),
  (11, 'Coral Garden', 1, 2, 24.987, -80.321, 'coral-garden-map.jpg', 'Easy reef');

INSERT INTO DL_Country (CountryID, Country, FlagImage)
VALUES
  (1, 'Bahamas', 'bahamas.png'),
  (2, 'Egypt', 'egypt.png');

INSERT INTO DL_City (CityID, CountryID, City, CityComment)
VALUES
  (1, 1, 'Nassau', 'Capital city'),
  (2, 1, 'Freeport', 'Northern island hub');

INSERT INTO DL_Shop (ShopID, CountryID, ShopName, ShopType, City, ShopComment)
VALUES
  (1, 1, 'Ocean Dive Center', 'Resort', 'Nassau', 'Friendly staff'),
  (2, 1, 'Coral Pro Shop', 'Day Boat', 'Freeport', 'Great guides');

INSERT INTO DL_Trip (TripID, TripName, DateFrom, DateTo, CountryID, ShopID, TripComment)
VALUES
  (1, 'Spring Bahamas', '2026-01-01', '2026-01-07', 1, 1, 'Warm water week'),
  (2, 'Reef Weekend', '2026-02-14', '2026-02-16', 1, 2, 'Quick getaway');

INSERT INTO DL_Equipment (EquipmentID, Object, Manufacturer, DatePurchase, DateService, DateServiceWarning, Comment, Picture)
VALUES
  (1, 'Regulator', 'Apeks', '2024-06-01', '2026-07-15', '2026-06-15', 'Primary set', 'regulator.jpg'),
  (2, 'BCD', 'Scubapro', '2023-03-20', '2026-12-01', '2026-11-01', 'Travel bcd', 'bcd.jpg');

INSERT INTO DL_Buddy (BuddyID, Firstname, Lastname, email, comment, Picture)
VALUES
  (1, 'Alex', 'Reef', 'alex@example.com', 'Steady diver', 'alex.jpg'),
  (2, 'Sam', 'Blue', 'sam@example.com', 'Great with navigation', 'sam.jpg');

INSERT INTO DL_Pictures (PictureID, LogID, Picture, Description)
VALUES
  (1, 100, 'dive-100-a.jpg', 'Shark pass'),
  (2, 100, 'dive-100-b.jpg', 'Coral arch'),
  (3, 101, 'dive-101-a.jpg', 'Sunbeams');

INSERT INTO DL_Tank (TankID, Number, Volume, Pstart, Pend, O2)
VALUES
  (1, 1, 12.0, 200.0, 70.0, 32.0),
  (2, 1, 11.0, 210.0, 90.0, 21.0),
  (3, 2, 11.0, 210.0, 80.0, 21.0);

INSERT INTO DL_Userdefined (UserdefinedID, LogID, Name, Value)
VALUES
  (1, 100, 'Visibility', '20m'),
  (2, 100, 'Current', 'Mild');

INSERT INTO DL_Personal (Firstname, Lastname, Email, City, Country, Comment, Picture)
VALUES
  ('Robin', 'Diver', 'robin@example.com', 'Nassau', 'Bahamas', 'Avid diver', 'profile.jpg');

INSERT INTO DL_DBInfo (PrgName, Version)
VALUES
  ('Diving Log', '6.0.22');
