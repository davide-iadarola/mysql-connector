<?php
/**
 * This connector provide various method to connect and query a mysql database
 * @author Davide Iadarola (@davide_iadarola)
 */

class mySQLConnector {

    private $error;
    private $selected_data;
    private $last_insert_id;
    private $connection;

    public function __construct() {}

    public function connect($host, $db, $user, $pass)
    {
        try 
        {
            $this->connection = new PDO("mysql:host=$host;dbname=".$db, $user, $pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
        }
        catch(PDOException $e)
        {
            $this->error = 'method connect('.$host.', '.$db.', '.$user.', '.$pass.') failed:' ."\n" .'> ' . $e->getMessage();
            return false;
        }
    }

    public function select($select_clause, $from_clause, $where_clause = '', $groupby_clause = '', $orderby_clause = '', $limit_clause = '')
    {
        $where_clause = !empty($where_clause) ? ' WHERE '.$where_clause : '';
		$groupby_clause = !empty($groupby_clause) ? ' GROUP BY '.$groupby_clause : '';
		$orderby_clause = !empty($orderby_clause) ? ' ORDER BY '.$orderby_clause : '';
		$limit_clause = !empty($limit_clause) ? ' LIMIT '.$limit_clause : '';
		
		$request = 'SELECT '.$select_clause.' FROM '.$from_clause.$where_clause.$groupby_clause.$orderby_clause.$limit_clause;
        
        try 
        {
            $sql = $this->connection->prepare($request); 
            $sql->execute();
            if(!$sql->setFetchMode(PDO::FETCH_ASSOC)) {return false;}
            $this->selected_data = $sql->fetchAll();
            $this->error = '';
            return true;
        }
        catch(PDOException $e) 
        {
            $this->error = 'method select('.$select_clause.', '.$from_clause.', '.$where_clause.', '.$groupby_clause.', '.$orderby_clause.', '.$limit_clause.') failed:' ."\n" .'> ' . $e->getMessage() ."\n" .'>> ' .$request;
            return false;
        }
    }

    public function insert($table, $row) 
    {
		$request_fields = '';
		$request_values = '';

		foreach($row as $field => $value)
		{
			$request_fields .= '`'.$field.'`,';
            if(is_numeric($value) || strpos($value, '()') !== false) {
                $request_values .= $value.',';
            } else {
                $request_values .= '\''.$value.'\',';
            }
		}

		$request_fields = substr($request_fields, 0, strlen($request_fields)-1);
		$request_values = substr($request_values, 0, strlen($request_values)-1);
		$request = 'INSERT INTO `'.$table.'` ('.$request_fields.') VALUES ('.$request_values.')';
		
        try 
        {
            $this->connection->exec($request);
            $this->last_insert_id = $this->connection->lastInsertId();
            $this->error = '';
            return true;
        }
        catch(PDOException $e)
        {
            $this->error = 'method insert('.$table.', '.str_replace("\n", "", print_r($row, true)).') failed:' ."\n" .'> ' . $e->getMessage() ."\n" .'>> ' .$request;
            return false;
        }
    }
    
    public function update($table, $where_clause, $row) 
    {	 
		$request_fields_values = '';
		
		foreach($row as $field => $value)
		{
            if(is_numeric($value) || strpos($value, '()') !== false) {
                $request_fields_values .= '`'.$field.'` = '.$value.', ';
            } else {
                $request_fields_values .= '`'.$field.'` = \''.$value.'\', ';
            }
		}
		
		$request_fields_values = substr($request_fields_values, 0, strlen($request_fields_values)-2);
		$request = 'UPDATE `'.$table.'` SET '.$request_fields_values.' WHERE '.$where_clause;
		
        try 
        {
            $sql = $this->connection->prepare($request);
            $sql->execute();
            $this->error = '';
            return true;
        }
        catch(PDOException $e)
        {
            $this->error = 'method update('.$table.', '.$where_clause.', '.str_replace("\n", "", print_r($row, true)).') failed:' ."\n" .'> ' . $e->getMessage() ."\n" .'>> ' .$request;
            return false;
        }
    }
    
    public function delete($table, $where_clause) 
    {
		$request = 'DELETE FROM `'.$table.'` WHERE '.$where_clause;

        try 
        {
            $this->connection->exec($request);
            $this->error = '';
            return true;
        }
        catch(PDOException $e)
        {
            $this->error = 'method delete('.$table.', '.$where_clause.') failed:' ."\n" .'> ' . $e->getMessage() ."\n" .'>> ' .$request;
            return false;
        }
    }
    
    public function query($request) 
    {
		try 
        {
            $this->connection->exec($request);
            $this->error = '';
            return true;
        }
        catch(PDOException $e)
        {
            $this->error = 'method query('.$request.') failed:' ."\n" .'> ' . $e->getMessage() ."\n" .'>> ' .$request;
            return false;
        }
    }
    
    public function start()
    {
        try 
        {
            $this->connection->beginTransaction();
            $this->error = '';
            return true;
        }
        catch(PDOException $e)
        {
            $this->error = 'method start() failed:' ."\n" .'> ' . $e->getMessage();
            return false;
        }
    }

    public function commit()
    {
        try 
        {
            $this->connection->commit();
            $this->error = '';
            return true;
        }
        catch(PDOException $e)
        {
            $this->error = 'method start() failed:' ."\n" .'> ' . $e->getMessage();
            return false;
        }
    }

    public function rollback()
    {
        try 
        {
            $this->connection->rollBack();
            $this->error = '';
            return true;
        }
        catch(PDOException $e)
        {
            $this->error = 'method start() failed:' ."\n" .'> ' . $e->getMessage();
            return false;
        }
    }

    public function getError() 
    {
        return $this->error;
    }

    public function getSelectedData() 
    {
        return $this->selected_data;
    }

    public function getLastInsertID() 
    {
        return $this->last_insert_id;
    }
}
