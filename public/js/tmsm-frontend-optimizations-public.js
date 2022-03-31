(function( $ ) {
	'use strict';

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

  $(document).on('ready', function () {
    dialogInsightRemoveUnwantedEmails();
  });

  $('.DialogInsightFormDiv input[type=email]').on('textInput paste change', function () {
    dialogInsightRemoveUnwantedEmails();
  });


})( jQuery );
