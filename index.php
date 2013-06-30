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

        <div class="red-bg">
        
        <header class="header home-page">
            <div class="search">
                <a class="home-button" href="index.php"><img src="img/indexd_badge_full_s.png" tabindex="3"/></a>
                <form name="search-form" id="search-form" method="get" action="results.php">
                    <input type="text" placeholder="What are you looking for?" id="search" name="search" autocomplete="false" tabindex="1"><a class="search-button" href="results.php" id="submit-search" tabindex="2">s</a>
                </form>
            </div>

            <?php
                if(!isset($user)) {
                    $user = new User();
                    if ($user->is_signed_in()){
                        $user->load_data();
                    }
                }
            ?>

            <div class="login" id="homepage">
                <a class="header-button" href="index.php">Home</a>
                <a class="header-button" href="about.php">About</a>
                <?php if ($user->is_signed_in()) { ?>
                <a class="header-button" href="#">Settings</a>
                <a class="header-button" href="<?php Database::$root_dir_link ?>lib/includes/sign_out.inc.php" id="sign_out">Sign Out</a>
                <?php } else { ?>
                <a class="header-button" href="login.php">Sign In</a>
                <a class="header-button" href="register.php">Join</a>
                <?php } ?>

            </div>
        </header>

        <div class="brand-container">
            <section class="brand">
                <div class="logo">
                    <img src="img/indexd_badge_blue_l.png" />
                </div>

                <h1>Indexd</h1>
                <!-- <p>An open, distributed network of artists, designers, and other creatives.</p> -->
            </section>
        </div>

        <section class="about">
            <p>Indexd.io is an open project that aims to document and connect an extensive collection of contemporary independent artists, designers, authors, and other creatives. When a user joins their website and information are added to our database allowing the public to visit their profile, view their work, and explore similar users. They are also encouraged to participate in the living archive by and saving some of there favorite links.</p>
            <p>Minimal profiles and straightforward relational results allow visitors to quickly explore new artists and works. Its like a big interactive address book for the creative field. Have a look around the site. Its simple.</p>
        </section>

        </div>

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
                    <a class="url" href="<?php echo $result->url ?>"><?php echo $result->url ?></a>
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