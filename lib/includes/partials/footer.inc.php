<footer>

	<div class="footer-nav">

		<a class="footer-button" href="index.php">Home</a>
		<a class="footer-button" href="about.php">About</a>
        <?php if ($user->is_signed_in()) { ?>
        <a class="footer-button" href="#">Settings</a>
        <a class="footer-button" href="<?php Database::$root_dir_link ?>lib/includes/sign_out.inc.php" id="sign_out">Sign Out</a>
        <?php } else { ?>
		<a class="footer-button" href="login.php">Sign In</a>
		<a class="footer-button" href="register.php">Join</a>
        <?php } ?>
		<a class="footer-button" href="https://github.com/brannondorsey/artistswebunion/tree/master/api">Public API</a>


	</div>
	<a href="index.php">
		<img src="img/network.png" />
	</a>
</footer>
