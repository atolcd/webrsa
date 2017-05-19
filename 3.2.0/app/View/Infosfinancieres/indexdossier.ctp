<?php
	$this->pageTitle = 'Paiement des allocations';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

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
?>

<?php echo $this->Form->create( 'Infosfinancieres', array( 'type' => 'post', 'url' => array( 'action' => 'indexdossier' ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );?>
	<fieldset>
		<?php echo $this->Form->input( 'Filtre.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>
		<?php echo $this->Form->input( 'Filtre.moismoucompta', array( 'label' => 'Recherche des paiements pour le mois de ', 'type' => 'date', 'dateFormat' => 'MY', 'maxYear' => $options['annees']['maxYear'], 'minYear' => $options['annees']['minYear'] ) );?>
		<?php echo $this->Form->input( 'Filtre.type_allocation', array( 'label' => 'Type d\'allocation', 'type' => 'select', 'options' => $type_allocation, 'empty' => true ) ); ?>
		<?php echo $this->Form->input( 'Filtre.nomcom', array( 'label' => 'Commune de l\'allocataire', 'type' => 'text' ) ); ?>
		<?php echo $this->Form->input( 'Filtre.numcom', array( 'label' => 'Code INSEE', 'options' => $options['numcom'], 'empty' => true ) ); ?>
	</fieldset>

	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>

<!-- Résultats -->
<?php if( isset( $infosfinancieres ) ):?>
<?php $mois = strftime('%B %Y', strtotime( $this->request->data['Filtre']['moismoucompta']['year'].'-'.$this->request->data['Filtre']['moismoucompta']['month'].'-01' ) ); ?>

	<h2 class="noprint">Liste des allocations pour le mois de <?php echo isset( $mois ) ? $mois : null ; ?></h2>

	<?php if( is_array( $infosfinancieres ) && count( $infosfinancieres ) > 0  ):?>
	<?php
		$pagination = $this->Xpaginator->paginationBlock( 'Infofinanciere', $this->passedArgs );
		echo $pagination;
	?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° Dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( __d( 'dossier', 'Dossier.matricule' ), 'Dossier.matricule' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom/prénom du bénéficiaire', 'Personne.nom' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Date de naissance du bénéficiaire', 'Personne.dtnai' );?></th>
					<th>Type d'allocation</th>
					<th>Montant de l'allocation</th>
					<th class="action noprint">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php $even = true;?>
				<?php foreach( $infosfinancieres as $index => $infofinanciere ):?>
					<?php
						// Nouvelle entrée
						if( Set::extract( $infosfinancieres, ( $index - 1 ).'.Dossier.numdemrsa' ) != Set::extract( $infofinanciere, 'Dossier.numdemrsa' ) ) {
							$rowspan = 1;
							for( $i = ( $index + 1 ) ; $i < count( $infosfinancieres ) ; $i++ ) {
								if( Set::extract( $infofinanciere, 'Dossier.numdemrsa' ) == Set::extract( $infosfinancieres, $i.'.Dossier.numdemrsa' ) )
									$rowspan++;
							}
							if( $rowspan == 1 ) {
								echo $this->Xhtml->tableCells(
									array(
										h( $infofinanciere['Dossier']['numdemrsa'] ),
										h( $infofinanciere['Dossier']['matricule'] ),
										h( $infofinanciere['Personne']['qual'].' '.$infofinanciere['Personne']['nom'].' '.$infofinanciere['Personne']['prenom'] ),
										$this->Locale->date( 'Date::short', $infofinanciere['Personne']['dtnai'] ),
										h( $type_allocation[$infofinanciere['Infofinanciere']['type_allocation']]),
										$this->Locale->money( $infofinanciere['Infofinanciere']['mtmoucompta'] ),
										array(
											$this->Xhtml->viewLink(
												'Voir les informations financières',
												array( 'controller' => 'infosfinancieres', 'action' => 'index', $infofinanciere['Infofinanciere']['dossier_id'] ),
												$this->Permissions->check( 'infosfinancieres', 'view' )
											),
											array( 'class' => 'noprint' )
										)
									),
									array( 'class' => ( $even ? 'even' : 'odd' ) ),
									array( 'class' => ( !$even ? 'even' : 'odd' ) )
								);
							}
							// Nouvelle entrée avec rowspan
							else {
								echo '<tr class="'.( $even ? 'even' : 'odd' ).'">
										<td rowspan="'.$rowspan.'">'.h( $infofinanciere['Dossier']['numdemrsa'] ).'</td>
										<td rowspan="'.$rowspan.'">'.h( $infofinanciere['Dossier']['matricule'] ).'</td>
										<td rowspan="'.$rowspan.'">'.h( $infofinanciere['Personne']['qual'].' '.$infofinanciere['Personne']['nom'].' '.$infofinanciere['Personne']['prenom'] ).'</td>
										<td rowspan="'.$rowspan.'">'.$this->Locale->date( 'Date::short', $infofinanciere['Personne']['dtnai'] ).'</td>

										<td>'.h( $type_allocation[$infofinanciere['Infofinanciere']['type_allocation']]).'</td>
										<td>'.$this->Locale->money( $infofinanciere['Infofinanciere']['mtmoucompta'] ).'</td>
										<td rowspan="'.$rowspan.'" class="noprint">'. $this->Xhtml->viewLink(
											'Voir les informations financières',
											array( 'controller' => 'infosfinancieres', 'action' => 'index', $infofinanciere['Infofinanciere']['dossier_id'] ),
											$this->Permissions->check( 'infosfinancieres', 'view' )
										).'</td>
									</tr>';
							}
						}
						// Suite avec rowspan
						else {
							echo '<tr class="'.( $even ? 'even' : 'odd' ).'">
									<td>'.h( $type_allocation[$infofinanciere['Infofinanciere']['type_allocation']]).'</td>
									<td>'.$this->Locale->money( $infofinanciere['Infofinanciere']['mtmoucompta'] ).'</td>
								</tr>';
						}
						if( Set::extract( $infosfinancieres, ( $index + 1 ).'.Dossier.numdemrsa' ) != Set::extract( $infofinanciere, 'Dossier.numdemrsa' ) ) {
							$even = !$even;
						}
					?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php if( Set::extract( $this->request->params, 'paging.Infofinanciere.count' ) > 65000 ):?>
			<p style="border: 1px solid #556; background: #ffe;padding: 0.5em;"><?php echo $this->Xhtml->image( 'icons/error.png' );?> <strong>Attention</strong>, il est possible que votre tableur ne puisse pas vous afficher les résultats au-delà de la 65&nbsp;000ème ligne.</p>
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
					array( 'controller' => 'infosfinancieres', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' )
				);
			?></li>
		</ul>
	<?php echo $pagination;?>
	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>

<?php endif?>