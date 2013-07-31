<?php
require_once("class.Database.inc.php");
require_once("class.PrivateAPI.inc.php");

class BookmarkHandler {

	protected $api;
	protected $IU;

	public function __construct(){
		$this->api = new PrivateAPI();
		$this->IU = new InsertUpdate();
	}

	//returns all users bookmarked users as JSON obj 
	public function get_bookmarked_users_JSON($user_id){
		
	}

	//adds the id to the bookmark list
	//returns false on failure
	public function add_bookmark($user_id, $bookmark_user_id){
		$query = "SELECT bookmarked_users FROM " . Database::$table . " WHERE id='" . $user_id . "' LIMIT 1";
		//if there was a user that matched the $user_id
		if($results = Database::get_all_results($query)){
			$bookmark_list = $results['bookmarkd_users'];
			$bookmark_list .= ", " . $bookmark_user_id;
			$assoc_array = array('id' => $user_id,
								'bookmarked_users' => $bookmark_list);
			//if the bookmarked users id was appended to the user's bookmark list successfully
			if($this->execute_from_assoc($assoc_array)){
				return $this->IU->increment($bookmark_user_id, 'likes');
			}else return false;
		}else return false;
		#maybe this should be a user function. i.e. $user->add_bookmark();
	}

	//called from inside get_bookmarked_users_JSON
	protected function form_query($numerical_array_of_ids){
		$query = "SELECT " . $this->api->columns_to_provide . " FROM " . Database::$table . " WHERE ";
		foreach ($numerical_array_of_ids as $id) {
			$query .= "id='" . $id . "' OR ";
		}
		$query = rtrim($query, " OR ");
		return $query;
	}
	
}
?>