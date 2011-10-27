SELECT		ID,
		Object,
		Manufacturer,
		Inactive,
		PhotoPath,
		((to_days( now()) > (to_days(DateRN) - $_config[equipment_service_warning]))
OR 		(to_days( now()) > (to_days(O2ServiceDate) - $_config[equipment_service_warning])))
AND		(Inactive = 'False')
AS 		Service
FROM		$_config[table_prefix]Equipment
ORDER BY 	Inactive DESC, Object ASC, DateP ASC, Manufacturer ASC
