<?php
require_once("class.Database.inc.php");
require_once("class.PrivateAPI.inc.php");
require_once("class.ContentOutput.inc.php");

class BookmarkHandler {

	protected $api;
	protected $IU;

	public function __construct(){
		$this->api = new PrivateAPI();
		$this->IU = new InsertUpdate();
	}

	//returns all users bookmarked users as JSON obj
	//returns false on failure
	public function get_bookmarked_users_JSON($user_id){
		if($bookmark_list = $this->get_bookmarks_list($user_id)){
			$bookmarks_list_array = ContentOutput::commas_to_list($bookmark_list);
			$query = $this->form_query($bookmarks_list_array);
			return $this->api->query_results_as_array_of_JSON_objs($query, "data", true);
		}else return false;
	}

	//adds the id to the bookmark list
	//returns false on failure and true on success or if $user_id user has already bookmarked $bookmark_user_id user
	public function add_bookmark($user_id, $bookmark_user_id){
		if($bookmark_list = $this->get_bookmarks_list($user_id)){
			if(!$this->already_bookmarked($bookmark_user_id, $bookmark_list)){
				if($bookmark_list != "") $bookmark_list .= ", ";
				$bookmark_list .= $bookmark_user_id;
				$assoc_array = array('id' => $user_id,
									'bookmarked_users' => $bookmark_list);
				//if the bookmarked users id was appended to the user's bookmark list successfully
				if($this->IU->execute_from_assoc($assoc_array, "UPDATE")){
					return $this->IU->increment($bookmark_user_id, 'likes');
				}else return false;
			}else return true; //the user has already bookmarked this user so everything is good
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

	//returns a string comma-space dilemited list of the ids of bookmarked users
	protected function get_bookmarks_list($user_id){
		$query = "SELECT bookmarked_users FROM " . Database::$table . " WHERE id='" . $user_id . "' LIMIT 1";
		//if there was a user that matched the $user_id
		if($results = Database::get_all_results($query)) $bookmark_list = $results['bookmarked_users'];
		else return false;
		return $bookmark_list;
	}

	protected function already_bookmarked($bookmark_user_id, $bookmark_list){
		$bookmark_list_array = ContentOutput::commas_to_list($bookmark_list);
		return (in_array($bookmark_user_id, $bookmark_list_array)) ? true : false;
	}
}
?>