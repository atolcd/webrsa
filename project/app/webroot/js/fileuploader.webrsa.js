/**
 * La classe WebrsaFileUploader étend la classe FileUploader:
 *	- information de statut: Copié ou le message d'erreur
 *	- liens voir et supprimer (@see o.links.add, o.links.delete)
 */
qq.WebrsaFileUploader = function( o ) {
	// call parent constructor
	qq.FileUploader.apply( this, arguments );

	// additional options
	qq.extend( this._options, {
		template: '<div class="qq-uploader">' +
			'<div class="qq-upload-drop-area"><span>Drop files here to upload</span></div>' +
			'<div class="qq-upload-button">Parcourir</div>' +
			'<ul class="qq-upload-list"></ul>' +
		'</div>',
		// template for one item in file list
		fileTemplate: '<li>' +
			'<span class="qq-upload-file"></span>' +
			'<span class="qq-upload-spinner"></span>' +
			'<span class="qq-upload-size"></span>' +
			'<a class="qq-upload-cancel" href="#">Annuler</a>' +
			'<span class="qq-upload-failed-text">Erreur</span>' +
		'</li>',
		onProgress: function(id, fileName, loaded, total){
			// Il s'agit toujours du dernier élément de la liste, id n'est pas fiable (lorsqu'on supprime un élément)

			// Fix pour l'attribut style écrit en dur
			var spans = $$( '.qq-upload-size' );
			var span = spans[$(spans).length-1];
			$(span).writeAttribute( 'style', '' );

			// Fix pour le nom du fichier tronqué en dur
			var files = $$( '.qq-upload-file' );
			var file = files[$(files).length-1];
			$(file).update( fileName );
		},
		setStatus: function( success, message ) {
			var statuses = $$( '.qq-upload-failed-text' );
			var status = statuses[$(statuses).length-1];

			$(status).update( message );
			$(status).addClassName( 'qq-upload-status-text' );
			if( success ) {
				$(status).addClassName( 'success' );
			}
			else {
				$(status).addClassName( 'error' );
			}
		},
		onComplete: function( id, fileName, responseJSON ) {
			var success = false;
			var message = 'Erreur inattendue';

			// 2°) Traitement du retour de l'appel ajax
			// 2° 1°) Succès
			if( typeof responseJSON.success !== 'undefined' && responseJSON.success === true ) {
				var files = $$( '.qq-upload-file' );
				var file = files[$(files).length-1];

				this.addAjaxUploadedFileLinks( file, fileName );

				message = 'Copié';
				success = true;
			}
			// 2° 2°) Erreur
			else if( typeof responseJSON.error !== 'undefined' ) {
				message = responseJSON.error;
			}

			this.setStatus( success, message );
		},
		showMessage: function( message ) {
			this.setStatus( false, message );
		},
		addAjaxUploadedFileLinks: function( elmt, fileName ) {
			if( typeof fileName === 'undefined' ) {
				fileName = $( elmt ).innerHTML;
			}

			// Lien voir
			var href = o['links']['view'] + '/' + fileName;
			var link = new Element( 'a', { 'href': href, 'class': 'qq-upload-view' } ).update( 'Voir' );
			$( elmt ).up( 'li' ).insert( { bottom: link } );

			// Lien supprimer
			href = o['links']['delete'] + '/' + fileName;
			link = new Element( 'a', { 'href': href, 'class': 'qq-upload-delete' } ).update( 'Supprimer' );
			Event.observe( link, 'click', function(e){
				Event.stop(e);
				new Ajax.Request(
					$(Event.element(e)).getAttribute('href'),
					{
						method: 'post',
						onComplete: function( transport ) {
							try {
								response = eval( '(' + transport.responseText + ')' );
							} catch(err){
								response = {};
							}

							if( response.success && response.success === true ) {
								$( elmt ).up( 'li' ).remove();
							}
							else {
								alert( 'Erreur!' );
							}
						}
					}
				);
			} );
			$( elmt ).up( 'li' ).insert( { bottom: link } );
		}
	} );
	// overwrite options with user supplied
	qq.extend(this._options, o);

	this._element = this._options.element;
	this._element.innerHTML = this._options.template;
	this._listElement = this._options.listElement || this._find(this._element, 'list');

	this._classes = this._options.classes;

	this._button = this._createUploadButton(this._find(this._element, 'button'));

	this._bindCancelEvent();
	this._setupDragDrop();
};

// Inherit from FileUploader
qq.extend( qq.WebrsaFileUploader.prototype, qq.FileUploader.prototype );
