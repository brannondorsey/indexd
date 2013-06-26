$(document).ready(function() {
	$("#submit-search").on("click", function(e) {

		e.preventDefault();
		var url = $(this).attr("href");

		$("#search-form").submit();

		//window.location = url;
	});
});