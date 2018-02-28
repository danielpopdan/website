/**
 * @file
 * DrupalCampTransylvania matchHeight behaviors.
 */

(function ($, Drupal) {

  'use strict';

  /**
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.dctMatchHeightFunctionality = {
    attach: function () {
      var windowW = $(window).width();

      var matchHeight = function() {
        var sponsorCategory = $('.sponsors-categories--category');
        sponsorCategory.matchHeight();
      };

      matchHeight();
    }
  };

})(jQuery, Drupal);
