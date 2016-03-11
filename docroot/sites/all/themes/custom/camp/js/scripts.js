(function ($) {
  jQuery(document).ready(function($) {
    $("#menu-button").on('touchstart', function(event) {
      event.preventDefault();
      $(".region-nav").toggle(0);
    });
    $("#nav li.expanded").on('touchstart', function(event) {
      if (event.target == this) {
        $(this).find(".menu").toggle(0);
      }
    });

    if ($("#sponsor-gauge").length > 0) {
      var percent = (parseInt($("#gauge-meter").data("value")) * 100) / parseInt($("#sponsor-gauge").data("value"));
      $("#gauge-meter").width(percent+'%');
     }
  }); 
}) (jQuery);