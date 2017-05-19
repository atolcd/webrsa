<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Xhtml->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->actions(
		array(
			'/Indicateurssuivis/search/#toggleform' => array(
				'onclick' => '$(\'IndicateurssuivisSearchForm\').toggle(); return false;'
			),
		)
	);

	echo $this->Xform->create( 'Search', array( 'id' => 'IndicateurssuivisSearchForm' ) );

	echo $this->Allocataires->blocDossier( array( 'options' => $options ) );
	echo $this->Allocataires->blocAdresse( array( 'options' => $options ) );
	echo $this->Allocataires->blocAllocataire( array( 'options' => $options ) );

	// Début spécificités indicateurs suivis
	echo '<fieldset id="specificites_indicateurssuivis"><legend>'.__d( 'indicateurssuivis', 'Search.Orientation' ).'</legend>';
	echo $this->Xform->input( 'Search.Referentorientant.structurereferente_id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'PersonneReferent.structurereferente_id' ), 'empty' => true, 'domain' => 'indicateurssuivis' ) );
	echo $this->Xform->input( 'Search.Referentorientant.id', array( 'type' => 'select', 'options' => (array)Hash::get( $options, 'PersonneReferent.referent_id' ), 'empty' => true, 'domain' => 'indicateurssuivis' ) );
	echo '</fieldset>';
	// Fin spécificités indicateurs suivis

	echo $this->Allocataires->blocReferentparcours( array( 'options' => $options ) );
	echo $this->Allocataires->blocPagination( array( 'options' => $options ) );
	echo $this->Allocataires->blocScript( array( 'options' => $options ) );

	echo $this->Xform->end( 'Search' );

	echo $this->Observer->dependantSelect(
		array(
			'Search.Referentorientant.structurereferente_id' => 'Search.Referentorientant.id',
		)
	);
?>
<?php if( isset( $results ) ):?>
	<?php
		App::uses( 'SearchProgressivePagination', 'Search.Utility' );

		$this->Default3->DefaultPaginator->options(
			array( 'url' => Hash::flatten( (array)$this->request->data, '__' ) )
		);

		$pagination = $this->Default3->pagination(
			array(
				'format' => __( SearchProgressivePagination::format( !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' ) ) )
			)
		);
	?>
	<h2 class="noprint">Résultats de la recherche</h2>
	<?php echo $pagination;?>
	<?php if( is_array( $results ) && count( $results ) > 0 ):?>
		<table id="searchResults">
			<thead>
				<tr>
					<th rowspan="2"><?php echo $this->Xpaginator->sort( __d( 'dossier', 'Dossier.matricule.large' ), 'Dossier.matricule' );?></th>
					<th colspan="2">Demandeur</th>
					<th rowspan="2">Adresse</th>
					<th>Nom / Prénom du Conjoint</th>
					<th rowspan="2"><?php echo $this->Xpaginator->sort( 'Date ouverture de droits', 'Dossier.dtdemrsa' );?></th>
					<th rowspan="2">Référent orientant</th>
					<th><?php echo $this->Xpaginator->sort( 'Date d\'orientation par la COV', 'Orientstruct.date_valid' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Rang orientation', 'Orientstruct.rgorient' );?></th>
					<th rowspan="2">Référent unique</th>
					<th colspan="3">CER</th>
					<th rowspan="2">Dernière information Pôle Emploi</th>
					<th colspan="2">Passage en EP</th>
					<th>Action</th>
				</tr>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'Nom/Prénom', 'Personne.nom_complet_court' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de naissance', 'Personne.dtnai' );?></th>
					<th></th>
					<th></th>
					<th></th>
					<th><?php echo $this->Xpaginator->sort( 'Date début', 'Contratinsertion.dd_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date fin', 'Contratinsertion.df_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Rang', 'Contratinsertion.rg_ci' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date', 'Commissionep.dateseance' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Motif', 'Dossierep.themeep' );?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( $results as $index => $result ):?>
					<?php
						$adresse = Set::classicExtract( $result, 'Adresse.numvoie' ).' '.Set::classicExtract( $result, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $result, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $result, 'Adresse.compladr' ).'<br /> '.Set::classicExtract( $result, 'Adresse.codepos' ).' '.Set::classicExtract( $result, 'Adresse.nomcom' );

						echo $this->Xhtml->tableCells(
							array(
								h( $result['Dossier']['matricule'] ),
								h( $result['Personne']['nom_complet'] ),
								h( date_short( $result['Personne']['dtnai'] ) ),
								$adresse,
								h( $result['Conjoint']['nom_complet'] ),
								h( date_short( $result['Dossier']['dtdemrsa'] ) ),
								h( $result['Referentorientant']['nom_complet'] ),
								h( date_short( $result['Orientstruct']['date_valid'])),
								h( $result['Orientstruct']['rgorient']),
								h( $result['Referentparcours']['nom_complet'] ),
								h( date_short( $result['Contratinsertion']['dd_ci'] ) ),
								h( date_short( $result['Contratinsertion']['df_ci'] ) ),
								h( $result['Contratinsertion']['rg_ci']),
								h( Set::enum( $result['Historiqueetatpe']['etat'], $options['Historiqueetatpe']['etat'] ).' '.date_short( $result['Historiqueetatpe']['date'] ) ),
								h( date_short( $result['Commissionep']['dateseance'] ) ),
								h( !empty( $result['Dossierep']['themeep'] ) ? Set::classicExtract( $options['Dossierep']['themeep'], $result['Dossierep']['themeep'] ) : null ),
								array(
									$this->Xhtml->link(
										'Voir',
										array(
											'controller' => 'dossiers',
											'action' => 'view',
											$result['Dossier']['id']
										),
										array(
											'class' => 'external dossiers view'
										)
									),
									array( 'class' => 'action' )
								)
							),
							array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
							array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
						);
					?>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php echo $pagination;?>
		<?php if( Set::extract( $this->request->params, 'paging.Dossier.count' ) > 65000 ):?>
			<p class="noprint" style="border: 1px solid #556; background: #ffe;padding: 0.5em;"><?php echo $this->Xhtml->image( 'icons/error.png' );?> <strong>Attention</strong>, il est possible que votre tableur ne puisse pas vous afficher les résultats au-delà de la 65&nbsp;000ème ligne.</p>
		<?php endif;?>
	<ul class="actionMenu">
			<li><?php
				echo $this->Xhtml->printLinkJs(
					'Imprimer le tableau',
					array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
				);
			?></li>
			<li><?php
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'indicateurssuivis', 'action' => 'exportcsv_search' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'indicateurssuivis', 'exportcsv_search' )
				);
			?></li>
		</ul>
	<?php else:?>
		<p>Vos critères n'ont retourné aucun résultat.</p>
	<?php endif?>
<?php endif?>