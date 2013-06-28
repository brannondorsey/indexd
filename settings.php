<?php 
    require_once("lib/includes/classes/class.PrivateAPI.inc.php");
    require_once("lib/includes/classes/class.User.inc.php");
    require_once("lib/includes/classes/class.Session.inc.php");

    Session::start();
    $api = new PrivateAPI();
    $user = new User(); 
    if($user->is_signed_in()){
        $user->load_data();
        $id = $user->data->id;
    } else {
        header('Location: login.php');
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

        <?php require_once 'lib/includes/classes/class.Validator.inc.php'; 
    
            $rules_array = array(
                'first_name'=>array('display'=>'first name', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>50, 'trim'=>true),
                'last_name'=>array('display'=>'last name', 'type'=>'string',  'required'=>true, 'min'=>2, 'max'=>50, 'trim'=>true),
                'email'=>array('display'=>'email', 'type'=>'email',  'required'=>true, 'min'=>5, 'max'=>50, 'trim'=>true),
                'url'=>array('display'=>'URL', 'type'=>'url', 'required'=>true, 'min'=>5, 'max'=>70, 'trim'=>true),
                'password'=>array('display'=>'password', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
                'password_conf'=>array('display'=>'password confirm', 'type'=>'string',  'required'=>true, 'min'=>6, 'max'=>50, 'trim'=>true),
                'description'=>array('display'=>'description', 'type'=>'string',  'required'=>true, 'min'=>10, 'max'=>140, 'trim'=>true),
                'media'=>array('display'=>'media', 'type'=>'string',  'required'=>true, 'min'=>3, 'max'=>70, 'trim'=>true),
                'tags'=>array('display'=>'tags', 'type'=>'string',  'required'=>true, 'min'=>5, 'max'=>70, 'trim'=>true),
                'zip'=>array('display'=>'zip code', 'type'=>'numeric', 'required'=>true, 'min'=>1, 'max'=>99999999, 'trim'=>true)
            );

            if(isset($_POST['first_name'])) {

                $validator = new Validation();
                $validator->addSource($_POST);
                $validator->addRules($rules_array);
                $validator->matchPasswords();
                $validator->run();

                if(sizeof($validator->errors) > 0) {
                    //var_dump($validator->errors);
                } else {
                    //register the user
                    Database::init_connection();
                    $post_array = Database::clean($_POST);
                    $post_array['country'] = "us"; //add country manually for now
                    unset($post_array['password_conf']); //unset the password confirmation because we don't need it
                    $user->register($post_array);
                    Database::close_connection();
                }

            }
            
        ?>
        
        <?php require_once("lib/includes/partials/header.inc.php"); ?>

        <section class="update-settings">
            <!-- SETTINGS UPDATE FORM -->
        </section>

        <section class="badge-info">
            <h2>Indexd Brand Materials</h2>

            <p>Adding a link to your indexd page from your website is easy. Here are eight copy/pastable options to add a badge to your site.</p>

            <div class="badge-demo-container">
                <div class="badge">
                    <a href="#"><img src="img/indexd_badge_full_l.png" /></a>
                </div>
                <h3>Large, Full Color</h3>
                <pre><code>&lt;a href="http://www.indexd.io/listing.php?id=<?php echo $id; ?>"&gt;&lt;img src="path/to/img" /&gt;&lt;/a&gt;</code></pre>
            </div>

            <div class="badge-demo-container">
                <div class="badge">
                    <a href="#"><img src="img/indexd_badge_blue_l.png" /></a>
                </div>
                <h3>Large, Blue</h3>
                <pre><code>&lt;a href="http://www.indexd.io/listing.php?id=<?php echo $id; ?>"&gt;&lt;img src="path/to/img" /&gt;&lt;/a&gt;</code></pre>
            </div>

            <div class="badge-demo-container">
                <div class="badge dark">
                    <a href="#"><img src="img/indexd_badge_white_l.png" /></a>
                </div>
                <h3>Large, White</h3>
                <pre><code>&lt;a href="http://www.indexd.io/listing.php?id=<?php echo $id; ?>"&gt;&lt;img src="path/to/img" /&gt;&lt;/a&gt;</code></pre>
            </div>

            <div class="badge-demo-container">
                <div class="badge">
                    <a href="#"><img src="img/indexd_badge_red_l.png" /></a>
                </div>
                <h3>Large, Red</h3>
                <pre><code>&lt;a href="http://www.indexd.io/listing.php?id=<?php echo $id; ?>"&gt;&lt;img src="path/to/img" /&gt;&lt;/a&gt;</code></pre>
            </div>

            <div class="badge-demo-container">
                <div class="badge">
                    <a href="#"><img src="img/indexd_badge_full_s.png" /></a>
                </div>
                <h3>Small, Full Color</h3>
                <pre><code>&lt;a href="http://www.indexd.io/listing.php?id=<?php echo $id; ?>"&gt;&lt;img src="path/to/img" /&gt;&lt;/a&gt;</code></pre>
            </div>

            <div class="badge-demo-container">
                <div class="badge">
                    <a href="#"><img src="img/indexd_badge_blue_s.png" /></a>
                </div>
                <h3>Small, Blue</h3>
                <pre><code>&lt;a href="http://www.indexd.io/listing.php?id=<?php echo $id; ?>"&gt;&lt;img src="path/to/img" /&gt;&lt;/a&gt;</code></pre>
            </div>

            <div class="badge-demo-container">
                <div class="badge dark">
                    <a href="#"><img src="img/indexd_badge_white_s.png" /></a>
                </div>
                <h3>Small, White</h3>
                <pre><code>&lt;a href="http://www.indexd.io/listing.php?id=<?php echo $id; ?>"&gt;&lt;img src="path/to/img" /&gt;&lt;/a&gt;</code></pre>
            </div>

            <div class="badge-demo-container">
                <div class="badge">
                    <a href="#"><img src="img/indexd_badge_red_s.png" /></a>
                </div>
                <h3>Small, Red</h3>
                <pre><code>&lt;a href="http://www.indexd.io/listing.php?id=<?php echo $id; ?>"&gt;&lt;img src="path/to/img" /&gt;&lt;/a&gt;</code></pre>
            </div>

            <p>If you want more control over the look of your indexd badge, <a href="#">here's a link to an illustrator document with our brand materials in it.</a> We don't have any rules about what you can and can't do to the logo. Go wild.</p>


        </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/main.js"></script>
    </body>
</html>