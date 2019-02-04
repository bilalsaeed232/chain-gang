<?php 

class Session {

    private $admin_id;



    public function __construct() {
        session_start();
        $this->verify_restore_session();
    }

    // public function __destruct() {
    //     session_destroy();
    // }

    public function login($admin) {
        if($admin) {
            $_SESSION['admin_id'] = $admin->id;
            $this->admin_id = $admin->id;
            return true;
        }

        return false;
    }

    public function is_logged_in() {
        return isset($this->admin_id);
    }



    public function logout() {
        unset($_SESSION['admin_id']);
        unset($this->admin_id);
    }

    protected function verify_restore_session() {
        if(isset($_SESSION['admin_id'])) {
            $this->admin_id = $_SESSION['admin_id'];
        }
    }


}

?>