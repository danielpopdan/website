(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.dct_map = {
    attach: function (context, settings) {

      $(".mapcontainer").mapael({
        map: {
          name: "european_union",
          zoom: {
            enabled: true,
            maxLevel:10
          }
        },
        plots:drupalSettings['connections-map']['plots'],
        links:drupalSettings['connections-map']['links']
      });
    }
  }
})(jQuery, Drupal, drupalSettings);
