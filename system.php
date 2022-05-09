<?php
declare(strict_types=1);
?>
<html>
<head>
   <?php
   $title = 'System Info';
   require('head.php'); ?>
</head>
<body>
<a href="/">
	<table>
		<tr>
			<td><img src="/favicon.png" width="50"></td>
			<td><h1 class="header">NetDrivers</h1><i>Archiving Drivers Since February 2022</i></td>
		</tr>
	</table>
</a>
<hr>
<?php require('nav.html'); ?>
<hr>
<?php
require('use_database.php');

if (isset($_GET['id'])) {
   $db = new UseDatabase();
   $systems = $db->selectSystem($_GET['id']);

   if (count($systems) > 0) {
      // output data of each row
      foreach ($systems as $system) {
         $driver = json_decode($system['data'], true, 512, JSON_THROW_ON_ERROR);
         echo '<h2 class="title"><i>' . $system['manufacturer'] . ' ' . $system['model'] . '</i></h2><hr>';
         echo '<a href="/link.php?type=system&id=' . urlencode($_GET['id']) . '">Linkback</a><br><br>';
         echo '<table border="1">';
         foreach ($driver['data'] as $item) {
            echo '<tr><th colspan="4"><b>' . $item['os'] . ':</b></th></tr>';
            if (count($item['drivers']) > 0) {
               // Commented out as it doesn't get used anywhere
               //$drstr = '';
               foreach ($item['drivers'] as $driver) {
                  $devices = $db->selectDevicesByFile($driver);
                  foreach ($devices as $device) {
                     echo '<tr><td class="drvdetails">' . $device['manufacturer'] . '</td><td class="drvdetails">' . $device['device_name']
                        . '</td><td class="drvdetails"><a href="/drivers.php?id=' . $driver . '">More Details</a></td><td class="drvdetails">'
                        . '<a href="/download.php?id=' . $driver .'">Download</a></td></tr>';
                  }
               }
            }
            echo '<tr><td>TBD</td></tr>';
         }
         echo '</table><br>';
      }
   } else {
      echo 'Invalid System ID';
   }
} else {
   echo '<b>Error:</b> No System ID Specified!';
}
?>
<hr>
</body>
</html>
