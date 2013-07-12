<?php 
    require_once("lib/includes/classes/class.User.inc.php");
    require_once("lib/includes/classes/class.ContentOutput.inc.php"); 
    require_once("lib/includes/classes/class.Session.inc.php");
    Database::init_connection();
    Session::start();
    $content_obj = new ContentOutput();
    $user_id = $_GET['id'];
    $numb_results = 10;
    $profile_data = $content_obj->output_profile($user_id)->data[0];
    $data = new stdClass();
    $data = $content_obj->output_related_users($user_id);
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
                <div class="profile">
                    <h2><?php echo $profile_data->first_name . " " . $profile_data->last_name; ?></h2>
                    <div class="media">
                    <?php 
                    $media_array = ContentOutput::commas_to_list($profile_data->media);
                    $media_array_index = 0;
                    foreach($media_array as $media){ 
                        $media_string = $media;
                        if($media_array_index < sizeof($media_array)-1) $media_string .= ", ";
                        ?>
                        <a href="results.php?media=<?php echo $media ?>"><?php echo $media_string; ?></a>
                    <?php $media_array_index++; } ?>
                    </div>
                    <!-- <a href="#" class="media"><?php //echo $profile_data->media; ?></a> -->

                    <div class="info">
                        <a class="url" href="<?php echo $profile_data->url ?>" target="blank"><?php echo $content_obj->format_url_for_display($profile_data->url); ?></a>
                        <a class="email" href="mailto:<?php echo $profile_data->email; ?>" target="blank"><?php echo $profile_data->email; ?></a>

                        <p class="descrip"><?php echo $profile_data->description; ?></p>

                        <div class="tags">
                            <?php foreach(ContentOutput::commas_to_list($profile_data->tags) as $tag) { ?>
                            <span class="tag"><a href="results.php?tags=<?php echo $tag; ?>"><?php echo $tag; ?></a></span>
                            <?php } ?>
                        </div>
                    </div> <!-- /.info -->

                    <a class="location" href="results.php?city=<?php echo $profile_data->city ?>&state=<?php echo $profile_data->state ?>&country=<?php echo $profile_data->country ?>"><?php echo ucfirst($profile_data->city) . ", " . ucfirst($profile_data->state) . ", " . ucfirst($profile_data->country);?></a>
                </div>
            </section>

            <section class="results">
                <?php
                //display related users
                $data_array = $data->data;
                foreach($data_array as $key => $result) { ?>
                <div class="result">
                    <h2><a href="listing.php?id=<?php echo $result->id ?>"><?php echo $result->first_name . " " . $result->last_name; ?></a></h2>
                    <p class="descrip"><?php echo $result->description ?></p>
                    <a class="url" href="<?php echo $result->url ?>" target="blank"><?php echo $content_obj->format_url_for_display($result->url) ?></a>
                </div>
                <?php } ?>
            </section>
        </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>




        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/main.js"></script>
    </body>
</html>