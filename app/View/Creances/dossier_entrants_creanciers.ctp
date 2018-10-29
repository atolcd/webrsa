<?php
	$this->pageTitle = 'Recherche des Allocataires Entrants ayant une créance';

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

<?php echo $this->Form->create( 'Creances', array( 'type' => 'post', 'url' => array( 'action' => 'dossierEntrantsCreanciers' ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );?>
	<fieldset>
		<fieldset><legend>Beneficiaire</legend>
		<?php echo $this->Form->input( 'Filtre.recherche',
			array(
				'label' => false,
				'type' => 'hidden',
				'value' => true
				)
			);
		echo $this->Form->input( 'Filtre.moisentrants',
			array( 'label' => 'Recherche des Entrants pour le mois de ',
				'type' => 'date',
				'dateFormat' => 'MY',
				'maxYear' => $options['annees']['maxYear'],
				'minYear' => $options['annees']['minYear']
			)
		);
		echo $this->Form->input( 'Filtre.dossier_dernier',
			array(
				'label' => 'Uniquement la dernière demande RSA pour un même allocataire',
				'type' => 'checkbox',
				)
			);
		echo "<fieldset><legend>Etat de Dossier</legend>" . $this->Form->input( 'Filtre.etat_dossier',
			array( 'label' => FALSE,
				'multiple' => 'checkbox',
				'options' => $etatdosrsa,
				)
			) . "</fieldset>";
		echo $this->Form->input( 'Filtre.droit_devoirs',
			array( 'label' => 'Soumis à Droit et Devoirs',
				'type' => 'select',
				'options' => $droitdevoirs,
				'empty' => true
			)
		);
	echo "</fieldset><fieldset><legend>Créance</legend>";
		echo $this->Form->input( 'Filtre.orig_creance',
			array( 'label' => 'Origine de la Créance',
				'type' => 'select',
				'options' => $orgcre,
				'empty' => true
			)
		);
		echo $this->Form->input( 'Filtre.creance_positive',
			array(
				'label' => 'Uniquement les créances dont la valeur est positive',
				'type' => 'checkbox',
				)
			);
	echo "</fieldset><fieldset><legend>Titres Créanciers</legend>";
		echo $this->Form->input( 'Filtre.has_titre_creancier',
			array(
				'label' => 'Uniquement les créances avec un titre créancier',
				'type' => 'checkbox',
				)
			);
		?>
	</fieldset>
	<div class="submit noprint">
		<?php echo $this->Form->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Form->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>
<?php echo $this->Form->end();?>
</fieldset>

<!-- Résultats -->
<?php if( isset( $dossierEntrantsCreanciers ) ):?>
<?php $mois = strftime('%B %Y', strtotime( $this->request->data['Filtre']['moisentrants']['year'].'-'.$this->request->data['Filtre']['moisentrants']['month'].'-01' ) ); ?>

	<h2 class="noprint">Liste des allocations pour le mois de <?php echo isset( $mois ) ? $mois : null ; ?></h2>

	<?php if( is_array( $dossierEntrantsCreanciers ) && count( $dossierEntrantsCreanciers ) > 0  ):?>
	<?php
		$pagination = $this->Xpaginator->paginationBlock( 'Creance', $this->passedArgs );
		echo $pagination;
	?>
		<table id="searchResults" class="tooltips">
			<thead>
				<tr>
					<th><?php echo $this->Xpaginator->sort( 'N° Dossier', 'Dossier.numdemrsa' );?></th>
					<th><?php echo $this->Xpaginator->sort( __d( 'dossier', 'Dossier.matricule' ), 'Dossier.matricule' );?></th>
					<th><?php echo $this->Xpaginator->sort( 'Nom/prénom du bénéficiaire', 'Personne.nom' );?></th>
					<th><?php echo __d( 'creance', 'Creance.dtimplcre' ); ?></th>
					<th><?php echo __d( 'creance', 'Creance.natcre' ); ?></th>
					<th><?php echo __d( 'creance', 'Creance.rgcre' ); ?></th>
					<th><?php echo __d( 'creance', 'Creance.motiindu' ); ?></th>
					<th><?php echo __d( 'creance', 'Creance.oriindu' ); ?></th>
					<th><?php echo __d( 'creance', 'Creance.respindu' ); ?></th>
					<th><?php echo __d( 'creance', 'Creance.mtsolreelcretrans' ); ?></th>
					<th><?php echo __d( 'creance', 'Creance.mtinicre' ); ?></th>
					<th class="action noprint">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php $even = true;
				 foreach( $dossierEntrantsCreanciers as $index => $dossierEntrantCreancier ):?>
					<?php
						// Nouvelle entrée
								echo $this->Xhtml->tableCells(
									array(
										h( $dossierEntrantCreancier['Dossier']['numdemrsa'] ),
										h( $dossierEntrantCreancier['Dossier']['matricule'] ),
										h( $dossierEntrantCreancier['Personne']['qual'].' '.$dossierEntrantCreancier['Personne']['nom'].' '.$dossierEntrantCreancier['Personne']['prenom'] ),
										$this->Locale->date( 'Date::short', $dossierEntrantCreancier['Creance']['dtimplcre'] ),
										$natcre[$dossierEntrantCreancier['Creance']['natcre']],
										$dossierEntrantCreancier['Creance']['rgcre'],
										$motiindu[$dossierEntrantCreancier['Creance']['motiindu']],
										$oriindu[$dossierEntrantCreancier['Creance']['oriindu']],
										$respindu[$dossierEntrantCreancier['Creance']['respindu']],
										$this->Locale->money( $dossierEntrantCreancier['Creance']['mtsolreelcretrans'] ),
										$this->Locale->money( $dossierEntrantCreancier['Creance']['mtinicre'] ),
										array(
											$this->Xhtml->viewLink(
												'Voir les informations créancières',
												array( 'controller' => 'creances', 'action' => 'index', $dossierEntrantCreancier['Creance']['foyer_id'] ),
												$this->Permissions->check( 'creances', 'index' )
											),
											array( 'class' => 'noprint' )
										)
									),
									array( 'class' => ( $even ? 'even' : 'odd' ) )
								);
						if( Set::extract( $dossierEntrantsCreanciers, ( $index + 1 ).'.Dossier.numdemrsa' ) != Set::extract( $dossierEntrantCreancier, 'Dossier.numdemrsa' ) ) {
							$even = !$even;
						}else{
							$even = $even;
						}
					?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php if( Set::extract( $this->request->params, 'paging.EntrantsCreanciers.count' ) > 65000 ):?>
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
					array( 'controller' => 'Creances', 'action' => 'exportcsv' ) + Hash::flatten( $this->request->data, '__' )
				);
			?></li>
		</ul>
	<?php echo $pagination;?>
	<?php else:?>
		<p>Vos critères n'ont retourné aucun dossier.</p>
	<?php endif?>

<?php endif?>