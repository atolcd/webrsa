<?php
	$departement = Configure::read( 'Cg.departement' );
	$controller = $this->params->controller;
	$action = $this->action;
	$formId = ucfirst($controller) . ucfirst($action) . 'Form';
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	echo $this->Default3->titleForLayout( array(), array( 'domain' => $domain ) );
	
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	$requestManagerGroupOptions = '';
	foreach ( Hash::get($options, 'Requestmanager.requestgroup_id') as $key => $value ) {
		$requestManagerGroupOptions .= "<option value=\"{$key}\">{$value}</option>";
	}
	
	$modelsList = '';
	foreach ( Hash::get($options, 'Requestmanager.modellist') as $key => $value ) {
		$modelsList .= "<option value=\"{$key}\">{$value}</option>";
	}
?>

<form action="<?php echo Router::url( array( 'controller' => $controller, 'action' => 'search' ) );?>" method="post">
	<fieldset>
		<legend><?php echo __m('Requestmanager.savedsearch');?></legend>
		<?php echo $this->Xform->input( "Requestmanager.name", 
			array( 
				'label' => __m('Requestmanager.name'), 
				'type' => 'select', 
				'options' => Hash::get($options, 'Requestmanager.grouped_name'), 
				'empty' => true 
			) 
		);?>
		<div class="center">
			<input type="submit" value="Rechercher">
			<input type="button" value="Charger" id="generateButton">
		</div>
		<div class="center" id="loading_generate" style="display: none; margin-top: 20px;">
			<img src="<?php echo $this->webroot; ?>img/loading.gif" />
			<marquee behavior="scroll" direction="right" style="width: 100px; display: inline-block;">Chargement...</marquee>
		</div>
	</fieldset>
</form>

<br><hr>

<h2><?php echo __m('Requestmanager.title_creation');?></h2>
<form action="<?php echo Router::url( array( 'controller' => $controller, 'action' => 'newrequest' ) );?>" method="post" id="FormRequestmaster">
	<fieldset>
		<legend><?php echo __m('Requestmanager.newsearch');?></legend>
		<input type="hidden" name="data[Requestmanager][actif]" id="RequestmanagerActif" value="1">
		<?php echo $this->Xform->input( "Requestmanager.requestgroup_id", 
			array( 
				'label' => __m('Requestmanager.requestgroup_id').REQUIRED_MARK, 
				'type' => 'select', 
				'options' => Hash::get($options, 'Requestmanager.requestgroup_id'), 
				'empty' => true 
			) 
		)
		.$this->Xform->input( "Requestmanager.name", 
			array( 
				'label' => __m('Requestmanager.name').REQUIRED_MARK, 
				'id' => 'RequestmanagerNameNew'
			) 
		);?>
		<div class="center"><input type="button" id="reset" value="Reset"/></div>
	</fieldset>
	<br>
	<h3><?php echo __m('Requestmanage.main_table');?></h3>
	<fieldset>
		<div class="input select">
			<label for="RequestmanagerFrom"><?php echo __m('Requestmanager.from').REQUIRED_MARK;?></label>
			<select name="data[Requestmanager][from]" id="RequestmanagerFrom">
				<option value=""></option>
				<?php echo $modelsList; ?>
			</select>			
		</div>
		<div class="error" style="display:none" id="error-from-from"><p>Une érreur s'est produite!</p></div>
		<div style="display:none" id="fields-from-from"></div>
		<div style="display:none" id="joins-from-from"></div>
	</fieldset>
	<div id="zoneJointure"></div>
	<fieldset id="endForm">
		<div class="input textarea">
			<label for="AddFields">Champs supplémentaires<label>
					<textarea id="Addfields" name="data[Add][fields]" placeholder='MYFUNCT("Matable1"."monchamp", "Matable2"."monautrechamp") AS "Monchampcustom__avec2underscore"'></textarea>
		</div>
		<div class="input textarea">
			<label for="AddConditions">Conditions supplémentaires<label>
			<textarea id="AddConditions" name="data[Add][conditions]" placeholder='(Matable1.monchamp IN (SELECT matable1_id from matable2) AND Matable1.monautrechamp = 2) OR Matable2.encoreunchamp IS NULL'></textarea>
		</div>
		<div class="input text">
			<label for="Order">Order by</label>
			<input type="text" id="Order" name="data[Add][order]" placeholder='Matable1.date DESC, Matable1.id'>
		</div>
		<div class="center notice" id="div-verification">
			<p id="msg-validation" class="center">Selectionnez une table principale</p>
			<input type="button" value="Vérifier" id="verificationButton">
		</div>
		<div class="center">
			<input type="submit" value="Enregistrer et rechercher" name="saveandsearch" class="disable-if-not-validated" disabled="true">
			<input type="submit" value="Ne pas enregistrer mais rechercher" name="donotsaveandsearch" class="disable-if-not-validated" disabled="true">
		</div>
	</fieldset>
</form>


<script>
	/* global Ajax, extract, $break, $$ */
	var labelTable = '<?php echo __m('Requestmanager.labeltable');?>';
	var labelJoin = '<?php echo __m('Requestmanager.labeljoin');?>';
	var modelsList = '<?php echo $modelsList;?>';
	var _collection = {};
	var joins = {};
	var generatedFields = [];
	var generatedConditions = [];
	var joinFinish = new Element('finish'); // Element spécial uniquement présent pour le support d'evenement
	var joinsToDo = {};
	var nbJoin = {}; // Enregistre le nombre de jointures effectué par Modeles
	var advanced = true; // active la gestion avancée des jointures
	var max_input_vars = <?php echo ini_get('max_input_vars'); ?>;
	var varimg = new Element('img', {src: '<?php echo $this->webroot; ?>img/loading.gif'});
	
	/**
	 * Créer la div pour présentation d'une table jointe
	 * 
	 * @param {string} prevAlias
	 * @param {string} newAlias
	 */
	function createZoneJointure(prevAlias, newAlias) {console.log("createZoneJointure:"+prevAlias+":"+newAlias);
		var idJoin = prevAlias+'__'+newAlias;
		
		// Ajout d'un nouveau fieldset (voir plus haut, la partie en html)
		$('zoneJointure').insert({bottom: '<div id="'+idJoin+'"><br><h3>Jointure depuis '+prevAlias+' vers '+newAlias+'</h3><fieldset><div class="error" style="display:none" id="error-'+prevAlias+'-'+newAlias+'"><p>Une érreur s\'est produite!</p></div><div style="display:none" id="fields-'+prevAlias+'-'+newAlias+'"></div><div style="display:none" id="joins-'+prevAlias+'-'+newAlias+'"></div></fieldset><div>'});
		console.log(['select:event:change:zoneJointure:insert', $(idJoin)]);

		// Appel ajax pour remplir le fieldset
		$('joins-'+prevAlias+'-'+newAlias).insert( {top: "<h4>Jointures sur la table "+newAlias+"</h4>"} );console.log(['select:event:change:joins-'+prevAlias+'-'+newAlias+':insert', $('joins-'+prevAlias+'-'+newAlias)]);
	}
	
	/**
	 * Lorsque on selectionne une jointure...
	 * 
	 * @param {DOM} that - Element select
	 * @param {Object} json
	 */
	function jointureOnChange(that, json) {console.log(['jointureOnChange:', that, json]);
		var alias = that.getValue(),
			index = that.getAttribute('index'),
			oldindex = that.getAttribute('oldindex'),
			idDiv = json.alias+'__'+alias,
			fieldset = new Element('fieldset', {}),
			divButtons = new Element('div', {'class': 'center'}),
			btnAnnuler = new Element('input', {type: 'button', value: 'Annuler'}),
			btnJoindre = new Element('input', {type: 'button', value: 'Joindre'})
		;

		if (alias !== '' && (!$(idDiv) || advanced)) {
			if ( $(that.prevId) !== undefined ) {console.log(['select:event:change:remove', $(that.prevId)]);
				$(that.prevId).remove();
			}

			// On ajoute un select en cas de selection pour jointure multiple
			createJoinList( json, index, oldindex );

			// jointure simple
			if (!advanced) {
				that.prevId = idDiv;

				createZoneJointure(json.alias, alias, index);

				// Lorsque getModel est fini, on indique que la jointure est terminée
				that.observe('finish:getModel:'+alias, function() {
					this.fire('finish:join:'+this.id);
				});

				getModel( alias, alias, json.alias, that );

			// jointure complexe
			} else {
				that.disabled = true;

				divButtons.insert(btnAnnuler);
				divButtons.insert(btnJoindre);
				fieldset.insert('<legend>'+alias+'</legend>\n\
					<div class="input text">\n\
						<label>Alias</label>\n\
						<input type="text" class="alias" name="data['+json.alias+']['+alias+'][alias]">\n\
					</div><div class="input text">\n\
						<label>Table</label>\n\
						<input type="text" class="table" name="data['+json.alias+']['+alias+'][table]">\n\
					</div><div class="input text">\n\
						<label>Conditions</label>\n\
						<input type="text" class="conditions" name="data['+json.alias+']['+alias+'][conditions]">\n\
					</div><div class="input text">\n\
						<label>Type</label>\n\
						<input type="text" class="type" name="data['+json.alias+']['+alias+'][type]">\n\
					</div>'
				);
				fieldset.insert(divButtons);
				that.up().insert(fieldset);
						
				getJointure(json.alias, alias);

				// Bouton Annuler
				btnAnnuler.observe('click', function(){
					fieldset.up().up().select('div.input.select').last().remove(); // Retire le dernier select
					fieldset.up().select('select').first().setValue(''); // Remet à vide le select (Jointure sur :)
					fieldset.up().select('select').first().disabled = false; // Réactive le select
					fieldset.remove(); // Retire les conditions de jointure
				});

				// Bouton Joindre
				btnJoindre.observe('click', function(){
					var join = {
							alias: this.up('fieldset').select('input.alias').first().getValue(),
							table: this.up('fieldset').select('input.table').first().getValue(),
							conditions: this.up('fieldset').select('input.conditions').first().getValue(),
							type: this.up('fieldset').select('input.type').first().getValue()
						},
						select = this.up('div.input.select').select('select').first()
					;

					// Empeche les jointures multiple avec même alias
					if ($('unique__'+join.alias)) {
						alert("Une jointure existe déjà sur "+join.alias+". Essayez de mettre par exemple dans Alias : "+join.alias+"_2.");
						return false;
					}

					createZoneJointure(json.alias, join.alias, join.alias);
					getTable(json.alias, join, join.alias, select);
				});
			}
		}
		else if (that.prevId !== undefined) {console.log(['select:event:change:remove', $(that.prevId)]);
			$(that.prevId).remove();console.log(['select:event:change:remove', that.up('div')]);
			that.up('div').remove();
		} 
		else {console.log(['select:event:change:this:setValue:', that]);
			that.setValue('');
		}
		
		that.fire('select:event:changed');
	}
		
	
	/**
	 * Création d'un menu déroulant pour les jointures
	 * 
	 * @param {json} json
	 * @param {string} index - Pour les noms dynamiques, garni les div et nomme en fonction de index
	 * @param {string} oldindex - ancien index pour le remplissage des champs Alias
	 * @returns {void}
	 */
	function createJoinList( json, index, oldindex ) {console.log(['createJoinList: (ajout d\'un select pour jointure', json, index, oldindex]);
		var idJoin = 'join-'+oldindex+'-'+index+'-'+$('joins-'+oldindex+'-'+index).select('select').length;
		var div = new Element('div', {'class': 'input select'});
		var label = new Element('label', {for: idJoin});
		var select = new Element('select', {
			id: idJoin,
			name: idJoin,
			index: index,
			oldindex: oldindex
		});
		var options = [];
		
		label.insert(labelJoin);
		select.insert(new Element('option',{value:''}));
		
		if (advanced) {
			select.insert(new Element('option').insert('---Jointure custom---'));
		}
		
		div.insert(label);
		div.insert({bottom: select});
		
		for (var i=0; i<json.joins.length; i++) {
			options[i] = new Element('option', {value: json.joins[i]});
			options[i].insert(json.joins[i]);
			select.insert({bottom: options[i]});
		}
		
		$('joins-'+oldindex+'-'+index).insert( {bottom: div} );console.log(['createJoinList:insert:joins-'+oldindex+'-'+index, div]);
		
		_collection[idJoin] = select;
		
		$(idJoin).observe('change', function(){ jointureOnChange(this, json); });
	}
	
	/**
	 * Action lors de la modification d'une condition
	 */
	function inputChange() {console.log('inputChange:');
		if ( this.oldValue === undefined ) {
			// On permet un bon alignement avec des elements invisibles identique
			var newDiv = new Element('div', {id: 'div-'+this.id+'_', class: 'subinput'});
			var clone = this.clone(true);
			
			clone.id = clone.id + '_';
			clone.name = clone.name + '_';
			clone.setValue('');
			
			newDiv.insert({bottom: clone});
			this.up('div').up('div').insert({bottom: newDiv});
			this.oldValue = $(this.id).getValue();
			clone.observe('change', inputChange);
		}
		else if ( this.getValue() !== '' ) {
			this.oldValue = this.getValue();
		}
		else {
			this.oldValue = undefined;
			this.setValue($(this.id+'_').getValue());
			$('div-'+this.id+'_').remove();
			this.simulate('change');
		}
		
		this.fire('finish:condition:'+this.id);
	}
	
	/**
	 * onClick des boutons aux '...' - Appel ajax des valeurs possibles d'un champ (max 100)
	 * Ajoute un select
	 * 
	 * @returns {void}	 
	 */
	function findList() {console.log('findList:');
		var button = this;
		new Ajax.Request('<?php echo Router::url( array( 'controller' => $controller, 'action' => 'ajax_list' ) ); ?>/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: {
				'alias': button.readAttribute('alias'),
				'field': button.readAttribute('field')
			}, 
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {console.log('findList:onComplete');
				// On transforme l'input en select
				var input = $(button.readAttribute('link'));
				var select = new Element('select', {
					id: input.id,
					name: input.name
				});
				input.simulate('change');
				input.remove();
				var option = [new Element('option', {value: ''})];
				select.insert(option[0]);
				
				for (var i=0; i<json.enum.length; i++) {
					option.push(new Element('option', {value: json.enum[i]}));
					option[option.length -1].insert(json.enum[i]);
					select.insert({bottom: option[option.length -1]});
				}
				// On selectionne la derniere valeur pour simuler un "change" pour recréer un input
				select.setValue(json.enum[i]);
				
				button.up('div').insertBefore(select, button);
				button.remove();
				
				select.observe('change', inputChange);
			}
		});
	}
	
	/**
	 * Création de la liste de champs pour une table avec les conditions et les checkbox
	 * 
	 * @param {} json - Informations sur la table (renvoyé par php)
	 * @param {} oldindex - Nom du modele sur lequel on effectue la jointure
	 * @param {} index - Sert uniquement pour la table principale (ex: div#fields-from-from)
	 * @param {} modelName - Alias de la jointure
	 * @param {} dom - Element select qui a servi a lancer cette methode
	 * @param {Object} join - Optionnel : {table: '', alias: '', conditions: '', type: ''}
	 */
	function onGetModelComplete(json, oldindex, index, modelName, dom, join) {console.log('onGetModelComplete()');console.log([json, oldindex, index, modelName, dom]);
		join = join === undefined ? {table: '', alias: '', conditions: '', type: ''} : join;
		
		// Permet de tester l'existance d'un alias
		dom.up('form').insert({top: new Element('input', {id: 'unique__'+modelName, type: 'hidden', class: modelName, value: 'unique'})});
		
		$('error-'+oldindex+'-'+index).hide();
		$('fields-'+oldindex+'-'+index).show();
		$('joins-'+oldindex+'-'+index).show();

		// On s'assure que les div portent le bon id (les premières portent from-from dans l'id, il faut renommer en from-Monmodel
		$('error-'+oldindex+'-'+index).oldid = 'error-'+oldindex+'-'+index;
		$('fields-'+oldindex+'-'+index).oldid = 'fields-'+oldindex+'-'+index;
		$('joins-'+oldindex+'-'+index).oldid = 'joins-'+oldindex+'-'+index;

		$('error-'+oldindex+'-'+index).id = 'error-'+oldindex+'-'+modelName;
		$('fields-'+oldindex+'-'+index).id = 'fields-'+oldindex+'-'+modelName;
		$('joins-'+oldindex+'-'+index).id = 'joins-'+oldindex+'-'+modelName;

		var h4 = new Element('h4');
		h4.insert('Champs de la table '+modelName);
		$('fields-'+oldindex+'-'+modelName).insert({bottom: h4});
		console.info(join);
		
		// Permet les jointures complexes
		if (join.alias !== '') {
			var joinTable = new Element('input', {
					'type': 'hidden',
					'name': 'joinscomplexe-'+modelName+'[table]',
					'value': join.table
				}),
				joinAlias = new Element('input', {
					'type': 'hidden',
					'name': 'joinscomplexe-'+modelName+'[alias]',
					'value': join.alias
				}),
				joinConditions = new Element('input', {
					'type': 'hidden',
					'name': 'joinscomplexe-'+modelName+'[conditions]',
					'value': join.conditions
				}),
				joinType = new Element('input', {
					'type': 'hidden',
					'name': 'joinscomplexe-'+modelName+'[type]',
					'value': join.type
				})
			;
			$('fields-'+oldindex+'-'+modelName).insert({bottom: joinTable})
					.insert({bottom: joinAlias}).insert({bottom: joinConditions}).insert({bottom: joinType})
			;
			dom.up().select('input').each(function(input){
				input.disable();
			});
			console.log('---------------------------------');
			console.info(dom.up().select('input[type="button"]'));
			console.log('---------------------------------');
		}
		
		// Verifi que ce model a bien des enums
		var enums = false;
		for (var key in json.enums){
			if ( json.enums.hasOwnProperty(key) ) {
				enums = true;
			}
		}

		// Sur chaque champs...
		for (var i=0; i<json.fields.length; i++) {
			// On remplace le nom de model Dans le json par modelName qui peut être un alias
			json.names[i] = json.names[i].replace(json.alias, modelName);
			
			// On creer la structure HTML avec un checkbox, un label avec un input et/ou select
			var divSelect = new Element('div', {class: 'input checkbox'});
			var divMain = new Element('div');
			divSelect.insert(divMain);
			var checkbox = new Element('input', {
				type: 'checkbox',
				name: oldindex+'-'+modelName+'-'+json.names[i],
				id: oldindex+'-'+modelName+'-'+json.ids[i],
				'original-name': json.names[i]
			});
			divMain.insert({bottom: checkbox});
			var label = new Element('label', {for: oldindex+'-'+modelName+'-'+json.ids[i]});
			var span = new Element('span');
			span.insert(json.fields[i]+' ('+json.traductions[i]+')');
			label.insert(span);
			divMain.insert({bottom: label});

			// Si un enum existe pour ce champ, on créer un select rempli des bonnes options
			if ( enums && json.enums[json.alias][json.fields[i]] !== undefined ) {
				var select = new Element('select', {
					id: 'conditions-select-'+oldindex+'-'+modelName+'-'+json.fields[i],
					name: 'conditions-select-'+oldindex+'-'+modelName+'-'+json.fields[i]
				});
				var option = [new Element('option', {value: ''})];
				select.insert({bottom: option[0]});

				// Pour chaque valeur de l'enum, on ajoute une option
				for (var key in json.enums[json.alias][json.fields[i]]){
					if ( json.enums[json.alias][json.fields[i]].hasOwnProperty(key) ) {
						option.push(new Element('option', {value: key}));
						option[option.length -1].insert(key + ' - ' + json.enums[json.alias][json.fields[i]][key]);
						select.insert({bottom: option[option.length -1]});
					}
				}

				divMain.insert({bottom: select});

				select.observe('change', inputChange);

				var input = new Element('input', {
					type: 'text',
					id: 'conditions-text-'+oldindex+'-'+modelName+'-'+json.fields[i],
					name: 'conditions-text-'+oldindex+'-'+modelName+'-'+json.fields[i],
					'original-name': json.names[i]
				});
				var subDiv = new Element('div', {class: 'subinput'});
				subDiv.insert(input);

				divMain.insert({bottom: subDiv});

				input.observe('change', inputChange);
			}

			// Sinon, on se contente d'un champ text et d'un boutton pour trouver des valeurs
			else {
				var input = new Element('input', {
					type: 'text',
					id: 'conditions-'+oldindex+'-'+modelName+'-'+json.fields[i],
					name: 'conditions-'+oldindex+'-'+modelName+'-'+json.fields[i],
					'original-name': json.names[i]
				});
				divMain.insert({bottom: input});
				var button = new Element('input', {
					value: '...',
					type: 'button',
					link: 'conditions-'+oldindex+'-'+modelName+'-'+json.fields[i],
					alias: json.alias,
					field: json.fields[i],
					title: 'Trouver des valeurs (max 100)'
				});
				divMain.insert({bottom: button});

				input.observe('change', inputChange);
				button.observe('click', findList);
			}

			$('fields-'+oldindex+'-'+modelName).insert( {bottom: divSelect} );
		}

		createJoinList( json, modelName, oldindex );
		joinsToDo[modelName] = false;
		dom.fire('finish:getModel:'+modelName);
		joinFinish.fire('join:finish');
	}
	
	/**
	 * Appel ajax pour obtenir la liste des champs d'un model et ses relations, ses enums et traductions.
	 * 
	 * @param {string} modelName - Nom du modele à intéroger
	 * @param {string} index - Pour les noms dynamiques, garni les div et nomme en fonction de index
	 * @param {string} oldindex - ancien index pour le remplissage des champs Alias
	 * @param {dom} dom - element sur lequel envoyer l'evenement finish
	 * @returns {void}	 
	 */ 
	function getModel( modelName, index, oldindex, dom ) {console.log(['getModel:', modelName, index, oldindex, dom]);
		var div = new Element('div', {'class': 'center'}),
			img = new Element('img', {src: '<?php echo $this->webroot; ?>img/loading.gif'});
		;
		div.insert(img);
		dom.up('fieldset').insert(div);
		
		new Ajax.Request('<?php echo Router::url( array( 'controller' => $controller, 'action' => 'ajax_get' ) ); ?>/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: {
				'model': modelName
			}, 
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {
				onGetModelComplete(json, oldindex, index, modelName, dom);
				div.remove();
			},
			onFail:function() {
				$('error-'+oldindex+'-'+index).show();
				console.error('error : getModel:'+oldindex+':'+modelName);
				div.remove();
			},
			onException:function() {
				$('error-'+oldindex+'-'+index).show();
				console.error('error : getModel:'+oldindex+':'+modelName);
				div.remove();
			}
		});
	}
	
	/**
	 * Select de la table principale, en cas de changement on fait un reset et on rempli avec le nouveau Modèle
	 */
	$('RequestmanagerFrom').observe('change', function(event) {console.log('RequestmanagerForm:event:change');console.info(this.getValue());
		$('RequestmanagerFrom').up('fieldset').select('div').each(function(div) {
			if ( div.oldid !== undefined ) {
				div.id = div.oldid;
				div.innerHTML = '';
			}
		});
		$('zoneJointure').innerHTML = '';
		num = 0;
		getModel( $F('RequestmanagerFrom'), 'from', 'from', $('RequestmanagerFrom') );
		$('joins-from-from').insert( {top: "<h4>Jointures sur la table "+$F('RequestmanagerFrom')+"</h4>"} );
	});
	
	/**
	 * En cas de changement sur le formulaire, il deviens à nouveau nécéssaire de Vérifier la requête (bouton en bas)
	 */
	$('FormRequestmaster').observe('change', function(){console.log('FormRequestmaster:event:change');
		var count = 0;
		
		// Compte le nombre d'element qui sera envoyé avec le formulaire
		$$('#FormRequestmaster select, #FormRequestmaster input, #FormRequestmaster textarea').each(function(element) {
			if (element.getAttribute('name') && (element.disabled === undefined || element.disabled === false)) {
				count++;
			}
		});
		if (count > max_input_vars) {
			alert("Le nombre d'inputs de la page ("+count+") dépasse la limite dans les réglages php ("+max_input_vars+"). Il y a un risque que certaines données soit perdu.");
		}
		
		$$('.disable-if-not-validated').each(function(submit){submit.setAttribute('disabled', true);});
		$('div-verification').removeClassName('success').removeClassName('error_message').addClassName('notice');
		$('msg-validation').innerHTML = 'La requête doit-être validée';
	});
	
	/**
	 * Appel ajax pour Vérification de la requête générée
	 */
	$('verificationButton').observe('click', function() {console.log('verificationButton:event:click');
		var params = Form.serializeElements( $$( '#FormRequestmaster input, #FormRequestmaster select, #FormRequestmaster textarea' ), { hash: true, submit: false } );

		new Ajax.Request('<?php echo Router::url( array( 'controller' => $controller, 'action' => 'ajax_check' ) ); ?>/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: params, 
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {
				if ( json.success ) {
					$$('.disable-if-not-validated').each(function(submit){
						// On ne permet la sauvegarde que si Catégorie et Titre de la requête sont remplis
						if ( submit.name !== 'saveandsearch' 
						|| ($('RequestmanagerRequestgroupId').getValue() !== '' && $('RequestmanagerNameNew').getValue() !== '' )) {
							submit.removeAttribute('disabled');
						}
					});
					$('div-verification').removeClassName('notice').removeClassName('error_message').addClassName('success');
					$('msg-validation').innerHTML = 'Requête validée';
				}
				else {
					$$('.disable-if-not-validated').each(function(submit){submit.setAttribute('disabled', true);});
					$('div-verification').removeClassName('notice').removeClassName('success').addClassName('error_message');
					$('msg-validation').innerHTML = 'Il y a une erreur dans votre requête :<br>'+json.message+
							'<br><br><div id="div-error-sql" style="display:none;">'+json.value+
							'</div><br><a href="#none" onclick="$(\'div-error-sql\').toggle();">Afficher SQL</a>'
					;
				}
			}
		});
	});
	
	/**
	 * Bouton charger tout en haut, remplissage auto du formulaire
	 */
	$('generateButton').observe('click', function(){console.log('generateButton:event:click');
		if ( $('RequestmanagerName').getValue() === '' ) {
			return false;
		}
		
//		this.disable();
		$('loading_generate').show();
		resetForm();
		
		new Ajax.Request('<?php echo Router::url( array( 'controller' => $controller, 'action' => 'ajax_load' ) ); ?>/'+$('RequestmanagerName').getValue(), {
			asynchronous:true, 
			evalScripts:true,  
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {
				var request = JSON.parse(json.json),
					terminated = false;
				delete request.recursive;
				delete request.contain;
				delete request.json;
				
				/*
				 * INFO : Ici on fait toutes les jointures.
				 * Lorsque toutes les jointures sont faites, un evenement est envoyé.
				 * On surveille cet evenement pour s'occuper de cocher les cases et remplir les champs.
				 * L'ordre des blocs de code est très important pour un bon déroulement de l'opération car
				 * on fait appel à de nombreux évenements pour générer le formulaire.
				 */
				
				// Si il n'y a pas de jointure, on met un array vide pour éviter de faire planter le reste du script
				if ( request.joins === undefined ) {
					request.joins = [];
				}
				
				// On défini la todo list
				for (var key in request.joins) {
					if ( !request.joins.hasOwnProperty(key) ) {
						continue;
					}
					
					joinsToDo[request.joins[key].alias] = true;
				}
				
			console.log(["joinsToDo:", joinsToDo]);
			
				// Lorsque toutes les jointures sont faite, on coche les checkbox
				joinFinish.observe('finish:allJoins', function() {console.log('joinFinish:event:finish:allJoins');
					console.log('-------------------------------------------------------------');
					// On coche toutes les cases à partir de request.field (si regex [\w]+.[\w]+)
					var matches,
						reg;
						
					if (terminated) {
						return ;
					}
					terminated = true;
					
		console.log(request.fields);
					
					console.log('------------------------Debut coche--------------------------');
					
					var inputCheckbox;
					for (var key in request.fields) {
						if ( !request.fields.hasOwnProperty(key) ) {
							continue;
						}
						
						matches = request.fields[key].match( /^([\w]+)\.([\w]+)$/ );
						console.log('coche:'+request.fields[key]);
						
						if (matches !== null) {
							inputCheckbox = $$('#FormRequestmaster input[type="checkbox"][original-name="data['+matches[1]+']['+matches[2]+']"]');
							if ( matches && inputCheckbox.length ) {
								inputCheckbox.first().setAttribute('checked', true);
							}
							else {
								generatedFields.push(request.fields[key]);
							}
						}
						else if (typeof request.fields[key] === 'string' && request.fields[key].trim() !== '') {
							generatedFields.push(request.fields[key]);
						}
					}
					
					console.log('------------------------Fin coche---------------------------');
					
					for (var key in request.conditions) {
						if ( !request.conditions.hasOwnProperty(key) ) {
							continue;
						}
						
						// if (key is not numeric)
						if ( !(!isNaN(parseFloat(key)) && isFinite(key)) ) {
							if (!Array.isArray(request.conditions[key])) {
								request.conditions[key] = [request.conditions[key]];
							}
							autoCondition(key, request.conditions[key], 0);
						}
						else if (typeof request.conditions[key] === 'string' && request.conditions[key] !== '') {
							generatedConditions.push(request.conditions[key]);
						}
					}
					
					// Pour chaques fields stockés, on génère un string pour remplir le textarea fields
					$('Addfields').setValue( generatedFields.join(", ") );
					
					// Pour chaques conditions stockés, on génère un string pour remplir le textarea conditions
					$('AddConditions').setValue( generatedConditions.join(" AND ") );
					
					$('loading_generate').hide();
					$('generateButton').enable();
					$('FormRequestmaster').simulate('change');
				});
			
				joinFinish.observe('join:finish', function() {console.log('event:join:finish');
					for (var key in joinsToDo) {
						if (joinsToDo[key] === true) {
							return ;
						}
					}
					
					console.error('finish:allJoins');
					this.fire('finish:allJoins');
				});
				
				// On prépare la liste de jointure
				if (!advanced) {
					var jointure;
					for (var i=0; i<request.joins.length; i++) {console.error(request.joins[i]);
						jointure = findJoin( request.joins[i] );

						if ( joins[jointure.base] === undefined ) {
							joins[jointure.base] = [];
						}

						nbJoin[jointure.base] = 0;
						joins[jointure.base].push(jointure.join);

						if ( request.joins[i].type === 'INNER' && !advanced ) {
							generatedConditions.push('"'+request.joins[i].alias+'"."id" IS NOT NULL');
							console.log(generatedConditions);
						}
					}
				} else {
					joins = request.joins;
					// Lorsque l'evenement change est fini, on fait la jointure sur le model suivant (fait toutes les jointures dans la vue)
					$('RequestmanagerFrom').observe('finish:getModel:'+json.model, function(){console.log('RequestmanagerFrom:event:finish:getModel:'+json.model);
						var finish = true;
						for (var key in joinsToDo) {
							if (joinsToDo[key] === true) {
								finish = false;
								break;
							}
						}
						
						if ( finish ) {
							joinFinish.fire('finish:allJoins');
						} else {
							autoJoin( $('RequestmanagerFrom').getValue(), 'from', 0 );
						}
					});
				}
				
				// On charge le modele principale
				$('RequestmanagerFrom').setValue(json.model);
				$('RequestmanagerFrom').simulate('change');
			},
			onFail:function() { console.error('error : autoGeneration'); },
			onException:function() { console.error('error : autoGeneration'); }
		});
	});
	
	/**
	 * Permet à partir d'une requete join de type cakephp en json, de trouver le modele sur lequel faire la jointure et le nom du modele join
	 * Fonctionne sur une condition de jointure classique : "Model1"."champ1" = "Model2"."champ2" AND ... (Conditions additionnelles)
	 * 
	 * @param {json} joinRequest
	 * @returns {json}
	 */
	function findJoin( joinRequest ) {console.log(['findJoin', joinRequest]);
		// Si l'alias est trouvé au debut :
		var reg = new RegExp('"'+joinRequest.alias+'"\."[^"]+" = "([^"]+)"');
		var testReg = joinRequest.conditions.match( reg );
		
		if ( !testReg ) {
			reg = new RegExp('"([^"]+)"\."[^"]+" = "'+joinRequest.alias+'"');
			testReg = joinRequest.conditions.match( reg );
		}
		
		return {
			base: testReg[1],
			join: joinRequest.alias
		};
	}
	
	/**
	 * Rempli automatiquement les selects en fonction de la variable globale "joins"
	 * 
	 * @param {string} index
	 * @param {string} oldindex
	 * @param {integer} i
	 * @returns {boolean}
	 */
	function autoJoin( index, oldindex, i ) {console.log(["autoJoin:", index, oldindex, i]);
		'use strict';
		console.log(['autoJoin', index, oldindex, i]);
		var baseId = 'join-'+oldindex+'-'+index,
			select;
	console.info('================= '+baseId + '-' + i);
	
		if (advanced) {
			for (var i=0; i<joins.length; i++) {
				select = _collection[baseId + '-' + i];
				select.setValue('---Jointure custom---');
				select.simulate('change');
				
				select.up().select('input.alias').last().setValue(joins[i].alias);
				select.up().select('input.table').last().setValue(joins[i].table);
				select.up().select('input.conditions').last().setValue(joins[i].conditions);
				select.up().select('input.type').last().setValue(joins[i].type);
				select.up().select('input[type="button"][value="Joindre"]').last().simulate('click');
			}
			
			joins = [];
			return true;
		}
		
		if ( joins[index] === undefined || i >= joins[index].length ) {
			return true;
		}
		
		select = _collection[baseId + '-' + i];

		select.observe('finish:join:'+select.id, function() {console.info('select:event:finish:join:'+select.id);
			var matches = this.id.match(/^join\-([\w]+)\-([\w]+)\-([0-9]+)$/); // Donne oldindex

			// On fait les jointures sur la jointure enfant
			autoJoin( this.getValue(), matches[2], 0 );

			// On fait la jointure suivante du modele actuel
			autoJoin( matches[2], matches[1], parseInt(matches[3], 10)+1 );
			
			joinFinish.simulate('change');
		});

		select.setValue(joins[index][i]);
		select.simulate('change');
		
		return true;
	}
	
	/**
	 * Permet de générer des conditions SQL a partir d'array imbriqué sur plusieurs niveaux
	 * 
	 * @param {array} value
	 * @param {string} type - 'AND' ou 'OR'
	 */
	function recursiveConditions(value, type) {console.log("recursiveConditions:"+type);console.log(value);
		var results = [],
			matches,
			valueIsArray,
			keyIsNumeric
		;
		
		for (var key in value) {
			if (!value.hasOwnProperty(key)) {
				continue;
			}
			
			matches = key.match(/^([\w]+)\.([\w]+)$/);
			valueIsArray = typeof value[key] === 'object';
			keyIsNumeric = !isNaN(parseFloat(key)) && isFinite(key);
			console.log({matches: matches, valueIsArray: valueIsArray, keyIsNumeric: keyIsNumeric});
			
			// Cas tout sur une ligne sans opérateur ex: array('Model.id' => '1')
			if (matches !== null && !valueIsArray) {
				results.push('('+key+' = '+value[key]+')');
			}
			
			// Cas tout sur une ligne avec opérateur ex: array('Model.id !=' => '1')
			else if (!valueIsArray && key.match(/^([\w]+)\.([\w]+) /)) {
				results.push('('+key+' '+value[key]+')');
			}
			
			// Cas tout sur la valeur avec clef numérique ex: array(0 => 'Model.id IS NULL')
			else if (!valueIsArray && keyIsNumeric) {
				results.push('('+value[key]+')');
			}
			
			// Cas valeur multiple ex: array('Model.field' => array(1, 2, 3))
			else if (matches !== null && valueIsArray) {
				results.push('('+key+" IN ('"+value[key].join("', '")+"'))");
			}
			
			// Cas "OR" ex: array('OR' => array(...))
			else if (valueIsArray && key.toUpperCase() === 'OR') {
				results.push(recursiveConditions(value[key], 'OR'));
			}
			
			// Cas "AND" ex: array(0 => array(...))
			else if (valueIsArray && keyIsNumeric) {
				results.push(recursiveConditions(value[key], 'AND'));
			}
			
			else {
				console.error("---- Cas non répertorié ----");
				console.error({type: type, value: value[key]});
				console.error("----------------------------");
			}
		}
		
		return results.join(' '+type+' ');
	}
	
	/**
	 * Rempli automatiquement les conditions en fonction de key et value
	 * 
	 * @param {String} key
	 * @param {Array} value
	 * @param {integer} i
	 * @returns {Boolean}	 
	 */
	function autoCondition( key, value, i ) {console.info(['autoCondition', key, value, i]);
		var matches = key.match(/^([\w]+)\.([\w]+)$/);
		
		if (i >= value.length) {console.info('i>length');
			return false;
		}
		
		// Cas classique d'utilisation des conditions du requestsmanager (rempli les champs conditions)
		if (matches !== null && typeof value[i] === 'string') {
			var input = $$('#FormRequestmaster input[type="text"][original-name="data['+matches[1]+']['+matches[2]+']"]').last();
			
			// Le champ condition n'a pas été trouvé, on ajoute la condition à la fin
			if (input === undefined) {
				generatedConditions.push(key+" = "+value[i]);
			}
			
			// L'input a été trouvé, on rempli le champ, on incrémente et on lance autoCondition pour la valeur suivante (si existante)
			else {
				input.observe('finish:condition:'+input.id, function() {console.info(['autoCondition:event:finish:condition:'+this.id, this]);
					autoCondition( key, value, i+1 );
				});
				
				console.info('setValue:'+input.id+':'+value[i]);
				input.setValue(value[i]);
				input.simulate('change');
			}
		}
		
		// Cas à possibilité douteuse ex: array('Model.field' => array(array(...)))
		else if (matches !== null) {
			console.error("Cas à possibilité douteuse détecté !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
		}
		
		// Cas du 'OR'
		else if (key === 'OR') {
			generatedConditions.push('('+recursiveConditions(value, 'OR')+')');
		}
		
		// Cas avec opérateur ex: array('Model.field !=' => '1')
		else if (key.match(/^[\w]+\.[\w]+ /) && typeof value === 'string') {
			generatedConditions.push('('+key+" '"+value.replace("'", "''")+"')");
		}
		
		else {
			console.error("Autre !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
		}
		
		return true;
	}
	
	/**
	 * Réinitialise le formulaire
	 * 
	 * @returns {void}	 
	 */
	function resetForm() {console.log('resetForm:');
		var value = $('RequestmanagerFrom').getValue(),
			suffix = value === '' ? 'from' : value;
		
		_collection = {};
		joins = {};
		generatedFields = [];
		generatedConditions = [];
		joinFinish = new Element('finish');
		joinsToDo = {};
		nbJoin = {};
		
		$('fields-from-'+suffix).innerHTML = '';
		$('fields-from-'+suffix).id = 'fields-from-from';
		$('joins-from-'+suffix).innerHTML = '';
		$('joins-from-'+suffix).id = 'joins-from-from';
		$('error-from-'+suffix).id = 'error-from-from';
		$('zoneJointure').innerHTML = '';
		$('Addfields').setValue('');
		$('AddConditions').setValue('');
		$('Order').setValue('');
		$('RequestmanagerFrom').setValue('');
		
		$$('input[type="hidden"][value="unique"]').each(function(element){ element.remove(); });
	}
	
	/**
	 * Recupère une jointure entre deux modèles
	 * 
	 * @param {string} modelName1
	 * @param {string} modelName2
	 * @param {DOM} elementToFill
	 * @returns {void}	 
	 */
	function getJointure(modelName1, modelName2) {console.log('getJointure:'+modelName1+':'+modelName2);
		var button = this,
			div = new Element('div', {'class': 'center'}),
			img = new Element('img', {src: '<?php echo $this->webroot; ?>img/loading.gif'});
		;
		div.insert(img);
		$('zoneJointure').insert({top: div});
		
		new Ajax.Request('<?php echo Router::url( array( 'controller' => $controller, 'action' => 'ajax_getjointure' ) ); ?>/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: {
				'modelName1': modelName1,
				'modelName2': modelName2
			}, 
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {console.log('getJointure:onComplete');
				var input;
				if (typeof json === 'object') {
					for (var inputName in json) {
						input = $$('input[name="data['+modelName1+']['+modelName2+']['+inputName+']"]');
						
						if (input.length) {
							$$('input[name="data['+modelName1+']['+modelName2+']['+inputName+']"]').last().setValue(json[inputName]);
						}
					}
				}
				div.remove();
			},
			onFail:function() { div.remove(); console.error('error : getJointure:'+modelName1+':'+modelName2); },
			onException:function() { div.remove(); console.error('error : getJointure:'+modelName1+':'+modelName2); }
		});
	}
	
	/**
	 * Permet l'ajout d'un model avec une jointure custom
	 * 
	 * @param {string} prevAlias
	 * @param {Object} join
	 * @param {string} newAlias
	 * @param {DOM} dom
	 */
	function getTable(prevAlias, join, newAlias, dom) {console.log(['getTable:', prevAlias, join, newAlias, dom]);
		var div = new Element('div', {'class': 'center'}),
			img = new Element('img', {src: '<?php echo $this->webroot; ?>img/loading.gif'});
		;
		div.insert(img);
		dom.up('fieldset').insert(div);
		
		new Ajax.Request('<?php echo Router::url( array( 'controller' => $controller, 'action' => 'ajax_gettable' ) ); ?>/', {
			asynchronous:true, 
			evalScripts:true, 
			parameters: {
				'table': join.table
			}, 
			requestHeaders: {Accept: 'application/json'},
			onComplete:function(request, json) {console.log('getTable:onComplete');
				if (json.echec) {
					alert("La table '"+join.table+"' n'a pas été trouvée.");
					div.remove();
					return false;
				}
				
				onGetModelComplete(json, prevAlias, newAlias, newAlias, dom, join);
				div.remove();
			},
			onFail:function() { div.remove(); console.error('error : getTable:'+prevAlias+':'+newAlias); },
			onException:function() { div.remove(); console.error('error : getTable:'+prevAlias+':'+newAlias); }
		});
	}
	
	$('reset').observe('click', function(){
		if (confirm("Cette action remet la page à zéro. Voulez vous continuer ?")) { resetForm(); } 
	});
</script>