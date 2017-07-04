SELECT  $_config[table_prefix]Place.ID AS ID, 
        $_config[table_prefix]Logbook.Country AS Country, 
        $_config[table_prefix]Place.Place AS Place, 
        $_config[table_prefix]Logbook.City AS City,
        $_config[table_prefix]Place.MaxDepth AS MaxDepth
FROM $_config[table_prefix]Place INNER JOIN $_config[table_prefix]Logbook 
        ON $_config[table_prefix]Place.ID = $_config[table_prefix]Logbook.PlaceID
GROUP BY ID

