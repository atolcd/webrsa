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

<?php require_once( 'filtre.ctp' );?>

<?php if( !empty( $this->request->data ) && $formSent ):?>
<h2 class="noprint">Résultats de la recherche</h2>
	<?php if( empty( $cohorte ) ):?>
		<?php
			switch( $this->action ) {
				case 'orientees':
					$message = 'Aucun allocataire orienté ne correspond à vos critères.';
					break;
				default:
					$message = 'Tous les allocataires ont été orientés.';
			}
		?>
		<p class="notice"><?php echo $message;?></p>
	<?php else:?>
		<?php
			$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

			$formatPagination = 'Results %start% - %end% out of %count%.';
			if( isset( $this->request->data['Filtre']['paginationNombreTotal'] ) && !$this->request->data['Filtre']['paginationNombreTotal'] ) {
				$page = Set::classicExtract( $this->request->params, "paging.Personne.page" );
				$count = Set::classicExtract( $this->request->params, "paging.Personne.count" );
				$limit = Set::classicExtract( $this->request->params, "paging.Personne.options.limit" );
				if( ( $count > ( $limit * $page ) ) ) {
					$formatPagination = 'Résultats %start% - %end% sur au moins %count% résultats.';
				}
			}

			$this->Xpaginator->options( array('url' => $this->passedArgs ) );
			$pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs, $formatPagination );
		?>
		<?php echo $pagination;?>
		<table class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'Commune', 'Adresse.nomcom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom, prenom', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date demande', 'Dossier.dtdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Présence DSP', 'Dsp.id' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Type de service instructeur', 'Suiviinstruction.typeserins' );?></th>
					<?php if( Configure::read( 'Cg.departement' ) == 93 ):?><th><?php echo $this->Xpaginator->sort( __d( 'orientstruct', 'Orientstruct.origine' ), 'Orientstruct.origine' );?></th><?php endif;?>
					<th><?php echo $this->Xpaginator->sort( 'PréOrientation', 'Orientstruct.propo_algo' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Orientation', 'Typeorient.lib_type_orient' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Structure', 'Structurereferente.lib_struc' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Décision', 'Orientstruct.statut_orient' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date préOrientation', 'Orientstruct.date_propo' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date d\'Orientation', 'Orientstruct.date_valid' );?></th>

					<th class="action">Action</th>
					<th class="innerTableHeader">Informations complémentaires</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $cohorte as $index => $personne ):?>
					<?php
						// FIXME: date ouverture de droits -> voir flux instruction
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
									<td>'.h( date_short( $personne['Situationdossierrsa']['dtclorsa'] ) ).'</td>
								</tr>
								<tr>
									<th>Motif de fin de droit</th>
									<td>'.h( @$moticlorsa[$personne['Situationdossierrsa']['moticlorsa']] ).'</td>
								</tr>
								<tr>
									<th>Rôle</th>
									<td>'.h( @$rolepers[$personne['Prestation']['rolepers']] ).'</td>
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

						$cells = array(
							h( $personne['Adresse']['nomcom'] ),
							h( $personne['Personne']['nom'].' '.$personne['Personne']['prenom'] ),
							h( date_short( $personne['Dossier']['dtdemrsa'] ) ),
							h( !empty( $personne['Dsp']['id'] ) ? 'Oui' : 'Non' ),
							h( value( $typeserins, Set::classicExtract( $personne, 'Suiviinstruction.typeserins') ) ),
						);

						if( Configure::read( 'Cg.departement' ) == 93 ) {
							array_push(
								$cells,
								h( Set::enum( $personne['Orientstruct']['origine'], $options['Orientstruct']['origine'] ) )
							);
						}

						array_push(
							$cells,
							h( Set::enum( $personne['Orientstruct']['propo_algo'], $typesOrient ) ),
							h( $personne['Typeorient']['lib_type_orient'] ),
							h( $personne['Structurereferente']['lib_struc'] ),
							h( $personne['Orientstruct']['statut_orient'] ),
							h( date_short( $personne['Orientstruct']['date_propo'] ) ),
							h( date_short( $personne['Orientstruct']['date_valid'] ) ),
							$this->Xhtml->printLink(
								'Imprimer la notification',
								array( 'controller' => 'orientsstructs', 'action' => 'impression', $personne['Orientstruct']['id'] ),
								$this->Permissions->check( 'orientsstructs', 'impression' )
							),
							array( $innerTable, array( 'class' => 'innerTableCell' ) )
						);

						echo $this->Xhtml->tableCells(
							$cells,
							array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
							array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
						);
					?>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php echo $pagination;?>

		<ul class="actionMenu">
			<li><?php
				echo $this->Xhtml->printCohorteLink(
					'Imprimer la cohorte',
					Set::merge(
						array(
							'controller' => 'cohortes',
							'action'     => 'cohortegedooo',
							'id' => 'Cohorteoriente'
						),
						Hash::flatten( $this->request->data, '__' )
					)
				);
			?></li>
		</ul>

	<?php endif;?>
<?php endif;?>