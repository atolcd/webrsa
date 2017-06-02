<h1><?php echo __d( 'gestionanomaliebdd', 'Gestionsanomaliesbdds::index' );?></h1>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->element( 'required_javascript' );

	if( is_array( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
		).'</li></ul>';
	}

	echo $this->Xform->create( null, array( 'id' => 'Search', 'url' => array( 'controller' => 'gestionsanomaliesbdds', 'action' => 'index' ), 'class' => 'gestionsanomaliesbdd index '.( isset( $results ) ? 'folded' : 'unfolded' ) ) );
	// Types de problèmes à détecter
	echo '<fieldset id="SearchProblemes"><legend>Détection des problèmes</legend>'.$this->Xform->input(
			'Gestionanomaliebdd.touteerreur', array( 'type' => 'checkbox', 'domain' => 'gestionanomaliebdd' )
	);
	echo '<fieldset id="SearchTypesProblemes"><legend>Types de problèmes à détecter</legend>'.$this->Default2->subform(
		array(
			'Gestionanomaliebdd.enerreur' => array( 'type' => 'select', 'empty' => true, 'domain' => 'gestionanomaliebdd', 'div' => array( 'class' => 'input select enerreur' ) ),
			'Gestionanomaliebdd.sansprestation' => array( 'type' => 'select', 'empty' => true, 'domain' => 'gestionanomaliebdd', 'div' => array( 'class' => 'input select sansprestation' ) ),
			'Gestionanomaliebdd.doublons' => array( 'type' => 'select', 'empty' => true, 'domain' => 'gestionanomaliebdd', 'div' => array( 'class' => 'input select doublons' ) ),
		),
		array(
			'options' => $options,
			'form' => false,
		)
	).'</fieldset>'.$this->Xform->input(
			'Gestionanomaliebdd.methode', array( 'type' => 'select', 'empty' => false, 'domain' => 'gestionanomaliebdd', 'options' => $options['Gestionanomaliebdd']['methode'] )
	).'</fieldset>';
	// Filtre sur le dossier
	echo '<fieldset id="SearchFiltreDossier"><legend>Filtrer sur le dossier</legend>'.$this->Default2->subform(
		array(
			'Dossier.numdemrsa' => array( 'type' => 'text', 'domain' => 'gestionanomaliebdd' ),
			'Dossier.dtdemrsa' => array( 'domain' => 'gestionanomaliebdd' ),
			'Dossier.matricule' => array( 'type' => 'text', 'domain' => 'gestionanomaliebdd' ),
			'Foyer.sitfam' => array( 'domain' => 'gestionanomaliebdd' ),
			'Foyer.ddsitfam' => array( 'domain' => 'gestionanomaliebdd' ),
			'Situationdossierrsa.etatdosrsa' => array( 'multiple' => 'checkbox', 'empty' => false, 'domain' => 'gestionanomaliebdd' ),
		),
		array(
			'options' => $options,
			'form' => false,
		)
	).'</fieldset>';
	// Filtre sur l'adresse actuelle
	echo '<fieldset id="SearchFiltreAdresse"><legend>Filtrer sur l\'adresse actuelle</legend>'.$this->Default2->subform(
		array(
			'Adresse.nomcom' => array( 'domain' => 'gestionanomaliebdd' ),
			'Adresse.numcom' => array( 'domain' => 'gestionanomaliebdd' ),//FIXME: ne fonctionne pas au 66
		),
		array(
			'options' => $options,
			'form' => false,
		)
	).'</fieldset>';
	// Filtrer sur une personne du dossier
	echo '<fieldset id="SearchFiltrePersonne"><legend>Filtrer sur une personne du foyer</legend>'.$this->Default2->subform(
		array(
			'Personne.nom' => array( 'domain' => 'gestionanomaliebdd' ),
			'Personne.prenom' => array( 'domain' => 'gestionanomaliebdd' ),
			'Personne.nir' => array( 'domain' => 'gestionanomaliebdd' ),
		),
		array(
			'options' => $options,
			'form' => false,
		)
	).'</fieldset>';

	echo $this->Search->paginationNombretotal( 'Search.nombre_total' );
	echo $this->Search->observeDisableFormOnSubmit( 'Search' );

	echo $this->Xform->end( 'Rechercher' );

	if( isset( $results ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		$filtresErreur = array(
			'touteerreur' => @$this->request->data['Gestionanomaliebdd']['touteerreur'],
			'enerreur' => @$this->request->data['Gestionanomaliebdd']['enerreur'],
			'sansprestation' => @$this->request->data['Gestionanomaliebdd']['sansprestation'],
			'doublons' => @$this->request->data['Gestionanomaliebdd']['doublons']
		);
		$filtresErreurNull = true;
		foreach( $filtresErreur as $filtreErreur ) {
			if( !is_null( $filtreErreur ) ) {
				$filtresErreurNull = false;
			}
		}
		if( $filtresErreurNull ) {
			echo $this->Xhtml->tag( 'p', 'Vous n\'avez sélectionné aucun filtre concernant les types de problèmes', array( 'class' => 'notice' ) );
		}

		if( empty( $results ) ) {
			echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond à vos critères.', array( 'class' => 'notice' ) );
		}
		else {
			$pagination = $this->Xpaginator2->paginationBlock( 'Dossier', $this->passedArgs );

			$urlParams = Hash::flatten( $this->request->data, '__' );

			$thead = '<tr>'
					.'<th>'.$this->Xpaginator2->sort( __d( 'dossier', 'Dossier.numdemrsa' ), 'Dossier.numdemrsa' ).'</th>'
					.'<th>'.$this->Xpaginator2->sort( __d( 'dossier', 'Dossier.dtdemrsa' ), 'Dossier.dtdemrsa' ).'</th>'
					.'<th>'.$this->Xpaginator2->sort( __d( 'dossier', 'Dossier.matricule' ), 'Dossier.matricule' ).'</th>'
					.'<th>'.$this->Xpaginator2->sort( __d( 'foyer', 'Foyer.sitfam' ), 'Foyer.sitfam' ).'</th>'
					.'<th>'.$this->Xpaginator2->sort( __d( 'gestionanomaliebdd', 'Foyer.ddsitfam' ), 'Foyer.ddsitfam' ).'</th>'
					.'<th>'.$this->Xpaginator2->sort( __d( 'situationdossierrsa', 'Situationdossierrsa.etatdosrsa' ), 'Situationdossierrsa.etatdosrsa' ).'</th>'
					.'<th>'.$this->Xpaginator2->sort( __d( 'adresse', 'Adresse.nomcom' ), 'Adresse.nomcom' ).'</th>'
					.'<th class="action noprint" colspan="3">Problèmes détectés</th>'
					.'<th class="action noprint">Verrouillé ?</th>'
					.'<th class="action noprint" colspan="2">Actions</th>'
					.'<th class="innerTableHeader noprint">Informations complémentaires</th>'
				.'</tr>';

			$innerThead = $this->Html->tableHeaders(
				array(
					__d( 'personne', 'Personne.qual' ),
					__d( 'personne', 'Personne.nom' ),
					__d( 'personne', 'Personne.prenom' ),
					__d( 'personne', 'Personne.nomnai' ),
					__d( 'personne', 'Personne.dtnai' ),
					__d( 'personne', 'Personne.nir' ),
					__d( 'prestation', 'Prestation.rolepers' )
				)
			);

			$tbody = '';
			foreach( $results as $i => $result ) {
				$rowId = "innerTableTrigger{$i}";

				$innerTbody = '';
				foreach( $result['Doublons'] as $doublon ) {
					$innerRow = "<td>".$this->Type2->format( $doublon, 'Personne.qual', array( 'options' => $options ) )."</td>";
					$innerRow .= "<td>{$doublon['Personne']['nom']}</td>";
					$innerRow .= "<td>{$doublon['Personne']['prenom']}</td>";
					$innerRow .= "<td>{$doublon['Personne']['nomnai']}</td>";
					$innerRow .= "<td>".$this->Type2->format( $doublon, 'Personne.dtnai' )."</td>";
					$innerRow .= "<td>{$doublon['Personne']['nir']}</td>";
					$innerRow .= "<td>".$this->Type2->format( $doublon, 'Prestation.rolepers', array( 'options' => $options ) )."</td>";

					$innerTbody .= "<tr>{$innerRow}</tr>";
				}

				if( empty( $innerTbody ) ) {
					$innerTable = "<table id=\"innerTablesearchResults{$i}\" class=\"innerTable\"><tbody><tr><td>Aucun doublon de personnes détecté</td></tr></tbody></table>";
				}
				else {
					$innerTable = "<table id=\"innerTablesearchResults{$i}\" class=\"innerTable\"><thead>{$innerThead}</thead><tbody>{$innerTbody}</tbody></table>";
				}

				$correction = ( !empty( $result['Foyer']['enerreur'] ) || !empty( $result['Foyer']['sansprestation'] ) || !empty( $result['Foyer']['doublonspersonnes'] ) );

				$tbody .= $this->Html->tableCells(
					array(
						h( Set::classicExtract( $result, 'Dossier.numdemrsa' ) ),
						$this->Type2->format( $result, 'Dossier.dtdemrsa' ),
						h( Set::classicExtract( $result, 'Dossier.matricule' ) ),
						h( @$options['Foyer']['sitfam'][Set::classicExtract( $result, 'Foyer.sitfam' )] ),
						$this->Type2->format( $result, 'Foyer.ddsitfam' ),
						h( @$options['Situationdossierrsa']['etatdosrsa'][Set::classicExtract( $result, 'Situationdossierrsa.etatdosrsa' )] ),
						h( Set::classicExtract( $result, 'Adresse.nomcom' ) ),
						array( $this->Gestionanomaliebdd->foyerErreursPrestationsAllocataires( $result, false ), array( 'class' => 'icon' ) ),
						array( $this->Gestionanomaliebdd->foyerPersonnesSansPrestation( $result, false ), array( 'class' => 'icon' ) ),
						array( $this->Gestionanomaliebdd->foyerErreursDoublonsPersonnes( $result, false ), array( 'class' => 'icon' ) ),
						( $result['Dossier']['locked'] ? $this->Xhtml->image( 'icons/lock.png', array( 'alt' => '', 'title' => 'Dossier verrouillé' ) ) : null ),
						array(
							$this->Default2->button(
								'view',
								array( 'controller' => 'personnes', 'action' => 'index', $result['Foyer']['id'] ),
								array(
									'label' => 'Voir',
									'enabled' => $this->Permissions->check( 'personnes', 'index' ),
									'title' => sprintf( 'Voir le dossier « %s »', $result['Dossier']['numdemrsa'] )
								)
							),
							array( 'class' => 'noprint' )
						),
						array(
							$this->Default2->button(
								'edit',
								array_merge( array( 'action' => 'foyer', $result['Foyer']['id'] ), $urlParams ),
								array(
									'label' => 'Corriger',
									'enabled' => $correction && $this->Permissions->check( 'gestionsanomaliesbdds', 'foyer' ),
									'title' => sprintf( 'Corriger le dossier « %s »', $result['Dossier']['numdemrsa'] ),
									'class' => 'external'
								)
							),
							array( 'class' => 'noprint' )
						),
						array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
					),
					array( 'class' => 'odd', 'id' => $rowId ),
					array( 'class' => 'even', 'id' => $rowId )
				);
			}
			echo $pagination.'<table id="searchResults" class="tooltips default2 gestionsanomaliesbdd"><thead>'.$thead.'</thead><tbody>'.$tbody.'</tbody></table>'.$pagination;
		}
	}
?>
<script type="text/javascript">
//<![CDATA[
	document.observe( "dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'GestionanomaliebddTouteerreur', $( 'SearchTypesProblemes' ), true );
	} );
//]]>
</script>