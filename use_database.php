<?php
declare(strict_types=1);
require('creds.php');

class UseDatabase
{
    protected $connection;

    public function __construct()
    {
        // Create connection
        $this->$connection = new mysqli(CONF["servername"], CONF["username"], CONF["password"], CONF["dbname"]);
        // Convert database ints and floats to php ints and floats
        $this->$connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
        if ($this->$connection->connect_error) {
            die('Connection failed: ' . $this->$connection->connect_error);
        }
    }

    /**
     * Select by ID a file's id, name, path, version, date and mirrors
     * 
     * @param id File id
     * @return array Resulting rows fetched as an associative array
     */
    public function selectFile($id)
    {
        $stmt = $this->$connection->prepare('SELECT id, file_name, file_path, version, date, mirrors FROM files WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Select by ID a system's id, manufacturer, model and file and os data 
     * 
     * @param id System id
     * @return array Resulting rows fetched as an associative array
     */
    public function selectSystem($id)
    {
        $stmt = $this->$connection->prepare('SELECT id, manufacturer, model, data FROM systems WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Select id, manufacturer, model and file and os data of all systems
     * 
     * @return array Resulting rows fetched as an associative array
     */
    public function selectSystems()
    {
        $result = $this->$connection->query('SELECT id, manufacturer, model, data FROM systems');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Select system's id, manufacturer, model and file and os data by searching for a given file in
     * its driver array, which part of the data json
     * 
     * @param fileID ID of the file to search for
     * @return array Resulting rows fetched as an associative array
     */
    public function selectSystemsByFile($fileID)
    {
        $stmt = $this->$connection->prepare('SELECT id, manufacturer, model, data FROM systems WHERE JSON_CONTAINS(data->"$.data[*].drivers", ?)');
        $stmt->bind_param('s', $fileID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Select by ID a device's id, manufacturer, name, model, category and files
     * 
     * @param id System id
     * @return array Resulting rows fetched as an associative array
     */
    public function selectDevice($id)
    {
        $stmt = $this->$connection->prepare('SELECT id, manufacturer, device_name, device_model, category, files FROM devices WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Select id, manufacturer, name, model, category and files of one or more devices by searching for
     * a given file in its files json array
     * 
     * @param fileID ID of the file to search for
     * @return array Resulting rows fetched as an associative array
     */
    public function selectDevicesByFile($fileID)
    {
        $stmt = $this->$connection->prepare('SELECT id, manufacturer, device_name, device_model, category, files FROM devices WHERE JSON_CONTAINS(files, ?)');
        $stmt->bind_param('s', $fileID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Select by ID a mirror's id, name, region, address, base url and whether it uses https or not
     * 
     * @param id Mirror id
     * @return array Resulting rows fetched as an associative array
     */
    public function selectMirror($id)
    {
        $stmt = $this->$connection->prepare('SELECT id, name, region, address, base_url, https FROM mirrors WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Select all stats from stats table
     * 
     * @return array Resulting rows fetched as an associative array
     */
    public function selectStats()
    {
        $result = $this->$connection->query('SELECT name, count FROM stats');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Search for a given model in the systems table
     * 
     * @param model Model search parameter
     * @return array Resulting rows fetched as an associative array
     */
    public function searchSystems($model)
    {
        $stmt = $this->$connection->prepare('SELECT id, manufacturer, model FROM systems WHERE model LIKE ?');
        $stmt->bind_param('s', $model);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Search for a given manufacturer or device name in the devices table
     * 
     * @param manufacturerOrName Manufacturer or device name search parameter
     * @return array Resulting rows fetched as an associative array
     */
    public function searchDevices($manufacturerOrName)
    {
        $stmt = $this->$connection->prepare('SELECT id, manufacturer, device_name FROM devices WHERE device_name LIKE ? OR manufacturer LIKE ?');
        $stmt->bind_param('ss', $manufacturerOrName, $manufacturerOrName);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Search for a given file name in the files table
     * 
     * @param fileName File name search parameter
     * @return array Resulting rows fetched as an associative array
     */
    public function searchFiles($fileName)
    {
        $stmt = $this->$connection->prepare("SELECT id, file_name, file_path, version, date FROM files WHERE file_name LIKE ?");
        $stmt->bind_param('s', $fileName);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function __destruct()
    {
        $this->$connection->close();
    }
}
?>