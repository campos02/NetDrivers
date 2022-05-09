<?php
declare(strict_types=1);
?>
<html>
<head>
   <?php $title = 'Select Mirror';
   require('head.php'); ?>
</head>
<body>

<?php

if (isset($_GET['id'])) {
   require('use_database.php');
   $db = new UseDatabase();
   $files = $db->selectFile($_GET['id']);

   if (count($files) > 0) {
      //echo "<table border=\"1\"><td><b>Linkback: </b><a href=\"http://drivers.nickandfloppy.com/link.php?type=driver&id=" . $_GET['id'] . "\">http://drivers.nickandfloppy.com/link.php?type=driver&id=" . $_GET['id'] . "</td></table>";
   
      // output data of each row
      foreach ($files as $file) {
         $fileMirrors = json_decode($file['mirrors'], true, 512, JSON_THROW_ON_ERROR);
         // Commented out as it doesn't get used anywhere
         //$fileurl = $file['File_URL'] === null || $file['File_URL'] === '' ? 'N/A' : $file['File_URL'];

         if (count($fileMirrors) < 1) {
            echo 'No mirrors available';
            continue;
         }
         echo '<h1>Downloading ' . $file['file_name'] .'</h1>';
         //echo '<b>Version:</b> ' . $file['version'] . '<br>';
         //echo '<b>Date:</b> ' . $file['date'] . '<br>';
         
         echo '<b>Select from the following mirrors...</b><br>';
         foreach($fileMirrors as $fileMirror) {
            $mirrors = $db->selectMirror($fileMirror);
            if (count($mirrors) > 0) {
               foreach($mirrors as $mirror) {
                  $url = $mirror['address'] . $mirror['base_url'] . $file['file_path'] . $file['file_name'];
                  echo '<a href="' . ($mirror['https'] === true ? 'https' : 'http') . '://' . $url . '">Mirror '
                     . $mirror['id'] . ' (' . $mirror['region'] . ')</a><br>';
               }
            }
         }
      }
      echo '<br> ID: ' . urlencode($_GET['id']);
   } else {
      echo 'Invalid ID provided';
   }
} else {
   echo 'No ID provided';
}

?>
</body>
</html>
