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
		footer.css({
			"position" : "absolute",
			"bottom" : "0",
			"width" : $(window).width()
		})
	}

	$("#submit-search").on("click", function(e) {

		e.preventDefault();
		var url = $(this).attr("href");

		$("#search-form").submit();

		//window.location = url;
	});

	$("#description").on("input", function(e) {
		var len = $("#description").val().length;
		$("#char-count").text(140 - len);

		if ((140 - len) <= 10 && (140 - len) > 0) {
			$("#char-count").removeClass("negative");
			$("#char-count").addClass("under10");
		} else if ((140 - len) <= 0) {
			$("#char-count").removeClass("under10");
			$("#char-count").addClass("negative");
		} else {
			$("#char-count").removeClass("under10");
			$("#char-count").removeClass("negative");
		}
	});

	$("#sign_out").on("click", function(e) {
		e.preventDefault();
		$.ajax({
			url : "lib/includes/sign_out.inc.php",
			success : function() {
				console.log("signed out");
				window.location.href = window.location.href;
			}
		});
	});
});