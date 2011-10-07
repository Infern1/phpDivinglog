SELECT		c.ID, c.City, c.Type, l.Country, COUNT(*) AS Dives
FROM 		$_config[table_prefix]City AS c, $_config[table_prefix]Logbook AS l
WHERE 		c.ID = l.CityID
GROUP BY 	l.CityID
ORDER BY 	l.Country ASC, c.City ASC