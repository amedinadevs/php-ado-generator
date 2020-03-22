<?php

Class DatabaseMySQL
{
	function __construct()
	{
		$databaseName = 'foronum';
		$serverName = 'localhost';
		$databaseUser = 'root';
        $databasePassword = '';
        
		if (!isset($this->connection))
		{
            $this->connection = new mysqli($serverName,$databaseUser,$databasePassword ,$databaseName );            
        }

        if ($this->connection->connect_errno) {
            //echo $this->connection->connect_error;
            //throw new Exception( "Fallo al conectar a MySQL: (".$this->connection->connect_errno."): ");
            die("Connection failed: " . $this->connection->connect_error);
        }

	}

	public static function Connect()
	{
		static $database = null;
		if (!isset($database))
		{
			$database = new DatabaseMySQL();
		}
		return $database->connection;
    }

	public static function Query($query, $connection)
	{
		try
		{       
			$result = $connection->Query($query);
		}
		catch(Exception $e)
		{
			return false;
		}
		return $result;
	}

	public static function ReadAll($result)
	{
		try
		{
			return $result->fetch_all();
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public static function ReadObject($result, $class="stdClass")
	{
		try
		{
			return $result->fetch_object($class);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public static function ReadArray($result)
	{
		try
		{
			return $result->fetch_array();
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public static function NonQuery($query, $connection)
	{
		try
		{
			$r = $connection->Query($query);
			if ($r === false)
			{
				return -1;
			}
			return $r->num_rows;
		}
		catch (Exception $e)
		{
			return false;
		}

	}

	public static function InsertOrUpdate($query, $connection)
	{
        echo $query;
		try
		{
            $r = $connection->Query($query);
            if ($r === false)
			{
				return -1;
			}
            return $connection->insert_id;
		}
		catch (Exception $e)
		{
			return false;
		}
    }


    public static function NowDateTime(){
        return date('Y-m-d H:i:s');
    }

    public static function NowDate(){
        return date('Y-m-d');
    }

}

?>