function nytt_medlem() {
  document.body.style.cursor='wait';
  $("div#form p#feilmeldinger").empty();
  $("div#form label").css("color", "");
  var data = $('div#form form').serialize();
  var url = window.location.pathname.replace('bli_','');
  $.ajax({
     type: 'POST',
     url: url,
     data: data,
     success: function(response) {
       if('id' in response) {
         $('div#form form')[0].reset();
         $("div#result span#soknadsid").text(response.id);
         hide("div#form");
         show("div#result");
       } else if('feil' in response) {
         var feilmeldinger = $("div#form p#feilmeldinger");
         feilmeldinger.append("<p>Oi, her mangler det litt informasjon</p>");
         var ul = document.createElement("ul");
         feilmeldinger.append(ul);
         for(i=0, len=response.feil.length; i<len; ++i) {
           $(ul).append("<li>"+response.feil[i]+"</li>");
         }
         for(i=0, len=response.feilfelt.length; i<len; ++i) {
           $("div#form label[for="+response.feilfelt[i]+"]").css("color", "red");
         }
         grecaptcha.reset();
       }
       document.body.style.cursor='default';
     },
    error: function(xhr) {
      var feilkode = xhr.getResponseHeader('X-GT-Error');
      console.log(xhr.getAllResponseHeaders());
      if(feilkode == null || feilkode == "") {
        feilkode = "eplekake";
      }
      console.log(feilkode);
      $("div#feil span#feilkode").text(feilkode);
      hide("div#form");
      show("div#feil");
      document.body.style.cursor='default';
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
    $("a[href='#form']").each(function(index, element) {
      element.addEventListener("click", function() {
        show('div#form');
      })
    });
    $("div.popup").each(function(index, element) {
      element.addEventListener("click", function(ev) {
        if(ev.target.getAttribute("class") == "popup") {
          hide("div.popup");
        }
      })
    });
    $(document).keyup(function(e) {
      if (e.key === "Escape") { // escape key maps to keycode `27`
        hide('div.popup');
      }
    });
  });
})(jQuery);
