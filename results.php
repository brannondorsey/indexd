<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->

<?php 
    error_reporting(E_ALL);
    require_once("lib/includes/classes/class.ContentOutput.inc.php"); 
    Database::init_connection();
    $content_obj = new ContentOutput();
    $search_string = Database::clean($_GET['search']);
    $numb_results = 10;
    $page = (isset($_GET['page']) ? $_GET['page'] : 1);
    $search_array = array(
       'search' => $search_string,
       'limit' => $numb_results,
       'page' => $page
    );
    $total_numb_results = $content_obj->total_numb_results($search_array); //gives total number of pages
    $total_pages = ceil($total_numb_results/$numb_results); //calculates total number of pages
    $data = new stdClass();
    $data = $content_obj->output_search_results($search_array);
?>

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

        <section class="listings">
            <section class="query">
                <h2>Showing results for <span class="search-term"><?php echo $search_string; ?></span></h2>
            </section>

            <section class="results">

                <?php
                foreach($data->data as $result) { ?>
                <div class="result">
                    <h2><a href="listing.php?id=<?php echo $result->id ?>"><?php echo $result->first_name . " " . $result->last_name; ?></a></h2>
                    <p class="descrip"><?php echo $result->description; ?></p>
                    <a class="url" href="<?php echo $result->url ?>"><?php echo "www." . $result->url . ".com" ?></a> 
                </div>

                <?php } ?>

            <section class="pagination">
                <div class="pagination-container">
                    <?php if ($page > 1) { ?>
                    <a class="prev" href="results.php?search=<?php echo $search_string; ?>&amp;page=<?php echo ($page - 1); ?>">&lt;</a>
                    <?php } ?>
                    <span class="count"><?php echo min($page, $total_pages) . " of " . $total_pages ?> <e</span>
                    <a class="next" href="results.php?search=<?php echo $search_string; ?>&amp;page=<?php echo ($page + 1); ?>">&gt;</a>
                </div>
            </section>
        </section>

        <?php require_once("lib/includes/partials/footer.inc.php"); ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
        <script src="js/main.js"></script>
    </body>
</html>
<?php Database::close_connection(); ?>