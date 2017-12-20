(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.dct_map = {
    attach: function (context, settings) {

      $(".mapcontainer").mapael({
        map: {
          name: "european_union",
          defaultArea : {
            attrs : {
              fill : "#200f17"
            },
            attrsHover : {
              fill: "#721139"
            }
          },
          defaultPlot: {
              text: {
              attrs: {
                fill: '#000'
              },
              attrsHover: {
                fill: '#000',
              }
            }
          },
          defaultLink: {
            factor: 0.3,
            attrs: {
              "stroke": "#fcb02a",
              "stroke-width": 1.5
            },
            attrsHover: {
              stroke: "#721139"
            }
          },
          zoom: {
            maxLevel:10
          }
        },
        plots:drupalSettings['connections-map']['plots'],
        links:drupalSettings['connections-map']['links'],
      });
    }
  }
})(jQuery, Drupal, drupalSettings);
