$(document).ready(function() {
	var footer = $("footer");
	var body = $("body");
	var windowState = $(window);

	console.log(windowState.height());
	console.log(body.height());
	console.log(footer.height());

	if (body.height() < $(window).height()) {
		var dif = windowState.height() - body.height();
		var newFooter = footer.height() + dif;
		console.log(dif);
		console.log(newFooter);
		footer.css("height", newFooter + "px")
	}

	$("#submit-search").on("click", function(e) {

		e.preventDefault();
		var url = $(this).attr("href");

		$("#search-form").submit();

		//window.location = url;
	});
});