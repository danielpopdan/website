(function ($) {
  jQuery(document).ready(function($) {
    $("#menu-button").on('touchstart', function(event) {
      event.preventDefault();
      $(".region-nav").toggle(0);
    });
  }); 
}) (jQuery);