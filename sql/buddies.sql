SELECT		ID, FirstName, LastName
FROM		$_config[table_prefix]Buddy
WHERE		ID IN ($globals[buddies])
