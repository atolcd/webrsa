<h1><?php echo $title_for_layout = __d( 'gestionanomaliebdd', 'Gestionsanomaliesbdds::personnes' );$this->set( 'title_for_layout', $title_for_layout );?></h1>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js' ) );
	}
?>

<?php if( !empty( $fichiersModuleLies ) ): ?>
	<div class="errorslist">
	Impossible de procéder à la fusion des enregistrements liés aux personnes en doublons car des fichiers liés à ces enregistrements existent:
	<ul>
		<?php
			foreach( $fichiersModuleLies as $fichier ) {
				$controller = Inflector::tableize( $fichier['Fichiermodule']['modele'] );
				echo "<li>".$this->Xhtml->link(
					$fichier['Fichiermodule']['modele'],
					array( 'controller' => $controller, 'action' => 'filelink', $fichier['Fichiermodule']['fk_value'] ),
					array( 'class' => 'external' )
				)."</li>";
			}
		?>
	</ul>
	</div>
<?php else: ?>
	<?php echo $this->element( 'required_javascript' );?>

	<?php if( isset( $validationErrors ) && !empty( $validationErrors ) ): ?>
		<div class="errorslist">
		Les erreurs suivantes ont été détectées:
		<ul>
			<?php
				foreach( $validationErrors as $validationError ) {
					echo "<li>{$validationError}</li>";
				}
			?>
		</ul>
		</div>
	<?php endif;?>

	<h2>Informations concernant le foyer</h2>
	<?php
		$informations = array(
			$this->Gestionanomaliebdd->foyerErreursPrestationsAllocataires( $foyer, false ),
			$this->Gestionanomaliebdd->foyerPersonnesSansPrestation( $foyer, false ),
			$this->Gestionanomaliebdd->foyerErreursDoublonsPersonnes( $foyer, false ),
			( $foyer['Dossier']['locked'] ? $this->Xhtml->image( 'icons/lock.png', array( 'alt' => '', 'title' => 'Dossier verrouillé' ) ) : null ),
		);
		$informations = Hash::filter( (array)$informations );

		if( !empty( $informations ) ) {
			echo '<ul>';
			foreach( $informations as $information ) {
				echo "<li>{$information}</li>";
			}
			echo '</ul>';
		}
	?>

	<ul>
		<li><?php echo $this->Xhtml->link( 'Voir', array( 'controller' => 'personnes', 'action' => 'index', $this->request->params['pass'][0] ) );?></li>
		<?php foreach( $methodes as $m ):?>
			<?php $m = strtolower( $m );?>
			<li><?php echo $this->Xhtml->link( "Comparaison {$m}", array( $this->request->params['pass'][0], $this->request->params['pass'][1], 'Gestionanomaliebdd__methode' => $m ) );?></li>
		<?php endforeach;?>
	</ul>

	<h2>Fusion des enregistrements liés à la personne</h2>

	<?php
		if( empty( $personnes ) ) {
			echo $this->Xhtml->tag( 'p', 'Aucune personne à sélectionner', array( 'class' => 'notice' ) );
		}
		else {
			echo $this->Xform->create( null, array( 'id' => 'PersonnesForm' ) );
			echo '<div>'.$this->Xform->input( 'Form.sent', array( 'type' => 'hidden', 'value' => true ) ).'</div>';

			echo '<table id="personnes">';
			echo '<thead>
				<tr>
					<th>Garder ?</th>
					<th>id</th>
					<th>foyer_id</th>
					<th>qual</th>
					<th>nom</th>
					<th>nomnai</th>
					<th>prenom</th>
					<th>prenom2</th>
					<th>prenom3</th>
					<th>dtnai</th>
					<th>sexe</th>
					<th>rgnai</th>
					<th>nir</th>
                    <th>idassedic</th>
                    <th>nomcomnai</th>
					<th>natprest</th>
					<th>rolepers</th>
				</tr>
			</thead>';
			echo '<tbody>';

			foreach( $personnes as $i => $personne ) {
				$checked = '';
				if( isset( $this->request->data['Personne']['garder'] ) && $this->request->data['Personne']['garder'] == $personne['Personne']['id'] ) {
					$checked = 'checked="checked"';
				}
				echo $this->Html->tableCells(
					array(
						"<label><input name=\"data[Personne][garder]\" id=\"PersonneGarder{$i}\" value=\"{$personne['Personne']['id']}\" type=\"radio\" {$checked} />Garder</label>",
						h( $personne['Personne']['id'] ),
						h( $personne['Personne']['foyer_id'] ),
						h( $personne['Personne']['qual'] ),
						h( $personne['Personne']['nom'] ),
						h( $personne['Personne']['nomnai'] ),
						h( $personne['Personne']['prenom'] ),
						h( $personne['Personne']['prenom2'] ),
						h( $personne['Personne']['prenom3'] ),
						h( $this->Type2->format( $personne, 'Personne.dtnai' ) ),
						h( $personne['Personne']['sexe'] ),
						h( $personne['Personne']['rgnai'] ),
						h( $personne['Personne']['nir'] ),
                        h( $personne['Personne']['idassedic'] ),
                        h( $personne['Personne']['nomcomnai'] ),
						h( $personne['Prestation']['natprest'] ),
						h( $personne['Prestation']['rolepers'] ),
					),
					array( 'class' => "odd id{$personne['Personne']['id']}" ),
					array( 'class' => "even id{$personne['Personne']['id']}" )
				);
			}
			echo '</tbody>';
			echo '</table>';

			foreach( $donnees as $modelName => $records ) {
				echo "<h3>{$modelName}</h3>";

				$association = $associations[$modelName];

				$modelClass = ClassRegistry::init( $modelName );
				$modelFields = array_keys( Hash::flatten( array( $modelClass->alias => Set::normalize( array_keys( $modelClass->schema() ) ) ) ) );
				$modelFields[] = $modelName.'.fichierslies';

				$fields = array(/* "{$modelName}.id", "{$modelName}.personne_id" */);
				foreach( $modelFields as $modelField ) {
					if( (!in_array( $modelField, array( "{$modelName}.id", "{$modelName}.personne_id", "{$modelName}.fichierslies" ) ) && ( $this->Type2->type( $modelField ) != 'binary' )) || $modelField === "{$modelName}.fichierslies" ) {
						$fields[] = $modelField;
					}
				}

				echo '<table id="'.$modelName.'" class="tableliee tooltips">';
				echo '<thead>';
				echo '<tr>
						<th class="action">Garder ?</th>
						<th>id</th>
						<th>personne_id</th>';
				foreach( $fields as $field ) {
					list( $modelName, $field ) = model_field( $field );
					echo "<th title=\"".__d(strtolower($modelName), $modelName.'.'.$field)."\">{$field}</th>";
				}
				echo "<th class=\"innerTableHeader noprint\">Enregistrements liés</th>";
				echo '</tr>';
				echo '</thead>';
				echo '<tbody>';

				foreach( $records as $i => $record ) {
					$checked = '';
					$inputType = ( $association == 'hasMany' ? 'checkbox' : 'radio' );
					$classList = array();
					$uncheckableRadioModelList = array(
						'Dsp',
						'Nonoriente66'
					);

					if( isset( $this->request->data[$modelName]['id'] ) && in_array( $record[$modelName]['id'], $this->request->data[$modelName]['id'] ) ) {
						$checked = 'checked="checked"';
					}

					if ( in_array($modelName, $uncheckableRadioModelList) ) {
						$classList[] = 'uncheckable';
					}

					$cells = array(
						"<label><input name=\"data[{$modelName}][id][]\" id=\"{$modelName}Id{$i}\" value=\"{$record[$modelName]['id']}\" type=\"{$inputType}\" {$checked} class=\"".implode(' ', $classList)."\" />Garder</label>",
						array(h( $record[$modelName]['id'] ), array('class' => 'id')),
						array(h( $record['Personne']['id'] ), array('class' => 'personne_id')),
					);

					foreach( $fields as $field ) {
						$fieldType = $field !== "{$modelName}.fichierslies" ? $this->Type2->type( $field ) : 'string';

						if( $fieldType != 'binary' ) {
							if( $fieldType !== 'string' ) {
								$value = $this->Type2->format( $record, $field );
							}
							else {
								$value = h( Set::classicExtract( $record, $field ) );//FIXME: traductions ?
							}

							list($m, $f) = model_field($field);
							$cells[] = array( $value, array( 'class' => $f ) );
						}
					}

					// Infobulle
					$linkedRecords = array();
					foreach( $record[$modelName] as $k => $v ) {
						if( preg_match( '/^nb_(.*)$/', $k, $matches ) ) {
							$linkedRecords[] = "<tr><th>".h( $matches[1] )."</th><td>".h( $v )."</td></tr>";
						}
					}
					if( empty( $linkedRecords ) ) {
						$innerTbody = "<tr><td>Aucun enregistrement lié</td></tr>";
					}
					else {
						$innerTbody = implode( "", $linkedRecords );
					}
					$cells[] = array( "<table id=\"innerTable{$modelName}{$i}\" class=\"innerTable\"><tbody>{$innerTbody}</tbody></table>", array( 'class' => 'innerTableCell noprint' ) );

					// FIXME: la même avec personne_id dans la table du haut et le javascript
					$class = array( 'class' => '' );
					foreach( $modelFields as $modelField ) {
						list( $m, $f ) = model_field( $modelField );
						if( ( $f == 'id' ) || preg_match( '/_id$/', $f ) ) {
							$class = $this->Html->addClass( $class, $f.Set::classicExtract( $record, $modelField ) );
						}
					}

					echo $this->Html->tableCells( $cells, $class, $class );
				}
				echo '</tbody>';
				echo '</table>';
			}

			echo '<div class="error_message" style="display: none;"><ul id="showerrors"></ul></div>';

			echo $this->Xform->end( 'Enregistrer' );
		}
	?>
	<script type="text/javascript">
		// <![CDATA[
		// Cocher les enregistrements dépendants depuis la table personnes
		var v = $( 'PersonnesForm' ).getInputs( 'radio', 'data[Personne][garder]' );
		var currentValue = undefined;
		$( v ).each( function( radio ) {
			$( radio ).observe( 'change', function( event ) {
				toutDecocher( '.tableliee input[type="checkbox"]' );
				toutDecocher( '.tableliee input[type="radio"]' );
				toutCocher( '.tableliee .personne_id' + radio.value + ' input[type=checkbox]' );
				toutCocher( '.tableliee .personne_id' + radio.value + ' input[type=radio]' );
				$('PersonnesForm').select('input[type="checkbox"], .tableliee input[type="radio"]').each(function(element){
					if (element.up('table').getAttribute('id') !== 'personnes') {
						element.simulate('change');
					}
				});
			} );
		} );

		// Cocher les enregistrements dépendants entre tables (ex.: dsps.id, dsps_revs.dsp_id)
		function foo( modelFrom, columnFrom, modelTo, columnTo ) {
			var v = $( 'PersonnesForm' ).getInputs( 'radio', 'data[' + modelTo + '][' + columnTo + '][]' );//FIXME
			var currentValue = undefined;
			$( v ).each( function( radio ) {
				$( radio ).observe( 'change', function( event ) {
					toutDecocher( '#' + modelFrom + ' input[type="checkbox"]' );
					toutDecocher( '#' + modelFrom + ' input[type="radio"]' );
					toutCocher( '#' + modelFrom + ' .' + columnFrom + radio.value + ' input[type=checkbox]' );
					toutCocher( '#' + modelFrom + ' .' + columnFrom + radio.value + ' input[type=radio]' );
				} );
			} );
		}

		<?php if( !empty( $dependencies ) ): ?>
			<?php foreach( $dependencies as $dependency ): ?>
				foo( '<?php echo $dependency['From']['model'];?>', '<?php echo $dependency['From']['column'];?>', '<?php echo $dependency['To']['model'];?>', '<?php echo $dependency['To']['column'];?>' );
			<?php endforeach; ?>
		<?php endif; ?>

		// Mise en évidence à partir de la table #personnes vers les tables liées
		var re = new RegExp( '^.*id([0-9]+).*$', 'g' );
		$$( '#personnes tr' ).each( function( elmt ) {
			// Ajout d'une classe
			$(elmt).observe( 'mouseover', function( event ) {
				var classes = this.classNames().toString();
				var personneId = classes.replace( re, '$1' );
				$(this).addClassName( 'highlight' );
				$$( '.tableliee tr.personne_id' + personneId ).each( function( row ) {
					$(row).addClassName( 'highlight' );
				} );
			} );
			// Suppression d'une classe
			$(elmt).observe( 'mouseout', function( event ) {
				var classes = this.classNames().toString();
				var personneId = classes.replace( re, '$1' );
				$(this).removeClassName( 'highlight' );
				$$( '.tableliee tr.personne_id' + personneId ).each( function( row ) {
					$(row).removeClassName( 'highlight' );
				} );
			} );
		} );

		// Mise en évidence à partir des tables liées vers la table #personnes -> FIXME
		/*var re2 = new RegExp( '^.*personne_id([0-9]+).*$', 'g' );
		$$( '.tableliee tr' ).each( function( elmt ) {
			// Ajout d'une classe
			$(elmt).observe( 'mouseover', function( event ) {
				var classes = this.classNames().toString();
				var personneId = classes.replace( re2, '$1' );
				$(this).addClassName( 'highlight' );
				$$( '#personnes tr.id' + personneId ).each( function( row ) {
					$(row).addClassName( 'highlight' );
				} );
			} );
			// Suppression d'une classe
			$(elmt).observe( 'mouseout', function( event ) {
				var classes = this.classNames().toString();
				var personneId = classes.replace( re2, '$1' );
				$(this).removeClassName( 'highlight' );
				$$( '#personnes tr.id' + personneId ).each( function( row ) {
					$(row).removeClassName( 'highlight' );
				} );
			} );
		} );*/
		// ]]>
	</script>
<?php endif;?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => $this->request->params['controller'],
			'action'     => 'foyer',
			$this->request->params['pass'][0],
			'Gestionanomaliebdd__methode' => $methode
		),
		array(
			'id' => 'Back'
		)
	);
?>
<script>
	/**
	 * Liste des modèles liés à la fois à Personne et entre eux
	 * ex: {12345: {'Model1.Model2.Model3': [0: {'Model1': {'id': 123}, 'Model3': {'id': 456}}]}}
	 * @type {Object}
	 */
	var links = <?php echo json_encode($links);?>;

	/**
	 * Affiche une alerte dans l'element showerrors
	 *
	 * @param {string} message - Message à afficher
	 * @param {boolean} condition - Si à vrai, ajoute le message, se contente de tout retirer sinon
	 * @param {string} className1 - class à appliquer sur les elements li pour destruction ultérieur
	 * @param {sting} className2
	 */
	function displayAlert(message, condition, className1, className2) {
		className2 = className2 === undefined ? '' : className2;

		$('showerrors').select('.'+className1+'.'+className2).each(function(toDelete){toDelete.remove();});
		if (condition) {
			$('showerrors').insert('<li class="'+className1+' '+className2+'">'+message+'</li>');
		}

		if ($('showerrors').innerHTML !== '') {
			$('showerrors').up('div').show();
		} else {
			$('showerrors').up('div').hide();
		}
	}

	/**
	 * Alerte Relations entre les modeles
	 */
	function checkRelations(that) {
		var personne_id = that.up('tr').select('td.personne_id').first().innerHTML,
			modele = that.up('table').getAttribute('id'),
			linkPath,
			link,
			i,
			coche1,
			coche2,
			input,
			classMsg1,
			classMsg2,
			otherModelName
		;
		/*
		 * links[personne_id][linkPath][i][link][id]
		 */
		for (linkPath in links[personne_id]) {
			if (linkPath.indexOf(modele+'.') === 0) {
				for (i=0; i<links[personne_id][linkPath].length; i++) {
					for (link in links[personne_id][linkPath][i]) {
						input = $(link).select('tr.id'+links[personne_id][linkPath][i][link].id).first().select('input[type="radio"], input[type="checkbox"]').first();
						if (link === modele) {
							classMsg1 = link+'_'+links[personne_id][linkPath][i][link].id;
							coche1 = input.checked;
						} else {
							otherModelName = link;
							classMsg2 = link+'_'+links[personne_id][linkPath][i][link].id;
							coche2 = input.checked;
						}
					}

					displayAlert(
						'Un lien existe entre '+modele+' id:'+links[personne_id][linkPath][i][modele].id+', et '+otherModelName+' id:'+links[personne_id][linkPath][i][otherModelName].id,
						coche1 !== coche2,
						classMsg1,
						classMsg2
					);
				}
			}
		}
	}

	/**
	 * Permet la vérification d'un lien entre deux enregistrements
	 *
	 * @param {DOM} element
	 */
	function verifyRelations(element) {
		element.observe('change', function() {
			checkRelations(this);
		});
		checkRelations(element);
	}

	$('PersonnesForm').select('input[type="radio"], input[type="checkbox"]').each(function(element) {
		if (element.up('table').getAttribute('id') !== 'personnes') {
			verifyRelations(element);
		}
	});

	/**
	 * Alerte spéciale Orientstruct
	 */
	function verifyOrientstruct() {
		var oriente = 0,
			nonOriente = 0, // Vide, Non orienté ou En attente
			classMsg1 = 'special_Orientstruct_1',
			classMsg2 = 'special_Orientstruct_2'
		;
		$('Orientstruct').select('input:checked').each(function(element){
			if (element.up('tr').select('td.statut_orient').first().innerHTML === 'Orienté') {
				oriente++;
			} else {
				nonOriente++;
			}
		});

		displayAlert(
			'Aucune Orientation (Orientstruct) n\'a été choisie',
			oriente === 0 && nonOriente === 0,
			classMsg1
		);
		displayAlert(
			'Une Orientation (Orientstruct) avec un statut_orient "Orienté" ne peut pas être selectionné en même temps qu\'un "Non orienté"',
			oriente > 0 && nonOriente > 0,
			classMsg2
		);
	}

	if ($('Orientstruct')) {
		$('Orientstruct').select('input[type="checkbox"], input[type="radio"]').each(function(element){
			element.observe('change', verifyOrientstruct);
		});
		verifyOrientstruct();
	}

	/**
	 * Alerte spéciale PersonneReferent
	 */
	function verifyPersonneReferent() {
		var sansDateFin = 0,
			classMsg1 = 'special_PersonneReferent_1',
			classMsg2 = 'special_PersonneReferent_2'
		;
		$('PersonneReferent').select('input:checked').each(function(element){
			if (element.up('tr').select('td.dfdesignation').first().innerHTML === '') {
				sansDateFin++;
			}
		});

		displayAlert(
			'Aucuns référent (PersonneReferent) n\'est actif (dfdesignation à vide)',
			sansDateFin === 0,
			classMsg1
		);
		displayAlert(
			'Plusieurs référents (PersonneReferent) sont actifs (dfdesignation à vide)',
			sansDateFin > 1,
			classMsg2
		);
	}

	if ($('PersonneReferent')) {
		$('PersonneReferent').select('input[type="checkbox"], input[type="radio"]').each(function(element){
			element.observe('change', verifyPersonneReferent);
		});
		verifyPersonneReferent();
	}

	/**
	 * Alerte spéciale Prestation
	 */
	function verifyPrestation() {
		var pfa = 0,
			rsa = 0,
			classMsg1 = 'special_Prestation_1'
		;
		$('Prestation').select('input:checked').each(function(element){
			if (element.up('tr').select('td.natprest').first().innerHTML === 'RSA') {
				rsa++;
			} else {
				pfa++;
			}
		});

		displayAlert(
			'Plusieurs Prestations avec même natprest ont été selectionné',
			pfa > 1 || rsa > 1,
			classMsg1
		);
	}

	if ($('Prestation')) {
		$('Prestation').select('input[type="checkbox"], input[type="radio"]').each(function(element){
			element.observe('change', verifyPrestation);
		});
		verifyPrestation();
	}

	/**
	 * Alerte spéciale Rattachement
	 */
	function verifyRattachement() {
		var noms = [],
			nirs = [],
			displayMsg = false,
			classMsg1 = 'special_Rattachement_1'
		;
		$('Rattachement').select('input:checked').each(function(element){
			var nom = element.up('tr').select('td.nomnai').first().innerHTML +'_'+ element.up('tr').select('td.prenom').first().innerHTML,
				nir = element.up('tr').select('td.nir').first().innerHTML
			;

			if (in_array(nom, noms) || in_array(nir, nirs)) {
				displayMsg = true;
				throw $break;
			}

			noms.push(nom);
			nirs.push(nir);
		});

		displayAlert(
			'Plusieurs Rattachements pointant sur la même personne ont été selectionné',
			displayMsg,
			classMsg1
		);
	}

	if ($('Rattachement')) {
		$('Rattachement').select('input[type="checkbox"], input[type="radio"]').each(function(element){
			element.observe('change', verifyRattachement);
		});
		verifyRattachement();
	}

	/**
	 * Liste des noms de modele suivi du nombre maximum de selection possible
	 * @type {Object}
	 */
	var maxCount = {
		'Allocationsoutienfamilial': 2,
		'Aviscgssdompersonne': 1, // Pas d'inflexion
		'Avispcgpersonne': 1,
		'Conditionactiviteprealable': 1,
		'Correspondancepersonne': 0,
		'Creancealimentaire': 1,
		'Dernierdossierallocataire': 1,
		'Dossiercaf': 1,
		'Dsp': 1,
		'Grossesse': 1,
		'Informationeti': 1,
		'Infoagricole': 1,
		'Parcours': 1,
		'Prestation': 2,
		'Rattachement': 2,
		'Ressource': 1,
		'Suiviappuiorientation': 1,
		'Titresejour': 1
	};

	/**
	 * Vérifications des quantités selectionné (max autorisé)
	 *
	 * @param {string} modelName
	 * @param {integer} max
	 */
	function verifyMaxCount(modelName, max) {
		displayAlert(
			'Le nombre de selections maximum pour '+modelName+' est de '+max+(max === 0 ? ' (ne pas selectionner)' : ''),
			$(modelName).select('input:checked').length > max,
			'verifyMaxCount_'+modelName
		);
	}

	for (var modelName in maxCount) {
		if ($(modelName)) {
			$(modelName).select('input[type="checkbox"], input[type="radio"]').each(function(element){
				var name = modelName, count = maxCount[modelName];
				element.observe('change', function() {
					verifyMaxCount(name, count);
				});
				verifyMaxCount(name, count);
			});
		}
	}

	/**
	 * Alerte sur les fichiers liés non selectionnés
	 */
	$('PersonnesForm').select('td.fichierslies').each(function(td){
		var modelName, id;

		if (td.innerHTML !== '0') {
			modelName = td.up('table').getAttribute('id');
			id = td.up('tr').select('td.id').first().innerHTML;

			td.up('tr').select('input[type="checkbox"], input[type="radio"]').first().observe('change', function(){
				displayAlert(
					'Des fichiers liés existent pour '+modelName+' id:'+id,
					!this.checked,
					'verifyFichierslies_'+modelName
				);
			});
			displayAlert(
				'Des fichiers liés existent pour '+modelName+' id:'+id,
				!td.up('tr').select('input[type="checkbox"], input[type="radio"]').first().checked,
				'verifyFichierslies_'+modelName
			);
		}
	});
</script>