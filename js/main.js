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
		var el = $(el);
		var fieldset = el.closest("fieldset");
		var hidden = fieldset.find(".autocomplete-output");
		var orgs = fieldset.find(".orgs");
		var content;

		function addToHidden() {
			fieldset.find(".org .organization").each(function(i) {
				var val = hidden.attr("value");
				if(i != 0) {
					hidden.attr("value", val + ", " + $(this).text());
				} else {
					hidden.attr("value", $(this).text());
				}
			});
		}

		function listenForRemoval() {
			fieldset.find(".remove").click(function(e) {
				e.preventDefault();
				var parent = $(this).closest(".org");
				parent.remove();
				addToHidden();
			});
			addToHidden();
		}

		function addOrg(org) {
			var wrapper = $("<span />").addClass("org");
			var title = $("<a />").attr("href", "#").addClass("organization").text(org);
			var close = $("<a />").attr("href", "#").addClass("remove").text("×");

			title.appendTo(wrapper);
			close.appendTo(wrapper);
			wrapper.appendTo(orgs);

			closeAutocomplete();
			addToHidden();
			listenForRemoval();
			el.val("");
		}

		function listenForSelection() {
			fieldset.find(".autocomplete-results a").click(function(e) {
				e.preventDefault();
				org = $(this).text();
				addOrg(org);
				closeAutocomplete();
				el.val("");
			})
		}

		function listenForHover() {
			$(".autocomplete-results li").on("mouseenter", function(e) {
				$(this).closest(".autocomplete-results").find(".selected").removeClass("selected");
				$(this).addClass("selected");
			})
		}

		function closeAutocomplete() {
			if(fieldset.find(".autocomplete-results")) {
				fieldset.find(".autocomplete-results").remove();
			}
		}

		function insertData(data) {
			fieldset.find(".autocomplete-results").remove();
			var ul = $("<ul />").addClass("autocomplete-results");
			for(var i=0; i < data.length; i+=1) {
				var result = data[i];
				var li = $("<li />");
				var a = $("<a />").attr("href", "#").text(result);
				a.appendTo(li);
				li.appendTo(ul);
			}

			ul.insertAfter(el);

			listenForSelection();
		}

		function arrowKeyActions(key) {
			if(key === 40) {
				if(fieldset.find(".autocomplete-results li.selected").length != 0) {
					fieldset.find(".autocomplete-results li.selected").removeClass("selected").next("li").addClass("selected");
					el.val(fieldset.find(".selected a").text());
				} else {
					fieldset.find(".autocomplete-results li").eq(0).addClass("selected");
					el.val(fieldset.find(".selected a").text());
				}
			} else if (key === 38) {
				if(fieldset.find(".autocomplete-results li.selected").length != 0) {
					fieldset.find(".autocomplete-results li.selected").removeClass("selected").prev("li").addClass("selected");
					el.val(fieldset.find(".selected a").text());
				}
			} else if (key === 13) {
				console.log("this worked");
				if (fieldset.find(".autocomplete-results li.selected").length != 0) {
					fieldset.find(".autocomplete-results li.selected a").click();
				} else {
					addOrg(el.val());
				}
			}

			if(fieldset.find(".selected").length === 0) {
				el.val(content);
				if(key === 13) {
					el.val("");
				}
			}
		}

		function initialize() {

			listenForRemoval();

			el.on("keyup", function(e) {

				var _this = $(this);

				if (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 13) {
					e.preventDefault();
					return false;
				} else {
					content = $(this).val();
				}

				if (content != "") {
					// AJAX call to api page
					var req = $.ajax({
						url : "/api/organization_list.php?chars=" + content,
						success : function(data) {
							var contents = $.parseJSON(data);
							var items = contents.data
							insertData(items);
							listenForHover();
						}
					});
				} else {
					closeAutocomplete();
				}
			});

			el.on("keydown", function(e) {
				if (e.keyCode === 38 || e.keyCode === 40 || e.keyCode === 13) {
					e.preventDefault();
					arrowKeyActions(e.keyCode);
				}
			})
		}

		initialize();
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