<?php 

class Session {
    public $username;

    protected const MAX_LOGIN_AGE = 60*60*24; // 1 day
    
    private $admin_id;
    private $last_login;





    public function __construct() {
        session_start();
        $this->verify_restore_session();
    }

    // public function __destruct() {
    //     session_destroy();
    // }

    public function login($admin) {
        if($admin) {
            //prevent session fixation attacks
            session_regenerate_id();
            $this->admin_id = $_SESSION['admin_id'] = $admin->id;
            $this->username = $_SESSION['username'] = $admin->username;
            $this->last_login = $_SESSION['last_login'] = time();
            return true;
        }

        return false;
    }

    public function is_logged_in() {
        return isset($this->admin_id) && $this->is_last_login_recent() ;
    }

    public function get_admin_id() {
        return ($this->is_logged_in() ? $this->admin_id : false);
    }


    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['username']);
        unset($_SESSION['last_login']);
        
        unset($this->username);
        unset($this->admin_id);
        unset($this->last_login);
    }

    protected function verify_restore_session() {
        if(isset($_SESSION['admin_id'])) {
            $this->admin_id = $_SESSION['admin_id'];
            $this->username = $_SESSION['username'];
            $this->last_login = $_SESSION['last_login'];
        }
    }

    private function is_last_login_recent() {
        if(!isset($this->last_login)){
            return false;
        } elseif ($this->last_login + self::MAX_LOGIN_AGE < time()) {
            return false;
        } else {
            return true;
        }
    }


}

?>