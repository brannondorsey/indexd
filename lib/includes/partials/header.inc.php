<header class="header">
    <div class="search">
        <a class="home-button" href="index.php"><img src="img/indexd_badge_full_s.png" /></a>
    	<form name="search-form" id="search-form" method="get" action="results.php">
	        <input type="text" placeholder="What are you looking for?" id="search" name="search" autocomplete="false"><a class="search-button" href="results.php" id="submit-search">s</a>
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

    <div class="login">
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