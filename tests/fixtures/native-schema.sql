-- Synthetic schema modeled on the real, unprefixed Diving Log SQLite desktop export
-- (table/column names sampled during design of the sqlite-database-support spec).
-- No real user data is included here; see native-seed.sql for small synthetic sample rows.

CREATE TABLE IF NOT EXISTS Logbook (
  ID INTEGER PRIMARY KEY,
  Number INTEGER,
  Divedate TEXT,
  Entrytime TEXT,
  Country TEXT,
  CountryID INTEGER,
  City TEXT,
  CityID INTEGER,
  Place TEXT,
  PlaceID INTEGER,
  Divetime REAL,
  Depth REAL,
  Buddy TEXT,
  BuddyIDs TEXT,
  Comments TEXT,
  Water INTEGER,
  Entry INTEGER,
  Divetype TEXT,
  Tanksize REAL,
  PresS REAL,
  PresE REAL,
  Airtemp REAL,
  Watertemp REAL,
  Weight REAL,
  Deco INTEGER,
  Rep INTEGER,
  Profile TEXT,
  ProfileInt INTEGER,
  O2 REAL,
  DblTank INTEGER,
  SupplyType INTEGER,
  ShopID INTEGER,
  TripID INTEGER
);

CREATE TABLE IF NOT EXISTS Place (
  ID INTEGER PRIMARY KEY,
  CountryID INTEGER,
  CityID INTEGER,
  Place TEXT,
  Rating INTEGER,
  MaxDepth REAL,
  Lat TEXT,
  Lon TEXT,
  MapPath TEXT,
  Comments TEXT,
  Water INTEGER,
  Altitude TEXT,
  WaterName TEXT,
  Difficulty TEXT
);

CREATE TABLE IF NOT EXISTS Country (
  ID INTEGER PRIMARY KEY,
  LogID INTEGER,
  Country TEXT,
  Gmt INTEGER,
  Currency TEXT,
  CurFactor REAL,
  FlagPath TEXT,
  Comments TEXT
);

CREATE TABLE IF NOT EXISTS City (
  ID INTEGER PRIMARY KEY,
  CountryID INTEGER,
  City TEXT,
  Type INTEGER,
  Comments TEXT,
  MapPath TEXT
);

CREATE TABLE IF NOT EXISTS Shop (
  ID INTEGER PRIMARY KEY,
  ShopName TEXT,
  ShopType TEXT,
  City TEXT,
  Comments TEXT,
  Rating INTEGER
);

CREATE TABLE IF NOT EXISTS Trip (
  ID INTEGER PRIMARY KEY,
  ShopID INTEGER,
  CountryID INTEGER,
  CityID INTEGER,
  TripName TEXT,
  StartDate TEXT,
  EndDate TEXT,
  Comments TEXT,
  Rating INTEGER
);

CREATE TABLE IF NOT EXISTS Equipment (
  ID INTEGER PRIMARY KEY,
  Object TEXT,
  Manufacturer TEXT,
  DateP TEXT,
  DateR TEXT,
  DateRN TEXT,
  Comments TEXT,
  PhotoPath TEXT
);

CREATE TABLE IF NOT EXISTS Buddy (
  ID INTEGER PRIMARY KEY,
  FirstName TEXT,
  LastName TEXT,
  Email TEXT,
  Comments TEXT,
  PhotoPath TEXT
);

CREATE TABLE IF NOT EXISTS Pictures (
  ID INTEGER PRIMARY KEY,
  LogID INTEGER,
  Path TEXT,
  Description TEXT
);

CREATE TABLE IF NOT EXISTS Tank (
  ID INTEGER PRIMARY KEY,
  LogID INTEGER,
  TankID INTEGER,
  SortOrd INTEGER,
  Tanktype INTEGER,
  Tanksize REAL,
  PresS REAL,
  PresE REAL,
  PresW REAL,
  O2 REAL,
  He REAL,
  DblTank INTEGER,
  SupplyType INTEGER,
  MinPPO2 REAL,
  MaxPPO2 REAL
);

CREATE TABLE IF NOT EXISTS Userdefined (
  ID INTEGER PRIMARY KEY,
  LogID INTEGER,
  galid TEXT,
  Field2 TEXT,
  Field3 TEXT,
  Field4 TEXT,
  Field5 TEXT,
  Field6 TEXT,
  Field7 TEXT,
  Field8 TEXT,
  Field9 TEXT,
  Field10 TEXT
);

CREATE TABLE IF NOT EXISTS Personal (
  ID INTEGER PRIMARY KEY,
  FirstName TEXT,
  LastName TEXT,
  City TEXT,
  Country TEXT,
  Email TEXT,
  Comments TEXT,
  PhotoPath TEXT
);

-- The real native export has no primary key on DBInfo (single-row table) and names the
-- version column DBVersion, not Version -- deliberately different from
-- tests/fixtures/schema.sql's DL_DBInfo(PrgName, Version), to exercise that divergence.
CREATE TABLE IF NOT EXISTS DBInfo (
  PrgName TEXT,
  DBVersion TEXT
);

CREATE TABLE IF NOT EXISTS Brevets (
  ID INTEGER PRIMARY KEY,
  Brevet TEXT,
  Org TEXT,
  CertDate TEXT,
  Number TEXT,
  Instructor TEXT,
  Scan1Path TEXT,
  Scan2Path TEXT
);
