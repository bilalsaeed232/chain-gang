<?php 

class Session {

    private $admin_id;
    public $username;



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
            
            return true;
        }

        return false;
    }

    public function is_logged_in() {
        return isset($this->admin_id);
    }

    public function get_admin_id() {
        return ($this->is_logged_in() ? $this->admin_id : false);
    }


    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['username']);
        unset($this->username);
        unset($this->admin_id);
        
    }

    protected function verify_restore_session() {
        if(isset($_SESSION['admin_id'])) {
            $this->admin_id = $_SESSION['admin_id'];
            $this->username = $_SESSION['username'];
        }
    }


}

?>