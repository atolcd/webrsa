/*global console, validationJS, document, validationRules, validationOnsubmit, traductions, Validation, validationOnchange, setTimeout, $, $$, giveDefaultValue, sprintf*/

/*
 * Fait le lien entre FormValidatorHelper et webrsa.validaterules.js
 * Permet la vérification des données d'un formulaire en fonction des règles de validation
 * contenu dans les models. Empèche l'envoi du formulaire et affiche les érreurs si les données
 * ont mal été rempli.
 * 
 * @package app.View.Helper
 * @subpackage FormValidator
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
var FormValidator = {
	/**
	 * Liste des variables globales utilisable entre les différentes fonctions
	 * @type json
	 */
	globalVars: {
		rules: [],
		values: {},
		editableList: {},
		errorElements: 'div.input.date, div.input.text, div.input.textarea, div.input.radio, div.input.select',
		
		// Lié au debug, mettre impérativement à false en production !
		debugMode: false,
		verbose: false,
		ultraVerbose: false
	},
	
	/**
	 * Permet au php par le biais de cette fonction, de définir des variables globales
	 * 
	 * @param {json} varList {validationRules, traductions, validationJS, validationOnchange, validationOnsubmit}
	 * @returns {void}
	 */
	initializeVars: function( varList ) {
		'use strict';
		for (var key in varList ){
			if ( varList.hasOwnProperty( key ) ){
				FormValidator.globalVars[key] = varList[key];
			}
		}
	},
	
	/**
	 * Permet de récupérer le nom d'un element débarassé de la premiere paire de crochets si besoin est
	 * ex : data[Search][Monmodel][Monchamp] deviens data[Monmodel][Monchamp]
	 * 
	 * @param {String} name
	 * @returns {String}
	 */
	getRealName: function( name ) {
		'use strict';
		var regex = /^.+?\[([^\]]+)\]\[([^\]]+)\](\[day\]|\[month\]|\[year\]|\[\]){0,1}$/,
			results = regex.exec( name ),
			returnName
		;
		
		if ( results === null || results.length !== 4 ) {
			return '';
		}
		
		returnName = 'data['+results[1]+']['+results[2]+']';
		
		if ( results[3] !== undefined ) {
			returnName += results[3];
		}

		return returnName;
   },
   
	/**
	 * On lui donne un nom d'editable et il renvoi le nom du model dont il dépend
	 * getModelName( data[Monmodel][Mon_field] ) = 'Monmodel'
	 * 
	 * @param {String} name
	 * @returns {String} ModelName
	 */
	getModelName: function ( name ){
	   'use strict';
	   var crochet1, crochet2;
	   name = FormValidator.getRealName( name );
	   crochet1 = name.indexOf('[');
	   crochet2 = name.indexOf(']');

	   // Il doit exister au moins une paire de crochets
	   if ( crochet1 === -1 || crochet2 === -1 ){
		   return null;
	   }

	   return name.substr(crochet1 +1, crochet2 - crochet1 -1);
	},
	
	/**
	 * On lui donne un nom d'editable et il renvoi le nom du champ dont il dépend
	 * getFieldName( data[Monmodel][Mon_field] ) = 'Mon_field'
	 * 
	 * @param {String} name
	 * @returns {String} ModelName
	 */
	getFieldName: function ( name ){
	   'use strict';
	   var crochet1, crochet2, crochet3, crochet4;
	   name = FormValidator.getRealName( name );
	   crochet1 = name.indexOf('[');
	   crochet2 = name.indexOf(']');
	   crochet3 = name.indexOf('[', crochet1 +1);
	   crochet4 = name.indexOf(']', crochet2 +1);

	   // Il doit exister au moins 2 paires de crochets
	   if ( crochet1 === -1 || crochet2 === -1 || crochet3 === -1 || crochet4 === -1 ){
		   return null;
	   }

	   // Renvoi le contenu de la deuxieme paire de crochets
	   return name.substr(crochet3 +1, crochet4 - crochet3 -1);
	},
	
	/**
	 * Fonctionne comme getModelName(),
	 * Permet d'obtenir le contenu de la 3e paire de crochets (utile pour les dates)
	 * 
	 * @param {String} name
	 * @returns {String}
	 */
	getThirdParam: function ( name ){
		'use strict';
		var crochets, result;
		name = FormValidator.getRealName( name );
		crochets = /^[^\[]*(\[[^\]]*\]){2}\[([^\]]*)\].*$/g,
		result = crochets.exec( name );

		if( result === null || result.length < 3 ) {
			return null;
		}

		return result[2];
	},
	
	/**
	 * Affiche message en console
	 * Nécéssite l'activation du debug au préalable
	 * Si v est à true, affichera le message seulement si verbose est activé
	 * Si vplus est à true, affichera le message seulement si ultraVerbose est activé
	 * Condition permet d'ajouter une condition suplémentaire
	 * 
	 * @param {Mixed} message
	 * @param {Boolean} v (verbose)
	 * @param {Boolean} vplus (ultraVerbose)
	 * @param {Boolean} condition
	 * @returns {void}
	 */
	debug: function ( message, v, vplus, condition ){
		'use strict';
		if ( condition === undefined || condition ){
			v = giveDefaultValue( v, false );
			vplus = giveDefaultValue( vplus, false );

			if ( FormValidator.globalVars.debugMode && ((v &&  FormValidator.globalVars.verbose) || !v) && ((vplus && FormValidator.globalVars.ultraVerbose) || !vplus) ){
				console.log( message );
			}
		}
	},
	
	/**
	 * Récupère les params d'une rule
	 * 
	 * @param {Object} rule
	 * @returns {Array}
	 */
	getParams: function ( rule ){
		'use strict';
		var	i,
			varParams = [];

		for (i=1; i<rule.length; i++){
			varParams.push( rule[i] );
		}

		return varParams;
	},
	
	/**
	 * Lié à getRules() ajoute une nouvelle règle de validation à un editable
	 * 
	 * @param {Object} contain
	 * @returns {Object}
	 */
	addRule: function ( contain ){
		'use strict';
		var		rule = [], 
				varAllowEmpty = contain.allowEmpty !== undefined ? contain.allowEmpty : false, 
				varParams = [], 
				message,
				ruleName;

		// Si le nom de la regle est un String
		if ( typeof contain.rule === 'string' ) {
			ruleName = contain.rule;		
		}

		// Si le nom de la regle est stocké dans un array
		else{
			varParams = FormValidator.getParams( contain.rule );

			// Note: contain.rule[0] est l'exacte position du nom de règle dans le cas d'un array		
			ruleName = contain.rule[0];
		}

		message = contain.message || (FormValidator.globalVars.traductions[ruleName] || null);
		rule = {name: ruleName, allowEmpty: varAllowEmpty, params: varParams, message: message};

		FormValidator.debug( ('addRule( contain ) - var rule :'), true, true );
		FormValidator.debug( rule, true, true );
		return rule;
	},
	
	/**
	 * extractRules() constitue le moteur de getRules() (Qui lui, effectue des vérifiactions avant)
	 * 
	 * @param {Object} validation
	 * @returns {Object}
	 */
	extractRules: function ( validation ){
		'use strict';
		var key, contain, rules = [];

		// Recherche les regles de validation pour ce champ
		for (key in validation){
			if (validation.hasOwnProperty(key)){
				contain = validation[key];
				FormValidator.debug( ('getRules( name ) - var contain :'), true, true );
				FormValidator.debug( contain, true, true );

				if ( contain.rule !== undefined ){
					rules.push( FormValidator.addRule( contain ) );
				}
				else{
					FormValidator.debug( ('pas de rule trouvé') );
					FormValidator.debug( contain, true );
				}
			}
		}

		FormValidator.debug( '', true, true );
		return rules;
	},
	
	/**
	 * Renvoi un fieldName dépourvu de _from et de _to pour vérification des between
	 * 
	 * @param {String} fieldName
	 * @returns {Boolean}
	 */
	checkIfFromTo: function ( fieldName ){
		'use strict';
		var from, to;

		if( fieldName === null ){
			return false;
		}

		from = fieldName.indexOf('_from');
		to = fieldName.indexOf('_to');

		return from > 0 ? fieldName.substr(0, from) : (to > 0 ? fieldName.substr(0, to) : fieldName);
	},
	
	/**
	 * Récupère la règle de validation en fonction du nom de l'editable
	 * 
	 * @param {String} name
	 * @returns {Object|Boolean}
	 */
	getRules: function ( name ){
		'use strict';
		var modelName, fieldName, rules;
		// Si le json n'existe pas, on renvoi FALSE (on ne peut pas valider sans)
		if ( FormValidator.globalVars.validationRules === undefined ) {
			return false;
		}

		modelName = FormValidator.getModelName( name );
		fieldName = FormValidator.checkIfFromTo ( FormValidator.getFieldName( name ) );

		// Si aucune vérification n'a été trouvé, le champ est correct quoi qu'il arrive
		if ( FormValidator.globalVars.validationRules[modelName] === undefined || FormValidator.globalVars.validationRules[modelName][fieldName] === undefined ){
			return null;
		}

		rules = FormValidator.extractRules( FormValidator.globalVars.validationRules[modelName][fieldName] );

		return rules;
	},
	
	/**
	 * Concatene les champs date et renvoi leurs valeurs
	 * 
	 * @param {Object} listedEditable
	 * @returns {String}
	 */
	extractDate: function ( listedEditable ){
		'use strict';
		var thisDate = {day: '', month: '', year: ''},
			thirdParam, i;
	
		if ( !listedEditable || listedEditable.length !== 3 ) {
			return false;
		}

		for ( i=0; i<3; i++ ){
			// On récupère day, month ou year et on l'affecte a la variable thisDate
			thirdParam = FormValidator.getThirdParam( listedEditable[i].editable.name );

			switch ( thirdParam ){
				case 'day': 
				case 'month':
				case 'year': thisDate[thirdParam] = listedEditable[i].editable.value; break;
				default: return false; // Si ne contien pas day, month ou year, c'est que ce n'est pas une date !
			}

			if ( thisDate[thirdParam] === '' ){
				return '';
			}
		}

		return thisDate;
	},
	
	/**
	 * Permet d'obtenir les 3 éléments date contenu dans editableList en fonction du name d'origine
	 * 
	 * @param {string} name
	 * @param {string} formatedName
	 * @returns {Array|FormValidator.getDateElementsByName.results|Boolean}
	 */
	getDateElementsByName: function ( name, formatedName ) {
		'use strict';
		var list = FormValidator.globalVars.editableList[formatedName],
			results = [],
			regex = /^(.+)\[(?:day|month|year)\]$/g,
			baseName = regex.exec( name ),
			i = 0
		;
		
		if ( baseName === null || baseName.length !== 2 ) {
			return false;
		}
		
		for (; i<list.length; i++) {
			switch (list[i].editable.name) {
				case baseName[1]+'[day]':
				case baseName[1]+'[month]':
				case baseName[1]+'[year]':
					results.push(list[i]);
					break;
			}
			
			if ( results.length === 3 ) {
				return results;
			}
		}
		
		return false;
	},
	
	/**
	 * Permet de gérer les elements dates (tordu) de cakephp (les 3 selects)
	 * On lui donne un nom de champ (peut importe si c'est data[Model][field][day] ou data[Model][field][year]...)
	 * Il renvoi la date au format 01-01-2015
	 * 
	 * @param {String} name
	 * @returns {String}
	 */
	getDate: function ( name ){
		'use strict';
		var formatedName, thisDate;
		// Converti le name de la forme data[Model][field][day] en Model.field
		formatedName = FormValidator.getModelName( name ) + '.' + FormValidator.getFieldName( name );

		// Il doit y avoir 3 Model.field stocké ( day, month et year )
		if ( FormValidator.globalVars.editableList[formatedName] === undefined || FormValidator.globalVars.editableList[formatedName].length % 3 !== 0 || formatedName === 'null.null' ){
			return false;
		}

		thisDate = FormValidator.extractDate( FormValidator.getDateElementsByName(name, formatedName) );

		if ( !thisDate ){
			return false;
		}

		return thisDate.day + '-' + thisDate.month + '-' + thisDate.year;
	},
	
	/**
	 * Renvoi la valeur se trouvant après le séparateur ('_' par defaut)
	 * @param {String} value
	 * @param {String} separator
	 * @returns {String}
	 */
	suffix: function ( value, separator ){
		'use strict';
		separator = giveDefaultValue ( separator, '_' );
		var cutPos = value.indexOf(separator) + separator.length;
		return cutPos > 0 ? value.substr(cutPos, value.length) : value;
	},
	
	/**
	 * Dans le cas d'un fieldName avec un _id, revoi si possible le suffix de cette valeur
	 * @param {HTML} editable
	 * @param {String} value
	 * @returns {String}
	 */
	formatValue: function ( editable, value ){
		'use strict';
		var fieldName = FormValidator.getFieldName( editable.name );
		if ( fieldName.match(/_id/) ){
			value = FormValidator.suffix( value );
		}

		return value.trim();
	},
	
	/**
	 * Récupère la valeur des boutons radio (renvoi la valeur du bouton selectionné)
	 * 
	 * @param {array} targets
	 * @returns {getRadioValue.valeur}
	 */
	getRadioValue: function ( targets ){
		'use strict';
		var i, valeur = '';
		for ( i=0; i<targets.length; i++ ){
			if ( targets[i].checked === true ){
				valeur += valeur.length ? ','+String( targets[i].value ) : String( targets[i].value );
			}
		}

		return valeur;
	},
	
	/**
	 * Permet de savoir si un editable possède une validation de type téléphone
	 * 
	 * @param {HTML} editable
	 * @returns {Boolean}
	 */
	isTelephone: function ( editable ){
		'use strict';
		for (var key in FormValidator.globalVars.rules[editable.index].rules ){
			if ( FormValidator.globalVars.rules[editable.index].rules.hasOwnProperty( key ) && FormValidator.globalVars.rules[editable.index].rules[key].name === 'phoneFr' ){
				return true;
			}
		}
		return false;
	},
	
	/**
	 * Renvoi la valeur réelle d'un editable
	 * Cherche les élements du même model et même field
	 * 
	 * @param {HTML} editable
	 * @returns {String}
	 */
	getValue: function ( editable ){
		'use strict';
		var targets, thisDate, valeur, cas;
		if ( FormValidator.globalVars.rules[editable.index] === undefined ){
			return null;
		}

		targets = $$('[name="' + FormValidator.globalVars.rules[editable.index].name + '"]');

		// Cas Date
		thisDate = FormValidator.getDate( FormValidator.globalVars.rules[editable.index].name );
		cas = thisDate ? 'date' : (	(targets.length === 1) ? 'normal' : 'radio');

		switch ( cas ){
			case 'date': valeur = thisDate; break;
			case 'normal': valeur = FormValidator.isTelephone( editable ) ? String( editable.value ).replace(/[\W]/g, '') : String( editable.value ); break;
			case 'radio': valeur = FormValidator.getRadioValue( targets ); break;
			default: FormValidator.debug( '/!\\ BUG /!\\ valeur non trouvé dans ' + editable.name ); return null;
		}

		FormValidator.globalVars.values[FormValidator.globalVars.rules[editable.index].name].value = valeur;
		FormValidator.debug( ('Valeur trouvé : ' + valeur), true, true );
		return thisDate || FormValidator.formatValue( editable, valeur );
	},
	empty: function( value ) {
		return value === undefined || value === null || value === false;
	},
	/**
	 * Permet le retrait d'un message d'erreur lié à un editable
	 * 
	 * @param {HTML} editable
	 * @returns {Boolean}
	 */
	removeError: function ( editable ){
		'use strict';
		var parentDiv, errorDiv;
		if ( editable === undefined ){
			return false;
		}

		// On remonte vers la div maman pour chercher une erreur à l'interieur
		parentDiv = editable.up(FormValidator.globalVars.errorElements);
		if( false === FormValidator.empty( parentDiv ) ) {
			parentDiv.removeClassName('error');
			errorDiv = parentDiv.select('div.error-message');
		}

		// Si on trouve une erreur affiché, on la retire
		if ( errorDiv !== undefined && errorDiv[0] !== undefined ){
			errorDiv[0].remove();
		}
	},
	
	/**
	 * Insert les paramètres de la validation dans les %s / %d
	 * 
	 * @param {Object} editable
	 * @param {String} message
	 * @returns {String}
	 */
	insertMessageVar: function ( editable, message ){
		'use strict';
		var editableRules = FormValidator.getRules( editable.name ),
			i;

		for(i=0; i<editableRules.length; i++){
			if ( editableRules[i].message === message ){
				switch( editableRules[i].params.length ){
					case 1: message = sprintf( message, editableRules[i].params[0] ); break;
					case 2: message = sprintf( message, editableRules[i].params[0], editableRules[i].params[1] ); break;
					case 3: message = sprintf( message, editableRules[i].params[0], editableRules[i].params[1], editableRules[i].params[2] ); break;
				}
				break;
			}
		}

		return message;
	},
	
	/**
	 * Affiche l'érreur lié à un editable (ex: Champ obligatoire)
	 * 
	 * @param {HTML} editable
	 * @param {String} message
	 * @returns {Boolean}
	 */
	showError: function ( editable, message ){
		'use strict';
		var parentDiv, errorMsg;
		if ( editable === undefined){
			return false;
		}

		setTimeout(function(){
			// Si aucun message n'est indiqué, on affiche Champ obligatoire, sinon le message
			message = message === undefined || message === null ? 'Champ obligatoire' : FormValidator.insertMessageVar( editable, message );

			// On attribu la class error à la div maman de l'editable
			parentDiv = editable.up(FormValidator.globalVars.errorElements);
			parentDiv.addClassName('error');

			// On verifi si un message d'erreur existe deja
			errorMsg = parentDiv.getElementsByClassName('error-message');

			// On ajoute le message si il n'y en a pas d'autres
			if ( errorMsg.length === 0 ){
				parentDiv.insert('<div class="error-message">' + message + '</div>');
			}
		},20);
	},
	
	/**
	 * Récupère et formate les params de rule
	 * 
	 * @param {HTML} editable
	 * @param {Number} i
	 * @returns {Object}
	 */
	getRulesParams: function ( editable, i ){
		'use strict';
		var params, modelName, targetName, target, name,
		ruleName = FormValidator.globalVars.rules[editable.index].rules[i].name;
		params = Object.create( FormValidator.globalVars.rules[editable.index].rules[i].params );
		name = FormValidator.globalVars.rules[editable.index].name;

		// Validation manquante...
		if ( Validation[ruleName] === undefined ){
			FormValidator.debug( ('Validation manquante : ' + ruleName) );
			FormValidator.debug( params, true );
			return undefined;
		}

		// Cas particulier : notEmptyIf
		if ( ruleName === 'notEmptyIf' || ruleName === 'notNullIf' ){
			modelName = FormValidator.getModelName( name );
			targetName = 'data[' + modelName + '][' + params[0] + ']';
			target = $$('[name="' + targetName + '"]')[0];

			if ( target === undefined ){
				FormValidator.debug( ('Cible du notEmptyIf non trouvé! '+targetName), true );
				FormValidator.debug( ('index = '+editable.index), true );
				FormValidator.debug( (FormValidator.globalVars.rules[editable.index]), true );
				return undefined;
			}
			params[0] = FormValidator.getValue( target ); 
			FormValidator.debug( ('Target.value = '+params[0]+'; condition = '+params[1]), true ); 
			FormValidator.debug( (params[2]), true );
		}

		return params;
	},
	
	/**
	 * Valide ou pas l'editable concerné et affiche l'erreur le cas échéan
	 * 
	 * @param {HTML} editable
	 * @param {String} value
	 * @param {Number} i
	 * @param {Object} params
	 * @param {Boolean} isOnchange
	 * @returns {Boolean}
	 */
	isValid: function ( editable, value, i, params, isOnchange ){
		'use strict';
		var message,
			ruleName = FormValidator.globalVars.rules[editable.index].rules[i].name,
			validation = false,
			j,
			val;

		if ( (editable.type.toLowerCase() === 'checkbox' && ruleName === 'date') || editable.type.toLowerCase() === 'hidden' ){
			return true;
		}
		
		// Cas multiple checkbox
		if (editable.type.toLowerCase() === 'checkbox' && value.indexOf(',') >= 0) {
			val = value.split(',');
			for (j=0; j<val.length; j++) {
				if (FormValidator.isValid(editable, val[j], i, params, isOnchange) === false) {
					return false;
				}
				return true;
			}
		}

		// C'est maintenant qu'on vérifie l'editable
		switch ( params.length ){
			case 0: validation = Validation[ruleName]( value ); break;
			case 1: validation = Validation[ruleName]( value, params[0] ); break;
			case 2: validation = Validation[ruleName]( value, params[0], params[1] ); break;
			case 3: validation = Validation[ruleName]( value, params[0], params[1], params[2] ); break;
		}

		// Si la validation à échoué
		if ( !validation && !(FormValidator.globalVars.rules[editable.index].rules[i].allowEmpty && value.length === 0) ){
			FormValidator.debug( (ruleName+' = false') );

			if ( FormValidator.globalVars.validationOnchange && isOnchange ){
				message = FormValidator.globalVars.rules[editable.index].rules[i].message;
				FormValidator.debug([
					FormValidator.getModelName( editable.name ),
					FormValidator.getFieldName( editable.name ),
					FormValidator.globalVars.validationRules[FormValidator.getModelName( editable.name )][FormValidator.getFieldName( editable.name )],
					message
				], true);
				FormValidator.showError( editable, message );
			}

			return false;
		}

		return true;
	},
	
	/**
	 * Décide ou pas d'effectuer la verification d'un editable
	 * 
	 * @param {HTML} editable
	 * @param {Boolean} onchange
	 * @returns {Boolean}
	 */
	doValidation: function ( editable, onchange ){
		'use strict';
		var i, params, rule, value;

		rule = FormValidator.globalVars.rules[editable.index].rules;
		value = FormValidator.getValue( editable );

		FormValidator.debug( ('-------------------- validation '+editable.name+' --------------------') );
		FormValidator.debug( ('Valeur = '+value), true );

		if ( rule === null || rule.length <= 0 || rule.allowEmpty ){
			return true;
		}

		// Un editable peut avoir plusieurs regles de validations...
		for (i=0; i<rule.length; i++){
			params = FormValidator.getRulesParams( editable, i );

			if( params !== undefined && !FormValidator.isValid( editable, value, i, params, onchange ) ) {
				return false;
			}
		}

		// Il n'y a pas eu de return false, c'est que l'editable a passer les tests
		FormValidator.debug( FormValidator.globalVars.rules[editable.index].rules[0].name+' = true' );

		return true;
	},
	
	/**
	 * Moteur de validation (utilise webrsa.validaterules.js)
	 * Vérifi un editable
	 * Renseigner onchange permet ou pas l'affichage du message d'érreur du champ (lié à l'evenement onchange)
	 * Fonctionne avec doValidation()->isValid()
	 * 
	 * @param {HTML} editable
	 * @param {Boolean} onchange
	 * @returns {Boolean}
	 */
	validate: function ( editable, onchange ){
		'use strict';
		if ( editable === undefined || editable === null || editable.index === undefined ||  FormValidator.globalVars.rules[editable.index] === undefined ){
			return true;
		}

		// onchange permet l'affichage des erreurs en true 
		// empeche les evenements comme onkeypress de déclancher l'affichage d'erreurs
		onchange = giveDefaultValue( onchange, false );

		// On retire l'éventuel message d'érreur
		if ( FormValidator.globalVars.validationOnchange && onchange ){
			FormValidator.removeError( editable );
		}

		return FormValidator.doValidation( editable, onchange );
	},
	
	/**
	 * Affiche le message d'erreur sous le menu de navigation (en haut)
	 * 
	 * @returns {undefined}
	 */
	showHeaderError: function (){
		'use strict';
		// Affiche le message d'erreur si aucun message n'est trouvé
		$$('#pageContent>p.error, #incrustation_erreur>p.error').each(function( obj ){ obj.remove(); });

		$('incrustation_erreur').innerHTML = '<p class="error"><img src="/img/icons/exclamation.png" alt="Erreur">	Erreur lors de l\'enregistrement</p>';
	},
	
	/**
	 * Vérifi l'intégralité des editables d'un formulaire
	 * 
	 * @param {HTML} form
	 * @returns {Boolean}
	 */
	checkAll: function ( form ){
		'use strict';
		// Si variable rules n'existe pas, on envoi le formulaire
		if ( FormValidator.globalVars.rules === undefined || $('noValidation').checked ){
			return true;
		}

		var valid = true;

		// Pour chaque éditables... On vérifi la valeur...
		$$('#' + form.id + ' input, #' + form.id + ' select,' + form.id + ' textarea').each( function( editable ){
			if ( editable.getAttribute('type') !== 'hidden' && !editable.disabled && !FormValidator.validate( $( editable ), true ) ){
				if ( valid ){
					$( editable ).scrollTo();
					window.scrollTo(0, window.pageYOffset);				
				}
				FormValidator.debug( ('Validation échoué :') );
				FormValidator.debug( editable );
				valid = false;
			}
		});

		// Si un élément est faux, on n'envoi pas le formulaire
		if ( !valid ){
			// Empeche les boutons submit de se griser
			$$('#' + form.id + ' input[type="submit"], #' + form.id + ' button').each( function( submitButton ){
				setTimeout( function(){ submitButton.removeAttribute('disabled'); }, 100 );
			});
			FormValidator.debug( ('/!\\ Le formulaire n\'a pas été envoyé car il y a un ou plusieurs champs pas/mal rempli.') );

			// Affiche le message d'érreur sous le menu de navigation
			FormValidator.showHeaderError();
			return false;
		}
		FormValidator.debug( ('Validation réussie') );

		// Empeche les submit de se griser en mode debug
		if ( FormValidator.globalVars.debugMode ){
			$$('#' + form.id + ' input[type="submit"], #' + form.id + ' button').each( function( submitButton ){
				setTimeout( function(){ submitButton.removeAttribute('disabled'); }, 100 );
			});
		}

		// En mode debug, empeche l'envoi du formulaire pour afficher en console les informations
		return !FormValidator.globalVars.debugMode;
	},
	
	/**
	 * Lié à validate()
	 * Attend 10 milisecondes avant de vérifier
	 * Indispensable lors de l'utilisation des evenements onchange et onkeypress
	 * (sinon l'evenement est envoyé avant la modification effective du champ...)
	 * 
	 * @param {HTML} editable
	 * @param {Boolean} onchange
	 * @returns {Boolean}
	 */
	validateWithTimeout: function ( editable, onchange ){
		'use strict';
		setTimeout( FormValidator.validate, 10, editable, onchange );
	},
	
	/**
	 * Ajoute les evenements onchange, onclick et onsubmit sur les elements concernés.
	 * 
	 * @param {HTML} editable
	 * @param {String} type
	 * @returns {void}
	 */
	addEvent: function ( editable, type ){
		'use strict';

		type = type === undefined ? 'editable' : type;

		if ( type === 'editable' ){
			// On lui attribu les evenements onchange et onkeypress qui déclancherons la validation
			Event.observe( editable, 'change', function(){
				// On ne lance la validation que si une différence est trouvé entre l'ancienne et la nouvelle valeur
				FormValidator.getValue( this );
				if ( FormValidator.globalVars.values[this.name].value !== FormValidator.globalVars.values[this.name].oldValue ){
					FormValidator.validateWithTimeout( this, true );
				}
				FormValidator.globalVars.values[this.name].oldValue = String( FormValidator.globalVars.values[this.name].value );
			}); // jshint ignore:line

			Event.observe( editable, 'keypress', function(){
				FormValidator.globalVars.values[this.name].oldValue = FormValidator.globalVars.values[this.name].oldValue === null ? String(  FormValidator.globalVars.values[this.name].value ) : FormValidator.globalVars.values[this.name].oldValue;
				FormValidator.getValue( this );
				// Lance la validation mais sans affichage d'erreur
				FormValidator.validateWithTimeout( this, false );
			});
		}
		else{
			Event.observe(
				editable,
				'submit',
				function( event ) {
					if( $$( 'input[type=hidden][name="data[Cancel]"]' ).length === 0 && FormValidator.checkAll( this ) === false ) {
						Event.stop( event );
					}
				}
			);
		}
	},
	
	/**
	 * Initialise les objets en rapport avec les editables pour le traitement des vérifications
	 * Leurs ajoute si besoin les evenements via addEvent()
	 * 
	 * @param {HTML} editables
	 * @returns {void}
	 */
	initEditables: function ( editables ){
		'use strict';
		var i, name, formatedName, editable;

		// On fait le tour de tout les editables
		for (i=0; i<editables.length; i++){
			name = editables[i].name;
			editables[i].index = i;
			FormValidator.debug( ('window.onload - Element '+name), true, true );

			// Si l'editable n'a pas de nom, on passe à un autre
			if ( name === undefined || name === '' || name === 'Cancel' ){
				continue;
			}

			// Pour chaque editable, on lui attribu des regles de validations (voir getRules() )
			editable = {name: name, id: editables[i].id, rules: FormValidator.getRules(name)};

			// Si ne possède pas une/des règles de validation
			if ( editable.rules === null ){
				continue;
			}

			// Value permet de stocker les valeurs pour vérifier si un champ à été modifié
			FormValidator.globalVars.values[name] = {value: null, oldValue: null};

			// Formate le nom en Model.field
			formatedName = FormValidator.getModelName( name ) + '.' + FormValidator.getFieldName( name );
			FormValidator.debug( ('window.onload - Nom formaté ' + formatedName), true, true );

			// Permet de faire le lien entre les editables ayant le même nom formaté (même model, même champ)
			FormValidator.globalVars.editableList[formatedName] = giveDefaultValue(  FormValidator.globalVars.editableList[formatedName], [] );
			FormValidator.globalVars.editableList[formatedName].push( {editable: editables[i], form: $(editables[i]).up('form')} );

			FormValidator.debug( ('window.onload - var editableList[formatedName] :'), true, true );
			FormValidator.debug( (FormValidator.globalVars.editableList[formatedName]), true, true );

			FormValidator.debug( ('window.onload - var editable :'), true, true );
			FormValidator.debug( editable, true, true );

			// On sauvegarde les regles pour cet editable
			FormValidator.globalVars.rules[i] = editable; 

			// On sauvegarde la valeur de l'editable
			FormValidator.globalVars.values[name] = {value: null};
			FormValidator.getValue( editable );

			// On lui attribu les evenements onchange et onkeypress qui déclancherons la validation
			FormValidator.addEvent( editables[i] );

			FormValidator.debug( '--------Fin de l\'attribution de regles pour cet element---------', true, true );
			FormValidator.debug( '', true, true );
			FormValidator.debug( '', true, true );
		}
	},
	
	/**
	 * Initialise les formulaires pour leur appliquer via addEvent() l'evenement onsubmit
	 * 
	 * @param {type} forms
	 * @returns {undefined}
	 */
	initForms: function ( forms ){
		'use strict';
		var key;

		for (key in forms){
			if ( forms.hasOwnProperty(key) ){
				// Empeche l'envoi d'un formulaire non valide si validationOnsubmit est vrais (voir webrsa.inc)
				if ( FormValidator.globalVars.validationOnsubmit !== 1 ){
					break;
				}
				// Envenement onsubmit sur le formulaire (lance une vérification complète)
				FormValidator.addEvent( forms[key], 'form' );
			}
		}
	},
	
	/**
	 * Attribu les règles de validation pour chaques input|select|textarea (editable)
	 * Surveille égelement quelques evenements :
	 * onsubmit sur les formulaires
	 * onchange et onkeypress sur les editables
	 * Utilise initEditables()|initForms() -> addEvent()
	 * 
	 * @returns {Boolean}
	 */
	init: function (){
		'use strict';
		var editables, forms;
		if ( FormValidator.globalVars.validationJS === undefined 
				|| FormValidator.globalVars.validationRules === undefined 
				|| FormValidator.globalVars.validationOnsubmit === undefined 
				|| FormValidator.globalVars.traductions === undefined 
				|| Validation === undefined 
				|| FormValidator.globalVars.validationOnchange === undefined 
				|| giveDefaultValue === undefined ) {
			FormValidator.debug( ('validationJS ou validationRules absent!') );
			return false;
		}

		// Editable fait référence à tout ce qui est modifiable par l'utilisateur (input, select et textarea)
		editables = $$('form input, form select, form textarea');

		FormValidator.initEditables( editables );

		FormValidator.debug( '------------------------rules--------------------------', true, true );
		FormValidator.debug( FormValidator.globalVars.rules, true, true );
		FormValidator.debug( '-------------------------end---------------------------', true, true );

		// On diférencie les formulaires
		forms = $$('form');

		FormValidator.initForms( forms );

		if ( $('pageFooter') ){
			$('pageFooter').insert('<input type="checkbox" id="noValidation">');
		}
	}
}

document.observe( "dom:loaded", FormValidator.init );
