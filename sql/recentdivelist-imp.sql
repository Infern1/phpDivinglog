SELECT  Number, 
        Divedate, 
        Divetime, 
        (Depth*3.2808) as Depth, 
        Place, 
        City,
	Profile <> '' AS Profile
FROM    $_config[table_prefix]Logbook 

