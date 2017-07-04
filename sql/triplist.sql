SELECT t.ID, t.TripName, t.ShopID, t.CountryID, c.Country, s.ShopName, t.PhotoPath
FROM (( $_config[table_prefix]Trip t
LEFT JOIN $_config[table_prefix]Country c ON t.CountryID = c.ID )
LEFT JOIN $_config[table_prefix]Shop s ON t.ShopID = s.ID )
ORDER BY t.TripName ASC
