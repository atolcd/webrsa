<?php
	$i = 0;
	$total = 0;
	$cells = array();

	echo '<h1>&nbsp;</h1>'.$this->Xform->create();
	foreach( $themes as $localite => $nombre ) {
		$cells[] = array(
			$localite,
			$this->Xform->input( "Ep.{$i}.nombre", array( 'type' => 'text', 'value' => $nombre, 'div' => false, 'label' => false ) )
		);
		$i++;
		$total += $nombre;
	}

	echo $this->Xhtml->tag( 'table', $this->Xhtml->tableCells( $cells )."<tr><th>Total</th><td id=\"tt\">{$total}</td></tr>" );
	echo $this->Xform->input( "Ep.nombretotal", array( 'type' => 'text', 'value' => $total ) );
	echo $this->Xform->submit( 'Enregistrer' );
	echo $this->Xform->end();

	debug( $themes );
?>

<script type="text/javascript">
	/**
	* TODO:
	* 	- http://task3.cc/819/how-to-limit-a-field-with-javascript-prototype-and-display-the-counter/
	*	- ne pas permettre de rentrer autre chose que [0-9] (choucroute)
	*	- ne pas permettre de rentrer de trop grandes valeurs
	* Ne pas permettre l'insertion de nombres négatifs dans les input
	*
	* ATTENTION:
	*	Pour chaque thème, les fonctionnalités suivantes sont proposées :
	*	L’outil indique le nombre de dossiers total et le nombre de dossiers par
	*		ville concernés par le thème sélectionné (sur le territoire de l’EP).
	*	L’utilisateur indique le nombre de dossiers total à traiter lors de l’EP concernée.
	*	L’application, en fonction du caractère « urgent » et de la date du dossier
	*		et en fonction du pourcentage de dossiers souhaités par ville (déterminé par
	*		paramétrage préalable), propose un nombre de dossiers à traiter par ville.
	*	L’utilisateur a la possibilité d’ajuster ce nombre par ville (l’application ajuste alors, le nombre
	*		total de dossiers à étudier.)
	*/

	var originalTotal = 0;
	var originalValues = new Array();
	var chosenValues = new Array();

	/*
	* Calcule la somme des dossiers à traiter dans les inputs du tableau
	*/
	function getTotal( path ) {
		var total = 0;
		var inputs = $$( path );
		$( inputs ).each( function ( input ) {
			total += parseInt( $F( input ) );
		} );
		return total;
	}

	function findValue( object, name ) {
		var retVal = undefined;
		object.each( function ( item ) {
			if( item.key == name ) {
				if( item.value != NaN ) {
					retVal = parseInt( item.value );
				}
			}
		} );
		return retVal;
	}

	function findKey( object, name ) {
		var retVal = NaN;
		var i = 0;
		object.each( function ( item ) {
			if( item.key == name ) {
				if( item.value != NaN ) {
					retVal = i;
				}
			}
			i++;
		} );
		return retVal;
	}

	/*
	*
	*/

	function recompute( path, oldTotal, newTotal ) {
		oldTotal = originalTotal; // FIXME
		var total = 0;
		var inputs = $$( path );

		// Total nombres fixés
		chosenTotal = 0;
		chosenValues.each( function ( chosenValue ) {
			chosenTotal += parseInt( chosenValue.value );
		} );

		$( inputs ).each( function ( input ) {
			var chosenKey = findKey( chosenValues, input.name );
			if( isNaN( chosenKey ) ) {
				var nombre = Math.round( findValue( originalValues, input.name ) / ( oldTotal - chosenTotal ) * ( newTotal - chosenTotal ) );
				if( nombre >= 0 ) {
					$( input ).value = nombre;
					total += nombre;
				}
			}
		} );

		if( total > ( newTotal - chosenTotal ) ) {
			originalValues.each( function ( item ) {
				var chosenKey = findKey( chosenValues, item.key );
				if( isNaN( chosenKey ) ) {
					if( total > ( newTotal - chosenTotal ) ) {
						var inputsTmp = document.getElementsByName( item.key );
						if( inputsTmp.length == 1 ) {
							if( $F( inputsTmp[0] ) > 0 ) {
								inputsTmp[0].value = ( parseInt( inputsTmp[0].value ) - 1 );
								total--;
							}
						}
					}
				}
			} );
		}

		// FIXME: faire la même si total >
		if( total < ( newTotal - chosenTotal ) ) {
			originalValues.each( function ( item ) {
				var chosenKey = findKey( chosenValues, item.key );
				if( isNaN( chosenKey ) ) {
					if( total < ( newTotal - chosenTotal ) ) {
						var inputsTmp = document.getElementsByName( item.key );
						if( inputsTmp.length == 1 ) {
							var remaining = ( originalValues.length - chosenValues.length );
							var value = Math.round( ( newTotal - ( total + chosenTotal ) ) / Math.max( 0, remaining ) );
							inputsTmp[0].value = ( parseInt( inputsTmp[0].value ) + value );
							total += value;
						}
					}
				}
			} );
		}
		// FIXME: on n'est toujours pas certains d'avoir le bon total!!!!

		/// FIXME: si tout est fixé, commencer à changer les nombres pour les valeurs fixées les plus anciennes (voir total <)

		return ( total + chosenTotal );
	}

	/*
	*	Init
	*/
	var i = 0;
	$$( 'table input' ).each( function ( input ) {
		var value = parseInt( $F( input ) );
		var key = $( input ).name;
		originalTotal += value;
		originalValues[i] = { 'key': key, 'value': value };
		i++;
	} );

	// Tri des valeurs originales
	originalValues.sort( function(a, b) {
		return ( a.value <= b.value );
	} );

	/*
	* Effectue le recalcul des dossiers à traiter par localisation lorsqu'on change la valeur totale
	*/
	$( "EpNombretotal" ).observe( 'blur', function( event ) {
		var name = $( "EpNombretotal" ).name;
		var newTotal = $F( "EpNombretotal" );
		var oldTotal = getTotal( 'table input' );

		$( 'tt' ).update( recompute( 'table input', oldTotal, newTotal ) );
	} );

	/*
	* Effectue le recalcul des dossiers à traiter par localisation lorsqu'on change une valeur
	*/
	$$( 'table input' ).each( function ( input ) {
		$( input ).observe( 'blur', function( event ) {
			var key = findKey( chosenValues, event.target.name );

			if( isNaN( key ) ) {
				chosenValues[chosenValues.length] = { 'key': event.target.name, 'value': $F( event.target.id ) };
			}
			else {
				chosenValues.splice( key, 1 );
				chosenValues[chosenValues.length] = { 'key': event.target.name, 'value': $F( event.target.id ) };
			}

			var newTotal = $F( "EpNombretotal" );
			var oldTotal = getTotal( 'table input' );

			$( 'tt' ).update( recompute( 'table input', oldTotal, newTotal ) );
// 			console.log( chosenValues );
		} );
	} );
</script>