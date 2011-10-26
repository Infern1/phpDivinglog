SELECT  Number, 
        Divedate, 
        Divetime, 
        Depth, 
        Place, 
        City,
	Profile <> '' AS Profile
FROM    $_config[table_prefix]Logbook



