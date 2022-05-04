<?php
declare(strict_types=1);
require('creds.php');
class UseDatabase
{
    protected $connection;

    public function __construct()
    {
        $this->$connection = new mysqli(CONF["servername"], CONF["username"], CONF["password"], CONF["dbname"]);
    }

    public function __destruct()
    {
        $this->$connection->close();
    }
}
?>