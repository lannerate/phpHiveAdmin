<?php
ignore_user_abort(true);
set_time_limit(0);


if(!$_GET['database'] || '' == $_GET['database'])
{
	die($lang['dieTableChoose']);
}
else
{

	echo $_GET['database'].' --  <a href=index.php?frame=right target="right">Back</a><br /><br />';
	
	include_once 'config.inc.php';
	include_once 'templates/sql_query_navi.html';
	echo "<br /><br />";
	
	$transport->open();

	$client->execute('add jar '.$env['hive_jar']);
	
	$sql = 'use '.$_GET['database'];
	//echo $sql.'<br /><br />';
	$client->execute($sql);
	$client->fetchOne();
	
	$sql = 'desc '.$_GET['table'];
	$client->execute($sql);
	$array_desc_table = $client->fetchAll();
	//get table description and explode the desc into a multi-dimensional array
	//��ȡ��˵�����������ά����$array_desc_desc
	$i = 0;
	while ('' != @$array_desc_table[$i])
	{
		$array_desc = explode('	',$array_desc_table[$i]);
		$array_desc_desc['name'][$i] = $array_desc[0];
		$array_desc_desc['type'][$i] = $array_desc[1];
		$i++;
	}
	//var_dump($array_desc_desc);

	if(!@$_POST['sql'] || '' == @$_POST['sql'])
	{
		echo "<body bgcolor=\"#EFEFEF\">";
		echo '<table border=1>';
		$i = 0;
		foreach ($array_desc_desc as $value)
		{
			if(0 == $i)
			{
				$color = "bgcolor=\"#FFFF99\"";
			}
			else
			{
				$color = "bgcolor=\"#99FFFF\"";
			}
			echo '<tr '.$color.'>';
			foreach($value as $v)
			{
				echo '<td>'.$v.'</td>';
				$i++;
			}
			echo '</tr>';
		}
		echo '</table>';
		include_once 'templates/sql_query.html';
	}
	else
	{
		$timer->start();
		echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		echo "<body bgcolor=\"#EFEFEF\">";
		$sql = $_POST['sql'];
		//add limit to standard sql
		if(preg_match('/limit/i',$sql) == '0')
		{
			$sql .= ' limit 30';
		}
		
		echo $sql.'<br /><br />';
		$client->execute($sql);
		$array = $client->fetchAll();

		//construct table desc table
		echo "<table border=1>\n";
		$i = 0;
		foreach ($array_desc_desc as $value)
		{
			if(0 == $i)
			{
				$color = "bgcolor=\"#FFFF99\"";
			}
			else
			{
				$color = "bgcolor=\"#99FFFF\"";
			}
			echo "<tr ".$color.">\n";
			foreach($value as $v)
			{
				echo "<td>".$v."</td>\n";
				$i++;
			}
			echo "</tr>\n";
			$i++;
		}
		//construct result file
		$time = date('Y-m-d_H-i-s',time());
		$timehash = sha1($time);
		$filename = 'tmp/'.$_GET['table'].'_'.$time.'_'.$timehash.'.csv';
		
		$fp = fopen($filename,'w');
		$i = 0;
		while ('' != @$array[$i])
		{
			$str = str_replace('	',',',$array[$i])."\n";
			fwrite($fp,$str);
			$i++;
		}
		fclose($fp);
		echo "<a href=download.php?filename=".$filename.">".$lang['download']."</a>";
		echo "</table>\n";
		include_once 'templates/sql_query.html';
		$timer->stop();
		echo 'Excution time: '.$timer->spent().'s';
		unset($timer);
	}

	$transport->close();
}
?>