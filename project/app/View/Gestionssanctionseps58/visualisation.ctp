<?php $this->pageTitle = 'Visualisation des sanctions émises par l\'EP';?>
<h1><?php echo $this->pageTitle;?></h1>
<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>

<?php if( isset( $gestionsanctionseps58 ) ):?>
	<?php echo $this->Html->tag( 'h2', 'Résultats de la recherche' );?>
    <?php if( empty( $gestionsanctionseps58 ) ):?>
        <p class="notice"><?php echo 'Aucune sanction présente.';?></p>
    <?php else:?>
<?php $pagination = $this->Xpaginator->paginationBlock( 'Personne', $this->passedArgs ); ?>
	<?php echo $pagination;?>
	<?php echo $this->Xform->create( 'Gestionsanctionep58' );?>
	<?php
		foreach( Hash::flatten( $this->request->data['Search'] ) as $filtre => $value  ) {
			echo $this->Xform->input( "Search.{$filtre}", array( 'type' => 'hidden', 'value' => $value ) );
		}
	?>
    <table id="searchResults" class="tooltips default2">
        <thead>
            <tr>
                <th>Allocataire</th>
                <th>Commune allocataire</th>
                <th>Identifiant EP</th>
                <th>Identifiant commission</th>
                <th>Date de la commission</th>
                <th>Thématique</th>
				<th>Sanction 1</th>
				<th>Sanction 2</th>
				<th>Date prévisionnelle de radiation</th>
				<th>Modification de la sanction</th>
				<th>Date fin de sanction</th>
				<th>Commentaire</th>
				<th colspan="3">Action</th>
				<th class="innerTableHeader noprint">Informations complémentaires</th>
            </tr>
        </thead>
        <tbody>
		<?php $datePrevisionnelleRadiationInterval = Configure::read( 'Decisionsanctionep58.datePrevisionnelleRadiation' );?>
        <?php foreach( $gestionsanctionseps58 as $index => $gestionanctionep58 ):?>
            <?php
				$innerTable = '<table id="innerTablesearchResults'.$index.'" class="innerTable">
					<tbody>
						<tr>
							<th>'.__d( 'search_plugin', 'Structurereferenteparcours.lib_struc' ).'</th>
							<td>'.Hash::get( $gestionanctionep58, 'Structurereferenteparcours.lib_struc' ).'</td>
						</tr>
						<tr>
							<th>'.__d( 'search_plugin', 'Referentparcours.nom_complet' ).'</th>
							<td>'.Hash::get( $gestionanctionep58, 'Referentparcours.nom_complet' ).'</td>
						</tr>
					</tbody>
				</table>';

				$modeleDecision = Inflector::classify( "decisions{$gestionanctionep58['Dossierep']['themeep']}" );

				// Date prévisionnelle de radiation
				$datePrevisionnelleRadiation = date( 'd/m/Y', strtotime( $datePrevisionnelleRadiationInterval, strtotime( Hash::get( $gestionanctionep58, 'Commissionep.dateseance' ) ) ) );

				$tableCells = array(
						h( $gestionanctionep58['Personne']['qual'].' '.$gestionanctionep58['Personne']['nom'].' '.$gestionanctionep58['Personne']['prenom'] ),
						nl2br( h( Set::classicExtract(  $gestionanctionep58, 'Adresse.numvoie' ).' '.Set::classicExtract( $gestionanctionep58, 'Adresse.libtypevoie' ).' '.Set::classicExtract(  $gestionanctionep58, 'Adresse.nomvoie' )."\n".Set::classicExtract(  $gestionanctionep58, 'Adresse.codepos' ).' '.Set::classicExtract(  $gestionanctionep58, 'Adresse.nomcom' ) ) ),
						h( $gestionanctionep58['Ep']['identifiant'] ),
						h( $gestionanctionep58['Commissionep']['identifiant'] ),
						h( date_short( $gestionanctionep58['Commissionep']['dateseance'] ) ),
						h( Set::classicExtract( $options['Dossierep']['themeep'], ( $gestionanctionep58['Dossierep']['themeep'] ) ) ),
						nl2br(
							Set::enum(
								$gestionanctionep58[$modeleDecision]['decision'],
								$regularisationlistesanctionseps58[$modeleDecision]['decision']
							)
							."\n"
							.Set::enum(
								$gestionanctionep58[$modeleDecision]['listesanctionep58_id'],
								$listesanctionseps58
							)
						),
						nl2br(
							Set::enum(
								$gestionanctionep58[$modeleDecision]['decision2'],
								$regularisationlistesanctionseps58[$modeleDecision]['decision2']
							)
							."\n"
							.Set::enum(
								$gestionanctionep58[$modeleDecision]['autrelistesanctionep58_id'],
								$listesanctionseps58
							)
						),
						h( $datePrevisionnelleRadiation ),
						Set::enum(
							$gestionanctionep58[$modeleDecision]['arretsanction'],
							$options[$modeleDecision]['arretsanction']
						),
						date_short( $gestionanctionep58[$modeleDecision]['datearretsanction'] ),
						$gestionanctionep58[$modeleDecision]['commentairearretsanction'],
						$this->Xhtml->viewLink(
							'Voir le dossier',
							array( 'controller' => 'historiqueseps', 'action' => 'view_passage', $gestionanctionep58['Passagecommissionep']['id'] ),
							$this->Permissions->check( 'historiqueseps', 'view_passage' )
						),
						$this->Default2->button(
							'suivisanction1',
							array( 'controller' => 'gestionssanctionseps58', 'action' => 'impressionSanction1', '1',
							$gestionanctionep58['Passagecommissionep']['id'], $gestionanctionep58['Dossierep']['themeep'] ),
							array(
								'enabled' =>(
									$this->Permissions->check( 'gestionssanctionseps58', 'impressionSanction1' ) == 1
									&& $gestionanctionep58[$modeleDecision]['impressionfin1']
								)
							)
						),
						$this->Default2->button(
							'suivisanction2',
							array( 'controller' => 'gestionssanctionseps58', 'action' => 'impressionSanction2', '2',
							$gestionanctionep58['Passagecommissionep']['id'], $gestionanctionep58['Dossierep']['themeep'] ),
							array(
								'enabled' =>
									( $this->Permissions->check( 'gestionssanctionseps58', 'impressionSanction2' ) == 1 )
									&& $gestionanctionep58[$modeleDecision]['impressionfin2']
							)
						),
						array( $innerTable, array( 'class' => 'innerTableCell noprint' ) ),
					);

					echo $this->Xhtml->tableCells(
						$tableCells,
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
                ?>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php echo $pagination;?>
		<ul class="actionMenu">
			<li><?php
				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'gestionssanctionseps58', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' )
				);
			?></li>
			<li><?php
				echo $this->Default2->button(
					'printcohorte1',
					Set::merge(
						array(
							'controller' => 'gestionssanctionseps58', 'action' => 'impressionsSanctions1'
						),
						Hash::flatten( $this->request->params['named'] )
					)
				);

			?></li>
			<li><?php
				echo $this->Default2->button(
					'printcohorte2',
					Set::merge(
						array(
							'controller' => 'gestionssanctionseps58', 'action' => 'impressionsSanctions2'
						),
						Hash::flatten( $this->request->params['named'] )
					)
				);
			?></li>
		</ul>
	<?php endif;?>
<?php endif;?>