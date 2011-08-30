SELECT		c.ID, c.City, l.Country, COUNT(*) 
FROM 		$_config[table_prefix]City AS c, $_config[table_prefix]Logbook AS l
WHERE 		c.ID = l.CityID
GROUP BY 	l.CityID
ORDER BY 	c.City ASC