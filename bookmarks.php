<?php

require_once("lib/includes/classes/class.PrivateAPI.inc.php");
require_once("lib/includes/classes/class.User.inc.php");
require_once("lib/includes/classes/class.Session.inc.php");
require_once("lib/includes/classes/class.Validator.inc.php");
require_once("lib/includes/classes/class.ContentOutput.inc.php");
require_once("lib/includes/classes/class.OrganizationAutocomplete.inc.php");

Session::start();
$api = new PrivateAPI();
$user = new User();
if (!$user->is_signed_in()) header('Location: login.php');
else $user->load_data();

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

        <section class="listings">
            <section class="query">
                <h2>Bookmarks</h2>
            </section>

            <section class="results bookmarks">
                <?php
                //display related users
                // $data_array = $data->data;
                // foreach($data_array as $key => $result) { ?>
                <div class="result">
                    <form action="" method="post" id="bookmark-form" class="pre">
                        <input type="submit" id="bookmark-toggle" class="bookmark true" value="b">
                    </form>
                    <h2><a href="listing.php?id=<?php //echo $result->id ?>"><?php //echo $result->first_name . " " . $result->last_name; ?>Kevin Zweerink</a></h2>
                    <p class="descrip">Design student at MICA in Baltimore. Into some things.<?php //echo $result->description ?></p>
                    <a class="url" href="<?php //echo $result->url ?>" target="blank"><?php //echo $content_obj->format_url_for_display($result->url) ?>www.kevinzweerink.com</a>
                </div>

                <div class="result">
                    <form action="" method="post" id="bookmark-form" class="pre">
                        <input type="submit" id="bookmark-toggle" class="bookmark true" value="b">
                    </form>
                    <h2><a href="listing.php?id=<?php //echo $result->id ?>"><?php //echo $result->first_name . " " . $result->last_name; ?>Brannon Dorsey</a></h2>
                    <p class="descrip">Student at SAIC in Chicago. Into some things.<?php //echo $result->description ?></p>
                    <a class="url" href="<?php //echo $result->url ?>" target="blank"><?php //echo $content_obj->format_url_for_display($result->url) ?>www.kevinzweerink.com</a>
                </div>

                <div class="result">
                    <form action="" method="post" id="bookmark-form" class="pre">
                        <input type="submit" id="bookmark-toggle" class="bookmark true" value="b">
                    </form>
                    <h2><a href="listing.php?id=<?php //echo $result->id ?>"><?php //echo $result->first_name . " " . $result->last_name; ?>Kevin Zweerink</a></h2>
                    <p class="descrip">Design student at MICA in Baltimore. Into some things.<?php //echo $result->description ?></p>
                    <a class="url" href="<?php //echo $result->url ?>" target="blank"><?php //echo $content_obj->format_url_for_display($result->url) ?>www.kevinzweerink.com</a>
                </div>

                <div class="result">
                    <form action="" method="post" id="bookmark-form" class="pre">
                        <input type="submit" id="bookmark-toggle" class="bookmark true" value="b">
                    </form>
                    <h2><a href="listing.php?id=<?php //echo $result->id ?>"><?php //echo $result->first_name . " " . $result->last_name; ?>Brannon Dorsey</a></h2>
                    <p class="descrip">Student at SAIC in Chicago. Into some things.<?php //echo $result->description ?></p>
                    <a class="url" href="<?php //echo $result->url ?>" target="blank"><?php //echo $content_obj->format_url_for_display($result->url) ?>www.kevinzweerink.com</a>
                </div>
                <?php // } ?>
            </section>
        </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>




        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/main.js"></script>
    </body>
</html>