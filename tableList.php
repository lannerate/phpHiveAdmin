﻿<?php

if(!@_GET['database'])
{
	die($lang['dieDatabaseChoose']);
}
else
{
	include_once 'config.inc.php';

	$transport->open();

	$client->execute('add jar '.$env['hive_jar']);
	$client->execute('use '.$_GET['database']);

	$client->execute($env['showTables']);
	$db_array = $client->fetchAll();

	$i = 0;
	echo '<a href="dbList.php" target=left><<< Back</a><br /><br />';
	while('' != @$db_array[$i]) {
		echo '<a href=sqlQuery.php?table='.$db_array[$i].'&database='.$_GET['database'].' target="right">'.$db_array[$i].'</a><br />';
		$i++;
	}
	echo '<a href="dbList.php" target=left><<< Back</a><br /><br />';
	$transport->close();
}
?>