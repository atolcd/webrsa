//
// INFO:
// * si on veut avoir les valeurs exactes des select, on peut voir
//   pour les enlever / remettre avec des classes
// * les textes qu'on met dans la BDD pour les selects ne peuvent
//   pas comprendre ' - ' ... ou alors faire une variable
//
// - http://codylindley.com/Webdev/315/ie-hiding-option-elements-with-css-and-dealing-with-innerhtml
// - http://bytes.com/forum/thread92041.html
// - http://www.javascriptfr.com/codes/GERER-OPTGROUP-LISTE-DEROULANTE_36855.aspx
// - http://www.highdots.com/forums/alt-html/optgroup-optgroup-display-none-style-264456.html
//

//*****************************************************************

function dependantSelect( select2Id, select1Id ) {
	var isSelect1 = ( $( select1Id ) !== undefined && $( select1Id ).tagName.toUpperCase() == 'SELECT' );
	var isSelect2 = ( $( select2Id ) !== undefined && $( select2Id ).tagName.toUpperCase() == 'SELECT' );

	if( !isSelect1 || !isSelect2 ) {
		return;
	}

	var selects = new Array();
	var value2 = $F( select2Id );

	// Nettoyage du texte des options
//	$$('#' + select2Id + ' option').each( function ( option ) {
//		var data = $(option).innerHTML;
//		$(option).update( data.replace( new RegExp( '^.* - ', 'gi' ), '' ) );
//	} );

	// Sauvegarde
	if( selects[select2Id] == undefined ) {
		selects[select2Id] = new Array();
		selects[select2Id]['values'] = new Array();
		selects[select2Id]['options'] = new Array();
	}

	$$('#' + select2Id + ' option').each( function ( option ) {
		selects[select2Id]['values'].push( option.value );
		selects[select2Id]['options'].push( option.innerHTML );
	} );

	// Vidage de la liste
	var select1ValueRegexp = new RegExp( '^' + $F( select1Id ).replace( new RegExp( '^[^_]+_', 'gi' ), '' ) + '_', 'gi' );
	$$('#' + select2Id + ' option').each( function ( option ) {
		if( ( $(option).value != '' ) && ( ( $(option).value != '' ) && ( $( option ).value.match( select1ValueRegexp ) == null ) ) )
		$(option).remove();
	} );

	// Onchage event - Partie dynamique
	Event.observe( select1Id, 'change', function( event ) {
		$$('#' + select2Id + ' option').each( function ( option ) {
			$(option).remove();
		} );

		// INFO: pour les select dépendants en cascade
		var select1IdValue = $( select1Id ).value.replace( new RegExp( '^[^_]+_', 'gi' ), '' );
		var select1IdRegexp = new RegExp( '^' + select1IdValue + '_' );

		for( var i = 0 ; i < selects[select2Id]['values'].length ; i++ ) {
			if( selects[select2Id]['values'][i] == '' || selects[select2Id]['values'][i].match( select1IdRegexp, "g" ) ) {
				$(select2Id).insert( new Element( 'option', { 'value': selects[select2Id]['values'][i] } ).update( selects[select2Id]['options'][i] ) );
			}
		}

		var opt = $$('#' + select2Id + ' option');
		$( opt ).each( function ( option ) {
			if( $(option).value == value2 ) {
				$(option).selected = 'selected';
			}
		} );

		$( select2Id ).simulate( 'change' );
	} );
}
