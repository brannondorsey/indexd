<?php 
    require_once("lib/includes/classes/class.PrivateAPI.inc.php");
    require_once("lib/includes/classes/class.User.inc.php");
    require_once("lib/includes/classes/class.Session.inc.php");
    require_once("lib/includes/classes/class.OrganizationAutocomplete.inc.php");
    require_once 'lib/includes/classes/class.Validator.inc.php'; 

    Session::start();
    $api = new PrivateAPI();
    $user = new User();
    if($user->is_signed_in()) $user->sign_out(); //don't let a signed in user register
    
    if(isset($_POST['first_name'])) {
        $failed_zip_msg = false;
        $validator = new Validation();
        $validator->addSource($_POST);
        $validator->addRules($validator->registration_rules);
        $validator->matchPasswords();
        $validator->run();
 
        if(sizeof($validator->errors) > 0) {
            //var_dump($validator->errors);
        } else {
            //register the user
            Database::init_connection();
            $_POST['url'] = $validator->processURLString($_POST['url']);
            $post_array = Database::clean($_POST);
            $post_array['country'] = "us"; //add country manually for now
            unset($post_array['password_conf']); //unset the password confirmation because we don't need it

            //submits new organizations to organizations list table
            if(isset($post_array['organizations'])){
                //var_dump($post_array['organizations']);
                $autocomplete = new OrganizationAutocomplete();
                $autocomplete->add_list_to_organization_table($post_array['organizations']);
            }
            //Kevin added this to check for the email set, couldn't tell whether it was being checked in the user class itself but looked like it probably wasn't
            //feel free to rearrange this code.
            if(!$user->email_already_exists($_POST['email'])) {
                $registration = $user->register($post_array);
            } else {
                $email_fail = "Looks like that e-mail address is already in use. Did you forget your password?";
            }
            if(isset($registration) && $registration != false){
                //registration success
                header("Location: login.php?from_registration=true");

            }else if(isset($registration) && $registration  === "ZIP_LOOKUP_FAILED"){
                $failed_msg = "Zip lookup failed";
            }else{
                $failed_msg = "Something went wrong, please try again later.";
            }
            Database::close_connection();
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
        <?php require_once("lib/includes/partials/header.inc.php"); ?>
        <section class="register-user">
            <h2>Register</h2>

            <?php 
            if (isset($validator)) {
                if (sizeof($validator->errors) > 0) {
                    echo "<p>Oops, there were some errors with your submission. Please fix them and try again.</p>"; 
                }
                if (isset($failed_msg)) {
                    echo "<p>".$failed_msg."</p>";
                }
                if (isset($email_fail)) {
                    echo "<p>".$email_fail."</p>";
                }
            }

            ?>

            <form id="registration" method="post" action="">
                <fieldset class="half">
                    <label for="first-name">First Name<?php echo (isset($validator->errors['first_name']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="first-name" name="first_name" value="<?php 
                       echo (isset($_POST['first_name']) ? $_POST['first_name'] : '');
                    ?>" placeholder="John" class="<?php echo (isset($validator->errors['first_name']) ? 'invalid' : ''); ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="last-name">Last Name<?php echo (isset($validator->errors['last_name']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="last-name" name="last_name" value="<?php 
                        echo (isset($_POST['last_name']) ? $_POST['last_name'] : '');
                    ?>" placeholder="Doe" class="<?php echo (isset($validator->errors['last_name']) ? 'invalid' : ''); ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="email">Email<?php echo (isset($validator->errors['email']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="email" id="email" name="email" value="<?php 
                        echo (isset($_POST['email']) ? $_POST['email'] : '');
                    ?>" placeholder="johndoe@gmail.com" class="<?php echo (isset($validator->errors['email']) || isset($email_fail) ? 'invalid' : ''); ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="url">Website<?php echo (isset($validator->errors['url']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="url" name="url" value="<?php 
                        echo (isset($_POST['url']) ? $_POST['url'] : '');
                    ?>" placeholder="www.johndoe.com" class="<?php echo (isset($validator->errors['url']) ? 'invalid' : ''); ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="password">Password (twice)<?php echo ((isset($validator->errors['password']) || isset($validator->errors['password_conf'])) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="password" id="password" name="password" class="<?php echo (isset($validator->errors['password']) ? 'invalid' : ''); ?>"/>
                </fieldset>

                <fieldset class="half no-label">
                    <input type="password" id="password_conf" name="password_conf"/>
                </fieldset>

                <fieldset class="full">
                    <label for="description">Description<?php echo (isset($validator->errors['description']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <span id="char-count">140</span>
                    <textarea id="description" name="description" placeholder="What are you into?" class="<?php echo (isset($validator->errors['description']) ? 'invalid' : ''); ?>"><?php 
                        echo (isset($_POST['description']) ? $_POST['description'] : '');
                    ?></textarea>
                </fieldset>

                <fieldset class="full">
                    <label for="media">Media<?php echo (isset($validator->errors['media']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="media" name="media" value="<?php 
                        echo (isset($_POST['media']) ? $_POST['media'] : '');
                    ?>" placeholder="e.g. Painting, Design, Sculpture, Etc." class="<?php echo (isset($validator->errors['media']) ? 'invalid' : ''); ?>"/>
                </fieldset>

                <fieldset class="full">
                    <label for="tags">Tags<?php echo (isset($validator->errors['tags']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="tags" name="tags" value="<?php 
                        echo (isset($_POST['tags']) ? $_POST['tags'] : '');
                    ?>" placeholder="e.g. photorealism, print, large-format, etc." class="<?php echo (isset($validator->errors['tags']) ? 'invalid' : ''); ?>"/>
                </fieldset>

                <fieldset class="full">
                    <label for="organization">Organizations (acronyms suggested)</label>
                    <input type="text" id="organization-text" value="" placeholder="Type to add organizations" autocomplete="off"/>

                    <div class="orgs">

                    </div>

                    <input type="hidden" id="organization" name="organizations" value="" class="autocomplete-output"/>
                </fieldset>

                <fieldset class="half">
                    <label for="zip">Zip/Postal Code<?php echo (isset($validator->errors['zip']) ? '<span class="form-error">*</span>' : ''); ?></label>
                    <input type="text" id="zip" name="zip" value="<?php 
                        echo (isset($_POST['zip']) ? $_POST['zip'] : '');
                    ?>" class="<?php echo (isset($validator->errors['zip']) ? 'invalid' : ''); ?>"/>
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
        <script type="text/javascript">
            $("#organization-text").autoComplete();
        </script>
    </body>
</html>