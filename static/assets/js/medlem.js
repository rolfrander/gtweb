function nytt_medlem() {
  console.log($('div#form form').serialize());
  $.ajax({
     type: 'POST',
     url: "mysubmitpage.php",
     data: $('#addCommentForm').serialize(),
     success: function(response) {
        alert("Submitted comment");
         $("#commentList").append("Name:" + $("#name").val() + "<br/>comment:" + $("#body").val());
     },
    error: function() {
         //$("#commentList").append($("#name").val() + "<br/>" + $("#body").val());
        alert("There was an error submitting comment");
    }
  });
}

function show(selector) {
  $(selector).show();
}

function hide(selector) {
  $(selector).hide();
}

function poststed_event(ev, poststedid) {
  var pnr = ev.target.value;
  if(pnr && pnr.length === 4) {
    poststed(pnr, function(sted) {
      document.getElementById(poststedid).value = sted;
    })
  }
}

function poststed(pnr, callback) {
  $.ajax({
    type: "GET",
    url: 'https://api.bring.com/shippingguide/api/postalCode.json',
    data: {
      clientUrl: "http://www.godliatrasop.no",
      pnr: pnr
    },
    success: function(result) {
      callback(result.result);
    }
  })
}

(function($) {
	var	$window = $(window);
  var $body = $('body');

  $window.on('load', function() {
    console.log("load");
    $("a[href='#form']").each(function(index, element) {
      element.addEventListener("click", function() {
        element.innerHTML = "foo!";
      })
    })
  });
})(jQuery);
