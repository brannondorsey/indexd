<?php 
	require_once 'classes/class.Database.inc.php';
	require_once 'classes/class.User.inc.php';
	require_once 'classes/class.OrganizationAutocomplete.inc.php';
	Database::init_connection();
	$autocomplete = new OrganizationAutocomplete();
	if(isset($_POST['organizations'])){
		$post_array = Database::clean($_POST);
		if($autocomplete->add_list_to_organization_table($post_array['organizations']) !== false){
			echo "Organizations added to db";
		}
	}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>
<script>
	$('document').ready(function(){
		$('input[name="organizations"]').keyup(function(){
			var inputValue = $(this).val();
			var lastComma = inputValue.lastIndexOf(',');
			var orgBeingTyped = inputValue.substring(lastComma+1);
			orgBeingTyped = $.trim(orgBeingTyped);
			console.log(orgBeingTyped);
			if(orgBeingTyped != ""){
				$.ajax("<?php echo Database::$root_dir_link?>api/organization_list.php?chars="+orgBeingTyped).success(function(results){
					var resultsFound;
					try{
						var resultsJSON = $.parseJSON(results);
						resultsFound = true;
					}
					catch(e){ //if there are no matches
						resultsFound = false;
					}

					//display the results next to "Suggestions: "
					if(resultsFound){
						$('#target').text(resultsJSON.data.join(", "));
					}else{
						$('#target').text("");
					}
				});
			}
		});
	});
	
</script>
<form method="post" action="">
	<input type="text" name="organizations" autocomplete="off"></input>
	<input type="submit" value="submit"/>
</form>
<p>Suggestions: <span id="target"></span></p>
<p>Words to try:<br>
<?php 
$obj = json_decode($autocomplete->get_results_as_JSON(""));
foreach ($obj->data as $result) {
	echo $result . "<br>";
}
?>
</p>