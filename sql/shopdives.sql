SELECT		Number
FROM		$_config[table_prefix]Logbook
WHERE		ShopID = '$globals[placeid]'
ORDER BY	Number ASC
