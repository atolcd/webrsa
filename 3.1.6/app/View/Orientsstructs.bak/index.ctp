<?php  $this->pageTitle = 'Orientation de la personne';?>

<h1>Orientation</h1>

<?php
	echo $this->element( 'ancien_dossier' );

	if ( empty( $orientstructs ) ) {
		echo '<p class="notice">Cette personne ne possède pas encore d\'orientation.</p>';
	}

	if( isset( $nbdossiersnonfinalisescovs ) && !empty( $nbdossiersnonfinalisescovs ) ) {
		echo '<p class="notice">Ce dossier va passer en COV.</p>';
	}
	elseif( !$ajout_possible ) {
		echo '<p class="notice">Impossible d\'ajouter une nouvelle orientation à ce dossier (passage en EP ou dossier ne pouvant être orienté).</p>';
	}
	elseif( !empty( $en_procedure_relance ) ) {
		echo '<p class="notice">Cette personne est en cours de procédure de relance.</p>';
	}
?>

<!-- Pour le CG 93, les orientations de rang >= 1 doivent passer en EP, donc il faut utiliser Reorientationseps93Controller::add -->
<?php if( Configure::read( 'Cg.departement' ) == 93 && $rgorient_max >= 1 ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.
				$this->Default2->button(
					'add',
					array( 'controller' => 'reorientationseps93', 'action' => 'add', $last_orientstruct_id ),
					array(
						'label' => 'Demander une réorientation',
						'enabled' => ( $ajout_possible && $this->Permissions->checkDossier( 'reorientationseps93', 'add', $dossierMenu ) )
					)
				).
			' </li>';
		?>
	</ul>
<?php elseif( Configure::read( 'Cg.departement' ) == 58 ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.
				$this->Xhtml->addLink(
					'Préconiser une orientation',
					array( 'controller' => 'proposorientationscovs58', 'action' => 'add', $personne_id ),
					$ajout_possible && $this->Permissions->checkDossier( 'proposorientationscovs58', 'add', $dossierMenu )
				).
			' </li>';
		?>
	</ul>
<?php else:?>
	<ul class="actionMenu">
		<?php
			if( Configure::read( 'Cg.departement' ) != 93 ){
				echo '<li>'.
					$this->Xhtml->addLink(
						'Préconiser une orientation',
						array( 'controller' => 'orientsstructs', 'action' => 'add', $personne_id ),
						!$force_edit && $ajout_possible && $this->Permissions->checkDossier( 'orientsstructs', 'add', $dossierMenu )
					).
				' </li>';
			}
			else {
				echo '<li>'.
					$this->Default2->button(
						'add',
						array( 'controller' => 'orientsstructs', 'action' => 'add', $personne_id ),
						array(
							'label' => 'Demander une réorientation',
							'enabled' => ( !$force_edit && $ajout_possible && $this->Permissions->checkDossier( 'orientsstructs', 'add', $dossierMenu ) )
						)
					).
				' </li>';
			}
		?>
	</ul>
<?php endif;?>

<?php if( Configure::read( 'Cg.departement' ) == 93 && isset( $reorientationep93 ) && !empty( $reorientationep93 ) ):?>
	<?php
		$etatdossierep = Set::enum( $reorientationep93['Passagecommissionep']['etatdossierep'], $optionsdossierseps['Passagecommissionep']['etatdossierep'] );
		if( empty( $etatdossierep ) ) {
			$etatdossierep = 'En attente';
		}
	?>
	<h2>Demande de Réorientation</h2>
	<table>
		<thead>
			<tr>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Date de la demande</th>
				<th>Type d'orientation</th>
				<th>Type de structure</th>
				<th>Rang d'orientation</th>
				<th>État du dossier d'EP</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo h( $reorientationep93['Personne']['nom'] );?></td>
				<td><?php echo h( $reorientationep93['Personne']['prenom'] );?></td>
				<td><?php echo $this->Locale->date( __( 'Date::short' ), $reorientationep93['Reorientationep93']['datedemande'] );?></td>
				<td><?php echo h( $reorientationep93['Typeorient']['lib_type_orient'] );?></td>
				<td><?php echo h( $reorientationep93['Structurereferente']['lib_struc'] );?></td>
				<td class="number"><?php echo h( $reorientationep93['Orientstruct']['rgorient'] + 1 );?></td>
				<td><?php echo h( $etatdossierep );?></td>
				<td><?php echo $this->Default->button( 'edit', array( 'controller' => 'reorientationseps93', 'action' => 'edit', $reorientationep93['Reorientationep93']['id'] ), array( 'enabled' => ( empty( $reorientationep93['Passagecommissionep']['etatdossierep'] ) ) ) );?></td>
				<td><?php echo $this->Default->button( 'delete', array( 'controller' => 'reorientationseps93', 'action' => 'delete', $reorientationep93['Reorientationep93']['id'] ), array( 'enabled' => ( empty( $reorientationep93['Passagecommissionep']['etatdossierep'] ) ), 'confirm' => 'Êtes-vous sûr de vouloir supprimer la demande de réorientation ?' ) );?></td>
			</tr>
		</tbody>
	</table>
<?php endif;?>

<?php if( Configure::read( 'Cg.departement' ) == 58 && isset( $propoorientationcov58 ) && !empty( $propoorientationcov58 ) ):?>
	<h2>Nouvelle orientation en cours de validation par la commission d'orientation et de validation</h2>
	<table class="aere">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Date de la demande</th>
				<th>Type d'orientation</th>
				<th>Type de structure</th>
				<th>Rang d'orientation</th>
				<th>État du dossier en COV</th>
				<th colspan="2" class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo h( $propoorientationcov58['Personne']['nom'] );?></td>
				<td><?php echo h( $propoorientationcov58['Personne']['prenom'] );?></td>
				<td><?php echo $this->Locale->date( __( 'Date::short' ), $propoorientationcov58['Propoorientationcov58']['datedemande'] );?></td>
				<td><?php echo h( $propoorientationcov58['Typeorient']['lib_type_orient'] );?></td>
				<td><?php echo h( $propoorientationcov58['Structurereferente']['lib_struc'] );?></td>
				<td class="number"><?php echo h( $propoorientationcov58['Propoorientationcov58']['rgorient'] );?></td>
				<td><?php echo h( Set::enum( $propoorientationcov58['Passagecov58']['etatdossiercov'], $optionsdossierscovs58['Passagecov58']['etatdossiercov'] ) );?></td>
				<td><?php echo $this->Default->button( 'edit', array( 'controller' => 'proposorientationscovs58', 'action' => 'edit', $propoorientationcov58['Personne']['id'] ), array( 'enabled' => ( $propoorientationcov58['Passagecov58']['etatdossiercov'] != 'associe' ) ) );?></td>
				<td><?php echo $this->Default->button( 'delete', array( 'controller' => 'proposorientationscovs58', 'action' => 'delete', $propoorientationcov58['Propoorientationcov58']['id'] ), array( 'enabled' => empty( $propoorientationcov58['Passagecov58']['etatdossiercov'] ) ), 'Confirmer ?' );?></td>
			</tr>
		</tbody>
	</table>
<?php endif;?>

<?php if( Configure::read( 'Cg.departement' ) == 58 && isset( $regressionorientaionep58 ) && !empty( $regressionorientaionep58 ) ):?>
	<h2>Réorientation du professionel de l'Emploi vers le Social en étude par l'équipe pluridisciplinaire</h2>
	<table class="aere">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Date de la demande</th>
				<th>Type d'orientation</th>
				<th>Type de structure</th>
				<th>État du dossier d'EP</th>
				<th class="action">Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo h( $regressionorientaionep58['Personne']['nom'] );?></td>
				<td><?php echo h( $regressionorientaionep58['Personne']['prenom'] );?></td>
				<td><?php echo $this->Locale->date( __( 'Date::short' ), $regressionorientaionep58['Regressionorientationep58']['datedemande'] );?></td>
				<td><?php echo h( $regressionorientaionep58['Typeorient']['lib_type_orient'] );?></td>
				<td><?php echo h( $regressionorientaionep58['Structurereferente']['lib_struc'] );?></td>
				<td><?php echo h( Set::enum( $regressionorientaionep58['Passagecommissionep']['etatdossierep'], $optionsdossierseps['Passagecommissionep']['etatdossierep'] ) );?></td>
				<td><?php echo $this->Default->button( 'delete', array( 'controller' => 'regressionsorientationseps', 'action' => 'delete', $regressionorientaionep58['Regressionorientationep58']['id'] ), array( 'enabled' => empty( $regressionorientaionep58['Passagecommissionep']['etatdossierep'] ) ), 'Confirmer ?' );?></td>
			</tr>
		</tbody>
	</table>
<?php endif;?>

<?php if( Configure::read( 'Cg.departement' ) == 58 && isset( $propoorientsocialecov58 ) && !empty( $propoorientsocialecov58 ) ):?>
	<h2>Orientation sociale de fait en étude par la COV</h2>
	<table class="aere">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Date de la demande</th>
				<th>État du dossier de COV</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo h( $propoorientsocialecov58['Personne']['nom'] );?></td>
				<td><?php echo h( $propoorientsocialecov58['Personne']['prenom'] );?></td>
				<td><?php echo $this->Locale->date( __( 'Date::short' ), $propoorientsocialecov58['Propoorientsocialecov58']['created'] );?></td>
				<td><?php echo h( Set::enum( $propoorientsocialecov58['Passagecov58']['etatdossiercov'], $optionsdossierseps['Passagecommissionep']['etatdossierep'] ) );?></td>
			</tr>
		</tbody>
	</table>
<?php endif;?>

<?php if( Configure::read( 'Cg.departement' ) == 58 && isset( $propononorientationprocov58 ) && !empty( $propononorientationprocov58 ) ):?>
	<h2>Proposition de maintien dans le social en étude par la COV</h2>
	<table class="aere">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Prénom</th>
				<th>Date de la demande</th>
				<th>État du dossier de COV</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo h( $propononorientationprocov58['Personne']['nom'] );?></td>
				<td><?php echo h( $propononorientationprocov58['Personne']['prenom'] );?></td>
				<td><?php echo $this->Locale->date( __( 'Date::short' ), $propononorientationprocov58['Dossiercov58']['created'] );?></td>
				<td><?php echo h( Set::enum( $propononorientationprocov58['Passagecov58']['etatdossiercov'], $optionsdossierseps['Passagecommissionep']['etatdossierep'] ) );?></td>
			</tr>
		</tbody>
	</table>
<?php endif;?>

<?php if( !empty( $orientstructs ) ):?>
	<h2>Orientations effectives</h2>
	<table class="tooltips default2">
		<thead>
			<tr>
				<th>Nom</th>
				<th>Prénom</th>
				<?php if( Configure::read( 'Cg.departement' ) == 93 ):?>
					<?php if( $this->Session->read( 'Auth.User.type' ) === 'cg' ):?>
					<th>Date de préOrientation</th>
					<?php endif;?>
					<th>Date d'orientation</th>
					<?php if( $this->Session->read( 'Auth.User.type' ) === 'cg' ):?>
					<th>PréOrientation</th>
					<?php endif;?>
					<th><?php echo __d( 'orientstruct', 'Orientstruct.origine' );?></th>
					<th>Orientation</th>
				<?php else:?>
					<th>Date de la demande</th>
					<th>Date d'orientation</th>
					<th>Type d'orientation</th>
				<?php endif;?>
				<th>Structure référente</th>
				<th>Rang d'orientation</th>
				<?php if( Configure::read( 'nom_form_ci_cg' ) == 'cg58' ):?>
					<th>COV ayant traitée le dossier</th>
					<th>Observations de la COV</th>
				<?php endif;?>
				<?php if( Configure::read( 'nom_form_ci_cg' ) == 'cg66' ):?>
					<th colspan="6" class="action">Actions</th>
				<?php else:?>
					<th colspan="5" class="action">Actions</th>
				<?php endif;?>
			</tr>
		</thead>
		<tbody>
			<?php
				foreach( $orientstructs as $i => $orientstruct ) {
					$isOrient = false;
					if( isset( $orientstruct['Orientstruct']['date_propo'] ) ){
						$isOrient = true;
					}

					$rgorient = null;
					if( !empty( $orientstruct['Orientstruct']['rgorient'] ) ) {
						if( Configure::read( 'Cg.departement' ) == 58 ) {
							if( !isset( $orientstructs[$i+1] ) ) {
								$rgorient = 'Première orientation';
							}
							else if( $orientstruct['Orientstruct']['typeorient_id'] != $orientstructs[$i+1]['Orientstruct']['typeorient_id'] ) {
								$rgorient = 'Réorientation';
							}
							else if( $orientstruct['Orientstruct']['typeorient_id'] == Configure::read( 'Typeorient.emploi_id' ) ) {
								$rgorient = 'Maintien en emploi';
							}
							else {
								$rgorient = 'Maintien en social';
							}
						}
						else {
							$rgorient = ( $orientstruct['Orientstruct']['rgorient'] > 1 ) ? 'Réorientation' : 'Première orientation';
						}
					}

					//Peut-n nimprimer la notif de changement de référent ou non, si 1ère orientation non sinon ok
					$NotifBenefPrintable = true;
					if( $orientstruct['Orientstruct']['rgorient'] > 1 ) {
						$NotifBenefPrintable = true;
					}
					else {
						$NotifBenefPrintable = false;
					}


					// Délai de modification orientation (10 jours par défaut)
					$dateCreation = Set::classicExtract( $orientstruct, 'Orientstruct.date_valid' );
					$periodeblock = false;
					if( !empty( $dateCreation ) ){
						if(  ( time() >= ( strtotime( $dateCreation ) + 3600 * Configure::read( 'Periode.modifiableorientation.nbheure' ) ) ) ){
							$periodeblock = true;
						}
					}

					$cells = array(
						h( $orientstruct['Personne']['nom']),
						h( $orientstruct['Personne']['prenom'] ),
					);

					if( Configure::read( 'Cg.departement' ) != 93 || $this->Session->read( 'Auth.User.type' ) === 'cg' ) {
						$cells[] = h( date_short( $orientstruct['Orientstruct']['date_propo'] ) );
					}

					$cells[] = h( date_short( $orientstruct['Orientstruct']['date_valid'] ) );

					if( Configure::read( 'Cg.departement' ) == 93 ) {
						if( $this->Session->read( 'Auth.User.type' ) === 'cg' ) {
							$cells[] = h( Set::enum( $orientstruct['Orientstruct']['propo_algo'], $typesorients ) );
						}
						$cells[] = h( Set::enum( $orientstruct['Orientstruct']['origine'], $options['Orientstruct']['origine'] ) );
					}

					array_push(
						$cells,
						h( Set::classicExtract( $orientstruct, 'Typeorient.lib_type_orient' ) ),
						h( $orientstruct['Structurereferente']['lib_struc']  ),
						h( $rgorient )
					);

					if( Configure::read( 'Cg.departement' ) == 58 ) {
						$infoscov = '';
						if( !empty( $orientstruct['Cov58']['datecommission'] ) ){
							$infoscov = 'Site "'.Set::classicExtract( $orientstruct, 'Sitecov58.name' ).'", le '.$this->Locale->date( "Datetime::full", Set::classicExtract( $orientstruct, 'Cov58.datecommission' ) );
						}
						$cells[] = h( $infoscov );
						$cells[] = h( Set::classicExtract( $orientstruct, 'Cov58.observation' ) );
					}


					if( Configure::read( 'Cg.departement' ) == 66 ) {
						array_push(
							$cells,
							$this->Default2->button(
								'edit',
								array( 'controller' => 'orientsstructs', 'action' => 'edit', $orientstruct['Orientstruct']['id'] ),
								array(
									'enabled' => (
										$this->Permissions->checkDossier( 'orientsstructs', 'edit', $dossierMenu ) && ( $orientstruct['Orientstruct']['rgorient'] == $rgorient_max )
										&& !( Configure::read( 'Cg.departement' ) == 93 && isset( $reorientationep93 ) && !empty( $reorientationep93 ) )
										&& $ajout_possible
										&& !$periodeblock
									)
								)
							),
							$this->Default2->button(
								'print',
								array( 'controller' => 'orientsstructs', 'action' => 'impression', $orientstruct['Orientstruct']['id'] ),
								array(
									'enabled' => (
										( $this->Permissions->checkDossier( 'orientsstructs', 'impression', $dossierMenu ) == 1 )
									)
								)
							),
							$this->Default2->button(
								'notifbenef',
								array( 'controller' => 'orientsstructs', 'action' => 'printChangementReferent',
								$orientstruct['Orientstruct']['id'] ),
								array(
									'enabled' => (
										( $this->Permissions->checkDossier( 'orientsstructs', 'printChangementReferent', $dossierMenu ) == 1 )
										&& $orientstruct['Orientstruct']['notifbenefcliquable']
										&& $NotifBenefPrintable
									)
								)
							),
							$this->Default2->button(
								'delete',
								array( 'controller' => 'orientsstructs', 'action' => 'delete', $orientstruct['Orientstruct']['id'] ),
								array(
									'enabled' => (
										$this->Permissions->checkDossier( 'orientsstructs', 'delete', $dossierMenu )
										&& ( $orientstruct['Orientstruct']['rgorient'] == $rgorient_max )
										&& $last_orientstruct_suppressible
									)
								),
                                'Confirmer la suppression ?'
//								true
							),
							$this->Default2->button(
								'filelink',
								array( 'controller' => 'orientsstructs', 'action' => 'filelink', $orientstruct['Orientstruct']['id'] ),
								array(
									'enabled' => (
										$this->Permissions->checkDossier( 'orientsstructs', 'filelink', $dossierMenu )
									)
								)
							),
							h( "({$orientstruct['Fichiermodule']['nombre']})" )
						);
					}
					else{
						array_push(
							$cells,
							$this->Xhtml->editLink(
								'Editer l\'orientation',
								array( 'controller' => 'orientsstructs', 'action' => 'edit', $orientstruct['Orientstruct']['id'] ),
								$this->Permissions->checkDossier( 'orientsstructs', 'edit', $dossierMenu ) && ( $orientstruct['Orientstruct']['rgorient'] == $rgorient_max )
								&& !( Configure::read( 'Cg.departement' ) == 93 && isset( $reorientationep93 ) && !empty( $reorientationep93 ) )
								&& $ajout_possible
							),
							$this->Xhtml->printLink(
								'Imprimer la notification',
								array( 'controller' => 'orientsstructs', 'action' => 'impression', $orientstruct['Orientstruct']['id'] ),
								$this->Permissions->checkDossier( 'orientsstructs', 'impression', $dossierMenu ) && $orientstruct['Orientstruct']['imprime']
							),
							$this->Xhtml->deleteLink(
								'Supprimer l\'orientation',
								array( 'controller' => 'orientsstructs', 'action' => 'delete', $orientstruct['Orientstruct']['id'] ),
								(
									$this->Permissions->checkDossier( 'orientsstructs', 'delete', $dossierMenu )
									&& ( $orientstruct['Orientstruct']['rgorient'] == $rgorient_max )
									&& $last_orientstruct_suppressible
								)
							),
							$this->Xhtml->fileLink(
								'Fichiers liés',
								array( 'controller' => 'orientsstructs', 'action' => 'filelink', $orientstruct['Orientstruct']['id'] ),
								$this->Permissions->checkDossier( 'orientsstructs', 'filelink', $dossierMenu )
							),
							h( "({$orientstruct['Fichiermodule']['nombre']})" )
						);
					}
					echo $this->Xhtml->tableCells( $cells, array( 'class' => 'odd' ), array( 'class' => 'even' ) );
				}
			?>
		</tbody>
	</table>
<?php  endif;?>