<?php 
require_once('../../private/initialize.php'); 

$errors = [];

if(is_post_request()) {
    $args = $_POST['login'];
    $admin = Admin::find_by_username($args['username']);
    
    if($admin != false && $admin->verify_password($args['password'])) { 
        //verified
        if($session->login($admin)) {
            //successfully logged in...
            redirect_to(url_for('/staff/index.php'));
        } else {
            //unable to log in...
            $errors[] = "Unable to log in, contact system admin.";
        }
    }else {
        $errors[] = "Invalid username or password.";
    }
    // print_r($errors); exit;
}

?>


<?php $page_title='Staff Login' ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="content">
    <h1>Staff Login</h1>
    <?php echo display_errors($errors); ?>
    <form action="<?php echo url_for('/staff/login.php') ?>" method="post">
        <dl>
            <dt>Username</dt>
            <dd><input type="text" name="login[username]" id="username"></dd>
        </dl>
        <dl>
            <dt>Password</dt>
            <dd><input type="password" name="login[password]" id="password"></dd>
        </dl>

        <div id="operations">
            <input type="submit" value="Login">
        </div>
    </form>

</div>



<?php include(SHARED_PATH . '/staff_footer.php') ?>