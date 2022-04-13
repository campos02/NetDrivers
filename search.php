<head>
	<title>Query Results</title>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="/res/style.css">
	<link rel="shortcut icon" type="image/png" href="/favicon.png"/>
</head>
<h1>NetDrivers Search</h1>
<br><a href="/">Home</a> | <a href="javascript:history.back()">Back</a>
<hr>
<form action="search.php" method="post">
		<input type="text" name="query">&nbsp;<input type="submit"><br>
		<input type="radio" name="scope" checked="true"
<?php if (isset($scope) && $scope=="systems") echo "checked";?>
value="systems">Systems<input type="radio" name="scope"
<?php if (isset($scope) && $scope=="devices") echo "checked";?>
value="devices">Devices<input type="radio" name="scope"
<?php if (isset($scope) && $scope=="drivers") echo "checked";?>
value="drivers">Filename
</form>
<br>
<?php

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
$queryScope = test_input($_POST["scope"]);
$query = '%'.$_POST["query"].'%';
if($query != "%%"){
	$cleanquery = str_replace("%","",$query);
	//echo "Results for \"" . $cleanquery . "\" in " . $queryScope;
}
?>
<?php
include 'creds.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}

if ($queryScope == "systems") {
	// Query by system model
	if ($query == "%%") {
		return;
	} else {
		$stmt = $conn->prepare("SELECT ID, Manufacturer, Model FROM systems WHERE Model LIKE ?");
		$stmt->bind_param(s,$query);
		$stmt->execute();
		$result = $stmt->get_result();
		echo $result->num_rows . " results for \"" . $cleanquery . "\" in " . $queryScope . "<hr>";
	}
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<h2><a href=\"/systems.php?id=" . $row["ID"] . "\">". $row["Manufacturer"] . " " . $row["Model"] . "</a></h2>";
			echo "<hr>";
		}
	} else {
		echo "No Results for " . $cleanquery;
	}
} else if ($queryScope == "devices") {
	// Query by device name/manufacturer
    if ($query == "%%") {
		return;
	} else {
		$stmt = $conn->prepare("SELECT ID, Manufacturer, Device_Name FROM devices WHERE Device_Name LIKE ? OR Manufacturer LIKE ?");
		$stmt->bind_param(ss,$query, $query);
		$stmt->execute();
		$result = $stmt->get_result();
		echo $result->num_rows . " results for \"" . $cleanquery . "\" in " . $queryScope . "<hr>";
	}
	
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			echo "<h2><a href=\"/devices.php?id=" . $row["ID"] . "\">". $row["Manufacturer"] . " " . $row["Device_Name"] . "</a></h2>";
			echo "<hr>";
		}
	} else {
		echo "No Results for \"" . $cleanquery . "\"";
	}
} else if ($queryScope == "drivers") {
	// Query by driver filename
	if ($query == "%%") {
		return;
	} else {
		$stmt = $conn->prepare("SELECT ID, File_Name, File_Path, Version FROM files WHERE File_Name LIKE ?");
		$stmt->bind_param(s,$query);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			echo $result->num_rows . " results for \"" . $cleanquery . "\" in files<hr>";
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$date = new DateTime($row["Date"]);
				echo "<p><b>Filename:</b> ". $row["File_Name"] . "<br><b>Version:</b> " . $row["Version"] . "<br><b>Date:</b> " . $date->format("d M Y") . "<br><a href=\"/download.php?id=" . $row["ID"] . "\"><button type=\"button\">Download</button></a></p>";
				echo "<hr>";
			}
		} else {
			echo "No Results for \"" . $cleanquery . "\" in files";
		}
	}
}
$conn->close();
?>
