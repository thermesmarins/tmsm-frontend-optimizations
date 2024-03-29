(function( $ ) {
	'use strict';

  /**
   * Gravity Forms: replace form action when changing input field inside .form-action-replacement
   */
  var GravityFormsFormActionReplacement = function(){
    Array.prototype.forEach.call(document.querySelectorAll('.form-action-replacement input[type=radio], .form-action-replacement select'), function(input) {
      input.addEventListener('change', function(e){
        input.closest('form').action = input.value;
      });
    });
  }

  /**
   * Gravity Forms: Display form with conditional logic inside an Elementor modal
   */
  $(document).on('elementor/popup/show', function (event, popupId, popup) {
    $('.gform_wrapper', popup.$element).each(function(){
      const gformId = $(this).get(0).id.replace(/^gform_wrapper_/, '');
      if (!gformId) {
        return;
      }
      $(document).trigger('gform_post_render', [gformId, 1]);
    });
  });

  /**
   * Gravity Forms: Phone Masks
   * @constructor
   */
	var GravityFormsPhoneMask = function(){
    $('input[data-phoneformat=fr]').mask('YZ0000000000', {
      placeholder: '', translation: {
        'Y': {pattern: /\+/, optional: true},
        'Z': {pattern: /[0-9]/, optional: true}
      }
    });

    $('input[data-phoneformat=internationalvalidation]').mask('Y000000ZZZZZZZZ', {
      placeholder: '', translation: {
        'Y': {pattern: /\+/, optional: true},
        'Z': {pattern: /[0-9]/, optional: true}
      }
    });
  }

  /**
   * Gravity Forms: Date Masks
   * @constructor
   */
  var GravityFormsDateMask = function(){
    $('.ginput_container_date input').each(function(e){
      var placeholder_original = $(this).attr('placeholder');
      var placeholder = $(this).attr('placeholder');
      placeholder = placeholder.replace(/[dmyjma]/g,'9');
      $(this).mask(placeholder, {placeholder: placeholder_original});
    });
  }

  /**
   * Gravity Forms: Execute function on load and after form validation
   */
  $(document).on('gform_post_render', function(event, form_id, current_page){
    GravityFormsDateMask();
    GravityFormsPhoneMask();
    GravityFormsFormActionReplacement();
  });

  /*
   * Dialog Insight: Edit email address containing @guest.booking.com or email-inconnu.tm
   */

  const applyWhenElementExists = function(selector, myFunction, intervalTime) {
    var interval = setInterval(function () {
      if ($(selector).length > 0) {
        myFunction();
        clearInterval(interval);
      }
    }, intervalTime);
  }

  const dialogInsightRemoveUnwantedEmails = function () {
    applyWhenElementExists(".DialogInsightFormDiv", function () {
      $('.DialogInsightFormDiv input').each(function () {
        if ($(this).val().includes('@guest.booking.com')) {
          $(this).val('');
        }
        if ($(this).val().includes('@email-inconnu.tm')) {
          $(this).val('');
        }

      });
    }, 200);
  };

  $('.DialogInsightFormDiv input[type=email]').on('textInput paste change', function () {
    dialogInsightRemoveUnwantedEmails();
  });

  document.addEventListener('readystatechange', () => {
    dialogInsightRemoveUnwantedEmails();
  });


})( jQuery );
