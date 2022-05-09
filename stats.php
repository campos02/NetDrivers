<?php
declare(strict_types=1);
?>

<head>
   <?php $title = 'Statistics';
   require('head.php'); ?>
</head>
<a href="/">
	<table>
		<tr>
			<td><img src="/favicon.png" width="50"></td>
			<td><h1 style="margin: 0">NetDrivers</h1><i>Archiving Drivers Since February 2022</i></td>
		</tr>
	</table>
</a>
<hr>
<?php
require('nav.html');
echo '<hr/>';
require('use_database.php');

$db = new UseDatabase();
$stats = $db->selectStats();

if (count($stats) > 0) {
	echo '<table border="1"><tr><th>Item</th><th>Count</th><tr>';
	foreach ($stats as $stat) {
		echo '<tr><td>' . $stat['name'] . '</td><td>' . $stat['count'] . '</td></tr>';
	}
	echo '</table>';
}
?>
