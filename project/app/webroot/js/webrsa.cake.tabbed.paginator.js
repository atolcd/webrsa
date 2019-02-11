var CakeTabbedPaginator = {
	/**
	 * On ajoute explicitement le paramètre nommé page:1 pour les liens <<,
	 * < et 1
	 * @param {String} paginationLinksSelector Le sélecteur vers les liens de
	 *	pagination de page en page, qui sera complété par ' a'
	 * @returns {undefined}
	 */
	explicitPages: function(paginationLinksSelector) {
		var rel, text;
		try {
			$$(paginationLinksSelector + ' a').each(function(link) {
				rel = $(link).readAttribute( 'rel' );
				text = $(link).innerHTML;
				if('1' === text || 'first' === rel || 'prev' === rel) {
					$(link).href = replaceUrlNamedParam( $(link).href, 'page', '1' );
				}
			});
		} catch( Exception ) {
			console.error( Exception );
		}
	},
	/**
	 *
	 * @param {String} tabSelector
	 * @param {String} paginationSelector
	 * @returns {undefined}
	 */
	initTab: function(tabSelector, paginationSelector) {
		var params = {},
			re = /\/(sort|direction|page)\[([^\]]+)\]:([^\/#]*)/ig,
			matches;

		// Collecte des paramètres nommés ayant des clés dans l'URL
		while( null !== ( matches = re.exec( window.location.href.toString() ) ) ) {
			params[matches[1] + '[' + matches[2] + ']'] = matches[3];
		}

		// Transformation des liens de chacuns des onglets en ajoutant la clé du modèle
		$$(tabSelector).each(function(div) {
			var id = $(div).readAttribute('id');
			['thead a', paginationSelector + ' a'].each(function(selector) {
				$(div).getElementsBySelector(selector).each(function(link) {
					$(link).href = $(link).href.replace( /\/(sort|direction|page):/g, '/$1[' + id + ']:' );
				});
			});
		});

		// Ajout, pour chaque lien, des paramètres nommés ayant des clés dans l'URL s'ils n'existent pas dans le lien
		[tabSelector + ' thead a', tabSelector + ' ' + paginationSelector + ' a'].each(function(selector) {
			$$(selector).each(function(link) {
				for (var key in params) {
					if( params.hasOwnProperty(key) ) {
						re = new RegExp( regExpQuote(key), 'gi' );
						if( null === re.exec( $(link).href ) ) {
							$(link).href = replaceUrlNamedParam( $(link).href, key, params[key] );
						}
					}
				}
			});
		});
	},
	init: function(wrapperId, titleLevel, tabSelector, paginationSelector) {
		wrapperId = 'undefined' === typeof wrapperId ? 'tabbedWrapper' : wrapperId;
		titleLevel = 'undefined' === typeof titleLevel ? 2 : titleLevel;
		tabSelector = 'undefined' === typeof tabSelector ? 'div.tab' : tabSelector;
		paginationSelector = 'undefined' === typeof paginationSelector ? '.pagination' : paginationSelector;

		makeTabbed(wrapperId, titleLevel);

		// Permet de rester sur le bon onglet lorsqu'on trie sur une colonne ou que l'on passe de page en page
		$$(tabSelector).each( function(tab) {
			var id = $(tab).readAttribute('id');
			$(tab).getElementsBySelector( 'thead a', paginationSelector + ' a' ).each( function(link) {
				$(link).writeAttribute( 'href', $(link).readAttribute( 'href' ) + '#' + wrapperId + ',' + id );
			} );
		} );

		this.explicitPages( tabSelector + ' ' + paginationSelector );
		this.initTab(tabSelector, paginationSelector);
	}
};
