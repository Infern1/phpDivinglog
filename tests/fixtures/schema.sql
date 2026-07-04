CREATE TABLE IF NOT EXISTS DL_Logbook (
  Number INTEGER PRIMARY KEY,
  LogID INTEGER,
  PlaceID INTEGER,
  ShopID INTEGER,
  TripID INTEGER,
  Divedate TEXT,
  Divetime TEXT,
  Depth REAL,
  Profile TEXT,
  ProfileInt INTEGER
);

CREATE TABLE IF NOT EXISTS DL_Place (
  PlaceID INTEGER PRIMARY KEY,
  Place TEXT,
  CountryID INTEGER,
  CityID INTEGER,
  Latitude REAL,
  Longitude REAL,
  PlaceMap TEXT,
  PlaceComment TEXT
);

CREATE TABLE IF NOT EXISTS DL_Country (
  CountryID INTEGER PRIMARY KEY,
  Country TEXT,
  FlagImage TEXT
);

CREATE TABLE IF NOT EXISTS DL_City (
  CityID INTEGER PRIMARY KEY,
  CountryID INTEGER,
  City TEXT,
  CityComment TEXT
);

CREATE TABLE IF NOT EXISTS DL_Shop (
  ShopID INTEGER PRIMARY KEY,
  CountryID INTEGER,
  ShopName TEXT,
  ShopType TEXT,
  City TEXT,
  ShopComment TEXT
);

CREATE TABLE IF NOT EXISTS DL_Trip (
  TripID INTEGER PRIMARY KEY,
  TripName TEXT,
  DateFrom TEXT,
  DateTo TEXT,
  CountryID INTEGER,
  ShopID INTEGER,
  TripComment TEXT
);

CREATE TABLE IF NOT EXISTS DL_Equipment (
  EquipmentID INTEGER PRIMARY KEY,
  Object TEXT,
  Manufacturer TEXT,
  DatePurchase TEXT,
  DateService TEXT,
  DateServiceWarning TEXT,
  Comment TEXT,
  Picture TEXT
);

CREATE TABLE IF NOT EXISTS DL_Buddy (
  BuddyID INTEGER PRIMARY KEY,
  Firstname TEXT,
  Lastname TEXT,
  email TEXT,
  comment TEXT,
  Picture TEXT
);

CREATE TABLE IF NOT EXISTS DL_Pictures (
  PictureID INTEGER PRIMARY KEY,
  LogID INTEGER,
  Picture TEXT,
  Description TEXT
);

CREATE TABLE IF NOT EXISTS DL_Tank (
  TankID INTEGER PRIMARY KEY,
  Number INTEGER,
  Volume REAL,
  Pstart REAL,
  Pend REAL,
  O2 REAL
);

CREATE TABLE IF NOT EXISTS DL_Userdefined (
  UserdefinedID INTEGER PRIMARY KEY,
  LogID INTEGER,
  Name TEXT,
  Value TEXT
);

CREATE TABLE IF NOT EXISTS DL_Personal (
  Firstname TEXT,
  Lastname TEXT,
  Email TEXT,
  City TEXT,
  Country TEXT,
  Comment TEXT,
  Picture TEXT
);

CREATE TABLE IF NOT EXISTS DL_DBInfo (
  PrgName TEXT,
  Version TEXT
);
