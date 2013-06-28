<?php 
    require_once 'lib/includes/classes/class.Validator.inc.php'; 
    require_once("lib/includes/classes/class.RelationalAlgorithm.inc.php");
    require_once("lib/includes/classes/class.User.inc.php");
    require_once("lib/includes/classes/class.Session.inc.php");

    Database::init_connection();
    $user = new User();
    Session::start();

    $rules_array = array(
        'email'=>array('display'=>'Email', 'type'=>'email',  'required'=>true, 'min'=>5, 'max'=>50, 'trim'=>true),
        'password'=>array('display'=>'Password', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true)
    );

    if(isset($_POST['email'])) {

        $validator = new Validation();
        $validator->addSource($_POST);
        $validator->addRules($rules_array);
        $validator->run();

        if(sizeof($validator->errors) > 0) {
            var_dump($validator->errors);
        } 
        //if validation of the sign in form passes do this stuff...
        else {
            $post_array = Database::clean($_POST);
            $success = $user->sign_in($post_array['email'], $post_array['password']);
            if($success){
                //var_dump($success);
                if($success === "EMAIL_NOT_CONFIRMED"){
                 echo "the email is not confirmed"; //email not confirmed code goes here
                }
                else{ 
                    header('Location:' . Database::$root_dir_link . "index.php");
                    echo "I just sent the header to " . str_replace("http://", "", Database::$root_dir_link) . "index.php";
                }
            }
            // else echo "user authentication failed"; //code for failed login here...
        }

    }
?> 
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Indexd</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="stylesheets/screen.css">
        <script type="text/javascript" src="//use.typekit.net/ljr0ywn.js"></script>
        <script type="text/javascript">try{Typekit.load();}catch(e){}</script>
    </head>

    <?php require_once("lib/includes/partials/header.inc.php"); ?>
    
    <body>
        <section class="login-page">
            <h2>Sign In</h2>
            <p>Sign in to edit your account details. Don't have an account? <a href="register.php">Join now, it's free.</a></p>
            <form class="login-form" method="post" name="login-page-form" id="login-page-form" action="">

                <fieldset class="half">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="<?php 
                        echo (isset($_POST['email']) ? $_POST['email'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"/>
                </fieldset>

                <input type="submit" id="submit-login" name="submit"/>
            </form>
        </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/main.js"></script>
    </body>
</html>