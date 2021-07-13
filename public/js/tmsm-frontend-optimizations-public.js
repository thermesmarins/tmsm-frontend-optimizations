(function( $ ) {
	'use strict';

  $('input[data-phoneformat=fr]').mask('YZ0000000000', {placeholder: '', translation:  {
      'Y': {pattern: /\+/, optional: true},
      'Z': {pattern: /[0-9]/, optional: true}
    }});
  $('input[data-phoneformat=internationalvalidation]').mask('Y000000ZZZZZZZZ', {placeholder: '', translation:  {
      'Y': {pattern: /\+/, optional: true},
      'Z': {pattern: /[0-9]/, optional: true}
    }});

})( jQuery );
