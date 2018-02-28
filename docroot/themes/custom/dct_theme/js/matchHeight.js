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

        var sponsorImage = $('.l-col .field--name-field-short-description');
        sponsorImage.matchHeight();
      };

      matchHeight();
    }
  };

})(jQuery, Drupal);
