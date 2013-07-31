<?php
	 require_once("classes/class.Database.inc.php");
	 require_once("classes/class.BookmarkHandler.inc.php");
	 require_once("classes/class.User.inc.php");
	 Session::start();
	 Database::init_connection();

	 $user = new User();
	 $email = "brannon@brannondorsey.com";
	 $password = "password";
	 $user->sign_in($email, $password);
	 if(isset($_GET['bookmark']))$user->add_bookmark((int) $_GET['bookmark']);

	 $bookmark_hand = new BookmarkHandler();
	 //perhaps the below method should also be called from inside of the user class.
	 //like $user->get_bookmarked_users();
	 //or maybe not... damn I don't know. It could be good as ContentOutput::get_bookmarks($id);
	 echo $user->data->bookmarked_users . "<br>";
	 echo $bookmark_hand->get_bookmarked_users_JSON($user->data->id);
	 
	 Database::close_connection();
?> 