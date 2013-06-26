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

            <form id="registration">
                <fieldset class="half">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" />
                </fieldset>

                <fieldset class="half">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" />
                </fieldset>

                <fieldset class="half">
                    <label for="email">Email</label>
                    <input type="email" id="email" />
                </fieldset>

                <fieldset class="half">
                    <label for="url">URL</label>
                    <input type="text" id="url" />
                </fieldset>

                <fieldset class="half">
                    <label for="password">Password (twice)</label>
                    <input type="password" id="password" />
                </fieldset>

                <fieldset class="half">
                    <input type="password" id="password-conf" />
                </fieldset>

                <fieldset class="full">
                    <label for="description">Description</label>
                    <textarea id="description"></textarea>
                </fieldset>

                <fieldset class="full">
                    <label for="media">Media</label>
                    <input type="text" id="media" />
                </fieldset>

                <fieldset class="full">
                    <label for="tags">Tags</label>
                    <input type="text" id="tags" />
                </fieldset>

                <fieldset class="half">
                    <label for="zip">Zip/Postal Code</label>
                    <input type="text" id="zip" />
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