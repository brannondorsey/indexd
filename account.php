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
    $validator = new Validation();
    $post_array = Database::clean($_POST);
    //if POST is coming from the change password form
    if(isset($post_array['new_password'])){
        $rules = array(
        'new_password'=>array('display'=>'password', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
        'new_password_conf'=>array('display'=>'password confirm', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
        'old_password'=>array('display'=>'password', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true));
        $validator->addSource($post_array);
        $validator->addRules($rules);
        //match passwords manually
        if($post_array['new_password'] != $post_array['new_password_conf']) $validator->errors['pword_match'] = 'passwords did not match';
        $validator->run();
        if(sizeof($validator->errors) > 0) {
            $reset_password_errors_exist = true;
        } else {
            if($user->reset_password($user->data->id, $post_array['old_password'], $post_array['new_password'])){
                $password_changed = true;
                #code to execute when a password is successfully changed
            }else{
                $reset_password_errors_exist = true;
                $validator->errors['pword_incorrect'] = "password was incorrect";
                #code to execute when password change fails
            }
        }
    }else{ //if the POST is comming from an edit to the user profile
        $rules = $validator->registration_rules;
        unset($rules['password']);
        unset($rules['password_conf']);
        $validator->addSource($post_array);
        $validator->addRules($rules);
        $validator->run();

        if(sizeof($validator->errors) > 0) {
            $account_edit_errors_exist = true;
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

            <p><?php echo (isset($account_edit_errors_exist)) ? "Oops, something isn't allowed to be changed to that" : "Don't forget to save the changes you make!"?></p>
            <?php if(isset($changed)){
                // foreach ($changed as $key => $value) {
                //     echo $key . " was changed <br>";
                // }
            }?>

            <form id="registration" method="post" action="account.php" class="account-form">
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
                    <label for="url">Website<?php echo (isset($validator->errors['url']) ? '<span class="form-error">*</span>' : ''); ?></label>
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

                <fieldset class="full">
                    <label for="organization">Organizations (acronyms suggested)</label>
                    <input type="text" id="organization-text" value="" placeholder="Type to add organizations" autocomplete="off"/>
                    <span class="return-prompt">&crarr;</span>
                    <div class="orgs">
                        <span class="org"><a class="organization" href="#">MICA</a><a href="#" class="remove">&times;</a></span>
                        <span class="org"><a class="organization" href="#">SAIC</a><a href="#" class="remove">&times;</a></span>
                    </div>

                    <input type="hidden" id="organization" value="" class="autocomplete-output"/>
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
        <section id="change-password" class="change-password">

            <h2>Change Password</h2>
            <?php if(isset($_GET['temp']) &&
                     !empty($_GET['temp'])){?>
            <p>Your password was set to <?php echo $_GET['temp']?></p>
            <p>You may want to change that to something more memorable now.</p>
                     <?php } ?>
            <?php if(isset($reset_password_errors_exist)){
                    if(isset($validator->errors['pword_match'])) echo "Your new passwords don't match";
                    else if(isset($validator->errors['pword_incorrect'])) echo "Your password is incorrect";
                    else echo "Oops, there was a problem changing your password";
                  }
                  else if(isset($password_changed)){ echo "Your password was changed successfully!";
            }?></p>
            <form id="password-set" method="post" action="account.php#change-password">
                <fieldset class="half">
                    <label for="old">New Password (twice)<?php echo ((isset($validator->errors['new_password']) || isset($validator->errors['new_password_conf'])) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="password" name="new_password" id="new1" />
                </fieldset>

                <fieldset class="half no-label">
                    <input type="password" name="new_password_conf" id="new1" />
                </fieldset>

                <fieldset class="half">
                    <label for="old">Old Password<?php echo (isset($validator->errors['old_password'])) ? '<span class="form-error">*</span>' : ''; ?></label>
                    <input type="password" name="old_password" id="old" />
                </fieldset>

                <fieldset class="half">
                    <input type="submit" id="submit" value="Change Password" />
                </fieldset>
            </form>

        </section>

        <section class="badge-section">
            <h2>Badge</h2>
            <p>The indexd badge will allow you to link your website to your piece of the community. You can download a full suite of brand materials, just the file you want, or just copy and paste the link we've built for you.</p>

            <input type="text" readonly value="&lt;a href='insert_account_url'&gt;&lt;img src='path_to_badge' /&gt;&lt;/a&gt;">

            <p>Badge Image Downloads</p>
            <span class="badge-button full-color"><span class="badge-preview"><img src="/img/indexd_badge_full_s.png" /></span><a href="/img/indexd_badge_full_l.png">Full Color</a></span>
            <span class="badge-button blue-color"><span class="badge-preview"><img src="/img/indexd_badge_blue_s.png" /></span><a href="/img/indexd_badge_blue_l.png">Blue</a></span>
            <span class="badge-button red-color"><span class="badge-preview"><img src="/img/indexd_badge_red_s.png" /></span><a href="/img/indexd_badge_red_l.png">Red</a></span>
            <span class="badge-button white-color"><span class="badge-preview"><img src="/img/indexd_badge_white_s.png" /></span><a href="/img/indexd_badge_white_l.png">White</a></span>
           </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/jquery.tokeninput.js"></script>
        <script src="js/main.js"></script>
        <script type="text/javascript">
            $("#organization-text").autoComplete();
        </script>
    </body>
</html>