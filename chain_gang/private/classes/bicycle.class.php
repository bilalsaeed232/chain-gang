<?php

class Bicycle {

  // --- START ACTIVE RECORD CODE -- //
  static protected $database;
  static protected $db_columns = ['id', 'brand', 'model', 'year', 'category', 'color', 'description', 'gender', 'price', 'weight_kg', 'condition_id'];

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
      $objects_array[] = self::instantiate($row);
    }

    // freeup the result variable;
    $result->free();

    return $objects_array;
  } 

  static public function find_all() {
    $sql = "SELECT * FROM bicycles";
    return self::find_by_sql($sql);
  }

  static public function find_by_id($id) {
    $sql = "SELECT * FROM bicycles ";
    $sql .= "WHERE id='" . self::$database->escape_string($id) . "'";

    $obj_array = self::find_by_sql($sql);

    
    
    if(empty($obj_array)) {
      return false;
    } else {
      // return only single object as expected
      return array_shift($obj_array);
    }
  }

  static protected function instantiate($record) {
    $object = new self;

    //get every key and value in db record and populate Bicycle object
    foreach($record as $property => $value) {
      $object->$property = $value;
    }
    return $object;
  }

  public function create() {
    // sql for inserting this object into db record
    $sql = "INSERT INTO `bicycles` (";
    $sql .= implode(",", array_keys($this->attributes()));
    $sql .= ") VALUES (";
    $sql .= "'" . implode("', '", array_values($this->attributes())) . "'";
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


  protected function attributes() {
      $attributes = [];
      foreach (self::$db_columns as $column) {
        if ($column == 'id') { continue; }
        $attributes[$column] = $this->$column;
      }

      if(!empty($attributes)) {
        return $attributes;
      } else {
        return false;
      }
  }

  // --- END ACTIVE RECORD CODE -- //

  public $id;
  public $brand;
  public $model;
  public $year;
  public $category;
  public $color;
  public $description;
  public $gender;
  public $price;
  protected $weight_kg;
  protected $condition_id;

  public const CATEGORIES = ['Road', 'Mountain', 'Hybrid', 'Cruiser', 'City', 'BMX'];

  public const GENDERS = ['Mens', 'Womens', 'Unisex'];

  public const CONDITION_OPTIONS = [
    1 => 'Beat up',
    2 => 'Decent',
    3 => 'Good',
    4 => 'Great',
    5 => 'Like New'
  ];

  public function __construct($args=[]) {
    //$this->brand = isset($args['brand']) ? $args['brand'] : '';
    $this->brand = $args['brand'] ?? '';
    $this->model = $args['model'] ?? '';
    $this->year = $args['year'] ?? '';
    $this->category = $args['category'] ?? '';
    $this->color = $args['color'] ?? '';
    $this->description = $args['description'] ?? '';
    $this->gender = $args['gender'] ?? '';
    $this->price = $args['price'] ?? 0;
    $this->weight_kg = $args['weight_kg'] ?? 0.0;
    $this->condition_id = $args['condition_id'] ?? 3;

    // Caution: allows private/protected properties to be set
    // foreach($args as $k => $v) {
    //   if(property_exists($this, $k)) {
    //     $this->$k = $v;
    //   }
    // }
  }

  public function get_name() {
    return "(" . $this->brand . " " . $this->year . " " . $this->model . ")";
  }

  public function weight_kg() {
    return number_format($this->weight_kg, 2) . ' kg';
  }

  public function set_weight_kg($value) {
    $this->weight_kg = floatval($value);
  }

  public function weight_lbs() {
    $weight_lbs = floatval($this->weight_kg) * 2.2046226218;
    return number_format($weight_lbs, 2) . ' lbs';
  }

  public function set_weight_lbs($value) {
    $this->weight_kg = floatval($value) / 2.2046226218;
  }

  public function condition() {
    if($this->condition_id > 0) {
      return self::CONDITION_OPTIONS[$this->condition_id];
    } else {
      return "Unknown";
    }
  }

}

?>
