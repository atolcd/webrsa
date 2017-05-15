/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var Cake = Cake !== undefined ? Cake : {};
Cake.Validation = Cake.Validation !== undefined ? Cake.Validation : {};
Cake.Search = Cake.Search !== undefined ? Cake.Search : {};
Cake.Form = Cake.Form !== undefined ? Cake.Form : {};

Cake.Validation.date = function( input ) {
	var date = {'year': null, 'month': null, 'day': null};

	input.select( 'select' ).each( function( select ) {
		['year', 'month', 'day'].each( function(part) {
			var re;

			if( $(select).disabled === false ) {
				re = new RegExp( part );
				if( re.test( $(select).name ) && $(select).value !== '' ) {
					date[part] = $(select).value;
				}
			}
		} );
	} );

	var empty = date['year'] === null && date['month'] === null && date['day'] === null;
	var incomplete = date['year'] === null || date['month'] === null || date['day'] === null;

	return empty || incomplete || isNaN( Date.parse( date['year'] + '-' + date['month'] + '-' + date['day'] ) ) === false;
}

Cake.Form.inputError = function( input, message ) {
	var errors = input.getElementsBySelector( '.error-message' );
	if ( errors.length > 0 ) {
		errors.each( function( error ) {
			$(error).remove();
		} );
	}
	input.removeClassName('error');

	if( message !== undefined ) {
		input.addClassName('error');
		input.insert('<div class="error-message">' + message + '</div>');
	}
};

Cake.Search.validate = function( form ) {
	var success = true;

	$$( '#' + form.id + ' div.input.date' ).each( function( date ) {
		if( Cake.Validation.date( date ) === true ) {
			Cake.Form.inputError( date );
		}
		else {
			Cake.Form.inputError( date, 'Veuillez entrer une date valide.' );
			success = false;
		}
	} );

	return success;
};

Cake.Search.onSubmit = function(event) {
	var form = Event.element(event),
		success = Cake.Search.validate( form );

	if( success === false ) {
		$$( '#' + form.id + ' *[type=submit]', '#' + form.id + ' *[type=reset]' ).each( function( submit ) {
			try{
				submit.enable();
			} catch( err ) {
				submit.disabled = false;
			}
		} );

		Event.stop(event);
		Element.scrollTo(form.getElementsBySelector( '.error-message' )[0].up('div.input'));
	}

	return success;
};