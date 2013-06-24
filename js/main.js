$(document).ready(function() {


	//Borrowed from h5utils
	var addEvent = (function () {
	  if (document.addEventListener) {
	    return function (el, type, fn) {
	      if (el && el.nodeName || el === window) {
	        el.addEventListener(type, fn, false);
	      } else if (el && el.length) {
	        for (var i = 0; i < el.length; i++) {
	          addEvent(el[i], type, fn);
	        }
	      }
	    };
	  } else {
	    return function (el, type, fn) {
	      if (el && el.nodeName || el === window) {
	        el.attachEvent('on' + type, function () { return fn.call(el, window.event); });
	      } else if (el && el.length) {
	        for (var i = 0; i < el.length; i++) {
	          addEvent(el[i], type, fn);
	        }
	      }
	    };
	  }
	})();

	var tags = document.querySelectorAll(".tag"),
		target = document.getElementById("drop-target"),
		clone = null;

	for (var i = 0; i < tags.length; i+=1) {
		var tag = tags[i];

		addEvent(tag, "dragstart", function(e) {
			console.log("hi");
			e.dataTransfer.effectAllowed = 'copy';
			e.dataTransfer.setData('Text', this.id);
			clone = this.cloneNode(true);
		});
	}

	addEvent(target, "dragover", function(e){
		e.preventDefault();
		$("#drop-target").addClass("over");
	});

	addEvent(target, "dragleave", function(e) {
		e.preventDefault();
		$("#drop-target").removeClass("over");
	})

	addEvent(target, "drop", function(e) {
		e.preventDefault;
		console.log("dropped");
		target.appendChild(clone);
	});
});