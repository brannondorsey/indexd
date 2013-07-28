<?php

require_once("lib/includes/classes/class.PrivateAPI.inc.php");
require_once("lib/includes/classes/class.User.inc.php");
require_once("lib/includes/classes/class.Session.inc.php");
require_once("lib/includes/classes/class.Validator.inc.php");

Session::start();
$api = new PrivateAPI();
$user = new User();
if (!$user->is_signed_in()) header('Location: login.php');
else $user->load_data();
if(isset($_POST) && !empty($_POST)){
    Database::init_connection();
    $post_array = Database::clean($_POST);
    $validator = new Validation();
    $rules = $validator->registration_rules;
    unset($rules['password']);
    unset($rules['password_conf']);
    $validator->addSource($post_array);
    $validator->addRules($rules);
    $validator->run();

    if(sizeof($validator->errors) > 0) {
        $errors_exist = true;
    } else {
        $post_array['id'] = $user->data->id; //add the id so that the right user can be updated
        $post_array['url'] = $validator->processURLString($post_array['url']);
        //if the email wasn't changed...
        if($user->data->email == $post_array['email']){
            $user->update_profile($post_array);
        }
        else{
            //update the profile if the email is unique
            if(!$user->email_already_exists($post_array['email'])) $user->update_profile($post_array);
            else{
                ##handle an email already exists message right here...
            }
        }

        //reloads the session vars because they were updated
        $user_data_obj = $user->get_user_data_obj($user->data->id);
        $user_properties = get_object_vars($user_data_obj);
        Session::add_session_vars($user_properties);
        $user->load_data();
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
    <body>
        
        <?php require_once("lib/includes/partials/header.inc.php");?>

        <section class="account-settings">
            <h2>Account Settings</h2>

            <p><?php echo (isset($errors_exist)) ? "Oops, something isn't allowed to be changed to that" : "Don't forget to save the changes you make!"?></p>
            <?php if(isset($changed)){
                // foreach ($changed as $key => $value) {
                //     echo $key . " was changed <br>";
                // }
            }?>

            <form id="registration" method="post" action="" class="account-form">
                <fieldset class="half">
                    <label for="first-name">First Name<?php echo (isset($validator->errors['first_name']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="first-name" name="first_name" value="<?php 
                       echo (isset($user->data->first_name) ? $user->data->first_name : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="last-name">Last Name<?php echo (isset($validator->errors['last_name']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="last-name" name="last_name" value="<?php 
                        echo (isset($user->data->last_name) ? $user->data->last_name : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="email">Email<?php echo (isset($validator->errors['email']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="email" id="email" name="email" value="<?php 
                        echo (isset($user->data->email) ? $user->data->email : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="url">URL<?php echo (isset($validator->errors['url']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="url" name="url" value="<?php 
                        echo (isset($user->data->url) ? str_replace('http://', '', $user->data->url) : '');
                    ?>"/>
                </fieldset>

                <fieldset class="full">
                    <label for="description">Description<?php echo (isset($validator->errors['description']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <span id="char-count">140</span>
                    <textarea id="description" name="description"><?php 
                        echo (isset($user->data->description) ? $user->data->description : '');
                    ?></textarea>
                </fieldset>

                <fieldset class="full">
                    <label for="media">Media<?php echo (isset($validator->errors['media']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="media" name="media" value="<?php 
                        echo (isset($user->data->media) ? $user->data->media : '');
                    ?>"/>
                </fieldset>

                <fieldset class="full">
                    <label for="tags">Tags<?php echo (isset($validator->errors['tags']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="tags" name="tags" value="<?php 
                        echo (isset($user->data->tags) ? $user->data->tags : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="zip">Zip/Postal Code<?php echo (isset($validator->errors['zip']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="zip" name="zip" value="<?php 
                        echo (isset($user->data->zip) ? $user->data->zip : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <input type="submit" id="submit" value="Save Changes" />
                </fieldset>
            </form>

        </section>
        <section class="change-password">

            <h2>Change Password</h2>

            <form id="password-set" method="post" action="">
                <fieldset class="half">
                    <label for="old">New Password (twice)</label>
                    <input type="password" name="new1" id="new1" />
                </fieldset>

                <fieldset class="half no-label">
                    <input type="password" name="new1" id="new1" />
                </fieldset>

                <fieldset class="half">
                    <label for="old">Old Password</label>
                    <input type="password" name="old" id="old" />
                </fieldset>

                <fieldset class="half">
                    <input type="submit" id="submit" value="Save Changes" />
                </fieldset>
            </form>

        </section>

        <section class="badge-section">
            <h2>Badge</h2>
            <p>The indexd badge will allow you to link your website to your piece of the community. You can download a full suite of brand materials, just the file you want, or just copy and paste the link we've built for you.</p>

            <pre>
<code>&lt;a href="insert_account_url"&gt;&lt;img src="path_to_badge" /&gt;&lt;/a&gt;</code>
            </pre>

            <input type="text" readonly value="&lt;a href='insert_account_url'&gt;&lt;img src='path_to_badge' /&gt;&lt;/a&gt;">



            <p>Small</p>
            <a href="img/indexd_badge_full_s.png" class="download-button">Full Color</a> <a href="img/indexd_badge_blue_s.png" class="download-button">Blue</a> <a href="img/indexd_badge_red_s.png" class="download-button">Red</a> <a href="img/indexd_badge_white_s.png" class="download-button">White</a>

            <p>Large</p>
            <a href="img/indexd_badge_full_l.png" class="download-button">Full Color</a> <a href="img/indexd_badge_blue_l.png" class="download-button">Blue</a> <a href="img/indexd_badge_red_l.png" class="download-button">Red</a> <a href="img/indexd_badge_white_l.png" class="download-button">White</a>

           </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/main.js"></script>
    </body>
</html>