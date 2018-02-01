//-----------------------------------------------------------------------------

function make_folded_forms() {
	$$( 'form.folded' ).each( function( elmt ) {
//         var a = new Element( 'a', { 'class': 'toggler', 'href': '#', 'onclick' : '$( '' + $( elmt ).id + '' ).toggle(); return false;' } ).update( 'Visibilité formulaire' );
//         var p = a.wrap( 'p' );
//         $( elmt ).insert( { 'before' : p } );
		$( elmt ).hide();
	} );
}

//-----------------------------------------------------------------------------

function make_treemenus( absoluteBaseUrl, large, urlmenu ) {
	var dir = absoluteBaseUrl + 'img/icons';
	$$( '.treemenu li' ).each( function ( elmtLi ) {
		if( elmtLi.down( 'ul' ) ) {
			if( large ) {
				var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre', 'width': '12px'
				} );
			}
			else  {
				var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre' } );
			}
			var link = img.wrap( 'a', { 'href': '#', 'class' : 'toggler', 'onclick' : 'return false;' } );
			var sign = '+';

			$( link ).observe( 'click', function( event ) {
				var innerUl = $( this ).up( 'li' ).down( 'ul' );
				innerUl.toggle();
				if( innerUl.visible() ) {
					$( this ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
					$( this ).down( 'img' ).alt = 'Réduire';
				}
				else {
					$( this ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
					$( this ).down( 'img' ).alt = 'Étendre';
				}
				return false;
			} );

			$( elmtLi ).down( 1 ).insert( { 'before' : link } );
			$( elmtLi ).down( 'ul' ).hide();
		}
	} );

	var currentUrl = location.href.replace( absoluteBaseUrl, '/' ).replace( new RegExp( '^(https{0,1}://[^/]+/)' ), '/' ).replace( /#$/, '' );;
//	var relBaseUrl = absoluteBaseUrl.replace( new RegExp( '^(https{0,1}://[^/]+/)' ), '/' );

	var menuUl = $$( '.treemenu > ul' )[0];

	$$( '.treemenu a' ).each( function ( elmtA ) {
		// TODO: plus propre
		var elmtAUrl = elmtA.href.replace( absoluteBaseUrl, '/' ).replace( new RegExp( '^(https{0,1}://[^/]+/)' ), '/' );

		if(
			elmtAUrl == currentUrl
			|| elmtAUrl == currentUrl.replace( '/edit/', '/view/' )
			|| elmtAUrl == currentUrl.replace( '/add/', '/view/' )
			|| elmtAUrl == currentUrl.replace( '/add/', '/index/' )
			|| ( ( urlmenu !== null ) && ( elmtAUrl === urlmenu ) ) ) {
			$( elmtA ).addClassName( 'selected' );

			var ancestorsDone = false;
			elmtA.ancestors().each( function ( aAncestor ) {
				if( aAncestor == menuUl ) {
					ancestorsDone = true;
				}
				else if( !ancestorsDone ) {
					$( aAncestor ).addClassName( 'selected' );
					aAncestor.show();
					if( aAncestor.tagName == 'LI' ) {
						var toggler = aAncestor.down( 'a.toggler img' );
						if( toggler != undefined ) {
							toggler.src = dir + '/bullet_toggle_minus2.png';
							toggler.alt = 'Réduire';
						}
					}
				}
			} );

			// Montrer son descendant direct
			try {
				var upLi = elmtA.up( 'li' );
				if( upLi != undefined ) {
					var ul = upLi.down( 'ul' );
					if( ul != undefined ) {
						ul.show();
					}
				}
			}
			catch( e ) {
			}
		}
	} );
}

/// Fonction permettant "d'enrouler" le menu du dossier allocataire
function expandableTreeMenuContent( elmt, sign, dir ) {
	$( elmt ).up( 'ul' ).getElementsBySelector( 'li > a.toggler' ).each( function( elmtA ) {
		if( sign == 'plus' ) {
			elmtA.up( 'li' ).down( 'ul' ).show();
		}
		else {
			elmtA.up( 'li' ).down( 'ul' ).hide();
		}

		if( elmtA.down( 'img' ) != undefined ) {
			if( sign == 'plus' ) {
				elmtA.down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
				elmtA.down( 'img' ).alt = 'Réduire';
			}
			else {
				elmtA.down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
				elmtA.down( 'img' ).alt = 'Étendre';
			}
		}
	} );
}

/// Fonction permettant "de dérouler" le menu du dossier allocataire
function treeMenuExpandsAll( absoluteBaseUrl ) {

	var toggleLink = $( 'treemenuToggleLink' );
	var dir = absoluteBaseUrl + 'img/icons';

	var sign = $( toggleLink ).down( 'img' ).src.replace( new RegExp( '^.*(minus|plus).*' ), '$1' );

	$$( '.treemenu > ul > li > a.toggler' ).each( function ( elmtA ) {
		// Montrer tous les ancètres
		if( sign == 'plus' ) {
			elmtA.up( 'li' ).down( 'ul' ).show();
		}
		else {
			elmtA.up( 'li' ).down( 'ul' ).hide();
		}

		if( elmtA.down( 'img' ) != undefined ) {
			if( sign == 'plus' ) {
				elmtA.down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
				elmtA.down( 'img' ).alt = 'Réduire';
			}
			else {
				elmtA.down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
				elmtA.down( 'img' ).alt = 'Étendre';
			}
		}

		expandableTreeMenuContent( elmtA, sign, dir );
	} );

	if( sign == 'plus' ) {
		$( toggleLink ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
	}
	else {

		$( toggleLink ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
	}
}

//-----------------------------------------------------------------------------





// TODO: mettre avant les actions
// function make_table_tooltips() {
//     $$( 'table.tooltips' ).each( function ( elmtTable ) {
//         // FIXME: colspans dans le thead -> alert( $( this ).attr( 'colspan' ) );
//         var tooltipPositions = new Array();
//         var tooltipHeaders = new Array();
//         var actionPositions = new Array();
//
//         var iPosition = 0;
//         elmtTable.getElementsBySelector( 'thead tr th' ).each( function ( elmtTh ) {
//             var colspan = ( $( elmtTh ).readAttribute( 'colspan' ) != undefined ) ? $( elmtTh ).readAttribute( 'colspan' ) : 1;
//             if( elmtTh.hasClassName( 'tooltip' ) ) {
//                 elmtTh.remove();
//                 for( k = 0 ; k < colspan ; k++ )
//                     tooltipPositions.push( iPosition + k );
//                 tooltipHeaders[iPosition] = elmtTh.innerHTML;
//             }
//             if( elmtTh.hasClassName( 'action' ) ) {
//                 for( k = 0 ; k < colspan ; k++ )
//                     actionPositions.push( iPosition + k );
//             }
//             iPosition++;
//         } );
//
//         // FIXME
//         var th = new Element( 'th', { 'class': 'tooltip_table' } ).update( 'Informations complémentaires' );
//         $( elmtTable ).down( 'thead' ).down( 'tr' ).insert( { 'bottom' : th } );
//
//         elmtTable.getElementsBySelector( 'tbody tr' ).each( function ( elmtTbodyTr ) {
//             var tooltip_table = new Element( 'table', { 'class': 'tooltip' } );
//
//             var iPosition = 0;
//             elmtTbodyTr.getElementsBySelector( 'td' ).each( function ( elmtTbodyTd ) {
//                 if( tooltipPositions.include( iPosition ) ) {
//                     var tooltip_tr = new Element( 'tr', {} );
//                     var tooltip_th = new Element( 'th', {} ).update( tooltipHeaders[iPosition] );
//                     var tooltip_td = new Element( 'td', {} ).update( elmtTbodyTd.innerHTML );
//                     tooltip_tr.insert( { 'bottom' : tooltip_th } );
//                     tooltip_tr.insert( { 'bottom' : tooltip_td } );
//                     $( tooltip_table ).insert( { 'bottom' : tooltip_tr } );
//                     elmtTbodyTd.remove();
//                 }
//                 else if( actionPositions.include( iPosition ) ) {
//                     $( elmtTbodyTd ).addClassName( 'action' );
//                 }
//                 iPosition++;
//             } );
//
//             var tooltip_td = new Element( 'td', { 'class': 'tooltip_table' } );
//             $( tooltip_td ).insert( { 'bottom' : tooltip_table } );
//             $( elmtTbodyTr ).insert( { 'bottom' : tooltip_td } );
//         } );
//
//         elmtTable.getElementsBySelector( 'tbody tr td' ).each( function ( elmtTd ) {
//             // Mouse over
//             $( elmtTd ).observe( 'mouseover', function( event ) {
//                 if( !$( this ).hasClassName( 'action' ) ) {
//                     $( this ).up( 'tr' ).addClassName( 'hover' ); // INFO: IE6
//                     $( this ).up( 'tr' ).getElementsBySelector( 'td.tooltip_table' ).each( function ( tooltip_table ) {
//                         $( tooltip_table ).setStyle( {
//                             'left' : ( event.pointerX() + 5 ) + 'px',
//                             'top' : ( event.pointerY() + 5 ) + 'px',
//                             'display' : 'block'
//                         } );
//                     } );
//                 }
//             } );
//
//             // Mouse move
//             $( elmtTd ).observe( 'mousemove', function( event ) {
//                 if( !$( this ).hasClassName( 'action' ) ) {
//                     $( this ).up( 'tr' ).getElementsBySelector( 'td.tooltip_table' ).each( function ( tooltip_table ) {
//                         $( tooltip_table ).setStyle( {
//                             'left' : ( event.pointerX() + 5 ) + 'px',
//                             'top' : ( event.pointerY() + 5 ) + 'px'
//                         } );
//                     } );
//                 }
//             } );
//
//             // Mouse out
//             $( elmtTd ).observe( 'mouseout', function( event ) {
//                 if( !$( this ).hasClassName( 'action' ) ) {
//                     $( this ).up( 'tr' ).removeClassName( 'hover' ); // INFO: IE6
//                     $( this ).up( 'tr' ).getElementsBySelector( 'td.tooltip_table' ).each( function ( tooltip_table ) {
//                         $( tooltip_table ).setStyle( {
//                             'display' : 'none'
//                         } );
//                     } );
//                 }
//             } );
//         } );
//     } );
// }

//*****************************************************************************

function mkTooltipTables() {
	var tips = new Array();
	$$( 'table.tooltips' ).each( function( table ) {
		var actionPositions = new Array(),
			inputPositions = new Array(),
			realPosition = 0,
			headRows = $( table ).getElementsBySelector( 'thead tr' ),
			loop = 0;

		$( headRows ).each( function( headRow ) {
			loop++;
			$( headRow ).getElementsBySelector( 'th' ).each( function ( th ) {
				if( loop === headRows.length ) {
					var colspan = ( $( th ).readAttribute( 'colspan' ) != undefined ) ? $( th ).readAttribute( 'colspan' ) : 1;

					if( $( th ).hasClassName( 'action' ) ) {
						for( var k = 0 ; k < colspan ; k++ ) {
							actionPositions.push( realPosition + k );
						}
					}
					else if( $( th ).hasClassName( 'input' ) ) {
						for( var k = 0 ; k < colspan ; k++ ) {
							inputPositions.push( realPosition + k );
						}
					}
					realPosition = ( parseInt( realPosition ) + parseInt( colspan ) );
				}

				if( $( th ).hasClassName( 'innerTableHeader' ) ) {
					$( th ).addClassName( 'dynamic' );
				}
			} );
		} );

		var iPosition = 0;
		$( table ).getElementsBySelector( 'tbody tr' ).each( function( tr ) {
			if( $( tr ).up( '.innerTableCell' ) == undefined ) {
				$( tr ).addClassName( 'dynamic' );
				var jPosition = 0;
				$( tr ).childElements().each( function( td ) {
					if(
						( !actionPositions.include( jPosition ) && $(td).hasClassName('action') === false )
						&& ( !inputPositions.include( jPosition ) && $(td).hasClassName('input') === false )
					) {
						tips.push( new Tooltip( $( td ), 'innerTable' + $( table ).readAttribute( 'id' ) + iPosition ) );
					}
					jPosition++;
				} );
				iPosition++;
			}
		} );
	} );
}

//*****************************************************************************

function disableFieldsOnCheckbox( cbId, fieldsIds, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	var cb = $( cbId );
	var checked = ( ( $F( cb ) == null ) ? false : true );

	fieldsIds.each( function ( fieldId ) {
		var field = $( fieldId );
		if( field != null ) {
			if( checked != condition ) {
				field.enable();
				//ajout
				if( toggleVisibility ) {
					field.show();
				}
				//fin ajout
				var div = field.up( 'div.input' );
				if( !div ) {
					field.up( 'div.checkbox' );
				}

				if( div ) {
					div.removeClassName( 'disabled' );
					if( toggleVisibility ) {
						div.show();
					}
				}
			}
			else {
				field.disable();
				//ajout
				if( toggleVisibility ) {
					field.hide();
				}
				//fin ajout
				var div = field.up( 'div.input' );
				if( !div ) {
					field.up( 'div.checkbox' );
				}

				if( div ) {
					div.addClassName( 'disabled' );
					if( toggleVisibility ) {
						div.hide();
					}
				}
			}
		}
	} );
}

//-----------------------------------------------------------------------------

function observeDisableFieldsOnCheckbox( cbId, fieldsIds, condition, toggleVisibility ) {
        toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;
	disableFieldsOnCheckbox( cbId, fieldsIds, condition, toggleVisibility );

	var cb = $( cbId );
	$( cb ).observe( 'click', function( event ) { // FIXME change ?
		disableFieldsOnCheckbox( cbId, fieldsIds, condition, toggleVisibility );
	} );
}

//*****************************************************************************

function disableFieldsOnValue( selectId, fieldsIds, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	if( ( typeof value ) != 'object' ) {
		value = [ value ];
	}

	var select = $( selectId );

	var result = false;
	value.each( function( elmt ) {
		if( $F( select ) == elmt ) {
			result = true;
		}
	} );

	fieldsIds.each( function ( fieldId ) {
		var field = $( fieldId );
		if( field != null ) {
			if( result == condition ) {

				field.disable();

				if( input = field.up( 'div.input' ) )
					input.addClassName( 'disabled' );
				else if( input = field.up( 'div.checkbox' ) )
					input.addClassName( 'disabled' );

				if( toggleVisibility ) {
					input.hide();
				}
			}
			else {
   				field.enable();

				if( input = field.up( 'div.input' ) )
					input.removeClassName( 'disabled' );
				else if( input = field.up( 'div.checkbox' ) )
					input.removeClassName( 'disabled' );

				if( toggleVisibility ) {
					input.show();
				}
			}
		}
	} );
}
//----------------------------------------------------------------------------

function observeDisableFieldsOnValue( selectId, fieldsIds, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	disableFieldsOnValue( selectId, fieldsIds, value, condition, toggleVisibility );

	$( selectId ).observe( 'change', function( event ) {
		disableFieldsOnValue( selectId, fieldsIds, value, condition, toggleVisibility );
	} );
}

//*****************************************************************************

function disableFieldsetOnValue( selectId, fieldsetId, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	if( ( typeof value ) != 'object' ) {
		value = [ value ];
	}

	var select = $( selectId );

	var result = false;
	value.each( function( elmt ) {
		if( $F( select ) == elmt ) {
			result = true;
		}
	} );

	var fieldset = $( fieldsetId );

	if( result ) {
		fieldset.removeClassName( 'disabled' );
		if( toggleVisibility ) {
			fieldset.show();
		}
		$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
			elmt.removeClassName( 'disabled' );
		} );
		$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
			// INFO: elmt.enable() ne fonctionne pas avec des button
			try{
				elmt.enable();
			} catch( err ) {
				elmt.disabled = false;
			}

		} );
	}
	else {
		fieldset.addClassName( 'disabled' );
		if( toggleVisibility ) {
			fieldset.hide();
		}
		$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
			elmt.addClassName( 'disabled' );
		} );
		$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
			// INFO: elmt.disable() ne fonctionne pas avec des button
			try{
				elmt.disable();
			} catch( err ) {
				elmt.disabled = true;
			}
		} );
	}

}

//----------------------------------------------------------------------------

function observeDisableFieldsetOnValue( selectId, fieldsetId, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	disableFieldsetOnValue( selectId, fieldsetId, value, condition, toggleVisibility );

	var select = $( selectId );
	$( select ).observe( 'change', function( event ) {
		disableFieldsetOnValue( selectId, fieldsetId, value, condition, toggleVisibility );
	} );
}

//*****************************************************************************

function disableFieldsetOnCheckbox( cbId, fieldsetId, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	var cb = $( cbId );
	var checked = ( ( $F( cb ) == null ) ? false : true );
	var fieldset = $( fieldsetId );

	if( checked != condition ) {
		fieldset.removeClassName( 'disabled' );
		if( toggleVisibility ) {
			fieldset.show();
		}
		$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
			elmt.removeClassName( 'disabled' );
		} );
		$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
			// INFO: elmt.disable() ne fonctionne pas avec des button
			try{
				elmt.enable();
			} catch( err ) {
				elmt.disabled = false;
			}
		} );
	}
	else {
		fieldset.addClassName( 'disabled' );
		if( toggleVisibility ) {
			fieldset.hide();
		}
		$( fieldset ).getElementsBySelector( 'div.input', 'div.checkbox' ).each( function( elmt ) {
			elmt.addClassName( 'disabled' );
		} );
		$( fieldset ).getElementsBySelector( 'input', 'select', 'button', 'textarea' ).each( function( elmt ) {
			// INFO: elmt.enable() ne fonctionne pas avec des button
			try{
				elmt.disable();
			} catch( err ) {
				elmt.disabled = true;
			}
		} );
	}
}

//-----------------------------------------------------------------------------

function observeDisableFieldsetOnCheckbox( cbId, fieldsetId, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	disableFieldsetOnCheckbox( cbId, fieldsetId, condition, toggleVisibility );

	var cb = $( cbId );
	$( cb ).observe( 'click', function( event ) { // FIXME change ?
		disableFieldsetOnCheckbox( cbId, fieldsetId, condition, toggleVisibility );
	} );
}

//*****************************************************************************
// @deprecated
function disableFieldsOnBoolean( field, fieldsIds, value, condition ) {
	var disabled = !( ( $F( field ) == value ) == condition );
	fieldsIds.each( function ( fieldId ) {
		var field = $( fieldId );
		if( !disabled ) {
			field.enable();
			if( input = field.up( 'div.input' ) )
				input.removeClassName( 'disabled' );
			else if( input = field.up( 'div.checkbox' ) )
				input.removeClassName( 'disabled' );
		}
		else {
			field.disable();
			if( input = field.up( 'div.input' ) )
				input.addClassName( 'disabled' );
			else if( input = field.up( 'div.checkbox' ) )
				input.addClassName( 'disabled' );
		}
	} );
}

//-----------------------------------------------------------------------------
// @deprecated
function observeDisableFieldsOnBoolean( prefix, fieldsIds, value, condition ) {
	if( value == '1' ) {
		var otherValue = '0';
		disableFieldsOnBoolean( prefix + otherValue, fieldsIds, otherValue, !condition );
	}
	else {
		var otherValue = '1';
		disableFieldsOnBoolean( prefix + value, fieldsIds, value, condition );
	}

	$( prefix + value ).observe( 'click', function( event ) {
		disableFieldsOnBoolean( prefix + value, fieldsIds, value, condition );
	} );

	$( prefix + otherValue ).observe( 'click', function( event ) {
		disableFieldsOnBoolean( prefix + otherValue, fieldsIds, otherValue, !condition );
	} );
}

//-----------------------------------------------------------------------------

function setDateInterval( masterPrefix, slavePrefix, nMonths, firstDay ) {
	// Initialisation
	var d = new Date();
	d.setDate( 1 );
	d.setMonth( $F( masterPrefix + 'Month' ) - 1 );
	d.setYear( $F( masterPrefix + 'Year' ) );

	// Ajout de trois mois, et retour au derenier jour du mois précédent
	d.setDate( d.getDate() + ( nMonths * 31 ) );
	d.setDate( 1 );
	if( !firstDay ) {
		d.setDate( d.getDate() - 1 );
	}

	// Assignation
	var day = d.getDate();
	$( slavePrefix + 'Day' ).value = ( day < 10 ) ? '0' + day : day;
	var month = d.getMonth() + 1;
	$( slavePrefix + 'Month' ).value = ( month < 10 ) ? '0' + month : month;
	$( slavePrefix + 'Year' ).value = d.getFullYear();
}

//-----------------------------------------------------------------------------

function setDateInterval2( masterPrefix, slavePrefix, nMonths, firstDay ) {
	// Initialisation
	var d = new Date();
	d.setDate( 1 );
	d.setMonth( $F( masterPrefix + 'Month' ) ); //FIXME: suppression du -1 afin d'obtenir le nombre de mois exact
	d.setYear( $F( masterPrefix + 'Year' ) );

	// Ajout de trois mois, et retour au dernier jour du mois précédent
	d.setDate( d.getDate() + ( nMonths * 31 ) );
	d.setDate( 1 );
	if( !firstDay ) {
		d.setDate( d.getDate() - 1 );
	}


	// Assignation
	var day = d.getDate();
	$( slavePrefix + 'Day' ).value =  $( masterPrefix + 'Day' ).value;//( day < 10 ) ? '0' + day : day;
	var month = d.getMonth() + 1;
	$( slavePrefix + 'Month' ).value = ( month < 10 ) ? '0' + month : month;
	$( slavePrefix + 'Year' ).value = d.getFullYear();

	// Calcul du dernier jour du mois
	var slaveDate = new Date();
	slaveDate.setDate( 1 );
	slaveDate.setMonth( $( slavePrefix + 'Month' ).value ); // FIXME ?
	slaveDate.setYear( $( slavePrefix + 'Year' ).value );
	slaveDate.setDate( slaveDate.getDate() - 1 );
	if( slaveDate.getDate() < $( slavePrefix + 'Day' ).value ) {
		$( slavePrefix + 'Day' ).value = slaveDate.getDate();
	}
}

function setDateIntervalCer( masterPrefix, slavePrefix, nMonths, firstDay ) {
	// -------------------------------------------------------------------------
	// Initialisation
	var d = new Date();
	d.setYear( parseInt( $F( masterPrefix + 'Year' ), 10 ) );
	d.setMonth( parseInt( $F( masterPrefix + 'Month' ), 10 ) - 1 );
	d.setDate( parseInt( $F( masterPrefix + 'Day' ), 10 ) );
	// -------------------------------------------------------------------------
	// Calcul de la nouvelle date: nombre de mois en plus, 1 jour en moins
	d.setMonth( d.getMonth() + parseInt( nMonths, 10 ) );
	d.setDate( d.getDate() - 1 );
	// -------------------------------------------------------------------------
	// Assignation
	var day = d.getDate();
	$( slavePrefix + 'Year' ).value = d.getFullYear();

	var month = d.getMonth() + 1;
	$( slavePrefix + 'Month' ).value = ( month < 10 ) ? '0' + month : month;

	$( slavePrefix + 'Day' ).value = ( day < 10 ) ? '0' + day : day;
}


function setNbDayInterval( masterPrefix, slavePrefix, nDays ) {
	// Initialisation
	var d = new Date();
	d.setDate( 1 );
	d.setMonth( $F( masterPrefix + 'Month' ) - 1 );
	d.setYear( $F( masterPrefix + 'Year' ) );

	// Ajout de trois mois, et retour au derenier jour du mois précédent
	d.setDate( d.getDate() + ( nMonths * 31 ) );
	d.setDate( 1 );
	if( !firstDay ) {
		d.setDate( d.getDate() - 1 );
	}

	// Assignation
	var day = d.getDate();
	$( slavePrefix + 'Day' ).value = ( day < 10 ) ? '0' + day : day;
	var month = d.getMonth() + 1;
	$( slavePrefix + 'Month' ).value = ( month < 10 ) ? '0' + month : month;
	$( slavePrefix + 'Year' ).value = d.getFullYear();
}

//==============================================================================

function disableFieldsOnRadioValue( form, radioName, fieldsIds, value, condition, toggleVisibility ) {
	if( ( typeof value ) != 'object' ) {
		value = [ value ];
	}
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;
	var v = $( form ).getInputs( 'radio', radioName );

	var currentValue = undefined;
	$( v ).each( function( radio ) {
		if( radio.checked ) {
			currentValue = radio.value;
		}
	} );

	var disabled = false;
	value.each( function( elmt ) {
		if( !( ( currentValue == elmt ) == condition ) ) {
			disabled = true;
		}
	} );

	//var disabled = !( ( currentValue == value ) == condition );

	fieldsIds.each( function ( fieldId ) {
		var field = $( fieldId );
		if( !disabled ) {


			field.enable();

			if( input = field.up( 'div.input' ) )
				input.removeClassName( 'disabled' );
			else if( input = field.up( 'div.checkbox' ) )
				input.removeClassName( 'disabled' );

			//Ajout suite aux modifs ds les traitements PDOs
			if( toggleVisibility ) {
				input.show();
			}
		}
		else {

			field.disable();


			if( input = field.up( 'div.input' ) )
				input.addClassName( 'disabled' );
			else if( input = field.up( 'div.checkbox' ) )
				input.addClassName( 'disabled' );

			//Ajout suite aux modifs ds les traitements PDOs
			if( toggleVisibility ) {
				input.hide();
			}
		}
	} );
}

//-----------------------------------------------------------------------------

function observeDisableFieldsOnRadioValue( form, radioName, fieldsIds, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;
	disableFieldsOnRadioValue( form, radioName, fieldsIds, value, condition, toggleVisibility );

	var v = $( form ).getInputs( 'radio', radioName );
	var currentValue = undefined;
	$( v ).each( function( radio ) {
		$( radio ).observe( 'change', function( event ) {
			disableFieldsOnRadioValue( form, radioName, fieldsIds, value, condition, toggleVisibility );
		} );
	} );
}


//*****************************************************************************

function disableFieldsetOnRadioValue( form, radioName, fieldsetId, value, condition, toggleVisibility ) {
	if( ( typeof value ) != 'object' ) {
		value = [ value ];
	}

	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	var v = $( form ).getInputs( 'radio', radioName );

	var fieldset = $( fieldsetId );

	if( fieldset != null ) {
		var currentValue = undefined;

		$( v ).each( function( radio ) {
			if( radio.checked ) {
				currentValue = radio.value;
			}
		} );

		var disabled = false;
		value.each( function( elmt ) {
			if( !( ( currentValue == elmt ) == condition ) ) {
				disabled = true;
			}
		} );

		if( disabled != condition ) {
			fieldset.removeClassName( 'disabled' );
			if( toggleVisibility ) {
				fieldset.show();
			}

			$$( '#'+fieldset.id+' div.input, #'+fieldset.id+' radio' ).each( function( elmt ) {
				elmt.removeClassName( 'disabled' );
			} );

			$$( '#'+fieldset.id+' input, #'+fieldset.id+' select, #'+fieldset.id+' button, #'+fieldset.id+' textarea' ).each( function( elmt ) {
				// INFO: elmt.enable() ne fonctionne pas avec des button
				try{
					elmt.enable();
				} catch( err ) {
					elmt.disabled = false;
				}
			} );
		}
		else {
			fieldset.addClassName( 'disabled' );
			if( toggleVisibility ) {
				fieldset.hide();
			}

			$$( '#'+fieldset.id+' div.input, #'+fieldset.id+' radio' ).each( function( elmt ) {
				elmt.addClassName( 'disabled' );
			} );

			$$( '#'+fieldset.id+' input, #'+fieldset.id+' select, #'+fieldset.id+' button, #'+fieldset.id+' textarea' ).each( function( elmt ) {
				// INFO: elmt.disable() ne fonctionne pas avec des button
				try{
					elmt.disable();
				} catch( err ) {
					elmt.disabled = true;
				}
			} );
		}
	}
}

//-----------------------------------------------------------------------------

function observeDisableFieldsetOnRadioValue( form, radioName, fieldsetId, value, condition, toggleVisibility ) {
	toggleVisibility = typeof(toggleVisibility) != 'undefined' ? toggleVisibility : false;

	disableFieldsetOnRadioValue( form, radioName, fieldsetId, value, condition, toggleVisibility );

	var v = $( form ).getInputs( 'radio', radioName );

	var currentValue = undefined;

	$( v ).each( function( radio ) {
		$( radio ).observe( 'change', function( event ) {
			disableFieldsetOnRadioValue( form, radioName, fieldsetId, value, condition, toggleVisibility );
		} );
	} );
}

//-----------------------------------------------------------------------------

function makeTabbed( wrapperId, titleLevel ) {
	var ul = new Element( 'ul', { 'class' : 'ui-tabs-nav' } );
	$$( '#' + wrapperId + ' h' + titleLevel + '.title' ).each( function( title ) {
		var parent = title.up();
		var classNames = $( title ).readAttribute( 'class' ).replace( /title/, 'tab' );
		var link = new Element( 'a', { href: '#' + parent.id } ).update( title.innerHTML );

		var titleAttr = $( title ).readAttribute( 'title' );
		if( titleAttr !== null && titleAttr !== '' ) {
			$( link ).writeAttribute( 'title', titleAttr );
		}

		var li = new Element( 'li', { 'class' : classNames } ).update( link );
		ul.appendChild( li );
		parent.addClassName( 'tab' );
		title.addClassName( 'tab hidden' );
	} );

	$( wrapperId ).insert( { 'before' : ul } );

	new Control.Tabs( ul );
}

//-----------------------------------------------------------------------------

function make_treemenus_droits( absoluteBaseUrl, large ) {
	var dir = absoluteBaseUrl + 'img/icons';

	$$( '#tableEditDroits tr.niveau0 td.label' ).each( function ( elmtTd ) {
		if( elmtTd.up( 'tr' ).next( 'tr' ).hasClassName('niveau1')) {

			var thisTr = $( elmtTd ).up( 'tr' );
			var nextTr = $( thisTr ).next( 'tr' );
			var value = 2;
			var etat = 'fermer';
			while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
				var checkboxes = $( nextTr ).getElementsBySelector( 'input[type=checkbox]' );
				if ( value == 2) { value = $F( checkboxes[0] ); }
				else if ( value != $F( checkboxes[0] )) { etat = 'ouvert'; }
				nextTr = $( nextTr ).next( 'tr' );
			}

			if( etat == 'fermer' ) {
				if( large )
					var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre', 'width': '12px' } );
				else
					var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre' } );

				nextTr = $( thisTr ).next( 'tr' );
				while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
					nextTr.hide();
					nextTr = $( nextTr ).next( 'tr' );
				}
			}
			else {
				if( large )
					var img = new Element( 'img', { 'src': dir + '/bullet_toggle_minus2.png', 'alt': 'Réduire', 'width': '12px' } );
				else
					var img = new Element( 'img', { 'src': dir + '/bullet_toggle_minus2.png', 'alt': 'Réduire' } );
			}

			// INFO: onclick -> return false est indispensable.
			var link = img.wrap( 'a', { 'href': '#', 'class' : 'toggler', 'onclick' : 'return false;' } );

			$( link ).observe( 'click', function( event ) {
				var nextTr = $( this ).up( 'td' ).up( 'tr' ).next( 'tr' );

				while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
					nextTr.toggle();

					if( nextTr.visible() ) {
						$( this ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
						$( this ).down( 'img' ).alt = 'Réduire';
					}
					else {
						$( this ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
						$( this ).down( 'img' ).alt = 'Étendre';
					}

					nextTr = $( nextTr ).next( 'tr' );
				}
			} );

			$( elmtTd ).insert( { 'top' : link } );
		}
	} );

	var tabledroit = $$( '#tableEditDroits' ).each(function (elmt) {
		if( large )
			var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre', 'width': '12px' } );
		else
			var img = new Element( 'img', { 'src': dir + '/bullet_toggle_plus2.png', 'alt': 'Étendre' } );

		var biglink = img.wrap( 'a', { 'href': '#', 'class' : 'toggler', 'onclick' : 'return false;' } );

		$( biglink ).observe( 'click', function( event ) {
			$$( '#tableEditDroits tr.niveau0 td.label' ).each( function ( elmtTd ) {
				if( elmtTd.up( 'tr' ).next( 'tr' ).hasClassName('niveau1')) {
					var nextTr = $( elmtTd ).up( 'tr' ).next( 'tr' );

					while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
						if( $( elmt ).down( 'img' ).alt == 'Étendre' ) {
							$( elmtTd ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
							$( elmtTd ).down( 'img' ).alt = 'Réduire';
							nextTr.show();
						}
						else {
							$( elmtTd ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
							$( elmtTd ).down( 'img' ).alt = 'Étendre';
							nextTr.hide();
						}

						nextTr = $( nextTr ).next( 'tr' );
					}
				}
			} );
			if( $( elmt ).down( 'img' ).alt == 'Étendre' ) {
				$( elmt ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
				$( elmt ).down( 'img' ).alt = 'Réduire';
			}
			else {
				$( elmt ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
				$( elmt ).down( 'img' ).alt = 'Étendre';
			}
		} );

		$( elmt ).insert( { 'top' : biglink } );
	});

}

function OpenTree(action, absoluteBaseUrl, large) {
	var dir = absoluteBaseUrl + 'img/icons';
	$$( '#tableEditDroits tr.niveau0 td.label' ).each( function ( elmtTd ) {
		if( elmtTd.up( 'tr' ).next( 'tr' ).hasClassName('niveau1')) {
			var thisTr = $( elmtTd ).up( 'tr' );
			if( action == 'open' ) {
				$( elmtTd ).down( 'a' ).down( 'img' ).src = dir + '/bullet_toggle_minus2.png';
				$( elmtTd ).down( 'a' ).down( 'img' ).alt = 'Réduire';
				var nextTr = $( thisTr ).next( 'tr' );
				while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
					nextTr.show();
					nextTr = $( nextTr ).next( 'tr' );
				}
			}
			else {
				$( elmtTd ).down( 'a' ).down( 'img' ).src = dir + '/bullet_toggle_plus2.png';
				$( elmtTd ).down( 'a' ).down( 'img' ).alt = 'Étendre';
				var nextTr = $( thisTr ).next( 'tr' );
				while( nextTr != undefined && Element.hasClassName( nextTr, 'niveau1' ) ) {
					nextTr.hide();
					nextTr = $( nextTr ).next( 'tr' );
				}
			}
		}
	} );
}

// Fonction non-prototype commune

function printit(){
	if (window.print) {
		window.print() ;
	} else {
		var WebBrowser = '<object id="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>';
		document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
		WebBrowser1.ExecWB(6, 2);//Use a 1 vs. a 2 for a prompting dialog box    WebBrowser1.outerHTML = "";
	}
}



/*
*   Title :     charcount.js
*   Author :        Terri Ann Swallow
*   URL :       http://www.ninedays.org/
*   Project :       Ninedays Blog
*   Copyright:      (c) 2008 Sam Stephenson
*               This script is is freely distributable under the terms of an MIT-style license.
*   Description :   Functions in relation to limiting and displaying the number of characters allowed in a textarea
*   Version:        2.1
*   Changes:        Added overage override.  Read blog for updates: http://blog.ninedays.org/2008/01/17/limit-characters-in-a-textarea-with-prototype/
*   Created :       1/17/2008 - January 17, 2008
*   Modified :      5/20/2008 - May 20, 2008
*
*   Functions:      init()                      Function called when the window loads to initiate and apply character counting capabilities to select textareas
*   charCounter(id, maxlimit, limited)  Function that counts the number of characters, alters the display number and the calss applied to the display number
*   makeItCount(id, maxsize, limited)   Function called in the init() function, sets the listeners on teh textarea nd instantiates the feedback display number if it does not exist
*/

function textareaCharCounter(id, maxlimit, limited){
	if (!$('counter-'+id)){
		$(id).insert({after: '<div id="counter-'+id+'"></div>'});
		}
	if($F(id).length >= maxlimit){
		if(limited){    $(id).value = $F(id).substring(0, maxlimit); }
		$('counter-'+id).addClassName('charcount-limit');
		$('counter-'+id).removeClassName('charcount-safe');
	} else {
		$('counter-'+id).removeClassName('charcount-limit');
		$('counter-'+id).addClassName('charcount-safe');
	}
	$('counter-'+id).update( $F(id).length + '/' + maxlimit );
}

function textareaMakeItCount(textareaId, maxsize, limited){
	if(limited == null) limited = true;
	if ($(textareaId)){
		Event.observe($(textareaId), 'keyup', function(){textareaCharCounter(textareaId, maxsize, limited);}, false);
		Event.observe($(textareaId), 'keydown', function(){textareaCharCounter(textareaId, maxsize, limited);}, false);
		textareaCharCounter(textareaId,maxsize,limited);
	}
}

// http://jehiah.cz/a/firing-javascript-events-properly
function fireEvent(element,event) {
	if (document.createEventObject) {// dispatch for IE
		var evt = document.createEventObject();
		return element.fireEvent('on'+event,evt)
	}
	else { // dispatch for firefox + others
		var evt = document.createEvent("HTMLEvents");
		evt.initEvent(event, true, true ); // event type,bubbling,cancelable
		return !element.dispatchEvent(evt);
	}
}

/**
 * Vérifie si une valeur existe dans un array.
 *
 * @url http://snippets.dzone.com/posts/show/4653
 *
 * @param {String|Number} p_val La valeur à rechercher
 * @param {Array} p_array L'array dans laquelle rechercher la valeur
 * @param {Boolean} p_suffix true s'il faut rechercher en tant que suffixe
 *	(false par défaut)
 * @returns {Boolean}
 */
function in_array(p_val, p_array, p_suffix) {
	var re = new RegExp( '_' + p_val + '$' );
	p_suffix = ( typeof p_suffix != 'undefined' ) ? p_suffix : false;

	for(var i = 0, l = p_array.length; i < l; i++) {
		if(p_array[i] == p_val || ( p_suffix && String(p_array[i]).match(re) ) ) {
			return true;
		}
	}
	return false;
}

/**
* Fonction pour la visualisation des décisions des EPs (app/views/commissionseps/decisionXXXX.ctp)
*
* @param string idColumnToChangeColspan L'id de la colonne qui s'étendra sur les colonnes à masquer
* @param string decision La décision courante
* @param integer colspanMax Le nombre de colonnes à masquer
* @param array idsNonRaisonpassage Les ids des colonnes à masquer
* @param array decisionsHide Les valeurs de decision entraînant un masquage
*/

function changeColspanViewInfosEps( idColumnToChangeColspan, decision, colspanMax, idsNonRaisonpassage, decisionsHide ) {
	decisionsHide = typeof(decisionsHide) != 'undefined' ? decisionsHide : [ 'reporte', 'annule', 'maintienref', 'refuse', 'suspensionnonrespect', 'suspensiondefaut'/*, 'maintien'*/ ];

	if ( in_array( decision, decisionsHide ) ) {
		$( idColumnToChangeColspan ).writeAttribute( "colspan", colspanMax );
		idsNonRaisonpassage.each( function ( id ) {
			$( id ).hide();
		});
	}
}

/*
* Fonction pour afficher/masquer les champs de décision complémentaires pour les EPs (app/views/commissionseps/traiterXXXX.ctp)
*/

function changeColspanFormAnnuleReporteEps( idColumnToChangeColspan, colspanMax, decision, idsNonRaisonpassage ) {
	if ( $(idColumnToChangeColspan) === null ) {
		throw idColumnToChangeColspan + " element not found!";
	}

	if ( $F( decision ) == 'reporte' || $F( decision ) == 'annule' ) {
		$( idColumnToChangeColspan ).writeAttribute( "colspan", colspanMax );
		idsNonRaisonpassage.each( function ( id ) {
			$( id ).disable().up(1).hide();
		});
	}
	else {
		$( idColumnToChangeColspan ).writeAttribute( "colspan", 1 );
		idsNonRaisonpassage.each( function ( id ) {
			$( id ).enable().up(1).show();
		});
	}
}

/**
* Permet de cocher un ensemble de cases à cocher.
* @param selecteur Le sélecteur CSS pour obtenir les cases à cocher (default: input[type="checkbox"])
* @param simulate Lorsqu'il est à true, permet de simuler l'action de click (default: false)
*/

function toutCocher( selecteur, simulate ) {
	if( selecteur == undefined ) {
		selecteur = 'input[type="checkbox"]';
	}

	$$( selecteur ).each( function( checkbox ) {
		if( simulate != true ) {
			$( checkbox ).checked = true;
		}
		else if( $( checkbox ).checked != true ) {
			$( checkbox ).simulate( 'click' );
		}
	} );

	return false;
}

/**
* Permet de décocher un ensemble de cases à cocher.
* @param selecteur Le sélecteur CSS pour obtenir les cases à cocher (default: input[type="checkbox"])
* @param simulate Lorsqu'il est à true, permet de simuler l'action de click (default: false)
*/

function toutDecocher( selecteur, simulate ) {
	if( selecteur == undefined ) {
		selecteur = 'input[type="checkbox"]';
	}

	$$( selecteur ).each( function( checkbox ) {
		if( simulate != true ) {
			$( checkbox ).checked = false;
		}
		else if( $( checkbox ).checked != false ) {
			$( checkbox ).simulate( 'click' );
		}
	} );

	return false;
}

/**
* Ajout les boutons "Tout cocher" et "Tout décocher" en haut d'un élément.
*
* @param elmt L'élément en haut duquel les bouton seront ajoutés
* @param selecteur Le sélecteur CSS pour obtenir les cases à cocher (default: input[type="checkbox"])
*/
function insertButtonsCocherDecocher( elmt, selecteur ) {
	elmt = $(elmt);

	if( undefined !== elmt ) {
		if( selecteur == undefined ) {
			selecteur = 'input[type="checkbox"]';
		}

		elmt.insert( {
			top: new Element(
				'button', {
					type: 'button',
					onclick: "return toutCocher( '" + selecteur + "' );"
				} ).update( 'Tout cocher' ).outerHTML
				+ new Element(
				'button', {
					type: 'button',
					onclick: "return toutDecocher( '" + selecteur + "' );"
				} ).update( 'Tout décocher' ).outerHTML
		} );
	}
}

/**
 * Active et affiche une partie d'un formulaire contenu dans une balise
 */

function enableAndShowFormPart( formpartid ) {
	$( formpartid ).removeClassName( 'disabled' );
	$( formpartid ).show();

	$( formpartid ).getElementsBySelector( 'div.input' ).each( function( elmt ) {
		$( elmt ).removeClassName( 'disabled' );
	} );

	$( formpartid ).getElementsBySelector( 'input', 'select', 'button', 'textarea', 'radio' ).each( function( elmt ) {
		// INFO: elmt.enable() ne fonctionne pas avec des button
		try{
			elmt.enable();
		} catch( err ) {
			elmt.disabled = false;
		}
	} );
}

/**
 * Désactive et cache une partie d'un formulaire contenu dans une balise
 */

function disableAndHideFormPart( formpartid ) {
	$( formpartid ).addClassName( 'disabled' );
	$( formpartid ).hide();

	$( formpartid ).getElementsBySelector( 'div.input' ).each( function( elmt ) {
		$( elmt ).addClassName( 'disabled' );
	} );

	$( formpartid ).getElementsBySelector( 'input', 'select', 'button', 'textarea', 'radio' ).each( function( elmt ) {
		// INFO: elmt.disable() ne fonctionne pas avec des button
		try{
			elmt.disable();
		} catch( err ) {
			elmt.disabled = true;
		}
	} );
}

/**
 * Marque les li correspondant aux onglets en erreur (classe error) lorsqu'ils
 * comportent une balise en erreur.
 */
function makeErrorTabs() {
	$$( '.error' ).each( function( elmt ) {
		$(elmt).ancestors().each( function( ancestor ) {
			if( $(ancestor).hasClassName( 'tab' ) ) {
				$$( 'a[href=#' + $(ancestor).readAttribute( 'id' ) + ']' ).each( function( tabLink ) {
					$(tabLink).up( 'li' ).addClassName( 'error' );
				} );
			}
		} );
	} );
}

/**
 * Fonction permettant de filtrer les options d'un select à partir de la valeur
 * d'un radio.
 * Une option avec une valeur vide est toujours conservée.
 * Lorsque le select valait une des valeurs que l'on cache, sa valeur devient
 * la chaîne vide.
 *
 * Exemple:
 * <pre>
 * filterSelectOptionsFromRadioValue(
 *		'FormHistochoixcer93',
 *		'data[Histochoixcer93][formeci]',
 *		'Histochoixcer93Decisioncs',
 *		{
 *			'S': ['valide', 'aviscadre'],
 *			'C': ['aviscadre', 'passageep']
 *		}
 * );
 * </pre>
 *
 * @param string formId
 * @param string radioName
 * @param string selectId
 * @param hash values
 */
function filterSelectOptionsFromRadioValue( formId, radioName, selectId, values ) {
	var v = $( formId ).getInputs( 'radio', radioName );

	var currentValue = undefined;
	$( v ).each( function( radio ) {
		if( radio.checked ) {
			currentValue = radio.value;
		}
	} );

	var accepted = values[currentValue];

	$$('#' + selectId + ' option').each( function ( option ) {
		if( option.value != '' ) {
			if( in_array( option.value, accepted ) ) {
				option.show();
			}
			else {
				option.hide();
			}
		}
	} );

	var currentSelectValue = $F( selectId );
	if( currentSelectValue != '' && !in_array( currentSelectValue, accepted ) ) {
		$( selectId ).value = '';
	}
}

/**
 * Fonction permettant de d'observer le changement de valeur d'un radio et de
 * filtrer les options d'un select à partir de sa valeur.
 *
 * Exemple:
 * <pre>
 * observeFilterSelectOptionsFromRadioValue(
 *		'FormHistochoixcer93',
 *		'data[Histochoixcer93][formeci]',
 *		'Histochoixcer93Decisioncs',
 *		{
 *			'S': ['valide', 'aviscadre'],
 *			'C': ['aviscadre', 'passageep']
 *		}
 * );
 * </pre>
 *
 * @see filterSelectOptionsFromRadioValue()
 *
 * @param string formId
 * @param string radioName
 * @param string selectId
 * @param hash values
 */
function observeFilterSelectOptionsFromRadioValue( formId, radioName, selectId, values ) {
	filterSelectOptionsFromRadioValue( formId, radioName, selectId, values );

	var v = $( formId ).getInputs( 'radio', radioName );
	$( v ).each( function( radio ) {
		$( radio ).observe( 'change', function( event ) {
			filterSelectOptionsFromRadioValue( formId, radioName, selectId, values );
		} );
	} );
}

/**
 * Retourne la valeur d'un radio présent au sein d'un formulaire particulier
 *
 * @param string form L'id du formulaire (ex.: 'contratinsertion')
 * @param string radioName Le name du radio (ex.: 'data[Cer93][duree]')
 * @return string
 */
function radioValue( form, radioName ) {
	var v = $( form ).getInputs( 'radio', radioName );

	var currentValue = undefined;
	$( v ).each( function( radio ) {
		if( radio.checked ) {
			currentValue = radio.value;
		}
	} );

	return currentValue;
}

/**
* Permet de cocher un ensemble de cases à cocher.
* @param selecteur Le sélecteur CSS pour obtenir les cases à cocher (default: input[type="checkbox"])
* @param simulate Lorsqu'il est à true, permet de simuler l'action de click (default: false)
*/
function toutChoisir( radios, valeur, simulate ) {
		$( radios ).each( function( radio ) {
			if( radio.value == valeur ) {
				if( simulate != true ) {
					$( radio ).writeAttribute("checked", "checked");
				}
				else {
					$( radio ).simulate( 'click' );
				}
			}
		} );

	return false;
}

//-----------------------------------------------------------------------------

/**
 * Transforme les liens ayant la classe "external" pour qu'ils s'ouvrent dans
 * une nouvelle fenêtre (un nouvel onglet) via Javascript.
 *
 * @return void
 */
function make_external_links() {
	$$('a.external').each( function ( link ) {
		var originalJavascript = $( link ).onclick;

		$( link ).onclick = function() {
			var result = true;

			if( null !== originalJavascript ) {
				result = originalJavascript();
			}

			if( true === result ) {
				window.open( $( link ).href, '_blank' );
			}

			return false;
		};
	} );
}

//-----------------------------------------------------------------------------

/**
 * Retourne les éléments de formulaires sérialisés d'une des lignes d'un tableau
 * (la ligne qui contient le lien Ajax passé en paramètre).
 *
 * Les éléments de formulaire doivent impérativement se trouver entre des balises
 * <form>...</form>
 *
 * @param Un sélecteur vers le lien Ajax permettant d'envoyer la ligne.
 * @return string
 */
function serializeTableRow( link ) {
	var form = $(link).up( 'form' );
	var trId = $(link).up('tr').id;

	return Form.serializeElements(
		$( form )
		.getElementsBySelector(
			'#' + trId + ' input',
			'#' + trId + ' select',
			'#' + trId + ' textarea'
		)
	);
}

/**
 * Fonction permettant d'éviter qu'un formulaire ne soit envoyé plusieurs fois.
 * Utilisée notamment pour la connexion.
 *
 * @param formId Le formulaire sur lequel appliquer la fonctionnalité
 * @param message Le message à afficher au-dessus du formulaire pour tenir l'utilisateur informé.
 */
function observeDisableFormOnSubmit( formId, message ) {
	message = typeof(message) != 'undefined' ? message : null;

	var submits = $(formId).getElementsBySelector( '*[type=submit]' );

	if( typeof submits !== 'undefined' ) {
		$(submits).each( function( submit ) {
			if( typeof $(submit).name === 'string' && $(submit).name.length > 0 ) {
				Event.observe(
					$(submit),
					'click',
					// Si le formulaire a été envoyé via un bouton, on l'ajoute aux données envoyées
					function( event ) {
						var name = 'data[' + $(submit).name + ']';

						// Si d'autres éléments du même nom existent, on les supprime
						$(formId).select( 'input[name="' + name + '"]' ).each( function( old ) {
							$(old).remove();
						} );

						var hidden = new Element(
							'input',
							{
								type: 'hidden',
								name: name,
								value: $(submit).value,
							}
						);
						$(formId).insert( { 'top' : hidden } );
					}
				);
			}
		} );
	}

	Event.observe(
		formId,
		'submit',
		function( submitter ) {
			// Ajout de l'enventuel message en haut du formaulire
			if( typeof(message) !== 'undefined' && message !== null ) {
				var notice = new Element( 'p', { 'class': 'notice' } ).update( message );
				$( formId ).insert( { 'top' : notice } );
			}

			// Désactivation des boutons
			$$( '#' + formId + ' *[type=submit]', '#' + formId + ' *[type=reset]' ).each( function( submit ) {
				$( submit ).disabled = true;
			} );
		}
	);
}



/**
 * Pour chacun des liens trouvés par le chemin, on remplace la partie signet
 * (#...) existante par le signet passé en paramètre.
 *
 * @param String links Le chemin des liens à modifier
 * @param String fragment Le signet à utiliser pour la modification
 * @param String prefix Le préfixe éventuel (ex. tabbedWrapper) qui sera ajouté
 *	avec une virgule le cas échéant.
 * @returns void
 */
function replaceUrlFragments( links, fragment, prefix ) {
	prefix = 'undefined' === typeof prefix ? null : prefix + ',';

	$$( links ).each( function( link ) {
		var href = $(link).readAttribute( 'href' );
		if(null !== prefix) {
			fragment = fragment.replace( /^#/, '#' + prefix );
		}
		href = href.replace( /#.*$/, '' ) + fragment;
		$(link).writeAttribute( 'href', href );
	} );
}

/**
 * Observe l'événement 'onclick' de chacun des liens du premier chemin, qui ne
 * doivent être composés que de signet (#nomsignet), pour modifier les signets
 * des liens du second chemin.
 *
 * @param String observedPath Le chemin des liens à observer
 * @param String replacedPath Le chemin des liens pour lesquels modifier le signet
 * @param String prefix Le préfixe éventuel (ex. tabbedWrapper) qui sera ajouté
 *	avec une virgule le cas échéant.
 * @returns void
 */
function observeOnclickUrlFragments( observedPath, replacedPath, prefix ) {
	$$( observedPath ).each( function( observed ) {
		$(observed).observe(
			'click',
			function() {
				replaceUrlFragments( replacedPath, $(observed).readAttribute( 'href' ), prefix );
			}
		);
	} );
}

/**
 * Observe l'événement 'onload' de la page pour modifier les liens du chemin en
 * fonction de la dernière partie du signet (#dossiers,propononorientationprocov58
 * donnera #propononorientationprocov58) présent dans l'URL de la page.
 *
 * @param String replacedPath Le chemin des liens pour lesquels modifier le signet
 * @param String prefix Le préfixe éventuel (ex. tabbedWrapper) qui sera ajouté
 *	avec une virgule le cas échéant.
 * @returns void
 */
function observeOnloadUrlFragments( replacedPath, prefix ) {
	document.observe( "dom:loaded", function() {
		if( window.location.href.indexOf( '#' ) !== -1 ) {
			var fragment = window.location.href.replace( /^.*#/, '#' ).replace( /^.*,([^,]+$)/g, '#$1' );
			replaceUrlFragments( replacedPath, fragment, prefix );
		}
	} );
}

/**
 * Retourne le nombre de jours séparant deux dates.
 *
 * @url http://www.htmlgoodies.com/html5/javascript/calculating-the-difference-between-two-dates-in-javascript.html#fbid=WAI_I5iVM_N
 *
 * @param Date date1 La date la plus ancienne
 * @param Date date2 La date la plus récente
 * @return int
 */
function nbJoursIntervalleDates( date1, date2 ) {
	//Get 1 day in milliseconds
	var one_day=1000*60*60*24;

	// Convert both dates to milliseconds
	var date1_ms = date1.getTime();
	var date2_ms = date2.getTime();

	// Calculate the difference in milliseconds
	var difference_ms = date2_ms - date1_ms;

	// Convert back to days and return
	return Math.round(difference_ms/one_day);
}

/**
 * Met à jour un champ avec le nombre de jours entre deux dates (constituées de SELECT CakePHP).
 *
 * @param string date1 Le préfix du champ date la plus ancienne
 * @param string date2 Le préfix du champ date la plus récente
 * @param string fieldId L'id de l'élément à mettre à jour
 * @return void
 */
function updateFieldFromDatesInterval( date1, date2, fieldId ) {
	var dateComplete1 = ( $F( date1 + 'Day' ) && $F( date1 + 'Month' ) && $F( date1 + 'Year' ) );
	var dateComplete2 = ( $F( date2 + 'Day' ) && $F( date2 + 'Month' ) && $F( date2 + 'Year' ) );

	if( dateComplete1 && dateComplete2 ) {
		var n = nbJoursIntervalleDates(
			new Date( $F( date1 + 'Year' ), $F( date1 + 'Month' ), $F( date1 + 'Day' ) ),
			new Date( $F( date2 + 'Year' ), $F( date2 + 'Month' ), $F( date2 + 'Day' ) )
		);
		$( fieldId ).update( n );
	}
}

/**
 * Met à jour une date de fin à partir d'une date de début et d'une durée.
 *
 * @param string date1 Le nom du champ de date de début (sera suffixé à la manière CakePHP)
 * @param string duree Le nom du champ de durée
 * @param string date2 Le nom du champ de date de fin (sera suffixé à la manière CakePHP)
 * @return void
 */
function updateDateFromDateDuree( date1, duree, date2 ) {
	var complete = (
		( $F( date1 + 'Year' ) && $F( date1 + 'Month' ) && $F( date1 + 'Day' ) )
		&& $F( duree )

	);

	if( complete ) {
		setDateInterval( date1, date2, $F( duree ), false );
	}
}


// http://phpjs.org/functions/strtotime/
 function strtotime (text, now) {
     // Convert string representation of date and time to a timestamp
     //
     // version: 1109.2015
     // discuss at: http://phpjs.org/functions/strtotime
     // +   original by: Caio Ariede (http://caioariede.com)
     // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
     // +      input by: David
     // +   improved by: Caio Ariede (http://caioariede.com)
     // +   improved by: Brett Zamir (http://brett-zamir.me)
     // +   bugfixed by: Wagner B. Soares
     // +   bugfixed by: Artur Tchernychev
     // +   improved by: A. Matías Quezada (http://amatiasq.com)
     // %        note 1: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
     // *     example 1: strtotime('+1 day', 1129633200);
     // *     returns 1: 1129719600
     // *     example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
     // *     returns 2: 1130425202
     // *     example 3: strtotime('last month', 1129633200);
     // *     returns 3: 1127041200
     // *     example 4: strtotime('2009-05-04 08:30:00');
     // *     returns 4: 1241418600
     if (!text)
         return null;

     // Unecessary spaces
     text = text.trim()
         .replace(/\s{2,}/g, ' ')
         .replace(/[\t\r\n]/g, '')
         .toLowerCase();

     var parsed;

     if (text === 'now')
         return now === null || isNaN(now) ? new Date().getTime() / 1000 | 0 : now | 0;
     else if (!isNaN(parse = Date.parse(text)))
         return parse / 1000 | 0;
     if (text === 'now')
         return new Date().getTime() / 1000; // Return seconds, not milli-seconds
     else if (!isNaN(parsed = Date.parse(text)))
         return parsed / 1000;

     var match = text.match(/^(\d{2,4})-(\d{2})-(\d{2})(?:\s(\d{1,2}):(\d{2})(?::\d{2})?)?(?:\.(\d+)?)?$/);
     if (match) {
         var year = match[1] >= 0 && match[1] <= 69 ? +match[1] + 2000 : match[1];
         return new Date(year, parseInt(match[2], 10) - 1, match[3],
             match[4] || 0, match[5] || 0, match[6] || 0, match[7] || 0) / 1000;
     }

     var date = now ? new Date(now * 1000) : new Date();
     var days = {
         'sun': 0,
         'mon': 1,
         'tue': 2,
         'wed': 3,
         'thu': 4,
         'fri': 5,
         'sat': 6
     };
     var ranges = {
         'yea': 'FullYear',
         'mon': 'Month',
         'day': 'Date',
         'hou': 'Hours',
         'min': 'Minutes',
         'sec': 'Seconds'
     };

     function lastNext(type, range, modifier) {
         var day = days[range];

         if (typeof(day) !== 'undefined') {
             var diff = day - date.getDay();

             if (diff === 0)
                 diff = 7 * modifier;
             else if (diff > 0 && type === 'last')
                 diff -= 7;
             else if (diff < 0 && type === 'next')
                 diff += 7;

             date.setDate(date.getDate() + diff);
         }
     }
     function process(val) {
         var split = val.split(' ');
         var type = split[0];
         var range = split[1].substring(0, 3);
         var typeIsNumber = /\d+/.test(type);

         var ago = split[2] === 'ago';
         var num = (type === 'last' ? -1 : 1) * (ago ? -1 : 1);

         if (typeIsNumber)
             num *= parseInt(type, 10);

         if (ranges.hasOwnProperty(range))
             return date['set' + ranges[range]](date['get' + ranges[range]]() + num);
         else if (range === 'wee')
             return date.setDate(date.getDate() + (num * 7));

         if (type === 'next' || type === 'last')
             lastNext(type, range, num);
         else if (!typeIsNumber)
             return false;

         return true;
     }

     var regex = '([+-]?\\d+\\s' +
         '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' +
         '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' +
         '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday)|(last|next)\\s' +
         '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' +
         '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' +
         '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday))(\\sago)?';

     match = text.match(new RegExp(regex, 'gi'));
     if (!match)
         return false;

     for (var i = 0, len = match.length; i < len; i++)
         if (!process(match[i]))
             return false;

     // ECMAScript 5 only
     //if (!match.every(process))
     //	return false;

     return (date.getTime() / 1000);
 }

// -----------------------------------------------------------------------------
// Fonctions "AjaxAction" utilisées par la méthode PrototypeAjaxHelper::observe()
// -----------------------------------------------------------------------------

/**
 * Permet de récupérer les valeurs de certains champs de formulaire dont les
 * id (au sens HTML) se trouvent dans l'Array parameters.fields.
 *
 * On peut forcer des valeurs qui ne sont pas encore remplies dans le formulaire
 * (par exemple au chargement de la page) dès lors que l'Array parameters.values
 * contient des id en "clé" et les valeurs à forcer en "valeur".
 *
 * Attributs de l'objet parameters:
 * {{{
 *	- full (boolean): permet de choisir sous quel format les paramètres post seront envoyés)
 *	- fields (Array): une liste d'id (HTML) de champs à envoyer
 *	- values (objet): une liste d'attributs id (HTML) / valeur à forcer pour les champs à envoyer
 * }}}
 *
 * @param object parameters
 * @returns object
 */
function cake_data(parameters) {
	var fields = parameters.fields;
	var data = {};

	parameters.fields.each( function(input) {
		if( typeof parameters.full !== 'undefined' && parameters.full ) {
			data[$(input).name] = {
				'domId': $(input).id,
				'name': $(input).name,
				'type': $(input).type,
				'value': $F(input)
			};
		}
		else {
			data[$(input).name] = $F(input);
		}
	} );

	// Possède-t'on des valeurs "forcées"
	if( typeof parameters.values === 'object' ) {
		for( domId in parameters.values ) {
			var value = parameters.values[domId];

			if( typeof parameters.full !== 'undefined' && parameters.full ) {
				data[$(domId).name]['value'] = value;
			}
			else {
				data[$(domId).name] = value;
			}
		};
	}

	return data;
}


/**
 * "Surcharge" de la classe Ajax.Updater pour s'assurer de n'avoir que seule la
 * dernière requête d'updater pour une URL soit prise en compte.
 *
 * Lorsqu'une requête précédente est trouvée, elle est annulée lors du lancement
 * de la nouvelle requête.
 *
 * La liste des updaters en cours est stockée dans windows.updaters (Hash).
 */
Ajax.AbortableUpdater = Class.create(
	Ajax.Updater,
	{
		initialize: function( $super, container, url, options ) {
			var key = url;

			// Création du dictionnaire associatif des updaters
			if( typeof window.updaters === 'undefined' ) {
				window.updaters = new Hash();
			}

			// Annulation de l'updater précédent
			var previous = window.updaters.get( key );
			if( typeof previous !== 'undefined' ) {
				previous.transport.abort();
				window.updaters.unset( key );
			}

			// "Surcharge" de la méthode onComplete des options
			options = Object.clone(options);
			var onComplete = options.onComplete;
			options.onComplete = ( function( response, json) {
				if( Object.isFunction( onComplete ) ) onComplete( response, json );

				// Suppression de la référence à l'updater
				window.updaters.unset( key );
			} ).bind( this );

			$super( container, url, options );

			// Sauvegarde de la référence à l'updater
			window.updaters.set( key, this );
		}
	}
);

/**
 * "Surcharge" de la classe Ajax.Request pour s'assurer de n'avoir que seule la
 * dernière requête pour une URL soit prise en compte.
 *
 * Lorsqu'une requête précédente est trouvée, elle est annulée lors du lancement
 * de la nouvelle requête.
 *
 * La liste des requests en cours est stockée dans windows.requests (Hash).
 */
Ajax.AbortableRequest = Class.create(
	Ajax.Request,
	{
		initialize: function( $super, url, options ) {
			var key = url;

			// Création du dictionnaire associatif des requests
			if( typeof window.requests === 'undefined' ) {
				window.requests = new Hash();
			}

			// Annulation de la request précédent
			var previous = window.requests.get( key );
			if( typeof previous !== 'undefined' ) {
				previous.transport.abort();
				window.requests.unset( key );
			}

			// "Surcharge" de la méthode onComplete des options
			options = Object.clone(options);
			var onComplete = options.onComplete;
			options.onComplete = ( function( response, json) {
				if( Object.isFunction( onComplete ) ) onComplete( response, json );

				// Suppression de la référence à la request
				window.requests.unset( key );
			} ).bind( this );

			$super( url, options );

			// Sauvegarde de la référence à la request
			window.requests.set( key, this );
		}
	}
);

/**
 * Valeurs de keyCode à ne pas prendre en compte pour les champs de type Ajax
 * autocomplete.
 *
 * @url http://www.javascripter.net/faq/keycodes.htm
 */
var unobservedKeys = [
	Event.KEY_TAB,
	Event.KEY_RETURN,
	Event.KEY_ESC,
	Event.KEY_LEFT,
	Event.KEY_UP,
	Event.KEY_RIGHT,
	Event.KEY_DOWN,
	Event.KEY_HOME,
	Event.KEY_END,
	Event.KEY_PAGEUP,
	Event.KEY_PAGEDOWN,
	Event.KEY_INSERT,
	16, // shift
	17, // ctrl
	18, // alt
	19, // pause (FF)
	20, // caps lock
	42, //PrntScrn (FF)
	44, //PrntScrn
	91, // 91
	112, // F1
	113, // F2
	114, // F3
	115, // F4
	116, // F5
	117, // F6
	118, // F7
	119, // F8
	120, // F9
	121, // F10
	122, // F11
	123, // F12
	144, // NumLock
	145 // ScrollLock
];

/**
 * La méthode de callback (par défaut) lancée par le onSuccess de l'appel
 * Ajax.Request de la fonction ajax_action.
 *
 * Attributs de l'objet parameters:
 * {{{
 *	- url (string): l'URL qui a été utilisée lors de l'appel Ajax post
 *	- data[Target][name] (string): dans le cas de l'événement click, l'attribut name du champ texte ayant servi au champ "autocomplete"
 * }}}
 *
 * @param object response
 * @param object parameters
 * @returns void
 */
function ajax_action_on_success(response, parameters) {
	var json = response.responseText.evalJSON(true);

	if( json.success ) {
		for( path in json.fields ) {
			try {
				var field = json.fields[path];
				// Test: $(field), $(field).type, $(field).id, $(field).value, $(field).options

				if( $(field).type === 'select' ) {
					if( typeof $(field).options !== 'undefined' ) {
						var select = new Element( 'select' );
						$(select).insert( { bottom: new Element( 'option', { 'value': '' } ) } );

						var options = $(field).options;
						if( $(options) != [] ) {
							$(options).each( function( result ) {
								var title = ( typeof $(result).title === 'undefined' ? '' : $(result).title );
								var option = Element( 'option', { 'value': $(result).id, 'title': title } ).update( $(result).name );
								$(select).insert( { bottom: option } );
							} );
						}

						$($(field).id).update( $(select).innerHTML );
					}
				}
				else if( $(field).type === 'ajax_select' ) {

					var domIdSelect = $(field).id + 'AjaxSelect';
					var oldAjaxSelect = $( domIdSelect );
					if( oldAjaxSelect ) {
						$( oldAjaxSelect ).remove();
					}

					if( typeof $(field).options !== 'undefined' && $($(field).options).length > 0 ) {
						var ajaxSelect = new Element( 'ul' );

						$($(field).options).each( function ( result ) {
							var a = new Element( 'a', { href: '#', onclick: 'return false;' } ).update( result.name );

							$( a ).observe( 'click', function( event ) {
								$( domIdSelect ).remove();

								var params = {
									'data[Event][type]': 'click',
									'data[id]': $(field).id,
									'data[name]': parameters['data[Target][name]'],
									'data[value]': $(result).id,
									'data[prefix]': $(field).prefix
								};

								new Ajax.AbortableRequest(
									parameters.url,
									{
										method: 'post',
										parameters: params,
										onSuccess: function( response ) {
											ajax_action_on_success( response, params );
										}
									}
								);

								return false;
							} );

							$( ajaxSelect ).insert( { bottom: $( a ).wrap( 'li' ) } );
						} );

						$( $(field).id ).up( 'div' ).insert(  { after: $( ajaxSelect ).wrap( 'div', { 'id': domIdSelect, 'class': 'ajax select' } ) }  );
					}
				}

				// On ne modifie / renvoie pas systématiquement la valeur des champs
				if( typeof $(field).value !== 'undefined' ) {
					// Si c'est une case à cocher, voir si on doit cocher / décocher
					if( $($(field).id).type === 'checkbox' ) {
						var before = $($(field).id).checked;
						var after = !( $(field).value === null || $(field).value === '' || $(field).value == '0' );

						if( before !== after ) {
							$( $($(field).id) ).simulate( 'click' );
						}
					}
					else {
						$($(field).id).value = $(field).value;

						if( $(field).simulate === true ) {
							$($(field).id).simulate( 'change' );
						}
					}
				}
			} catch( Exception ) {
				console.log( Exception );
			}
		}

		// Événements à lancer ?
		if( typeof json.events === 'object' && json.events.length > 0 ) {
			$(json.events).each( function ( customEvent ) {
				Event.fire( document, customEvent );
			} );
		}
	}
}

/**
 * Effectue un appel Ajax post "à la mode CakePHP" (grâce à la méthode cake_data())
 * suite au déclenchement d'un événement.
 *
 * Attributs de l'objet parameters:
 * {{{
 *	- url (string): l'URL (relative ou absolue) à appeler en Ajax.
 *	- prefix (string): le préfixe utilisé dans les id et name (HTML) des champs
 *	- full (boolean): permet de choisir sous quel format les paramètres post seront envoyés (@see cake_data())
 *	- fields (Array): une liste d'id (HTML) de champs à envoyer
 *	- values (objet): une liste d'attributs id (HTML) / valeur à forcer pour les champs à envoyer
 *	- delay (integer): le nombre de millisecondes de délai à utiliser avant l'envoi lorsque l'événement est de type keyup ou keydown. Par défaut: 500.
 *	- min (integer): le nombre minimum de caractères devant être remplis lorsque l'événement est de type keyup ou keydown. Par défaut: 3.
 * }}}
 *
 * En cas de succès de Ajax.Request, la fonction de rappel ajax_action_on_success()
 * sera appelée.
 *
 * Les paramètres post ajoutés par la méthode sont:
 * {{{
 *	- data[Event][type]: le type d'événement (dataavailable, keyup, keydown, change, ...)
 *	- data[Target][domId]: l'id (HTML) de l'élément qui a déclenché l'événement (non rempli lorsque l'événement dataavailable)
 *	- data[Target][name]: le name (HTML) de l'élément qui a déclenché l'événement (non rempli lorsque l'événement dataavailable)
 * }}}
 *
 * Si la même requête (url, prefix) est déjà en cours, on l'annule.
 *
 * @param Event event L'événement qui a déclenché l'appel à la fonction.
 * @param object parameters
 * @returns void
 */
function ajax_action( event, parameters ) {
	var keyEvents = [ 'keyup', 'keydown' ],
		doAjaxAction = function( event, parameters ) {
		var postParams = cake_data( parameters );

		postParams['data[prefix]'] = parameters.prefix;
		postParams['data[Event][type]'] = $(event).type;

		var element = $(event).element(); // Dans les cas du change et du keyup
		postParams['data[Target][domId]'] = $(element).id;
		postParams['data[Target][name]'] = $(element).name;

		new Ajax.AbortableRequest(
			parameters.url,
			{
				parameters: postParams,
				onSuccess: function( response ) {
					postParams.url = parameters.url;
					ajax_action_on_success( response, postParams );
					if( in_array( $(event).type, keyEvents ) && !in_array( event.keyCode, unobservedKeys ) ) {
						element.removeClassName( 'loading' );
						delete window.ajax_timeout_queue[event.type][element.id];
					}
				}
			}
		);
	};

	// Si ce n'est pas un événement de type keyup/keydown sur un champ texte
	if( !in_array( event.type, keyEvents ) ) {
		doAjaxAction( event, parameters );
	}
	// Si c'est un événement de type keyup/keydown sur un champ texte
	else if( !in_array( event.keyCode, unobservedKeys ) ) {
		var element = $(event).element(),
			delay = ( parameters.delay === undefined ? 500 : parameters.delay ),
			min = ( parameters.min === undefined ? 3 : parameters.min );

		// Liste globale des timeouts
		window.ajax_timeout_queue = ( window.ajax_timeout_queue === undefined ? {} : window.ajax_timeout_queue );
		window.ajax_timeout_queue[event.type] = ( window.ajax_timeout_queue[event.type] === undefined ? {} : window.ajax_timeout_queue[event.type] );

		// Si on a un nombre de lettres suffisant
		if( $F(element).length >= min ) {
			element.addClassName( 'loading' );

			if( window.ajax_timeout_queue[event.type][element.id] !== undefined ) {
				clearTimeout( window.ajax_timeout_queue[event.type][element.id] );
				delete window.ajax_timeout_queue[event.type][element.id];
			}

			window.ajax_timeout_queue[event.type][element.id] = setTimeout(
				function() { doAjaxAction( event, parameters ); },
				delay
			);
		}
		// Sinon, on nettoie le timeout, la classe, la liste de résultats
		else {
			if( window.ajax_timeout_queue[event.type][element.id] !== undefined ) {
				clearTimeout( window.ajax_timeout_queue[event.type][element.id] );
				delete window.ajax_timeout_queue[event.type][element.id];
			}

			element.removeClassName( 'loading' );

			var oldAjaxSelect = $( $(element).id + 'AjaxSelect' );
			if( oldAjaxSelect ) {
				$( oldAjaxSelect ).remove();
			}
		}
	}
}

/**
 * Permet de tronquer la longueur du texte d'un élément à la valeur demandée avec
 * ajout de l'ellipse à la fin et mise en attribut title de l'élément du texte
 * complet lorsque le texte dans la balise doit être tronqué.
 *
 * @param string|Element tag
 * @param integer maxLength
 * @param string ellipsis
 * @returns void
 */
function truncateWithEllipsis( tag, maxLength, ellipsis ) {
	maxLength = typeof(maxLength) != 'undefined' ? maxLength : 100;
	ellipsis = typeof(ellipsis) != 'undefined' ? ellipsis : '...';

	var oldTitle = $(tag).innerHTML;
	if( oldTitle.length > maxLength ) {
		var newTitle = oldTitle.substr( 0, maxLength - ellipsis.length ) + ellipsis;
		$(tag).update( newTitle );
		$(tag).writeAttribute( 'title', oldTitle );
	}
}

/**
 * Permet de séparer la partie date de la partie time d'un champ datetime
 * généré par CakePHP avec la chaîne de caractères spécifiée en paramètre.
 *
 * @param string id L'id de base du champ datetime. Par exemple: ModeField
 * @param string text La chaîne servant de séparateur. Par défaut: ' à '
 * @returns void
 */
function cakeDateTimeSeparator( id, text ) {
	if( typeof text === 'undefined' ) {
		text = ' à ';
	}

	try {
		var hour = $( id + 'Hour' );
		var oldDatetimeSeparators = $( hour ).up( 'div.input' ).down( 'span.datetime_separator' );

		if( typeof $(oldDatetimeSeparators) !== 'undefined' ) {
			$(oldDatetimeSeparators).each( function ( datetimeSeparator ) {
				$(datetimeSeparator).remove();
			} );
		}
		var span = new Element( 'span', { 'class': 'datetime_separator' } ).update( text );
		$( hour ).insert( { 'before' : span } );
	} catch( Exception ) {
		console.log( Exception );
	}
}

/**
 * Permet de récupérer le nombre de requêtes SQL se trouvant dans la table de
 * classe cake-sql-log générée par CakePHP lorsque debug > 0.
 *
 * @returns {Number}
 */
function getCakeQueriesCount() {
	var count = 0;

	$$( 'table.cake-sql-log' ).each( function( table ) {
		count += table.rows.length - 1;
	} );

	return count;
}

/**
 * Objet contenant des méthodes utilitaires pouvant être utilisées dans la prise
 * de décision de différentes thématiques de COV et d'EP.
 *
 * @namespace Commission
 */
var Commission = {
	myTriggerEvent: function( id, name ) {
		try {
			Element.getStorage( id )
				.get( 'prototype_event_registry' )
				.get( name )
				.each( function( wrapper ){ wrapper.handler(); } );
		} catch( e ) {
			console.log( e );
		}
	},
	preremplissageDecisionOrientation: function( modele, index, preremplissages ) {
		var select = modele + index + 'Decisioncov';

		$( select ).observe( 'change', function( event ) {
			var found = false;

			$( preremplissages ).each( function( preremplissage ) {
				if( found === false && $F( select ) === preremplissage.value ) {
					found = true;

					// Type d'orientation
					$( modele + index + 'TypeorientId' ).value = preremplissage.typeorient_id;
					Commission.myTriggerEvent( modele + index + 'TypeorientId', 'change' );

					// Structure référente
					$( modele + index + 'StructurereferenteId' ).value = preremplissage.typeorient_id + '_' + preremplissage.structurereferente_id;
					Commission.myTriggerEvent( modele + index + 'StructurereferenteId', 'change' );

					// Référent
					if( preremplissage.referent_id !== '' ) { // TODO: à vérifier
						$( modele + index + 'ReferentId' ).value = preremplissage.structurereferente_id + '_' + preremplissage.referent_id;
					}
					else {
						$( modele + index + 'ReferentId' ).value = '';
					}
					Commission.myTriggerEvent( modele + index + 'ReferentId', 'change' );
				}
			} );

			if( found === false ) {
				// Type d'orientation
				$( modele + index + 'TypeorientId' ).value = '';
				Commission.myTriggerEvent( modele + index + 'TypeorientId', 'change' );

				// Structure référente
				$( modele + index + 'StructurereferenteId' ).value = '';
				Commission.myTriggerEvent( modele + index + 'StructurereferenteId', 'change' );

				// Référent
				$( modele + index + 'ReferentId' ).value = '';
				Commission.myTriggerEvent( modele + index + 'ReferentId', 'change' );
			}
		} );
	}
};

/* Mise en évidence de champs et d'options de formulaires. @see Evidence (css) */
var Evidence = ( function() {
	'use strict';

	var empty = function( value ) {
			return value === undefined || value === null || value === false;
		},
		getElement = function(selector) {
			var elements = $$(selector);
			if(elements.length === 1) {
				return elements[0];
			}
			return null;
		},
		getQuestion = function (field) {
			if(!empty(field)) {
				return $(field).up('div.input, fieldset');
			}
			return null;
		},
		config = function (params) {
			params['class'] = params['class'] === undefined ? 'evidence' : params['class'];
			params['title'] = params['title'] === undefined ? false : params['title'];

			return params;
		},
		setParams = function (element, params) {
			params = config(params === undefined ? {} : params);

			if( !empty(element) ) {
				if( !empty(params['class']) ) {
					$(element).addClassName(params['class']);
				}
				if( !empty(params['title']) ) {
					$(element).addClassName( 'title' );

					if( $(element).tagName === 'DIV' ) {
						$(element).down('label').title = params['title'];
					}
					else if( $(element).tagName === 'FIELDSET' ) {
						$(element).down('legend').title = params['title'];
					}
					else {
						$(element).title = params['title'];
					}
				}
			}
		};

	return {
		find: function(selector) {
			var element = getElement(selector);
			if( element !== null ) {
				return element;
			}
			element = $$(selector);
			if( element.length === 0 ) {
				console.log( 'Le sélecteur ' + selector + ' ne retourne aucun élément' );
			} else {
				console.log( 'Le sélecteur ' + selector + ' retourne ' + element.length + ' éléments:' );
				console.log( element );
			}

		},
		setQuestionParams: function(selector, params) {
			var question = getQuestion(getElement(selector));

			if( question !== null ) {
				setParams(question, params);
			}
			// TODO: else

		},
		setOptionParams: function(selector, params) {
			var option = getElement(selector);
			if( option !== null ) {

				if( option.type === 'checkbox' ) {
					option = $(option).up('div.input.checkbox, div.checkbox');
				} else if( option.type === 'radio' ) {
					option = getElement('label[for=' + option.id + ']');
				}

				setParams(option, params);
			}
			// TODO: else
		}
	};
} () );

/**
 * Initialisation des tables triables en JavaScript (classe sortable sur la table),
 * pour éviter le lien de tri sur les actions.
 *
 * @param {String} className La classe des tables à trier (sortable par défaut).
 * @returns {undefined}
 */
function initSortableTables( className ) {
	className = ( className === undefined ? 'sortable' : className );

	TableKit.options.rowEvenClass = 'even';
	TableKit.options.rowOddClass = 'odd';
	TableKit.options.descendingClass = 'desc';
	TableKit.options.ascendingClass = 'asc';
	TableKit.options.sortableSelector = ['table.' + className];

	TableKit.Sortable.addSortType(
		new TableKit.Sortable.Type(
			'date-fr',
			{
				'pattern': Webrsa.Regexps.datetime(),
				'normal': function(v) {
					return Webrsa.Date.fromText(v);
				}
			}
		)
	);

	TableKit.Sortable.detectors = $A($w('date-fr date-iso date date-eu date-au time currency datasize number casesensitivetext text'));

	$$( 'table.' + className + ' thead th' ).each( function ( th ) {
		if( $(th).hasClassName( 'actions' ) ) {
			$(th).addClassName( 'nosort' );
		}

		if( $(th).hasClassName( 'date' ) ) {
			$(th).addClassName( 'date-au' );
		}
	} );
}

//------------------------------------------------------------------------------
// Partie "Listes déroulantes liées" pour les projets de villes territoriaux (CG 93)
//------------------------------------------------------------------------------

/**
 * Suppression des optgroups vides d'une list déroulante; s'il n'existe plus qu'un
 * seul optgroup, on fera remonter les options d'un niveau.
 *
 * @param {String} selectId L'id de la liste déroulante
 * @returns {void}
 */
function cleanSelectOptgroups( selectId ) {
	var optgroups, selected;

	try {
		// On sauvegarde la valeur sélectionnée
		selected = $F(selectId);

		// Suppression des optgroup vides
		optgroups = $(selectId).select( 'optgroup' );
		$(optgroups).each( function( optgroup ) {
			if( 0 === $(optgroup).childElements().length ) {
				$(optgroup).remove();
			}
		} );

		// Si c'est le seul optgroup, on remonte les options d'un niveau
		optgroups = $(selectId).select( 'optgroup' );
		if( 1 === optgroups.length ) {
			$(optgroups[0]).replace( optgroups[0].innerHTML );
		}

		// On remet la valeur sélectionnée
		$(selectId).value = selected;
	} catch( exception ) {
		console.log( exception );
	}
}

/**
 * Permet de limiter une liste déroulante d'options en fonction du préfixe
 * (avec "_" comme séparateur) des valeurs des options ou du préfixe de la
 * valeur sélectionnée.
 *
 * Si le select comporte des optgroups, après limitation des options, les
 * optgroups vides seront supprimés et s'il n'existe plus qu'un seul optgroup,
 * on fera remonter les options d'un niveau.
 *
 * @param {String} selectId L'id de la liste déroulante
 * @param {String} prefix Le préfixe (sans séparateur)
 * @returns {void}
 */
function limitSelectOptionsByPrefix( selectId, prefix ) {
	try {
		var options = $(selectId).select( 'option' ),
			selected = $F(selectId),
			re = new RegExp( '^(' + prefix + '|' + selected.replace( /_.*$/, '' ) + ')_' ),
			value/*,
			optgroups*/;

		// Restriction de la liste des options
		$(options).each( function( option ) {
			value = $(option).value;
			if( '' != value && null === value.match( re ) ) {
				$(option).remove();
			}
		} );

		cleanSelectOptgroups( selectId );

		/*// Suppression des optgroup vides
		optgroups = $(selectId).select( 'optgroup' );
		$(optgroups).each( function( optgroup ) {
			if( 0 === $(optgroup).childElements().length ) {
				$(optgroup).remove();
			}
		} );

		// Si c'est le seul optgroup, on descend d'un niveau
		optgroups = $(selectId).select( 'optgroup' );
		if( 1 === optgroups.length ) {
			$(optgroups[0]).replace( optgroups[0].innerHTML );
		}

		// On remet la valeur sélectionnée
		$(selectId).value = selected;*/
	} catch( exception ) {
		console.log( exception );
	}
}

//------------------------------------------------------------------------------

/**
 * Suppression des optgroups vides d'une list déroulante; s'il n'existe plus qu'un
 * seul optgroup, on fera remonter les options d'un niveau.
 *
 * @param {String} selectId L'id de la liste déroulante
 * @returns {void}
 */
function cleanSelectOptgroups2( selectId ) {
	var optgroups, selected, visible;

	try {
		// On sauvegarde la valeur sélectionnée
		selected = $F(selectId);

		// On cache les optgroup vides
		optgroups = $(selectId).select( 'optgroup' );
		$(optgroups).each( function( optgroup ) {
			visible = 0;
			$(optgroup).select( 'option' ).each( function( option ) {
				visible += ( $(option).visible() ? 1 : 0 );
			} );

			if( 0 === visible ) {
				$(optgroup).hide();
			}
		} );
	} catch( exception ) {
		console.log( exception );
	}
}

//------------------------------------------------------------------------------

function onChangeDependantSelect( slaveId, masterId ) {
	try {
		var masterValue = $F(masterId).replace( /^.*_([^_]+)$/, '$1' ),
			slaveValue = $F(slaveId);

		if( false === $(masterId).enabled() ) {
			return;
		}

		// On "reset"
		$(slaveId).select( 'optgroup', 'option' ).each( function( option ) {
			$(option).enable();
			$(option).show();
		} );

		if( '' != masterValue ) {
			$(slaveId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value && $(option).value.replace( /^([^_]+)_.*$/, '$1' ) !== masterValue ) {
					$(option).disable();
					$(option).hide();
				}
			} );
		} else {
			$(slaveId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value ) {
					$(option).disable();
					$(option).hide();
				}
			} );
		}

		// Si la valeur de la liste déroulante esclave n'est pas visible, on change la valeur du champ
		if( false === selectOptionVisible(slaveId, slaveValue) ) {
			$(slaveId).value = '';
		}

		cleanSelectOptgroups2(slaveId);

		$(slaveId).simulate( 'change' );
	} catch(e) {
		console.log(e);
	}
}

function dependantSelect( slaveId, masterId ) {
	try {
		if( null !== $(slaveId) ) {
			onChangeDependantSelect( slaveId, masterId );

			$(masterId).observe( 'change', function() {
				onChangeDependantSelect( slaveId, masterId );
			} );
		}
	} catch(e) {
		console.log(e);
	}
}

//------------------------------------------------------------------------------

/**
 * Vérifie si la première option d'un SELECT possédant une valeur donnée est
 * visible.
 *
 * @param {String} selectId
 * @param {String} value
 * @returns {Boolean}
 */
function selectOptionVisible( selectId, value ) {
	var option;

	try {
		option = $(selectId).select( 'option[value="' + value + '"]' );
		return option[0].visible();
	} catch(e) {
		return false;
	}
}

//------------------------------------------------------------------------------

/**
 * Limite la liste des structures référentes en fonction de la valeur sélectionnée
 * dans la liste des PDVCOM.
 *
 * @param {String} communautesrId L'id du select contenant les projets de villes communautaires
 * @param {String} structurereferenteId L'id du select contenant les structures référentes
 * @param {Object} links Pour chacun des id de PDVCOM configurés, un array d'ids de structures référentes
 * @param {Boolean} hide Doit-on cacher la liste des valeurs de la structure si la valeur de la communaute est vide
 * @returns {void}
 */
function onChangeDependantSelectsCommunautesr( communautesrId, structurereferenteId, links, hide ) {
	try {
		var communautesrValue = $F(communautesrId),
			structurereferenteValue = $F(structurereferenteId);

		if( false === $(communautesrId).enabled() ) {
			return;
		}

		// On "reset"
		$(structurereferenteId).select( 'optgroup', 'option' ).each( function( option ) {
			$(option).show();
		} );

		if( '' != communautesrValue ) {
			// Suppression des options non présentes dans le projet de ville territorial
			$(structurereferenteId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value && false === in_array( $(option).value.replace( /^.*_([^_]+)$/, '$1' ), links[communautesrValue], true ) ) {
					$(option).hide();
				}
			} );

		} else if( true === hide ) {
			$(structurereferenteId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value ) {
					$(option).hide();
				}
			} );
		}

		// Si la valeur de la liste déroulante esclave n'est pas visible, on change la valeur du champ
		if( false === selectOptionVisible(structurereferenteId, structurereferenteValue) ) {
			$(structurereferenteId).value = '';
		}

		cleanSelectOptgroups2(structurereferenteId);

		// On prévient la liste des structures référentes qu'elle a changé
		$( structurereferenteId ).simulate( 'change' );
	} catch(e) {
		console.log(e);
	}
}

/**
 * Observe le select de projets de villes communautaires afin de limiter la liste
 * des structures référentes à celles se trouvant dans le PDVCOM.
 *
 * @param {String} communautesrId L'id du select contenant les projets de villes communautaires
 * @param {String} structurereferenteId L'id du select contenant les structures référentes
 * @param {Object} links Pour chacun des id de PDVCOM configurés, un array d'ids de structures référentes
 * @param {Boolean} hide Doit-on cacher la liste des valeurs de la structure si la valeur de la communaute est vide
 * @returns {void}
 */
function dependantSelectsCommunautesr( communautesrId, structurereferenteId, links, hide ) {
	try {
		if( null !== $(communautesrId) ) {
			onChangeDependantSelectsCommunautesr( communautesrId, structurereferenteId, links, hide );

			$(communautesrId).observe( 'change', function() {
				onChangeDependantSelectsCommunautesr( communautesrId, structurereferenteId, links, hide )
			} );
		}
	} catch(e) {
		console.log(e);
	}
}

/**
 * Limite la liste des référents en fonction de la valeur sélectionnée dans la
 * liste des PDVCOM.
 *
 * @param {String} communautesrId L'id du select contenant les projets de villes communautaires
 * @param {String} referentId L'id du select contenant les référents (et dont la valeur est préfixée par l'id de la structure référente)
 * @param {Object} links Pour chacun des id de PDVCOM configurés, un array d'ids de structures référentes
 * @param {Boolean} hide Doit-on cacher la liste des valeurs de la structure si la valeur de la communaute est vide
 * @returns {void}
 */
function onChangeDependantSelectsCommunautesrReferent( communautesrId, referentId, links, hide ) {
	try {
		var communautesrValue = $F(communautesrId),
			referentValue = $F(referentId);

		if( false === $(communautesrId).enabled() ) {
			return;
		}

		// On "reset"
		$(referentId).select( 'optgroup', 'option' ).each( function( option ) {
			$(option).show();
		} );

		if( '' != communautesrValue ) {
			// Suppression des options non présentes dans le projet de ville territorial
			$(referentId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value && false === in_array( $(option).value.replace( /^([^_]+)_.*$/, '$1' ), links[communautesrValue], true ) ) {
					$(option).hide();
				}
			} );

		} else if( true === hide ) {
			$(referentId).select( 'option' ).each( function( option ) {
				if( '' != $(option).value ) {
					$(option).hide();
				}
			} );
		}

		// Si la valeur de la liste déroulante esclave n'est pas visible, on change la valeur du champ
		if( false === selectOptionVisible(referentId, referentValue) ) {
			$(referentId).value = '';
		}

		cleanSelectOptgroups2(referentId);

		// On prévient la liste des structures référentes qu'elle a changé
		$( referentId ).simulate( 'change' );
	} catch(e) {
		console.log(e);
	}
}

/**
 * Observe le select de projets de villes communautaires afin de limiter la liste
 * des référents à celles se trouvant dans le PDVCOM.
 *
 * @param {String} communautesrId L'id du select contenant les projets de villes communautaires
 * @param {String} referentId L'id du select contenant les référents (et dont la valeur est préfixée par l'id de la structure référente)
 * @param {Object} links Pour chacun des id de PDVCOM configurés, un array d'ids de structures référentes
 * @param {Boolean} hide Doit-on cacher la liste des valeurs de la structure si la valeur de la communaute est vide
 * @returns {void}
 */
function dependantSelectsCommunautesrReferent( communautesrId, referentId, links, hide ) {
	try {
		if( null !== $(communautesrId) ) {
			onChangeDependantSelectsCommunautesrReferent( communautesrId, referentId, links, hide );

			$(communautesrId).observe( 'change', function() {
				onChangeDependantSelectsCommunautesrReferent( communautesrId, referentId, links, hide )
			} );
		}
	} catch(e) {
		console.log(e);
	}
}

/**
 * Remplace ou ajoute un paramètre nommé dans une URL "à la CakePHP".
 *
 * @param {String} url
 * @param {String} key
 * @param {*} value
 * @returns {String}
 */
var replaceUrlNamedParam = function(url, key, value) {
	var re = /^([a-z]+:\/\/|\/)([^#]*)(#.*){0,1}$/gi,
		matches = re.exec(url);

	if(null !== matches) {
		if('undefined' === typeof matches[3]) {
			matches[3] = '';
		}

		matches[2] = matches[2].replace( new RegExp( '\/' + key + ':[^\/]+' ), '/' );
		matches[2] = matches[2] + '/' + key + ':' + value;

		url = matches[1] + matches[2].replace( /\/+/g, '/' ) + matches[3];

	}

	return url;
};

/**
 *
 * @see {@url https://stackoverflow.com/a/2593661}
 *
 * @param {String} str
 * @returns {String}
 */
var regExpQuote = function(str) {
    return (str+'').replace(/[.?*+^$[\]\\(){}\/|-]/g, "\\$&");
};