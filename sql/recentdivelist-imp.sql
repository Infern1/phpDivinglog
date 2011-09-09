SELECT  Number, 
        Divedate, 
        Divetime, 
        ROUND(Depth*3.2808,1) as Depth, 
        Place, 
        City,
	Profile
FROM    $_config[table_prefix]Logbook 

