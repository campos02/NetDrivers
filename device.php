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
<?php
echo '<hr>';
require('nav.html');
echo '<hr>';
require('use_database.php');

if (isset($_GET['id'])) {
   $db = new UseDatabase();
   $devices = $db->selectDevice($_GET['id']);

   if (count($devices) > 0) {
      // output data of each row
      foreach ($devices as $device) {
         // Make table data array to avoid duplicate headers later
         $systemsData = $db->selectSystems();
         foreach($systemsData as $key => $system) {
            $systemsData[$key]['data'] = [];
         }
         echo '<h2 class="title"><i>' . $device['manufacturer'] . ' ' . $device['device_name'] . '</i></h2><hr>';
         echo "<b>Category:</b> " . $device['category'] . '<br><br>';
         echo '<table border="1">';
         $files = json_decode($device['files']);
         foreach($files as $file) {
            $systems = $db->selectSystemsByFile($file);
            if (count($systems) > 0) {
               foreach($systems as $system) {
                  $data = json_decode($system['data'], true, 512, JSON_THROW_ON_ERROR);
                  foreach($data['data'] as $dataElement) {
                     if (in_array($file, $dataElement['drivers'])) {
                        $systemKey = 0;
                        // Match system in table data array and add file data to it
                        foreach($systemsData as $key => $singleSystem) {
                           if ($singleSystem['id'] == $system['id']) {
                              $systemKey = $key;
                           }
                        }
                        array_push($systemsData[$systemKey]['data'], array(
                           'os' => $dataElement['os'],
                           'driver' => $file
                        ));
                     }
                  }
               }
            }
         }
         foreach($systemsData as $system) {
            if (count($system['data']) > 0) {
               echo '<tr><th colspan="4"><b>' . $system['manufacturer'] . ' ' . $system['model'] . ':</b></th></tr>';
               foreach($system['data'] as $data) {
               echo '<tr><td class="drvdetails">' . $data['os'] . '</td>
                        <td class="drvdetails"><a href="/drivers.php?id=' . $data['driver'] . '">More Details</a></td><td class="drvdetails">'
                        . '<a href="/download.php?id=' . $data['driver'] .'">Download</a></td></tr>';
               }
            }
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
