<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	switch( @$this->action ) {
		case 'creationmodification':
			$this->pageTitle = 'Création / modification d\'une commission d\'EP';
			break;
		case 'attributiondossiers':
			$this->pageTitle = 'Attribution des dossiers à une commission d\'EP';
			break;
		case 'recherche':
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$this->pageTitle = '2. Recherche de commission d\'EP';
			}
			else {
				$this->pageTitle = '2. Constitution de la commission d\'EP';
			}
			break;
		case 'arbitrageep':
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$this->pageTitle = '3. Avis de l\'EP';
			}
			else {
				$this->pageTitle = '3. Arbitrage d\'une commission d\'EP (niveau EP)';
			}
			break;
		case 'arbitragecg':
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$this->pageTitle = '3. Décisions CG';
			}
			else {
				$this->pageTitle = '3. Arbitrage d\'une commission d\'EP (niveau CG)';
			}

			break;
		case 'decisions':
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$this->pageTitle = '4. Consultation des avis/décisions';
			}
			else {
				$this->pageTitle = '4. Consultation des décisions';
			}
			break;
		default:
			$this->pageTitle = 'Liste des commissions d\'EP';
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( @$this->action == 'creationmodification' ):?>
	<ul class="actionMenu">
		<?php
			echo '<li>'.$this->Xhtml->addLink(
				'Ajouter',
				array( 'controller' => 'commissionseps', 'action' => 'add' ),
				( $compteurs['Ep'] > 0 )
			).' </li>';
		?>
	</ul>
<?php endif;?>

<?php
	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "$( 'Search' ).toggle(); return false;" )
	).'</li></ul>';
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnCheckbox( 'CommissionepDateseance', $( 'CommissionepDateseanceFromDay' ).up( 'fieldset' ), false );
	});
</script>

<?php echo $this->Xform->create( 'Commissionep', array( 'type' => 'post', 'url' => array( 'action' => $this->action ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );?>

	<fieldset>
			<?php echo $this->Xform->input( 'Commissionep.recherche', array( 'label' => false, 'type' => 'hidden', 'value' => true ) );?>

			<fieldset>
				<legend>Filtrer par Equipe Pluridisciplinaire</legend>
				<?php echo $this->Default2->subform(
					array(
						'Ep.regroupementep_id' => array('type'=>'select'),
						'Commissionep.name',
						'Commissionep.identifiant'
					),
					array(
						'options' => $options
					)
				); ?>
			</fieldset>

			<fieldset>
				<legend>Filtrer par adresse</legend>
				<?php echo $this->Default2->subform(
					array(
						'Structurereferente.ville'
					),
					array(
						'options' => $options
					)
				); ?>
			</fieldset>

			<?php echo $this->Xform->input( 'Commissionep.dateseance', array( 'label' => 'Filtrer par date de Commission', 'type' => 'checkbox' ) );?>
			<fieldset>
				<legend>Filtrer par période</legend>
				<?php
					$dateseance_from = Set::check( $this->request->data, 'Commissionep.dateseance_from' ) ? Set::extract( $this->request->data, 'Commissionep.datecomite_from' ) : strtotime( '-1 week' );
					$dateseance_to = Set::check( $this->request->data, 'Commissionep.dateseance_to' ) ? Set::extract( $this->request->data, 'Commissionep.datecomite_to' ) : strtotime( 'now' );
				?>
				<?php echo $this->Xform->input( 'Commissionep.dateseance_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $dateseance_from ) );?>
				<?php echo $this->Xform->input( 'Commissionep.dateseance_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1, 'minYear' => date( 'Y' ) - 10, 'selected' => $dateseance_to ) );?>
			</fieldset>

	</fieldset>

	<div class="submit noprint">
		<?php echo $this->Xform->button( 'Rechercher', array( 'type' => 'submit' ) );?>
		<?php echo $this->Xform->button( 'Réinitialiser', array( 'type' => 'reset' ) );?>
	</div>

<?php echo $this->Xform->end();
	if( isset( $commissionseps ) ) {
		echo $this->Html->tag( 'h2', 'Résultats de la recherche' );

		if( empty( $commissionseps ) ) {
			echo $this->Xhtml->tag( 'p', 'Aucun résultat ne correspond aux critères choisis.', array( 'class' => 'notice' ) );
		}
		else {
			$pagination = $this->Xpaginator->paginationBlock( 'Commissionep', $this->passedArgs );

			$showLienAvisEp = ( Configure::read( 'Cg.departement' ) == 66 );

			echo $pagination;

			switch( @$this->action ) {
				case 'creationmodification':
					$colspan = 1;
					break;
				case 'attributiondossiers':
					$colspan = 1;
					break;
				case 'arbitrageep':
					$colspan = 4;
					break;
				case 'arbitragecg':
					$colspan = ( $showLienAvisEp ) ? 2 : 1;
					break;
				case 'decisions':
					if( Configure::read( 'Cg.departement' ) == 66 ) {
						$colspan = 2;
					}
					else {
						$colspan = 1;
					}
					break;
				default:
					$colspan = 1;
			}

			echo '<table><thead>';
				echo '<tr>
					<th>'.$this->Xpaginator->sort( __d( 'ep', 'Ep.identifiant' ), 'Ep.identifiant' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'ep', 'Ep.name' ), 'Ep.name' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'commissionep', 'Commissionep.identifiant' ), 'Commissionep.identifiant' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'commissionep', 'Commissionep.name' ), 'Commissionep.name' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'commissionep', 'Commissionep.lieuseance' ), 'Commissionep.lieuseance' ).'</th>
					<th>'.$this->Xpaginator->sort( __d( 'commissionep', 'Commissionep.dateseance' ), 'Commissionep.dateseance' ).'</th>
					<th>Nombre de participants</th>
					<th>Nombre d\'absents</th>
					<!--<th>'.$this->Xpaginator->sort( __d( 'commissionep', 'Commissionep.etatcommissionep' ), 'Commissionep.etatcommissionep' ).'</th>-->
					<th>Présence</th>
					<th>Statut de la commission</th>
					<th>'.$this->Xpaginator->sort( __d( 'commissionep', 'Commissionep.observations' ), 'Commissionep.observations' ).'</th>
					<th colspan=\''.$colspan.'\'>Actions</th>
				</tr></thead><tbody>';

			foreach( $commissionseps as $commissionep ) {
				switch( @$this->action ) {
					case 'creationmodification':
						$lien = '<td>'.$this->Xhtml->link( 'Voir', array( 'controller' => 'commissionseps', 'action' => 'view', $commissionep['Commissionep']['id'] ) ).'</td>';
						break;
					case 'attributiondossiers':
						$lien = '<td>'.$this->Xhtml->link( 'Attribution des dossiers à une commission', array( 'controller' => 'dossierseps', 'action' => 'choose', $commissionep['Commissionep']['id'] ) ).'</td>';
						break;
					case 'arbitrageep':
						list( $jourCommission, $heureCommission ) = explode( ' ', $commissionep['Commissionep']['dateseance'] );
						$presencesPossible = ( date( 'Y-m-d' ) >= $jourCommission );

						//Ajout de l'ordre du jour
						$lien = '<td>'.$this->Xhtml->link( 'Ordre du jour', array( 'controller' => 'commissionseps', 'action' => 'printOrdresDuJour', $commissionep['Commissionep']['id'] ), array( 'enabled' => true ) ).'</td>';

						$lien .= '<td>'.$this->Xhtml->link( 'Présences', array( 'controller' => 'membreseps', 'action' => 'editpresence', $commissionep['Commissionep']['id'] ), array( 'enabled' => ( ( $commissionep['Commissionep']['etatcommissionep'] == 'quorum' || $commissionep['Commissionep']['etatcommissionep'] == 'presence' || $commissionep['Commissionep']['etatcommissionep'] == 'valide' ) && $presencesPossible ) ) ).'</td>';

						$lien .= '<td>'.$this->Xhtml->link( 'Arbitrage', array( 'controller' => 'commissionseps', 'action' => 'traiterep', $commissionep['Commissionep']['id'] ), array( 'enabled' => ( $commissionep['Commissionep']['etatcommissionep'] == 'presence' || $commissionep['Commissionep']['etatcommissionep'] == 'decisionep' ) ) ).'</td>';

						$lien .= '<td>'.$this->Xhtml->link( 'Avis', array( 'controller' => 'commissionseps', 'action' => 'decisionep', $commissionep['Commissionep']['id'] ), array( 'enabled' => ( $commissionep['Commissionep']['etatcommissionep'] == 'traiteep' || $commissionep['Commissionep']['etatcommissionep'] == 'decisioncg' || $commissionep['Commissionep']['etatcommissionep'] == 'traite' ) ) ).'</td>';
						break;
					case 'arbitragecg':
						$lien = '';
						if( $showLienAvisEp ) {
							$lien = '<td>'.$this->Xhtml->link( 'Voir avis EP', array( 'controller' => 'commissionseps', 'action' => 'decisionep', $commissionep['Commissionep']['id'] ), array( 'enabled' => ( $commissionep['Commissionep']['etatcommissionep'] == 'traiteep' || $commissionep['Commissionep']['etatcommissionep'] == 'decisioncg' ) ) ).'</td>';
						}

						$lien .= '<td>'.$this->Xhtml->link( 'Arbitrage', array( 'controller' => 'commissionseps', 'action' => 'traitercg', $commissionep['Commissionep']['id'] ), array( 'enabled' => ( $commissionep['Commissionep']['etatcommissionep'] == 'traiteep' || $commissionep['Commissionep']['etatcommissionep'] == 'decisioncg' ) ) ).'</td>';
						break;
					case 'decisions':
						if( Configure::read( 'Cg.departement' ) == 66 ) {
							$lien = '<td>'.$this->Xhtml->link( 'Voir les avis', array( 'controller' => 'commissionseps', 'action' => 'decisionep', $commissionep['Commissionep']['id'] ), array( 'enabled' => in_array( $commissionep['Commissionep']['etatcommissionep'], array( 'traite', 'annule' ) ) ) ).'</td>';
							$lien .= '<td>'.$this->Xhtml->link( 'Voir les décisions', array( 'controller' => 'commissionseps', 'action' => 'decisioncg', $commissionep['Commissionep']['id'] ), array( 'enabled' => in_array( $commissionep['Commissionep']['etatcommissionep'], array( 'traite', 'annule' ) ) ) ).'</td>';
						}
						else {
							$niveaudecisionmax = $commissionep['Commissionep']['niveaudecisionmax'];
							$libelledecisionmax = $commissionep['Commissionep']['libelledecisionmax'];
							$lien = '<td>'.$this->Xhtml->link( $libelledecisionmax, array( 'controller' => 'commissionseps', 'action' => $niveaudecisionmax, $commissionep['Commissionep']['id'] ), array( 'enabled' => in_array( $commissionep['Commissionep']['etatcommissionep'], array( 'traite', 'annule' ) ) ) ).'</td>';
						}
						break;
					default:
						$lien = '<td>'.$this->Xhtml->link( 'Voir', array( 'controller' => 'commissionseps', 'action' => 'view', $commissionep['Commissionep']['id'] ) ).'</td>';
				}

				echo '<tr>
					<td>'.h( $commissionep['Ep']['identifiant'] ).'</td>
					<td>'.h( $commissionep['Ep']['name'] ).'</td>
					<td>'.h( $commissionep['Commissionep']['identifiant'] ).'</td>
					<td>'.h( $commissionep['Commissionep']['name'] ).'</td>
					<td>'.h( @$commissionep['Commissionep']['lieuseance'] ).'</td>
					<td>'.h( $this->Locale->date( '%d/%m/%Y %H:%M', $commissionep['Commissionep']['dateseance'] ) ).'</td>
					<td>'.h( $commissionep['Commissionep']['nbparticipants'] ).'</td>
					<td>'.h( $commissionep['Commissionep']['nbabsents'] ).'</td>
					<td>'.h( ( $commissionep['Commissionep']['etatcommissionep'] == 'cree' || $commissionep['Commissionep']['etatcommissionep'] == 'quorum' || $commissionep['Commissionep']['etatcommissionep']  == 'associe' || $commissionep['Commissionep']['etatcommissionep']  == 'valide' ) ? 'Non validée' : 'Validée' ).'</td>
					<td>'.h( Set::enum( $commissionep['Commissionep']['etatcommissionep'], $options['Commissionep']['etatcommissionep'] ) ).'</td>
					<td>'.h( $commissionep['Commissionep']['observations'] ).'</td>
					'.$lien.'
				</tr>';
			}
			echo '</tbody></table>';

			echo $pagination;
		}
	}
?>