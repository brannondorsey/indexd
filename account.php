<?php

require_once("lib/includes/classes/class.PrivateAPI.inc.php");
require_once("lib/includes/classes/class.User.inc.php");
require_once("lib/includes/classes/class.Session.inc.php");

Session::start();
$api = new PrivateAPI();
$user = new User();
if (!$user->is_signed_in()) header('Location: login.php');

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
    <body>
        
        <?php require_once("lib/includes/partials/header.inc.php");?>

        <section class="account-settings">
            <h2>Account Settings</h2>

            <p>Don't forget to submit any changes!</p>

            <form id="registration" method="post" action="">
                <fieldset class="half">
                    <label for="first-name">First Name<?php echo (isset($validator->errors['first_name']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="first-name" name="first_name" value="<?php 
                       echo (isset($_POST['first_name']) ? $_POST['first_name'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="last-name">Last Name<?php echo (isset($validator->errors['last_name']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="last-name" name="last_name" value="<?php 
                        echo (isset($_POST['last_name']) ? $_POST['last_name'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="email">Email<?php echo (isset($validator->errors['email']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="email" id="email" name="email" value="<?php 
                        echo (isset($_POST['email']) ? $_POST['email'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="url">URL<?php echo (isset($validator->errors['url']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="url" name="url" value="<?php 
                        echo (isset($_POST['url']) ? $_POST['url'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="password">Password (twice)<?php echo ((isset($validator->errors['password']) || isset($validator->errors['password_conf'])) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="password" id="password" name="password"/>
                </fieldset>

                <fieldset class="half">
                    <input type="password" id="password_conf" name="password_conf"/>
                </fieldset>

                <fieldset class="full">
                    <label for="description">Description<?php echo (isset($validator->errors['description']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <span id="char-count">140</span>
                    <textarea id="description" name="description"><?php 
                        echo (isset($_POST['description']) ? $_POST['description'] : '');
                    ?></textarea>
                </fieldset>

                <fieldset class="full">
                    <label for="media">Media<?php echo (isset($validator->errors['media']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="media" name="media" value="<?php 
                        echo (isset($_POST['media']) ? $_POST['media'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="full">
                    <label for="tags">Tags<?php echo (isset($validator->errors['tags']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="tags" name="tags" value="<?php 
                        echo (isset($_POST['tags']) ? $_POST['tags'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="zip">Zip/Postal Code<?php echo (isset($validator->errors['zip']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="zip" name="zip" value="<?php 
                        echo (isset($_POST['zip']) ? $_POST['zip'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <input type="submit" id="submit" value="Submit" />
                </fieldset>
            </form>
        </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/main.js"></script>
    </body>
</html>