jQuery.fn.animateAuto = function(prop, speed, callback){
    var elem, height, width;
    return this.each(function(i, el){
        el = jQuery(el), elem = el.clone().css({"height":"auto","width":"auto"}).appendTo("body");
        height = elem.css("height"),
        width = elem.css("width"),
        elem.remove();
        
        if(prop === "height")
            el.animate({"height":height}, speed, callback);
        else if(prop === "width")
            el.animate({"width":width}, speed, callback);  
        else if(prop === "both")
            el.animate({"width":width,"height":height}, speed, callback);
    });  
}

$.fn.autoComplete = function(props) {
	return this.each(function (i, el) {
		el = $(el);
		fieldset = el.closest("fieldset");
		hidden = fieldset.find(".autocomplete-output");

		function addToHidden() {
			fieldset.find(".org .organization").each(function(i) {
				console.log("hello");
				val = hidden.attr("value");
				console.log(val);
				console.log($(this).text())
				hidden.attr("value", val + ", " + $(this).text());
			});
		}

		addToHidden();

		el.on("keyup", function() {

		});
	});
}

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

	if (window.location.pathname == "/login.php") {
		$(".password-reset-container").css("height", "0");
		$(".password-reset-container").css("opacity", "0")

		$(".forgot-password").on("click", function(e) {
			e.preventDefault();
			
			if( $(".password-reset-container").hasClass("expanded") ) {
				$(".password-reset-container").animate({"opacity" : "0"}, 150, function() {
					$(".password-reset-container").animate({"height" : "0"}, 150)
				});
				$(".password-reset-container").removeClass("expanded");
			} else {
				$(".password-reset-container").animateAuto("height", 150, function() {
					$(".password-reset-container").animate({"opacity" : "1"}, 150);
				});
				$(".password-reset-container").addClass("expanded");
			}
		});
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