#PHP backend documentation

##Static Database Class

This static class handles the direct connections with the database.

###Public Properties
     public static $table  // holds db table name

###Public Methods
     public static function Database::init_connection() //initializes database connection
     public static function Database::close_connection() //closes database connection
     public static function Database::execute_sql($query) //execute sql query statement. Used for INSERT and UPDATE mostly.
     public static function Database::get_all_results($query) //returns 1D associative array if one result was found. Returns a 2D
            array with the first array numeric and the second associative if more than one result was found. 
     public static function Database::clean($array) //used to clean $_GET and $_POST arrays

It is important to remember to call `Database::init_connection()` at the beginning of any page that requires api data. Forgetting to do so will result in no data ever.  The most useful functions in this class are `Database::clean()` and `Database::get_all_results()`.

##API Class

This class handles the translation of the [API parameters](https://github.com/brannondorsey/artistswebunion/blob/master/api_doc.md) into a valid JSON string.

###Public Properties
     public $public_columns_to_provide //string that holds comma-space delimited column names for the public API to output

###Public Methods
     public function API::get_json_from_assoc(&$get_array, $object_parent_name="data") //Returns a valid JSON string from $_GET
            values or another associative array with values where keys as desired column names. Array must be sanitized before
            using this function.
     public function API::query_results_as_array_of_JSON_objs($query, $object_parent_name=NULL, $b_wrap_as_obj=false)  
            //returns a string of JSON objects. The optional parameters specify wether or not the method should wrap the 
            objects in a larger JSON obj, and if so, what it should be called. 

While `get_json_from_assoc()` usually accepts a cleaned`$_GET` array filled with API params it is not uncommon to form a custom assoc array when calling this function in another class. That looks like this 
                
      $array_of_parameters = array( "tags" => $desired_tag, "order_by" => $desired_order, "limit" => 10);
      $obj = json_decode($this->api->get_json_from_assoc);

It is important to remember to `json_decode()` output before using it in a function. 
####Catching the results
The results from `get_json_from_assoc` once `json_decoded`ed can be accessed like this  
     
   
	  $obj = $api->get_json_from_assoc($cleaned_get_array);
	  $obj->data[0]->first_name
	  $obj->error //catches the error if there is one

	  //check for error like this
	  $b_error = (isset($data->error) ? true : false);
	  
Currently error values include things like invalid API keys and no results found.
    

####More on the API
This class also contains all the fancy dynamic query building from $_GET in the legendary `API::form_query()` protected method. 

##PrivateAPI Class (extends API class)

	public function check_API_key(){
		return true;
	}  //bypass API key

This class has one public function that is used only to override the need for a public API key. The `PrivateAPI` class is used __ONLY__ on our own web pages. It is never used for the Indexd public API because it contains variable overrides that grant its output access to private data. 

     protected PrivateAPI::columns_to_provide = $this->columns_to_provide . ", password, API_key, API_hits, verified";
     protected PrivateAPI::max_output_limit = 1000;

While the above two methods are not public, they illustrate how the `PrivateAPI` gives access to more data fields and a larger amount of data than the regular `API` class.

##RelationalAlgorithm Class

###Public Method
     public function RelationalAlgorithm::get_related_users($user_id) //returns a string of x most related users as an array of
             JSON objects wrapped in a related_users obj

While this class only has one public method it is the sort of creme dela creme if you will of the way indexd creates connections between its users. This class has lots of protected properties and methods that are nested inside of the `get_related_users()` function working the magic. 

####How it works
The algorithm displays related users by providing an equal (more or less) number of the most related users
in the media, tags, and location columns. If certain related users share more than one tag or media similarity they are prioritized and have a greater chance of being included in the final x users returned.

I don't believe that this is cross checking similarities between user's columns (i.e. if a user has similar tags and location) however this could be implemented later if need be.

##InsertUpdate Class

###Public Method
     public function InsertUpdate::execute_from_assoc($post_array, $statement_type="INSERT") //handles dynamic formation of
           INSERT and UPDATE queries from $_POST and executes them

This method dynamically builds and executes `INSERT` and `UPDATE` MySQL statements and is primarily used for in the `$User` class to handle register and sign in forms.
