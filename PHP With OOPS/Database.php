<?php

/**
 * Database
 *
 * This file has class to interact with MySQL server
 *
 */
class Database 
{

    private static $theOnlyConnection = null;

    private static $modelObjectCount = 0;

    /**
     *
     * holds the PDO object
     *
     * @var object
     */
    private $db;

    /**
     *
     * stores the statement generated from prepare query
     *
     * @var object
     */
    private $stmt;

    /**
     * establishes connection with mysql
     */
    function __construct()
    {
             
        $root = dirname(__FILE__);
        $this->config = parse_ini_file($root . '/config.ini', true);
        
        if (null === self::$theOnlyConnection) {
            $db = $this->config['database settings'];
            $host = $db['host'];
            $user = $db['user'];
            $pword = $db['pword'];
            $db_name = $db['db'];

            self::$theOnlyConnection = new PDO("mysql:host=$host;dbname=$db_name", $user, $pword, array(
                PDO::MYSQL_ATTR_FOUND_ROWS => true,
                PDO::ATTR_PERSISTENT => true
            ));
            self::$theOnlyConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$theOnlyConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }

        $this->db = self::$theOnlyConnection;
    }

    /**
     *
     * exectues query
     *
     * @param string $query
     * @param array $params
     */
    function executeQuery($query, $params = array())
    {
        $this->stmt = $this->db->prepare($query);

        if (! ($this->stmt)) {
            throw new Exception('Query failed while preparing');
        }
        try {
            $this->stmt->execute($params);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     *
     * Gets single column from single record, it internally uses executeQuery
     *
     * @param string $query
     * @param array $params
     */
    function getOne($query, $params = array())
    {
        $this->executeQuery($query, $params);
        $column = $this->stmt->fetchColumn();
        unset($this->stmt);
        return $column;
    }

    /**
     *
     * fetches single record as an object
     *
     * @param string $query
     * @param array $params
     * @return object
     */
    function getRecord($query, $params = array(), $array = false)
    {
        $this->executeQuery($query, $params);

        $record = array();
        if ($this->totalRecords() > 0) {
            if ($array) {
                $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
            } else {
                $this->stmt->setFetchMode(PDO::FETCH_OBJ);
            }
            $record = $this->stmt->fetch();
        }
        unset($this->stmt);
        return $record;
    }

    /**
     *
     * fetches all the records as array of objects
     *
     * @param string $query
     * @param array $params
     * @return array
     */
    function getRecords($query, $params = array(), $array = false, $all = false)
    {
        $this->executeQuery($query, $params);

        $records = array();
        if ($this->totalRecords() > 0) {
            if ($array) {
                $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
            } else {
                $this->stmt->setFetchMode(PDO::FETCH_OBJ);
            }
            if ($all) {
                $records = $this->stmt->fetchAll();
            } else {
                while (($record = $this->stmt->fetch()) !== false) {
                    $records[] = $record;
                }
            }
        }
        unset($this->stmt);
        return $records;
    }

    /**
     *
     * inserts record
     *
     * @param string $table
     * @param array $params
     */
    function insertRecord($table, $params)
    {
        $query = '';
        $fields = $place_holders = array();
        $values = array_values($params);
        foreach ($params as $field => $val) {
            array_push($fields, $field);
            array_push($place_holders, '?');
        }

        $query = "INSERT INTO $table(" . implode(", ", $fields) . ") VALUES(" . implode(", ", $place_holders) . ")";
        $this->executeQuery($query, $values);

        $this->ID = $this->db->lastInsertId();
    
    }

    /**
     *
     * updates record
     *
     * @param string $table
     * @param array $params
     */
    function updateRecord($table, $params, $where, $multiple = array(), $comment = '')
    {
        $query = '';
        $fields = array();
        $values = array_values($params);
        foreach ($params as $field => $val) {
            array_push($fields, $field . ' = ?');
        }

        $where_clause = array();
        $where_values = array();
        foreach ($where as $col => $val) {
            if (is_array($val)) {
                if (isset($val['op'])) {
                    array_push($where_clause, $col . ' ' . $val['op'] . ' ? ');
                }
                array_push($values, $val['value']);
                array_push($where_values, $val['value']);
            } else {
                array_push($where_clause, $col . ' = ?');
                array_push($values, $val);
                array_push($where_values, $val);
            }
        }

        if (count($multiple)) {
            foreach ($multiple as $column => $mvalues) {
                if (count($mvalues)) {
                    $placeholders = implode(", ", array_fill(0, count($mvalues), '?'));
                    array_push($where_clause, $column . ' IN (' . $placeholders . ')');
                    $values = array_merge($values, $mvalues);
                }
            }
        }
        
        $query = "UPDATE $table SET " . implode(", ", $fields) . " WHERE " . implode(" AND ", $where_clause);
        $this->executeQuery($query, $values);
    }

    /**
     *
     * deletes record
     *
     * @param string $table
     * @param array $params
     */
    function deleteRecord($table, $params, $comment = '')
    {
        $where_clause = array();
        $values = array_values($params);
        foreach ($params as $col => $val) {
            array_push($where_clause, $col . ' = ?');
        }

        $query = "DELETE FROM $table  WHERE " . implode(" AND ", $where_clause);
        $this->executeQuery($query, $values);
    }

    /**
     *
     * sends the automatically generated id for the recent insert query
     *
     * @return integer
     */
    function getRecordID()
    {
        // return $this->db->lastInsertId();
        return $this->ID;
    }

    /**
     *
     * count the no. of records in the resultset for recently executed select query
     *
     * @return integer
     */
    function totalRecords()
    {
        return $this->stmt->rowCount();
    }

    /**
     *
     * returns the unparsed query
     *
     * @return string
     */
    function showQuery()
    {
        return $this->stmt->queryString;
    }

    /**
     * used to force close the db connection
     */
    function dbClose()
    {
        $this->db = null;
    }

    /**
     * start transaction
     */
    function start()
    {
        $this->db->beginTransaction();
    }

    /**
     * commit
     */
    function save()
    {
        $this->db->commit();
    }

    /**
     * rollbact
     */
    function undo()
    {
        $this->db->rollBack();
    }

    private function getPrimaryKey($table)
    {
        $record = $this->getRecord("SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
        return $record->Column_name;
    }

    public function __destruct()
    {
        self::$modelObjectCount --;
        if (0 == self::$modelObjectCount) {
            $this->dbClose();
            self::$theOnlyConnection = null;
        }
    }
}