/*global console, validationJS, toString, Array, zeroFillDate, giveDefaultValue*/

/* 
 * Contien l'équivalent des vérifications de CakePhp et des vérifications php additionnels en Javascript
 * 
 * @package app.View.Helper
 * @subpackage FormValidator
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/* @namespace Validation */
var Validation = {
	/**
	 * Vérifi qu'une chaine soit alphaNumérique (accents et charactères étranger autorisé)
	 * @param {String} value
	 * @returns {Boolean}
	 * @function Validation.alphaNumeric
	 */
	alphaNumeric: function( value ) {
		'use strict';
		if ( value === undefined || value === null ){
			return false;
		}
		var test = !Array.isArray(value.match( /[!:;,§\/.?*%\^¨$£=()-+œ<>°@_-`\[\]\\{}#"'~& ]|\s/g )) && value.length > 0;
		return test;
	},
	
	/**
	 * Alias de la function alphaNumeric()
	 * @param {String} value
	 * @returns {Boolean}
	 */
	alphanumeric: function( value ) {
		'use strict';
		return Validation.alphaNumeric( value );
	},
	
	/**
	 * Vérifi qu'une chaine soit alphaNumérique (accents et charactères étranger autorisé)
	 * @param {String} value
	 * @returns {Boolean}
	 * @function Validation.alphaNumeric
	 */
	numeric: function( value ) {
		'use strict';
		if ( value === undefined || value === null ){
			return false;
		}
		var test = value.match( /^[0-9]+([.,][0-9]+){0,1}$/ ) !== null;
		return test;
	},
	
	/**
	 * Vérifi qu'une chaine n'est pas vide (exclu retour à la ligne, tabulation et espaces)
	 * @param {String|Number} value
	 * @returns {Boolean}
	 */
	notEmpty: function( value ){
		'use strict';
		if ( value === null ){
			return false;
		}
		
		value = value.replace(/\s/g, '').replace(/ /g, '');
		var test = value.length > 0;
		return test;
	},
	
	/**
	 * Vérifi la taille d'une chaine avec valeur min et max (inclu)
	 * @param {String|Number} value
	 * @param {Number} min
	 * @param {Number} max
	 * @returns {Boolean}
	 */
	between: function( value, min, max ){
		'use strict';
		value = String(value).length;
		min = parseInt( min, 10 );
		max = parseInt( max, 10 );
		
		var test = value >= min && value <= max;
		return test;
	},
	
	/**
	 * Moteur de inList()
	 * @param {String|Number} value
	 * @param {Array} array
	 * @param {String|Number} sameType
	 * @returns {Boolean}
	 */
	checkIfInList: function( value, array, sameType ){
		'use strict';
		var i;
		if ( typeof toString(value) === 'string' && Array.isArray( array ) ){
			for(i=0; i<array.length; i++){
				array[i] = array[i] === null ? '' : array[i];
				if ( (sameType && value === array[i]) || (!sameType && Validation.similarTo( value, array[i] )) ){
					return true;
				}
			}
		}
		return false;
	},
	
	/**
	 * Vérifi l'existance de value dans array
	 * @param {String|Number} value
	 * @param {Array} array
	 * @param {String|Number} sameType
	 * @returns {Boolean}
	 */
	inList: function( value, array, sameType ){
		'use strict';
		sameType = giveDefaultValue( sameType, true );
		sameType = ( sameType === 'f' || Validation.similarTo( sameType, 0 ) || Validation.similarTo( sameType, -1 ) || Validation.similarTo( sameType, 'false' ) || sameType === false ) ?
			false : true;
		
		return Validation.checkIfInList( value, array, sameType );
	},
	
	/**
	 * Vérifi que la valeur de value est bien entre min et max (inclu ou pas selon le dernier param) 
	 * @param {Number|Float} value
	 * @param {Number|Float} min
	 * @param {Number|Float} max
	 * @param {Boolean} inclusive
	 * @returns {Boolean}
	 */
	inRange: function( value, min, max, inclusive){
		'use strict';
		min = parseFloat( giveDefaultValue( min, -Infinity ) );
		max = parseFloat( giveDefaultValue( max, Infinity ) );
		inclusive = giveDefaultValue( inclusive, true );
		value = parseFloat( value );
		
		var test = inclusive ? (value >= min && value <= max) : (value > min && value < max);
		return test;
	},
	
	/**
	 * Alias de la function inRange avec le param inclusive à false
	 * @param {Number|Float} value
	 * @param {Number|Float} min
	 * @param {Number|Float} max
	 * @returns {Boolean}
	 */
	range: function ( value, min, max ){
		'use strict';
		return Validation.inRange( value, min, max, false );
	},
	
	/**
	 * Alias de la function inRange avec le param inclusive à true
	 * @param {Number|Float} value
	 * @param {Number|Float} min
	 * @param {Number|Float} max
	 * @returns {Boolean}
	 */
	inclusiveRange: function ( value, min, max ){
		'use strict';
		return Validation.inRange( value, min, max );
	},
	
	/**
	 * Vérifi la syntaxe ssn (n° de sécu)
	 * @param {String|Number} value
	 * @returns {Boolean}
	 */
	ssn: function( value ){
		'use strict';
		value = String(value);
		var test = Array.isArray(value.match( /^(1|2|7|8)[0-9]{2}(0[1-9]|10|11|12|[2-9][0-9])((0[1-9]|[1-8][0-9]|9[0-5]|2A|2B)(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990)|(9[7-8][0-9])(0[1-9]|0[1-9]|[1-8][0-9]|90)|99(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990))(00[1-9]|0[1-9][0-9]|[1-9][0-9][0-9]|)(0[1-9]|[1-8][0-9]|9[0-7])$/ ));
		return test;
	},
	
	/**
	 * Converti, si besoin, une date du format français vers le format anglais (dmy -> ymd)
	 * @param {type} value
	 * @param {type} option
	 * @returns {String}
	 */
	getEnglishDate: function( value, option ){
		'use strict';
		if ( option.toLowerCase() === 'dmy' ){
			// Format de date française selon si l'année est défini sur 4 chiffres ou seulement 2
			value = ( value.indexOf(' ', 7) < 10 || value.indexOf('T') < 10 ) ?
				value.substr(6,2) + value.substr(2,4) + value.substr(0,2) + value.substr(8, value.length -8) :
				value.substr(6,4) + value.substr(2,4) + value.substr(0,2) + value.substr(10, value.length -10)
			;
		}
		
		return value;
	},
	
	/**
	 * Vérifi la validitée d'une date
	 * formats acceptés :
	 * YYYY-MM-DD HH:MM:SS -> Format SQL
	 * YY-MM-DD -> Format alternatif, rajoute 1900 si YY > 70 et 2000 si YY < 70 (fonctionne avec tout autre format)
	 * YYYY/MM/DD -> Format standart (fonctionne pour tout autre format)
	 * YYYY.MM.DD -> Format particulier (fonctionne pour tout autre format)
	 * YYYY MM DD -> Format particulier (fonctionne pour tout autre format)
	 * YYYY-MM-DDTHH:MM:SS.000Z-> Format Javascript (avec microsecondes)
	 * DD-MM-YYYY -> Date française
	 * DD-MM-YYYY HH:MM:SS -> DateTime français
	 * HH:MM:SS -> Format heure
	 * 
	 * @param {Date} value
	 * @returns {Boolean}
	 */
	date: function( value, option ){
		'use strict';
		option = giveDefaultValue( option, [''] )[0];
		
		value = Validation.getEnglishDate( value, option );
		
		// On reformate la date pour faciliter le traitement
		value = Validation.transformIntoDate( value );
		
		// On converti la date formatté en objet javascript Date et on retransforme en chaine formaté
		var test = new Date( value ).toJSON();
		if ( test === null ){
			return false;
		}
		
		// On ne garde que les chiffres pour éviter les érreurs dû au multi-bytes
		value = value.replace(/([^0-9]?)/g, '');
		test = test.replace(/([^0-9]?)/g, '');
		
		// Plus qu'a comparer les dates, si il y a eu un changement ou bien si ça n'a pas fonctionné,
		// c'est que c'est une mauvaise date/syntaxe
		// PS: on vire les microsecondes qui peuvent provoquer des problèmes
		return (test.substr(0,14) === value.substr(0,14));
	},
	
	/**
	 * Ajoute les parties manquante d'un dateTime (ex: 1/3/15 => 01-03-2015T00:00:00.000Z)
	 * @param {String} value
	 * @returns {String}
	 */
	completeDateTime: function( value ){
		'use strict';
		// Sur la partie date, on s'assure d'avoir des - et non des espaces, des slash ou des points
		// La date doit ressemble à ça pour l'instant : 01-03-2015 11:55:22, on met un T au milieu à la place de l'espace
		value = zeroFillDate( value.substr(0,8).replace(/\.| |\//g, '-') ) + value.substr(8, value.length-8).replace(' ', 'T');
		
		// Ajoute une date fictive dans le cas d'un Time
		value = value.indexOf('-') > 0 ? value : '01-01-20T' + value;
		
		// Ajoute un time fictif dans le cas d'un date
		value = value.indexOf('T') > 0 ? value : value + 'T00:00:00';
		
		// Ajoute les microsecondes si elles n'existent pas
		value = value.indexOf('Z') > 0 ? value : value + '.000Z';
		
		return value;
	},
	
	/**
	 * Transforme si besoin, une année de 2 chiffres en 4 chiffres (ex: 15 => 2015)
	 * @param {String} value
	 * @returns {String}
	 */
	yyToyyyy: function( value ){
		'use strict';
		var year = value.substr( 0, value.indexOf('-') );
		
		// Pour l'année, si seul 2 chiffres sont renseigné, on ajoute 1900 ou 2000 si la valeur est inférieur ou supérieur à 30
		if( year.length === 2 && value.indexOf('T') === 8 ){
			year = year >= 30 ? '19' + year : '20' + year;
		}
		
		return year;
	},
	
	/**
	 * Reformate la date au format yyyy-mm-ddThh:mm:ss.mmmZ
	 * @param {String} value
	 * @returns {String}
	 */
	transformIntoDate: function ( value ){
		'use strict';
		var pos;
		if ( Validation.similarTo( value, null ) ) {
			return false;
		}
		
		value = Validation.completeDateTime( value );
		
		pos = value.indexOf('-');
		value = Validation.yyToyyyy( value ) + value.substr( pos );
				
		// Traitements date française JJ-MM-YYYY
		if ( value.indexOf('-') === 2 ){
			value = value.substr(6,4) + value.substr(2,4) + value.substr(0,2) + value.substr(10,value.length -10);
		}
		
		return value;
	},
	
	/**
	 * Alias de la function date
	 * @param {String} value
	 * @returns {Boolean}
	 */
	dateTime: function( value ){
		'use strict';
		return Validation.date( value );
	},
	
	/**
	 * Vérifi la syntaxe d'un numéro de téléphone en france
	 * @param {String|Number} value
	 * @returns {Boolean}
	 */
	phoneFr: function( value ){
		'use strict';
		value = String(value);
		value = value.length === 9 ? '0' + value : value;
		value = value.replace(/ /g, '').replace(/\./g, '');
		
		var test = Array.isArray(value.match(/^(((0)[1-9](\s?\d{2}){4})|(1[0-9]{1,3})|(11[0-9]{4})|(3[0-9]{3}))$/));
		return test;
	},
	
	/**
	 * Vérifi la synthaxe d'une adresse email
	 * @param {type} value
	 * @returns {Boolean}
	 */
	email: function( value ){
		'use strict';
		var test = Array.isArray(value.match(/^[a-z0-9!#$%&\'*+\/=?\^_`{|}~\-]+(?:\.[a-z0-9!#$%&\'*+\/=?\^_`{|}~\-]+)*@(?:[\-_a-z0-9][\-_a-z0-9]*\.)*(?:[a-z0-9][\-a-z0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/i));
		return test;
	},
	
	/**
	 * Vérifi qu'un champ possède une valeur de type integer
	 * @param {Number} value
	 * @returns {Boolean}
	 */
	integer: function ( value ){
		'use strict';
		var test = ( !isNaN(value) && value % 1 === 0 && value !== null );
		return  test;
	},
	
	/**
	 * Vérifi qu'un champ possède une valeur de type boolean
	 * @param {Number|Boolean} value
	 * @returns {Boolean}
	 */
	'boolean': function ( value ){
		'use strict';
		var test = Validation.inList( value, [0, 1, '0', '1', 'true', 'false', true, false] );
		return test;
	},
	
	/**
	 * Vérifie que le contenu de la liste d'id est vide
	 * @param {Array} value
	 * @returns {Boolean}
	 */
	allEmpty: function ( value ){
		'use strict';
		var i;
		if ( !Array.isArray(value) ){
			return false;
		}
		
		for (i=0; i<value.length; i++) {
			if ( value[i] !== null && value[i].length > 0 ){
				return false;
			}
		}
		return true;
	},
	
	/**
	 * Renvoi true si l'input indiqué dans "idInputTest" n'est pas vide ou
	 * si l'input indiqué par "fieldName" possède ou pas ("condition") une valeur
	 * contenu dans "valeurs"
	 * @param {String} value
	 * @param {String} fieldName
	 * @param {Boolean} condition
	 * @param {Array} valeurs
	 * @returns {Boolean}
	 */
	notEmptyIf: function ( value, targetValue, condition, valeurs ){
		'use strict';
		if ( Validation.similarTo( value, null ) || Validation.similarTo( targetValue, null ) || Validation.similarTo( condition, null ) || Validation.similarTo( valeurs, null ) || !Array.isArray( valeurs ) ) {
			return false;
		}
		
		if ( Validation.inList( targetValue, valeurs, false ) === condition ){
			return Validation.notEmpty( value );
		}		
		return true;
	},
	
	/**
	 * Compare deux dates selon l'operateur de comparaison
	 * @param {String} date1
	 * @param {String} date2
	 * @param {String} operateur
	 * @returns {Boolean}
	 */
	compareDates: function( date1, date2, operateur ){
		'use strict';
		
		switch ( operateur ) {
			case '<': return date1 < date2;
			case '>': return date1 > date2;
			case '<=': return date1 <= date2;
			case '>=': return date1 >= date2;
			case '==': return Validation.similarTo(date1.toJSON(), date2.toJSON());
			case '!=': return !Validation.similarTo(date1.toJSON(), date2.toJSON());
			case '===': return date1.toJSON() === date2.toJSON();
			case '!==': return date1.toJSON() !== date2.toJSON();
			default: return false;
		}
	},
	
	/**
	 * Inutile en javascript donc renvoi vers notEmptyIf 
	 * @param {String} value
	 * @param {String} fieldName
	 * @param {Boolean} condition
	 * @param {Array} valeurs
	 * @returns {Boolean}
	 */
	notNullIf: function ( idInputTest, idInputMaitre, condition, valeurs ) { 
		'use strict';
		return Validation.notEmptyIf( idInputTest, idInputMaitre, condition, valeurs );
	},
	
	/**
	 * Vérifi que le nombre de char de value ne dépasse pas maxLength 
	 * @param {String} value
	 * @param {Numeric} maxLength
	 * @returns {Boolean}
	 */
	maxLength: function ( value, maxLength ){
		'use strict';
		value = value === undefined || value === null ? 0 : value;
		var test = value.length <= maxLength;
		return test;
	},
	
	/**
	 * Converti les params en String avant de les comparer
	 * 
	 * @param {String|Numeric} first
	 * @param {String|Numeric} last
	 * @returns {Boolean}
	 */
	similarTo: function ( first, last ){
		'use strict';
		return String(first) === String(last);
	}
};