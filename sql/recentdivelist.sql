SELECT  Number, 
        Divedate, 
        Divetime, 
        round ( Depth , 1 )  AS Depth, 
        Place, 
        City,
	Profile <> '' AS Profile
FROM    $_config[table_prefix]Logbook



