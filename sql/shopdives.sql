SELECT		Number
FROM		$_config[table_prefix]Logbook
WHERE		ShopID = '$globals[shopid]'
ORDER BY	Number ASC
