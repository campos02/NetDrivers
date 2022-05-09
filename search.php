<?php
declare(strict_types=1);
?>

<head>
   <?php
   if (isset($_POST['query'])) {
      $title = 'Search for "' . $_POST['query'] . '"';
   } else {
      $title = 'Search';
   }
   require('head.php');
   ?>
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
<?php require('nav.html'); ?>
<hr>
<form action="search.php" method="post">
	<input type="text" name="query">&nbsp;<input type="submit"><br>
	<input type="radio" name="scope" checked="true"
       <?php
       // @TODO: This should be moved to a function. Furthermore, $scope is not defined
       //        therefore I'm commenting this line out for the time being.
       // - if (isset($scope) && $scope === 'system') echo 'checked';?>
	       value="system">Systems<input type="radio" name="scope"
      <?php // - if (isset($scope) && $scope=="device") echo "checked";?>
	                                     value="device">Devices<input type="radio" name="scope"
      <?php // - if (isset($scope) && $scope=="drivers") echo "checked";?>
	                                                                   value="files">Filename
</form>
<?php
if (!isset($_POST['scope'])) {
   return;
}

/**
 * Cleans a string
 *
 * @param string $data The string to be cleaned
 *
 * @return string Clean string
 */

function cleanInput(string $data): string {
   $data = trim($data);
   $data = stripslashes($data);

   return htmlspecialchars($data);
}

function listName(string $list, array $row): string {
   if ($list === 'system' || $list === 'device') {
      if ($list === 'system') {
         $output = $row['manufacturer'] . ' ' . $row['model'];
      } else {
         $output = $row['manufacturer'] . ' ' . $row['device_name'];
      }

      return '<h2><a href="/' . $list . '.php?id=' . $row['id'] . '">'
         . $output
         . '</a></h2>';
   } else if ($list === 'files') {
      $date = new DateTime($row['date']);

      return '<p><b>Filename:</b> ' . $row['file_name'] . '<br><b>Version:</b> ' . $row['version'] . '<br><b>Date:</b> ' . $date->format('d M Y') .
         '<br><a href="/download.php?id=' . $row['id'] . '"><button type="button">Download</button></a></p>';
   }

   return '';
}

$queryScope = cleanInput($_POST['scope']);

$query      = null;
$cleanquery = '';
if (isset($_POST['query'])) {
   $query      = '%' . $_POST['query'] . '%';
   $cleanquery = str_replace('%', '', $query);
}

require('use_database.php');

$db = new UseDatabase();
$results = [];
$list   = '';
$querytime = microtime(true);
switch ($queryScope) {
   case 'system':
   {
      $results = $db->searchSystems($query);
      break;
   }
   case 'device':
   {
      $results = $db->searchDevices($query);
      break;
   }
   case 'files':
   {
      $results = $db->searchFiles($query);
   }
}

$querytime = microtime(true) - $querytime;
if (count($results) > 0) {

   echo count($results) . ' results for "' . $cleanquery . '" in ' . $queryScope . ' (took ' . round($querytime, 5) . 'ms)<hr>';
   // output data of each row
   foreach ($results as $result) {
      echo listName($queryScope, $result);
      echo '<hr>';
   }
} else {
   echo 'No Results for ' . $cleanquery;
}
?>
