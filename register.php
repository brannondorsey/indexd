<?php // require_once 'lib/includes/register_include.php'; ?>
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

        <?php require_once 'lib/includes/classes/class.Validator.inc.php'; 
    
            $rules_array = array(
                'first_name'=>array('display'=>'First Name', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>50, 'trim'=>true),
                'last_name'=>array('display'=>'Last Name', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>50, 'trim'=>true),
                'email'=>array('display'=>'Email', 'type'=>'email',  'required'=>true, 'min'=>5, 'max'=>50, 'trim'=>true),
                'url'=>array('display'=>'URL', 'type'=>'url', 'required'=>true, 'min'=>5, 'max'=>70, 'trim'=>true),
                'password'=>array('display'=>'Password', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
                'password_conf'=>array('display'=>'Password Confirm', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
                'description'=>array('display'=>'Description', 'type'=>'string',  'required'=>true, 'min'=>10, 'max'=>140, 'trim'=>true),
                'media'=>array('display'=>'Media', 'type'=>'string',  'required'=>true, 'min'=>3, 'max'=>70, 'trim'=>true),
                'tags'=>array('display'=>'Tags', 'type'=>'string',  'required'=>true, 'min'=>0, 'max'=>70, 'trim'=>true),
                'zip'=>array('display'=>'Zip Code', 'type'=>'numeric', 'required'=>true, 'min'=>1, 'max'=>99999999, 'trim'=>true)
            );

            if(isset($_POST['first_name'])) {

                $validator = new Validation();
                $validator->addSource($_POST);
                $validator->addRules($rules_array);
                $validator->matchPasswords();
                $validator->run();

                if(sizeof($validator->errors) > 0) {
                    var_dump($validator->errors);
                } else {
                    // PUT ALL THE THINGS IN HERE RAISIN
                }

            }
            
        ?>
        
        <?php require_once("lib/includes/partials/header.inc.php"); ?>

        <section class="register-user">
            <h2>Register</h2>

            <form id="registration" method="post" action="">
                <fieldset class="half">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first_name" value="<?php 
                       echo (isset($_POST['first_name']) ? $_POST['first_name'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last_name" value="<?php 
                        echo (isset($_POST['last_name']) ? $_POST['last_name'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php 
                        echo (isset($_POST['email']) ? $_POST['email'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="url">URL</label>
                    <input type="text" id="url" name="url" value="<?php 
                        echo (isset($_POST['url']) ? $_POST['url'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="password">Password (twice)</label>
                    <input type="password" id="password" name="password"/>
                </fieldset>

                <fieldset class="half">
                    <input type="password" id="password_conf" name="password_conf"/>
                </fieldset>

                <fieldset class="full">
                    <label for="description">Description</label>
                    <span id="char-count">140</span>
                    <textarea id="description" name="description"><?php 
                        echo (isset($_POST['description']) ? $_POST['description'] : '');
                    ?></textarea>
                </fieldset>

                <fieldset class="full">
                    <label for="media">Media</label>
                    <input type="text" id="media" name="media" value="<?php 
                        echo (isset($_POST['media']) ? $_POST['media'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="full">
                    <label for="tags">Tags</label>
                    <input type="text" id="tags" name="tags" value="<?php 
                        echo (isset($_POST['tags']) ? $_POST['tags'] : '');
                    ?>"/>
                </fieldset>

                <fieldset class="half">
                    <label for="zip">Zip/Postal Code</label>
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