<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
        $this->pageTitle = __d( 'indicateursuivi', "Indicateurssuivis::{$this->action}" )
    );
?>

    <?php
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

	$this->Xpaginator->options( $this->passedArgs );
	$pagination = $this->Xpaginator->paginationBlock( 'Dossier', $this->passedArgs );
?>

<?php echo $this->Form->create( 'Indicateursuivi', array( 'type' => 'post', 'action' => '/index/', 'id' => 'Search', 'class' => ( ( !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ) ) );?>
	<?php
		echo $this->Search->etatdosrsa($etatdosrsa);
		echo $this->Search->natpf($natpf);
	?>
	<fieldset>
		<legend>Recherche par Adresse</legend>
		<?php echo $this->Form->input( 'Adresse.nomcom', array( 'label' => 'Commune de l\'allocataire ', 'type' => 'text' ) );?>
		<?php echo $this->Form->input( 'Adresse.numcom', array( 'label' => 'Numéro de commune au sens INSEE', 'type' => 'select', 'options' => $mesCodesInsee, 'empty' => true ) );?>
		<?php //echo $this->Form->input( 'Adresse.codepos', array( 'label' => 'Code postal ', 'type' => 'text' ) );?>
		<?php
			if( Configure::read( 'CG.cantons' ) ) {
				echo $this->Form->input( 'Canton.canton', array( 'label' => 'Canton', 'type' => 'select', 'options' => $cantons, 'empty' => true ) );
			}

		?>
	</fieldset>
		<?php
			$valueDossierDernier = isset( $this->request->data['Dossier']['dernier'] ) ? $this->request->data['Dossier']['dernier'] : true;
			echo $this->Form->input( 'Dossier.dernier', array( 'label' => 'Uniquement la dernière demande RSA pour un même allocataire', 'type' => 'checkbox', 'checked' => $valueDossierDernier ) );
		?>
		<fieldset>
			<legend>Recherche par orientation</legend>
			<?php
				echo $this->Form->input( 'Orientstruct.structurereferente_id', array( 'label' => __d( 'structurereferente', 'Structurereferente.lib_struc' ), 'type' => 'select', 'options' => $structs, 'empty' => true) );
				echo $this->Form->input( 'Orientstruct.referent_id', array(  'label' => __d( 'structurereferente', 'Structurereferente.nom_referent' ), 'type' => 'select', 'options' => $referents, 'empty' => true ) );
			?>
		</fieldset>
		<fieldset>
			<legend>Recherche par chargé d'évaluation</legend>
			<?php
				echo $this->Form->input( 'Propoorientationcov58.structureorientante_id', array( 'label' => __d( 'structurereferente', 'Structurereferente.lib_struc' ), 'type' => 'select', 'options' => $structs, 'empty' => true) );
				echo $this->Form->input( 'Propoorientationcov58.referentorientant_id', array(  'label' => __d( 'structurereferente', 'Structurereferente.nom_referent' ), 'type' => 'select', 'options' => $referents, 'empty' => true ) );
			?>
		</fieldset>

 <?php
//  echo $this->Form->input( 'Indicateursuivi.annee', array( 'label' => 'Recherche pour l\'année', 'type' => 'select', 'empty' => true, 'options' => array_range( date( 'Y' )-4, date( 'Y' ) +1 ) ) );
?>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Filtrer', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>



<?php if( isset( $indicateurs ) ):?>
	<h2 class="noprint">Résultats de la recherche</h2>
	<?php echo $pagination;?>
	<?php if( is_array( $indicateurs ) && count( $indicateurs ) > 0 ):?>
		<table id="searchResults" class="tooltips">
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
					<th><?php echo $this->Xpaginator->sort( 'Nom/Prénom', 'Personne.nom' );?></th>
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
				<?php foreach( $indicateurs as $index => $indicateur ):?>
					<?php
						$adresse = Set::classicExtract( $indicateur, 'Adresse.numvoie' ).' '.Set::classicExtract( $indicateur, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $indicateur, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $indicateur, 'Adresse.compladr' ).'<br /> '.Set::classicExtract( $indicateur, 'Adresse.codepos' ).' '.Set::classicExtract( $indicateur, 'Adresse.nomcom' );

						echo $this->Xhtml->tableCells(
							array(
								h( $indicateur['Dossier']['matricule'] ),
								h( $indicateur['Personne']['nom_complet'] ),
								h( date_short( $indicateur['Personne']['dtnai'] ) ),
								$adresse,
								h( $indicateur['Personne']['qualcjt'].' '.$indicateur['Personne']['nomcjt'].' '.$indicateur['Personne']['prenomcjt']),
								h( date_short( $indicateur['Dossier']['dtdemrsa'] ) ),
								h( $indicateur['Referentorientant']['nom_complet'] ),
								h( date_short( $indicateur['Orientstruct']['date_valid'])),
								h( $indicateur['Orientstruct']['rgorient']),
								h( $indicateur['Referentunique']['nom_complet'] ),
								h( date_short( $indicateur['Contratinsertion']['dd_ci'] ) ),
								h( date_short( $indicateur['Contratinsertion']['df_ci'] ) ),
								h( $indicateur['Contratinsertion']['rg_ci']),
								h( Set::enum( $indicateur['Historiqueetatpe']['etat'], $etatpe['etat'] ).' '.date_short( $indicateur['Historiqueetatpe']['date'] ) ),
								h( date_short( $indicateur['Commissionep']['dateseance'] ) ),
								h( !empty( $indicateur['Dossierep']['themeep'] ) ? Set::classicExtract( $options['themeep'], $indicateur['Dossierep']['themeep'] ) : null ),
								$this->Xhtml->link(
									'Voir',
									array(
										'controller' => 'dossiers',
										'action' => 'view',
										$indicateur['Dossier']['id']
									),
									array(
										'class' => 'external'
									)
								)
							),
							array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
							array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
						);
					?>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php /*debug($indicateurs);*/?>
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
					array( 'controller' => 'indicateurssuivis', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' ),
					$this->Permissions->check( 'indicateurssuivis', 'exportcsv' )
				);
			?></li>
		</ul>
	<?php else:?>
		<p>Vos critères n'ont retourné aucun résultat.</p>
	<?php endif?>
<?php endif?>