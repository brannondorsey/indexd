<?php 
    //error_reporting(E_ALL);
    require_once("lib/includes/classes/class.ContentOutput.inc.php"); 
    require_once("lib/includes/classes/class.User.inc.php");
    require_once("lib/includes/classes/class.Session.inc.php");
    Database::init_connection();
    Session::start();
    $user = new User();

    //use this bool to test if a user is signed in and do inline things
    if($user->is_signed_in()){
        $user->load_data(); //load the data object DO NOT FORGET TO DO THIS
    }
    $liked_users = new ContentOutput();
    $numb_results = 10;
    $data = $liked_users->output_highest_liked_users($numb_results);
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

        <div class="brand-container">
            <section class="brand">
                <div class="logo">
                    <img src="img/logo.png" />
                </div>

                <h1>Indexd</h1>
                <!-- <p>An open, distributed network of artists, designers, and other creatives.</p> -->
            </section>
        </div>

        <section class="about">
            <p>Indexd.io is an open, distributed network of artists, designers, writers, and other creatives. Our goal is the decentralization of the creative networking tool. Super simple profiles and straightforward relational results allow you to quickly explore artists' work on whatever platform they choose to host it.</p> 
        </section>

        <section class="listings">
            <section class="query">
                <h2>Get Started</h2>
                <p class="pre-results">We've gone ahead and picked out some of our users for you. Click their name to see more info and related users, or their url to visit their website.</p>
            </section>

            <section class="results">

                <?php
                $data_array = $data->data;
                $data_array_keys = array_keys($data_array);
                $last_key = end($data_array_keys);
                foreach($data_array as $key => $result) { ?>
                <div class="result <?php if($key === $last_key) { echo "last-result"; }?>">
                    <h2><a href="listing.php?id=<?php echo $result->id ?>"><?php echo $result->first_name . " " . $result->last_name; ?></a></h2>
                    <p class="descrip"><?php echo $result->description ?></p>
                    <a class="url" href="<?php echo $result->url ?>">www.<?php echo $result->url ?>.com</a>
                </div>
                <?php } ?>
            </section>
        </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>




        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>