<?php
session_start();
class Database
{
    private $host;
    private $dbusername;
    private $dbpassword;
    private $dbname;
    private $con;
    private $table;
    private $selectColumns = ['*'];
    private $insertData = [];
    private $updateData = [];
    private $joinClauses = [];
    private $orderByField = '';
    private $orderByType = '';
    private $whereConditions = [];

    public function __construct()
    {
        $this->host = 'localhost';
        $this->dbusername = 'root';
        $this->dbpassword = '';
        $this->dbname = 'skindisease';

        $this->con = new mysqli($this->host, $this->dbusername, $this->dbpassword, $this->dbname);

        if ($this->con->connect_error) {
            die("Connection failed: " . $this->con->connect_error);
        }

        $this->con->set_charset("utf8");
    }

    private function checkTable()
    {
        if (empty($this->table)) {
            throw new Exception("Table not set. Use the table method before performing any operations.");
        }
    }

    public function table($tableName)
    {
        $this->table = $tableName;
        $this->resetQuery();
        return $this;
    }

    public function select(...$columns)
    {
        $this->checkTable();

        if (empty($columns)) {
            throw new Exception("At least one column should be specified.");
        }

        $this->selectColumns = $columns;
        return $this;
    }

    public function insert($data)
    {
        $this->checkTable();
        $this->insertData = $data;
        $fields = implode(',', array_keys($data));
        $values = "'" . implode("','", array_map([$this, 'get_safe_str'], array_values($data))) . "'";
        $sql = "INSERT INTO $this->table ($fields) VALUES ($values)";
        return $this->executeQuery($sql) !== false;
    }

    public function insertGetId($data)
    {
        $this->checkTable();
        $this->insertData = $data;
        $fields = implode(',', array_keys($data));
        $values = "'" . implode("','", array_map([$this, 'get_safe_str'], array_values($data))) . "'";
        $sql = "INSERT INTO $this->table ($fields) VALUES ($values)";
        $result = $this->executeQuery($sql);
        return $result ? $this->con->insert_id : null;
    }

    public function update($data = [])
    {
        $this->checkTable();
        $this->updateData = array_merge($this->updateData, $data);

        $sql = "UPDATE $this->table SET ";
        $setClause = $this->buildSetClause($this->updateData);
        $sql .= $setClause;

        if (!empty($this->whereConditions)) {
            $sql .= " WHERE " . $this->buildCondition($this->whereConditions);
        }

        return $this->executeQuery($sql) !== false;
    }

    public function join($table, $firstColumn, $operator, $secondColumn)
    {
        $this->checkTable();
        $this->joinClauses[] = "JOIN $table ON $firstColumn $operator $secondColumn";
        return $this;
    }

    public function orderBy($field, $type = 'asc')
    {
        $this->checkTable();
        $this->orderByField = $field;
        $this->orderByType = strtoupper($type) === 'DESC' ? 'DESC' : 'ASC';
        return $this;
    }
    public function where($column, $operator = '=', $value = null, $type = 'AND')
    {
        $this->checkTable();

        if (is_array($column)) {
            foreach ($column as $col => $val) {
                $this->whereConditions[] = [
                    'column' => $col,
                    'operator' => '=',
                    'value' => $val,
                    'type' => 'AND',
                ];
            }
        } elseif (func_num_args() == 2 || func_num_args() == 3) {
            $this->whereConditions[] = [
                'column' => $column,
                'operator' => $operator,  // Use the provided operator
                'value' => $value,
                'type' => 'AND',
            ];
        } elseif (func_num_args() == 4) {
            $this->whereConditions[] = [
                'column' => $column,
                'operator' => $operator,
                'value' => $value,
                'type' => strtoupper($type),
            ];
        } else {
            throw new Exception("Invalid number of arguments for where() method.");
        }

        return $this;
    }


    private function resetQuery()
    {
        $this->selectColumns = ['*'];
        $this->insertData = [];
        $this->updateData = [];
        $this->joinClauses = [];
        $this->orderByField = '';
        $this->orderByType = '';
        $this->whereConditions = [];
        return $this;
    }

    public function delete()
    {
        $this->checkTable();
        $sql = "DELETE FROM $this->table";
        if (!empty($this->whereConditions)) {
            $sql .= " WHERE " . $this->buildCondition($this->whereConditions);
        }
        return $this->executeQuery($sql) !== false;
    }

    public function first()
    {
        $result = $this->getOne();
        return $result ? (object) $result : null;
    }

    public function get()
    {
        $this->checkTable();
        $sql = $this->buildSelectQuery();

        if (!empty($this->joinClauses)) {
            $sql .= ' ' . implode(' ', $this->joinClauses);
        }

        if (!empty($this->whereConditions)) {
            $sql .= " WHERE " . $this->buildCondition($this->whereConditions);
        }

        if (!empty($this->orderByField)) {
            $sql .= " ORDER BY $this->orderByField $this->orderByType";
        }

        $result = $this->executeQuery($sql);
        $objects = [];
        while ($row = $result->fetch_object()) {
            $objects[] = $row;
        }

        return $objects;
    }


    private function buildSelectQuery()
    {
        $columns = implode(', ', $this->selectColumns);
        return "SELECT $columns FROM $this->table";
    }

    private function getOne()
    {
        $this->checkTable();
        $sql = $this->buildSelectQuery();

        if (!empty($this->joinClauses)) {
            $sql .= ' ' . implode(' ', $this->joinClauses);
        }

        if (!empty($this->whereConditions)) {
            $sql .= " WHERE " . $this->buildCondition($this->whereConditions);
        }

        if (!empty($this->orderByField)) {
            $sql .= " ORDER BY $this->orderByField $this->orderByType";
        }

        $sql .= " LIMIT 1";

        $result = $this->executeQuery($sql);
        return $result ? $result->fetch_object() : null;
    }


    private function executeQuery($sql)
    {
        $result = $this->con->query($sql);

        if (!$result) {
            throw new Exception("Query execution failed: " . $this->con->error);
        }

        return $result;
    }


    private function buildSetClause($data)
    {
        $setValues = array_map(
            function ($key, $value) {
                return "$key='" . $this->get_safe_str($value) . "'";
            },
            array_keys($data),
            $data
        );
        return implode(', ', $setValues);
    }

    private function buildCondition($condition_arr)
    {
        $conditions = [];

        foreach ($condition_arr as $condition) {
            $conditions[] = "{$condition['column']} {$condition['operator']} '" . $this->get_safe_str($condition['value']) . "'";
        }

        return implode(' AND ', $conditions);
    }


    private function get_safe_str($str)
    {
        return $this->con->real_escape_string($str);
    }
}
