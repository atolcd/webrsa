<?php $this->pageTitle = 'Comité d\'examen pour l\'APRE';?>
<h1>Détails Comité d'examen</h1>
<?php if( $this->Permissions->check( 'comitesapres', 'edit' ) ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->editLink(
				'Modifier Comité',
				array( 'controller' => 'comitesapres', 'action' => 'edit', Set::classicExtract( $comiteapre, 'Comiteapre.id' ) )
			).' </li>';
		?>
	</ul>
<?php endif;?>

<div id="ficheCI">
		<table>
			<tbody>
				<tr class="even">
					<th><?php echo __( 'Date du comité');?></th>
					<td><?php echo date_short( Set::classicExtract( $comiteapre, 'Comiteapre.datecomite' ) );?></td>
				</tr>
				<tr class="odd">
					<th><?php echo __( 'Heure du comité' );?></th>
					<td><?php echo $this->Locale->date( 'Time::short', Set::classicExtract( $comiteapre, 'Comiteapre.heurecomite' ) );?></td>
				</tr>
				<tr class="even">
					<th><?php echo __( 'Lieu du comité' );?></th>
					<td><?php echo Set::classicExtract( $comiteapre, 'Comiteapre.lieucomite' );?></td>
				</tr>
				<tr class="odd">
					<th><?php echo __( 'Intitulé du comité' );?></th>
					<td><?php echo Set::classicExtract( $comiteapre, 'Comiteapre.intitulecomite' );?></td>
				</tr>
				<tr class="even">
					<th><?php echo __( 'Observations du comité' );?></th>
					<td><?php echo Set::classicExtract( $comiteapre, 'Comiteapre.observationcomite' );?></td>
				</tr>
			</tbody>
		</table>
</div>

<br />

<div id="tabbedWrapper" class="tabs">
	<?php if( isset( $comiteapre['Participantcomite'] ) ):?>
		<div id="participants">
			<h2 class="title">Liste des participants</h2>
			<?php if( is_array( $comiteapre['Participantcomite'] ) && count( $comiteapre['Participantcomite'] ) > 0  ):?>
				<ul class="actionMenu">
					<?php
						echo '<li>'.$this->Xhtml->editLink(
							'Modifier Liste des participants',
							array( 'controller' => 'comitesapres_participantscomites', 'action' => 'edit', Set::classicExtract( $comiteapre, 'Comiteapre.id' ) ),
							$this->Permissions->check( 'comitesapres_participantscomites', 'edit' )
						).' </li>';
					?>
				</ul>
				<div>
					<table class="tooltips">
						<thead>
							<tr>
								<th>Nom/Prénom</th>
								<th>Fonction</th>
								<th>Organisme</th>
								<th>N° Téléphone</th>
								<th class="action">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach( $comiteapre['Participantcomite'] as $participant ) {

									echo $this->Xhtml->tableCells(
										array(
											h( Set::classicExtract( $participant, 'qual' ).' '.Set::classicExtract( $participant, 'nom' ).' '.Set::classicExtract( $participant, 'prenom' ) ),
											h( Set::classicExtract( $participant, 'fonction' ) ),
											h( Set::classicExtract( $participant, 'organisme' ) ),
											h( Set::classicExtract( $participant, 'numtel' ) ),
											$this->Xhtml->viewLink(
												'Voir les participants',
												array( 'controller' => 'participantscomites', 'action' => 'index', Set::classicExtract( $participant, 'id' ) ),
												$this->Permissions->check( 'comitesapres', 'index' )
											)
										),
										array( 'class' => 'odd' ),
										array( 'class' => 'even' )
									);
								}
							?>
						</tbody>
					</table>
				</div>
			<?php else:?>
				<ul class="actionMenu">
					<?php
						echo '<li>'.$this->Xhtml->editLink(
							'Modifier Participant',
							array( 'controller' => 'comitesapres_participantscomites', 'action' => 'add', Set::classicExtract( $comiteapre, 'Comiteapre.id' ) ),
							$this->Permissions->check( 'comitesapres_participantscomites', 'add' )
						).' </li>';
					?>
				</ul>
				<p class="notice">Aucun participant présent.</p>
			<?php endif;?>
		</div>
	<?php endif;?>

	<?php
		/**
			$apresSansRecours = Set::extract( $comiteapre, '/Apre/ApreComiteapre[id=/[^(159|160)]/]' );
			debug( $apresSansRecours );
		*/

		$apresAvecRecours = array();
		$apresSansRecours = array();
		$apreComplementaire = false;
		foreach( $comiteapre['Apre'] as $apre ) {
			$comite_pcd_id = Set::classicExtract( $apre, 'ApreComiteapre.comite_pcd_id' );
			if( !empty( $comite_pcd_id ) ) {
				$apresAvecRecours[] = array( 'Apre' => $apre );
			}
			else {
				$apresSansRecours[] = array( 'Apre' => $apre );
			}

		}
	?>

	<?php if( isset( $apresSansRecours ) ):?>
		<div id="apres">
			<h2 class="title">Liste des APREs</h2>
			<?php if( is_array( $apresSansRecours ) && count( $apresSansRecours ) > 0 ):?>
				<ul class="actionMenu">
					<?php
						echo '<li>'.$this->Xhtml->editLink(
							'Modifier Liste APRES',
							array( 'controller' => 'apres_comitesapres', 'action' => 'edit', Set::classicExtract( $comiteapre, 'Comiteapre.id' ) ),
							$this->Permissions->check( 'apres_comitesapres', 'edit' )
						).' </li>';
					?>
				</ul>

				<div>
					<table id="searchResults" class="tooltips">
						<thead>
							<tr>
								<th>N° demande APRE</th>
								<th>NIR</th>
								<th>Nom/Prénom</th>
								<th>Date de naissance</th>
								<th>Localité</th>
								<th>Préscripteur/Préinscripteur</th>
								<th>Type d'aide</th>
								<th>Montant demandé</th>
								<th>Date demande APRE</th>
								<th>Quota</th>
								<th class="action" colspan="2">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach( $apresSansRecours as $apre ) {
									$apre = $apre['Apre'];
									foreach( $listeAidesApre as $key => $aideApre ) {
										if ( !empty( $apre[$aideApre] ) ) {
											$apre['typeaideapre'] = $aideApre;
											$apre['montantdemande'] = $apre[$aideApre]['montantaide'];
										}
									}
									$isRecours = Set::classicExtract( $apre, 'ApreComiteapre.comite_pcd_id' );
									$isRecours = !empty( $isRecours );
									if( !$isRecours ) {
										echo $this->Xhtml->tableCells(
											array(
												h( Set::classicExtract( $apre, 'numeroapre' ) ),
												h( Set::classicExtract( $apre, 'Personne.nir' ) ),
												h( Set::classicExtract( $apre, 'Personne.qual' ).' '.Set::classicExtract( $apre, 'Personne.nom' ).' '.Set::classicExtract( $apre, 'Personne.prenom' ) ),
												h( date_short( Set::classicExtract( $apre, 'Personne.dtnai' ) ) ),
												h( Set::classicExtract( $apre, 'Personne.Foyer.Adressefoyer.0.Adresse.nomcom' ) ),
												h( Set::enum( Set::classicExtract( $apre, 'referent_id' ), $referent) ),
												h( __d( 'apre', Set::classicExtract( $apre, 'typeaideapre' ) ) ),
												h( Set::classicExtract( $apre, 'montantdemande' ) ),
												h( date_short( Set::classicExtract( $apre, 'datedemandeapre' ) ) ),
												h( Set::classicExtract( $apre, 'quota' ) ),
												$this->Xhtml->viewLink(
													'Voir les apres',
													array( 'controller' => 'apres', 'action' => 'index', Set::classicExtract( $apre, 'personne_id' ) ),
													$this->Permissions->check( 'apres', 'index' )
												),
												$this->Xhtml->printLink(
													'Imprimer l\'apre',
													array( 'controller' => 'apres', 'action' => 'impression', Set::classicExtract( $apre, 'id' ) ),
													$this->Permissions->check( 'apres', 'impression' )
												)
											),
											array( 'class' => 'odd' ),
											array( 'class' => 'even' )
										);
									}
								}
							?>
						</tbody>
					</table>
				</div>
			<?php else:?>
				<ul class="actionMenu">
					<?php
						echo '<li>'.$this->Xhtml->editLink(
							'Modifier Liste APRE',
							array( 'controller' => 'apres_comitesapres', 'action' => 'add', Set::classicExtract( $comiteapre, 'Comiteapre.id' ) ),
							$this->Permissions->check( 'apres_comitesapres', 'add' )
						).' </li>';
					?>
				</ul>
				<p class="notice">Aucune demande d'APRE  présente.</p>
			<?php endif;?>
		</div>
	<?php endif;?>

	<?php if( isset( $apresAvecRecours ) ):?>
		<div id="apresrecours">
			<h2 class="title">Liste des APREs en Recours</h2>
			<?php if( is_array( $apresAvecRecours ) && count( $apresAvecRecours ) > 0 ):?>
				<ul class="actionMenu">
					<?php
						echo '<li>'.$this->Xhtml->editLink(
							'Modifier Liste APRES en Recours',
							array( 'controller' => 'apres_comitesapres', 'action' => 'edit', Set::classicExtract( $comiteapre, 'Comiteapre.id' ), 'recours' => 1 ),
							$this->Permissions->check( 'apres_comitesapres', 'edit' )
						).' </li>';
					?>
				</ul>
				<div>
					<table  class="tooltips">
						<thead>
							<tr>
								<th>N° demande APRE</th>
								<th>NIR</th>
								<th>Nom/Prénom</th>
								<th>Localité</th>
								<th>Préscripteur/Préinscripteur</th>
								<th>Date demande APRE</th>
								<th>Recours</th>
								<th class="action">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach( $apresAvecRecours as $apre ) {
									$apre = $apre['Apre'];
									$isRecours = Set::classicExtract( $apre, 'ApreComiteapre.comite_pcd_id' );
									$isRecours = !empty( $isRecours );

									if( $isRecours ) {
										echo $this->Xhtml->tableCells(
											array(
												h( Set::classicExtract( $apre, 'numeroapre' ) ),
												h( Set::classicExtract( $apre, 'Personne.nir' ) ),
												h( Set::classicExtract( $apre, 'Personne.qual' ).' '.Set::classicExtract( $apre, 'Personne.nom' ).' '.Set::classicExtract( $apre, 'Personne.prenom' ) ),
												h( Set::classicExtract( $apre, 'Personne.Foyer.Adressefoyer.0.Adresse.nomcom' ) ),
												h( Set::enum( Set::classicExtract( $apre, 'referent_id' ), $referent ) ),
												h( date_short( Set::classicExtract( $apre, 'datedemandeapre' ) ) ),
												h( Set::enum( Set::classicExtract( $apre, 'ApreComiteapre.recoursapre' ), $options['recoursapre'] ) ),
												$this->Xhtml->viewLink(
													'Voir les apres',
													array( 'controller' => 'apres', 'action' => 'index', Set::classicExtract( $apre, 'personne_id' ) ),
													$this->Permissions->check( 'comitesapres', 'index' )
												)
											),
											array( 'class' => 'odd' ),
											array( 'class' => 'even' )
										);
									}
								}
							?>
						</tbody>
					</table>
				</div>
			<?php else:?>
				<ul class="actionMenu">
					<?php
						echo '<li>'.$this->Xhtml->editLink(
							'Modifier Liste APRES en Recours',
							array( 'controller' => 'apres_comitesapres', 'action' => 'add', Set::classicExtract( $comiteapre, 'Comiteapre.id' ), 'recours' => 1 ),
							$this->Permissions->check( 'apres_comitesapres', 'add' )
						).' </li>';
					?>
				</ul>
				<p class="notice">Aucune demande d'APRE en Recours présente.</p>
			<?php endif;?>
		</div>
	<?php endif;?>
</div>
	<?php
		$participants = Set::classicExtract( $comiteapre, 'Participantcomite' );
		$apres = Set::classicExtract( $comiteapre, 'Apre' );
	?>
<?php if( !empty( $participants ) && !empty( $apres ) ):?>
	<fieldset class="noborder center invisible">
		<ul class="actionMenu">
			<?php
				echo $this->Xhtml->decisionLink(
					'Prise de décision du comité',
					array( 'controller' => 'cohortescomitesapres', 'action' => 'aviscomite', 'Cohortecomiteapre__id' => Set::classicExtract( $comiteapre, 'Comiteapre.id' ) ),
					$this->Permissions->check( 'cohortescomitesapres', 'aviscomite' )
				);
			?>
		</ul>
	</fieldset>
<?php endif;?>
<div class="clearer"><hr /></div>

<!-- *********************************************************************** -->

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>