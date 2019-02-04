<?php

class DatabaseObject {
    static protected $database;
    static protected $table_name = "";
    static protected $db_columns = [];
    public $errors = [];

    static public function set_database($database) {
        self::$database = $database;
    }


    // get object array of this class from records fetched
    static private function find_by_sql($sql) {
        $result = self::$database->query($sql);
        if(!$result) {
        exit("Database query failed.");
        }

        $objects_array = [];

        while ($row = $result->fetch_assoc()) {
        $objects_array[] = static::instantiate($row);
        }

        // freeup the result variable;
        $result->free();

        return $objects_array;
    } 

    static public function find_all() {
        $sql = "SELECT * FROM ". static::$table_name ." ";
        return static::find_by_sql($sql);
    }

    static public function find_by_id($id) {
        $sql = "SELECT * FROM ". static::$table_name ." ";
        $sql .= "WHERE id='" . self::$database->escape_string($id) . "'";

        $obj_array = static::find_by_sql($sql);

        
        
        if(empty($obj_array)) {
        return false;
        } else {
        // return only single object as expected
        return array_shift($obj_array);
        }
    }




    static protected function instantiate($record) {
        $object = new static;

        //get every key and value in db record and populate Bicycle object
        foreach($record as $property => $value) {
        $object->$property = $value;
        }
        return $object;
    }



    protected function validate() {
        $this->errors = [];

        return $this->errors;
    }

    public function save() {
        
        //because new bicycle donot have id so we can differentiate
        if(!isset($this->id)) {
        return $this->create();
        } else {
        return $this->update();
        }
    }
    
    protected function create() {
        //check if validation passes
        $this->validate();
        if(!empty($this->errors)) { return false; }


        $sanitized_attributes = $this->sanitize_attributes();
        // sql for inserting this object into db record
        $sql = "INSERT INTO ". static::$table_name ." (";
        $sql .= implode(",", array_keys($sanitized_attributes));
        $sql .= ") VALUES (";
        $sql .= "'" . implode("', '", array_values($sanitized_attributes)) . "'";
        $sql .=")";


    //  echo $sql; exit;

        $result = self::$database->query($sql);
        //echo self::$database->error; exit;
        if($result) {
        //so that our object is not inconsistent with db record
        // as db id is generated automatically
        $this->id = self::$database->insert_id; //gets generated db id
        }

        return $result;
    }

    protected function update() {
        //check if validation passes
        $this->validate();
        if(!empty($this->errors)) { return false; }

        $sanitized_attributes = $this->sanitize_attributes();
        $attribute_pairs = [];

        foreach ($sanitized_attributes as $key => $value) {
        $attribute_pairs[] = "{$key}='". self::$database->escape_string($value) ."'";
        }

        $sql = "UPDATE ". static::$table_name ." SET ";
        $sql .= implode(', ', $attribute_pairs) . " ";
        $sql .= "WHERE id='". self::$database->escape_string($this->id) . "' ";
        $sql .= "LIMIT 1";


        //echo $sql; exit;

        $result = self::$database->query($sql);
        return $result;
    }

    public function delete() {
        $sql = "DELETE FROM ". static::$table_name ." ";
        $sql .= "WHERE id='". self::$database->escape_string($this->id) ."' ";
        $sql .= "LIMIT 1";

        $result = self::$database->query($sql);
        return $result;
    }

    protected function attributes() {
        $attributes = [];
        foreach (static::$db_columns as $column) {
            if ($column == 'id') { continue; }
            $attributes[$column] = $this->$column;
        }

        if(!empty($attributes)) {
            return $attributes;
        }
        
        return false;
    }

    public function merge_attributes($args=[]) {
        //merge the attributes with actual object, to insure consistency
        foreach ($args as $key => $value) {
        if (property_exists($this, $key) && !is_null($value)) {
            //if this property exists on this object
            $this->$key = $value;
        }
        }
    }

    protected function sanitize_attributes() {
        $sanitized_attributes = [];

        foreach($this->attributes() as $column => $value) {
        $sanitized_attributes[$column] = self::$database->escape_string($value);
        }

        if(!empty($sanitized_attributes)) {
        return $sanitized_attributes;
        }

        return false;
    }

}

?>