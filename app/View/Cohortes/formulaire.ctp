<h1><?php echo $this->pageTitle = $pageTitle;?></h1>
<?php
	if( !empty( $this->request->data ) ) {
		echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
			$this->Xhtml->image(
				'icons/application_form_magnify.png',
				array( 'alt' => '' )
			).' Formulaire',
			'#',
			array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Filtre' ).toggle(); return false;" )
		).'</li></ul>';
	}
?>

<?php if( isset( $cohorte ) ):?>
	<?php
		if( Configure::read( 'debug' ) > 0 ) {
			echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
		}
	?>
	<script type="text/javascript">
		var structAuto = new Array();
		<?php foreach( $structuresAutomatiques as $typeId => $structureAutomatique ):?>
				if( structAuto["<?php echo $typeId;?>"] == undefined ) { structAuto["<?php echo $typeId;?>"] = new Array(); }
				<?php foreach( $structureAutomatique as $codeInsee => $structure ):?>
					structAuto["<?php echo $typeId;?>"]["<?php echo $codeInsee;?>"] = "<?php echo $structure;?>";
				<?php endforeach;?>
		<?php endforeach;?>

		function selectStructure( index ) {
			var typeOrient = $F( 'Orientstruct' + index + 'TypeorientId' );
			var codeinsee = $F( 'Orientstruct' + index + 'Codeinsee' );
			if( ( structAuto[typeOrient] != undefined ) && ( structAuto[typeOrient][codeinsee] != undefined ) ) {
				$( 'Orientstruct' + index + 'StructurereferenteId' ).value = structAuto[typeOrient][codeinsee];
			}
		}

		document.observe("dom:loaded", function() {
			var indexes = new Array( <?php echo "'".implode( "', '", array_keys( $cohorte ) )."'";?> );
			indexes.each( function( index ) {
				/* Dépendance des deux champs "select" */
				dependantSelect( 'Orientstruct' + index + 'StructurereferenteId', 'Orientstruct' + index + 'TypeorientId' );

				/* Structures automatiques suivant le code Insée */
				// Initialisation
				if( $F( 'Orientstruct' + index + 'StructurereferenteId' ) == '' ) {
					selectStructure( index );
				}

				// Traquer les changements
				Event.observe( $( 'Orientstruct' + index + 'TypeorientId' ), 'change', function() {
					selectStructure( index );
				} );
			} );
		});
	</script>
<?php endif;?>

<?php require_once( 'filtre.ctp' );?>

<?php if( !empty( $this->request->data ) && $formSent ):?>
<h2 class="noprint">Résultats de la recherche</h2>
	<?php if( empty( $cohorte ) ):?>
		<p class="notice">Aucune demande dans la cohorte.</p>
	<?php else:?>
		<?php
			$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

			$formatPagination = 'Nombre de pages: %s - Nombre de résultats: %s.';
			if( isset( $this->request->data['Filtre']['paginationNombreTotal'] ) && !$this->request->data['Filtre']['paginationNombreTotal'] ) {
				$page = Set::classicExtract( $this->request->params, "paging.Personne.page" );
				$count = Set::classicExtract( $this->request->params, "paging.Personne.count" );
				$limit = Set::classicExtract( $this->request->params, "paging.Personne.options.limit" );
				if( ( $count > ( $limit * $page ) ) ) {
					$formatPagination = 'Nombre de pages: au moins %s - Nombre de résultats: au moins %s.';
				}
			}
		?>
		<p><?php echo sprintf( $formatPagination, $this->Locale->number( $this->request->params['paging']['Personne']['pageCount'] ), $this->Locale->number( $this->request->params['paging']['Personne']['count'] ) );?></p>
		<?php echo $this->Form->create( 'NouvellesDemandes', array() );?>
		<?php
			foreach( Hash::flatten( $filtre ) as $key => $value ) {
				echo '<div>'.$this->Form->input( $key, array( 'type' => 'hidden', 'value' => $value, 'id' => 'FiltreBas'.Inflector::camelize( str_replace( '.', '_', $key ) ) ) ).'</div>';
			}
			$typesorientsNamesToIds = array_flip( $typesOrient );
		?>
		<table class="tooltips">
			<thead>
				<tr>
					<th>Commune</th>
					<th>Date demande</th>
					<th>Présence DSP</th>
					<th>Nom prenom</th>
					<th>Type de service instructeur</th>
					<th>PréOrientation</th>
					<th class="action">Orientation</th>
					<th class="action">Structure</th>
					<th class="action">Décision</th>
					<th>Statut</th>
					<?php if( in_array( $this->action, array( 'nouvelles', 'enattente', 'preconisationscalculables', 'preconisationsnoncalculables' ) ) ):?><th class="action">Détails</th><?php endif;?>
					<th class="innerTableHeader">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $cohorte as $index => $personne ):?>
					<?php
						$innerTable = '<table id="innerTable'.$index.'" class="innerTable">
							<tbody>
								<tr>
									<th>N° de dossier</th>
									<td>'.h( $personne['Dossier']['numdemrsa'] ).'</td>
								</tr>
								<tr>
									<th>Date ouverture de droit</th>
									<td>'.h( date_short( $personne['Dossier']['dtdemrsa'] ) ).'</td>
								</tr>
								<tr>
									<th>Date naissance</th>
									<td>'.h( date_short( $personne['Personne']['dtnai'] ) ).'</td>
								</tr>
								<tr>
									<th>' . __d( 'dossier', 'Dossier.matricule.large' ) . '</th>
									<td>'.h( $personne['Dossier']['matricule'] ).'</td>
								</tr>
								<tr>
									<th>NIR</th>
									<td>'.h( $personne['Personne']['nir'] ).'</td>
								</tr>
								<tr>
									<th>Code postal</th>
									<td>'.h( $personne['Adresse']['codepos'] ).'</td>
								</tr>
								<tr>
									<th>Canton</th>
									<td>'.h( $personne['Adresse']['canton'] ).'</td>
								</tr>
								<tr>
									<th>Date de fin de droit</th>
									<td>'.h( $personne['Situationdossierrsa']['dtclorsa'] ).'</td>
								</tr>
								<tr>
									<th>Motif de fin de droit</th>
									<td>'.h( $personne['Situationdossierrsa']['moticlorsa'] ).'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.h( $rolepers[$personne['Prestation']['rolepers']] ).'</td>
								</tr>
								<tr>
									<th>État du dossier</th>
									<td>'.h( value( $etatdosrsa, $personne['Situationdossierrsa']['etatdosrsa'] ) ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ).'</th>
									<td>'.Hash::get( $personne, 'Structurereferenteparcours.lib_struc' ).'</td>
								</tr>
								<tr>
									<th>'.__d( $domain_search_plugin, 'Referentparcours.nom_complet' ).'</th>
									<td>'.Hash::get( $personne, 'Referentparcours.nom_complet' ).'</td>
								</tr>
							</tbody>
						</table>';
						$typeorient_id = Set::extract( $this->request->data, 'Orientstruct.'.$index.'.typeorient_id' );
						$structurereferente_id = ( !empty( $typeorient_id ) ? $typeorient_id.'_'.preg_replace( '/^[0-9]+_([0-9]+)$/', '\1', Set::extract( $this->request->data, 'Orientstruct.'.$index.'.structurereferente_id' ) ) : null );
						$statut_orient = Set::extract( $this->request->data, 'Orientstruct.'.$index.'.statut_orient' );


						$tableCells = array(
							h( $personne['Adresse']['nomcom'] ),
							h( date_short( $personne['Dossier']['dtdemrsa'] ) ),
							h( $personne['Dsp']['id'] ? 'Oui' : 'Non' ),
							h( $personne['Personne']['nom'].' '.$personne['Personne']['prenom'] ),
							h( isset( $typeserins[Set::classicExtract( $personne, 'Suiviinstruction.typeserins')] ) ? $typeserins[Set::classicExtract( $personne, 'Suiviinstruction.typeserins')] : '' ),
							h( Set::enum( $personne['Orientstruct']['propo_algo'], $typesOrient ) ).
							$this->Form->input( 'Orientstruct.'.$index.'.propo_algo', array( 'label' => false, 'type' => 'hidden', 'value' => $personne['Orientstruct']['propo_algo'] ) ).
							$this->Form->input( 'Orientstruct.'.$index.'.origine', array( 'label' => false, 'type' => 'hidden', 'value' => 'cohorte' ) ).
							/* FIXME -> id unset ? */
							$this->Form->input( 'Orientstruct.'.$index.'.id', array( 'label' => false, 'type' => 'hidden', 'value' => $personne['Orientstruct']['id'] ) ).
							$this->Form->input( 'Orientstruct.'.$index.'.dossier_id', array( 'label' => false, 'type' => 'hidden', 'value' => $personne['Foyer']['dossier_id'] ) ).
							$this->Form->input( 'Orientstruct.'.$index.'.codeinsee', array( 'label' => false, 'type' => 'hidden', 'value' => $personne['Adresse']['numcom'] ) ).
							$this->Form->input( 'Orientstruct.'.$index.'.personne_id', array( 'label' => false, 'type' => 'hidden', 'value' => $personne['Personne']['id'] ) ),
							$this->Form->input( 'Orientstruct.'.$index.'.typeorient_id', array( 'label' => false, 'type' => 'select', 'options' => $typesOrient, 'value' => ( !empty( $typeorient_id ) ? $typeorient_id : $personne['Orientstruct']['propo_algo'] ), 'empty' => true ) ),
							$this->Form->input( 'Orientstruct.'.$index.'.structurereferente_id', array( 'label' => false, 'type' => 'select', 'options' => $structuresReferentes, 'empty' => true, 'value' => ( !empty( $structurereferente_id ) ? $structurereferente_id : $personne['Orientstruct']['structurereferente_id'] ) ) ),
							$this->Form->input( 'Orientstruct.'.$index.'.statut_orient', array( 'label' => false, 'div' => false, 'legend' => false, 'type' => 'radio', 'options' => array( 'Orienté' => 'A valider', 'En attente' => 'En attente' ), 'value' => ( !empty( $statut_orient ) ? $statut_orient : 'Orienté' ) ) ),
							h( $personne['Dossier']['statut'] ),
						);

						if( in_array( $this->action, array( 'nouvelles', 'enattente', 'preconisationscalculables', 'preconisationsnoncalculables' ) ) ) {
							$tableCells[] = array(
								$this->Xhtml->link(
									'Voir',
									array(
										'controller' => 'dossiers',
										'action' => 'view',
										$personne['Dossier']['id']
									),
									array(
										'class' => 'external',
										'title' => "Accéder aux informations de {$personne['Personne']['nom']} {$personne['Personne']['prenom']}"
									)
								),
								array( 'class' => 'action button view' )
							);
						}

						$tableCells[] = array( $innerTable, array( 'class' => 'innerTableCell' ) );

						echo $this->Xhtml->tableCells(
							$tableCells,
							array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
							array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
						);

					?>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php echo $this->Form->submit( 'Validation de la liste' );?>
		<?php echo $this->Form->end();?>
	<?php endif;?>
<?php endif;?>
