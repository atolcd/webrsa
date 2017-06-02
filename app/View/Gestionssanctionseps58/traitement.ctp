<?php $this->pageTitle = 'Gestion des sanctions émises par l\'EP';?>
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
<table id="searchResults" class="tooltips">
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
				<th class="action">Modification de la sanction</th>
				<th class="action">Date fin de sanction</th>
				<th class="action">Commentaire</th>
				<th class="action">Action</th>
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

				// Type de sanction
				$decisionSanction1 = Set::enum( $gestionanctionep58[$modeleDecision]['decision'], $regularisationlistesanctionseps58[$modeleDecision]['decision'] );
				$decisionSanction2 = Set::enum( $gestionanctionep58[$modeleDecision]['decision2'], $regularisationlistesanctionseps58[$modeleDecision]['decision'] );
				// Libellé de la sanction
				$libelleSanction1 = Set::enum( $gestionanctionep58[$modeleDecision]['listesanctionep58_id'], $listesanctionseps58 );
				$libelleSanction2 = Set::enum( $gestionanctionep58[$modeleDecision]['autrelistesanctionep58_id'], $listesanctionseps58 );

				// Date prévisionnelle de radiation
				$datePrevisionnelleRadiation = date( 'd/m/Y', strtotime( $datePrevisionnelleRadiationInterval, strtotime( Hash::get( $gestionanctionep58, 'Commissionep.dateseance' ) ) ) );

				//Champ permettant la modification de la sanction
				$fieldDecisionSanction = $this->Xform->input( "{$modeleDecision}.{$index}.id", array( 'type' => 'hidden', 'value' => $gestionanctionep58[$modeleDecision]['id'] ) ).
					$this->Xform->input( "{$modeleDecision}.{$index}.arretsanction", array( 'type' => 'select', 'options' => $options[$modeleDecision]['arretsanction'], 'label' => false, 'empty' => true ) );
				//Champ permettant de saisir la date de la fin de la sanction
				$dateFinSanction =
					$this->Xform->input( "{$modeleDecision}.{$index}.datearretsanction", array( 'type' => 'hidden', 'value' => '', 'id' => false  ) )
					.$this->Xform->input( "{$modeleDecision}.{$index}.datearretsanction", array( 'label' => false, 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 3, 'maxYear' => date( 'Y' ) + 3 )  );
				//Champ permettant de saisir le commentaire de fin de la sanction
				$commentaireFinSanction = $this->Xform->input( "{$modeleDecision}.{$index}.commentairearretsanction", array( 'label' => false, 'type' => 'textarea' ) );

				$tableCells = array(
					$this->Xform->input( "Foyer.{$index}.dossier_id", array( 'label' => false, 'type' => 'hidden', 'value' => $gestionanctionep58['Foyer']['dossier_id'] ) ).
					h( $gestionanctionep58['Personne']['qual'].' '.$gestionanctionep58['Personne']['nom'].' '.$gestionanctionep58['Personne']['prenom'] ),
					nl2br( h( Set::classicExtract(  $gestionanctionep58, 'Adresse.numvoie' ).' '.Set::classicExtract( $gestionanctionep58, 'Adresse.libtypevoie' ).' '.Set::classicExtract(  $gestionanctionep58, 'Adresse.nomvoie' )."\n".Set::classicExtract(  $gestionanctionep58, 'Adresse.codepos' ).' '.Set::classicExtract(  $gestionanctionep58, 'Adresse.nomcom' ) ) ),
					h( $gestionanctionep58['Ep']['identifiant'] ),
					h( $gestionanctionep58['Commissionep']['identifiant'] ),
					h( date_short( $gestionanctionep58['Commissionep']['dateseance'] ) ),
					h( Set::classicExtract( $options['Dossierep']['themeep'], ( $gestionanctionep58['Dossierep']['themeep'] ) ) ),
					nl2br( $decisionSanction1."\n".$libelleSanction1 ),
					nl2br( $decisionSanction2."\n".$libelleSanction2 ),
					h( $datePrevisionnelleRadiation ),
					$fieldDecisionSanction,
					$dateFinSanction,
					$commentaireFinSanction,
					$this->Xhtml->viewLink(
						'Voir le dossier',
						array( 'controller' => 'historiqueseps', 'action' => 'view_passage', $gestionanctionep58['Passagecommissionep']['id'] ),
						$this->Permissions->check( 'historiqueseps', 'view_passage' )
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
		<?php echo $this->Xform->submit( 'Validation de la liste' );?>
		<?php echo $this->Xform->end();?>

        <ul class="actionMenu">
			<li><?php
                $params = $this->request->data;
                $params = array( 'Search' => $params['Search'] );

				echo $this->Xhtml->exportLink(
					'Télécharger le tableau',
					array( 'controller' => 'gestionssanctionseps58', 'action' => 'exportcsv' ) + Hash::flatten( $params, '__' )
				);
			?></li>
		</ul>
	<?php endif;?>
<?php endif;?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		<?php if( isset( $gestionsanctionseps58 ) ):?>
			<?php foreach( $gestionsanctionseps58 as $index => $gestionanctionep58 ):?>
				<?php $modeleDecision = Inflector::classify( "decisions{$gestionanctionep58['Dossierep']['themeep']}" );?>
				observeDisableFieldsOnValue(
					'<?php echo "{$modeleDecision}{$index}";?>Arretsanction',
					[
						'<?php echo "{$modeleDecision}{$index}";?>DatearretsanctionDay',
						'<?php echo "{$modeleDecision}{$index}";?>DatearretsanctionMonth',
						'<?php echo "{$modeleDecision}{$index}";?>DatearretsanctionYear'
					],
					[ '' ],
					true
				);
			<?php endforeach;?>
		<?php endif;?>
	});
</script>