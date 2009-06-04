<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Spam Cloud</title>
	<style type="text/css" media="screen">
		/*<![CDATA[*/
		body { font-size: 14px; text-align: left; margin: 35px; }
		/*]]>*/
	</style>
</head>
<body>
	<p><?=$added?> added, <?=$skipped?> skipped, <?=$total_records_deleted?> deleted, <?=$total_records?> total in database from a maximum of <?=$max_db_records?>, averaging <?=$records_per_day?> spam messages per day, over a period of <?=$total_period?>, from <?=$first_record_date?> to <?=$last_record_date?>.</p>
		<p>Executed in <?=$exec_time?> seconds.</p>
	</div>
</body>
</html>
