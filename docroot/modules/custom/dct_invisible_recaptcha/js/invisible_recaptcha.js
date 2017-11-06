/**
 * @file
 * Contains implementation for invisible recaptcha.
 */

// The id of the current form.
var submittedFormId = '';

// Calls the form submit after recaptcha validation.
function recaptcha_callback() {
    document.getElementById(submittedFormId).submit();
}

(function ($, Drupal) {

    'use strict';

    /**
     * Handles the submission of the form with the invisible reCaptcha.
     *
     * @type {Drupal~behavior}
     *
     * @prop {Drupal~behaviorAttach} attach
     *   Attaches the behavior for the invisible reCaptcha.
     */
    Drupal.behaviors.invisibleRecaptcha = {
        attach: function (context) {
            $('form', context).each(function () {
                var $form = $(this);

                if ($form.find('.g-recaptcha[data-size="invisible"]').length) {
                    $form.find('input[type="submit"]').click(function (e) {
                        e.preventDefault();
                        validateInvisibleCaptcha($form);
                    });
                }
            });

            /**
             * Triggers the reCaptcha to validate the form.
             *
             * @param {jQuery} $form
             *   The form object to be validated.
             */
            function validateInvisibleCaptcha($form) {
                submittedFormId = $form.attr('id');
                grecaptcha.execute();
            }
        }
    };
})(jQuery, Drupal);
