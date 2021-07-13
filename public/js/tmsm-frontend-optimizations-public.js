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

})( jQuery );
