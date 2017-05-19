	/**
	* Contient le contenu des parenthèses retiré du rapport
	* @type Array
	*/
	var brackets = [];

	/**
	* Transforme un SELECT [0] FROM ... en SELECT (contenu de bracket[0]) FROM ...
	* 
	* @param {HTML} span L'evenement contenant en target: le <span> contenant un texte de type [0]
	* @returns {Boolean}
	*/
	function restoreBrackets( span ) {
		var innerText = span.target.innerHTML !== undefined ? span.target.innerHTML : '',
			className;
	
		if ( innerText.substr(0, 1) === '[' && brackets[innerText.substr(1, innerText.length -2)] !== undefined ) {
			span.target.innerHTML = '('+ brackets[innerText.substr(1, innerText.length -2)] +')';
			span.target.removeAttribute('style');
			span.target.style.cursor = 'auto';	
			className = span.target.className === undefined || span.target.className === 'even' ? 'odd' : 'even';
			span.target.select('span').each(function(innerSpan){ innerSpan.style.color = 'red'; innerSpan.className = className; });
			return true;
		}
		
		return false;
	}
	
	/**
	 * Utilise restoreBrackets() de façon recursive
	 * 
	 * @param {HTML} span
	 * @returns {Boolean}
	 */
	function restoreAllBrackets( span ) {
		if (restoreBrackets({target: span})) {
			span.select('span').each(function(subspan){
				restoreAllBrackets(subspan);
			});
			return true;
		}
		return false;
	}
	
	/**
	* Envoi sql à url et affiche le resultat dans pre
	* 
	* @param {string} sql code SQL à traiter
	* @param {HTML} pre container pour l'affichage du resultat
	* @param {string} url pour la requete Ajax
	* @param {string} image image de chargement
	* @param {string} failureMsg message en cas d'evenement onFailure
	* @param {string} exceptionMsg message en cas d'evenement onException
	* @returns {void}
	*/
	function analyse( sql, pre, url, image, failureMsg, exceptionMsg ){
		/**
		 * On affiche le bloc <pre> et on y colle une image de charchement
		 */
		pre.style.display = 'block';
		pre.innerHTML = '<div class="center">'+image+'</div>';

		/**
		 * On demande à AnalysesqlsController de produire un rapport sur le contenu de sql
		 */
		new Ajax.Request(url+'/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: {
				'sql': sql.replace("\t", "")
			}, 
			requestHeaders: {Accept: 'application/json'},
			/**
			 * En cas de succès, en rempli la variable brackets et on insert le rapport dans la balise <pre>
			 * On ajoute également un evenement au clic sur les span du rapport qui lance restoreBrackets()
			 * 
			 * @param {object} request
			 * @param {json} json
			 * @returns {void}
			 */
			onComplete:function(request, json) {
				brackets = json.innerBrackets;
				pre.innerHTML = json.text;
				pre.select('span').each(function(span){
					span.style.color = 'red';
					span.observe('click', restoreBrackets, span);
				});
				pre.select('div.restoreBrackets span').each(function(span){
					restoreAllBrackets( span );
				});
			},

			/**
			 * Affiche d'un message d'érreur en cas de problème
			 * 
			 * @returns {void}
			 */
			onFailure:function() {
				pre.innerHTML = failureMsg;
			},
			onException:function() {
				pre.innerHTML = exceptionMsg;
			}
		});
	}