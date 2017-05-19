/*
 * ...
 */
var Webrsa = ( function() {
	'use strict';

	var date = {
			/**
			 * Convertit une date générée par le FormHelper de CakePHP (sous forme de
			 * trois listes déroulantes) en un objet Date javascript.
			 *
			 * @param {String} prefix Le préfixe de l'id des listes déroulantes (ex. UserBirthday)
			 * @returns {Date|null}
			 */
			'fromCakeSelects': function( prefix ) {
				/*global $F, console */
				var result = null;

				try {
					result = new Date( 1970, 0, 1, 0, 0, 0, 0 );
					result.setDate( parseInt( $F( prefix + 'Day' ), 10 ) );
					result.setMonth( parseInt( $F( prefix + 'Month' ), 10 ) - 1 );
					result.setYear( parseInt( $F( prefix + 'Year' ), 10 ) );
				} catch( e ) {
					console.log( e );
				}

				return result;
			},
			/**
			 * Convertit une date et une éventuelle heure en un objet Date
			 * javascript.
			 *
			 * Les formats acceptés sont:
			 *	- Dates seules: JJ/MM/AAAA, J/M/AAAA, J/M/AA, ...
			 *	- Partie heures: HH:MM:SS, H:M:S, HH:MM, H:M
			 *	- Dates et heures: <Date> à <Heure>, <Date> <Heure>
			 *
			 * @param {String} text La chaîne de caractères contenant la date (et l'heure)
			 * @returns {Date|null}
			 */
			'fromText': function( text ) {
				/*global console, regexps */
				var result = null, matches;

				try {
					matches = text.match( regexps.datetime() );

					if( null !== matches ) {
						result = new Date( 1970, 0, 1, 0, 0, 0, 0 );
						result.setDate( parseInt( matches[1], 10 ) );
						result.setMonth( parseInt( matches[2], 10 ) - 1 );
						result.setYear( parseInt( matches[3], 10 ) );

						if( undefined !== matches[4] ) {
							result.setHours( parseInt( matches[6], 10 ) );
							result.setMinutes( parseInt( matches[7], 10 ) );
							if( undefined !== matches[8] ) {
								result.setSeconds( parseInt( matches[9], 10 ) );
							}
						}
					}
				} catch( e ) {
					console.log( e );
				}

				return result;
			}
		},
		regexps = {
			'datetime': function() {
				return ( /^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})( (à ){0,1}([0-9]{1,2}):([0-9]{1,2})(:([0-9]{1,2})){0,1}){0,1}$/ );
			}
		};

	return {
		'Date': date,
		'Regexps': regexps
	};
} () );
