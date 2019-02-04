<?php

class Admin extends DatabaseObject {
   static protected $table_name = "admins";
   static protected $db_columns = ['id', 'first_name', 'last_name', 'email', 'username', 'hashed_password'];
   public $errors = [];

   public $id;
   public $first_name;
   public $last_name;
   public $email;
   public $username;
   protected $hashed_password;
   public $password;
   public $confirm_password;

   public $check_password = true;


   static public function find_by_username($username) {
      $sql = "SELECT * FROM " .static::$table_name . " ";
      $sql .= "WHERE username='" . parent::$database->escape_string($username) . "' ";
      $sql .= "LIMIT 1";

      $obj_array = parent::find_by_sql($sql);

 

      if(empty($obj_array)) {
         return false;
      } 

      return array_shift($obj_array);
   }

   public function __construct($args=[]) {
      $this->id = $args['id'] ?? NULL;
      $this->first_name = $args['first_name'] ?? NULL;
      $this->last_name = $args['last_name'] ?? NULL;
      $this->email = $args['email'] ?? NULL;
      $this->username = $args['username'] ?? NULL;
      $this->password = $args['password'] ?? NULL;
      $this->confirm_password = $args['confirm_password'] ?? NULL;
   }

   public function get_name() {
      return $this->first_name . " " . $this->last_name;
   }

   public function verify_password($password) {
      return password_verify($password, $this->hashed_password); 
   }

   protected function create() {
      $this->hash_password();
      if($this->hashed_password){
         return parent::create();
      }

      return false;
   }

   protected function update() {
      //NOTE: when editing we don't want to check password unless user tries to give one.
      //becuase user might be interested in changing other fields only
      if(is_blank($this->password)) 
      { 
         $this->check_password = false; 
      } else {
         $this->check_password = true;
      }

      $this->hash_password();
      return parent::update();
   }

   protected function hash_password() {
      if(!is_null($this->password)) {
         $this->hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
      }
   }

   

   protected function validate() {
      $this->errors = [];

      if(is_blank($this->first_name)) {
         $this->errors[] = "First name is required.";
      }

      if(is_blank($this->last_name)) {
         $this->errors[] = "Last name is required.";
      }
      
      //EMAIL VALIDATIONS
      if(is_blank($this->email)) {
         $this->errors[] = "Email is required.";
      }
      if (!has_valid_email_format($this->email)) {
         $this->errors[] = "Email format is invalid.";
      }


      //USERNAME VALIDATIONS
      if(is_blank($this->username)) {
         $this->errors[] = "Username is required.";
      } elseif(!has_unique_username($this->username, $this->id ?? 0)) {
         $this->errors[] = "Username is already taken, provide a different one.";
      }



      //PASSWORD VALIDATIONS
      if($this->check_password) { //check to see is user is only editing other fields (not password)
         if(is_blank($this->password)) {
            $this->errors[] = "Password is required.";
         } elseif (!has_length_greater_than($this->password, 7)) {
            $this->errors[] = "Password must be atleast 8 characters long.";
         } elseif (is_blank($this->confirm_password)) {
            $this->errors[] = "Confirm password is required.";
         } elseif ($this->password != $this->confirm_password) {
            $this->errors[] = "Password and confirm password must match.";
         }
      }

      return $this->errors;
    }



}

?>