SELECT		t.*, c.Country, s.ShopName
FROM		(( $_config[table_prefix]Trip t
LEFT JOIN	$_config[table_prefix]Country c ON t.CountryID = c.ID )
LEFT JOIN	$_config[table_prefix]Shop s ON t.ShopID = s.ID )
WHERE		t.ID = '$globals[tripid]'
LIMIT		1

