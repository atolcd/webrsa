<h1><?php echo $this->pageTitle = '3. Traitement de la commission du '.date('d/m/Y à H:i', strtotime($commissionep['Commissionep']['dateseance'])).' par l\'EP : "'.$commissionep['Ep']['name'].'"';
?></h1>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<br/>
<?php
	if( Configure::read( 'Cg.departement' ) == 93 ) {
		echo '<ul class="actionMenu">';
			echo '<li>'.$this->Xhtml->link(
				__d( 'commissionep', 'Commissionseps::view' ),
				array( 'controller' => 'commissionseps', 'action' => 'view', $commissionep['Commissionep']['id'] ),
				array( 'class' => 'button view external' )
			).' </li>';
		echo '</ul>';
	}
?>
<div id="tabbedWrapper" class="tabs">
	<?php
		// L'allocataire passe-t'il plusieurs fois dans cette commission
		foreach( $dossiers as $thmeme => $dossiersTmp ) {
			foreach( $dossiersTmp['liste'] as $dossier ) {
				$dossiersAllocataires[$dossier['Personne']['id']][] = $dossier['Dossierep']['themeep'];
			}
		}

		echo $this->Form->create( null, array() );

		foreach( array_keys( $dossiers ) as $theme ) {
			// S'il s'agit d'une ancienne thématique pour laquelle il n'existe pas de dossier, on n'affiche pas l'onglet
			if( !in_array( Inflector::tableize( $theme ), $options['Dossierep']['vx_themeep'] ) || !empty( $dossiers[$theme]['liste'] ) ) {
				$modeleDecision = Inflector::classify( 'Decision'.Inflector::underscore( $theme ) );
				$errorClass = ( !empty( $this->validationErrors[$modeleDecision] ) ? 'error' : '' );

				$file = sprintf( 'traiterep.%s.liste.ctp', Inflector::underscore( $theme ) );
				echo '<div id="'.$theme.'" class="'.$errorClass.'"><h2 class="title '.$errorClass.'">'.__d( 'dossierep', 'ENUM::THEMEEP::'.Inflector::tableize( $theme ) ).'</h2>';
				if( !empty( $dossiers[$theme]['liste'] ) ) {
					require_once( $file );
				}
				else {
					echo '<p class="notice">Aucun dossier à traiter pour cette thématique.</p>';
				}
				echo '</div>';
			}
		}

		//Ajout d'une distinction entre avis et décisions pour le CG66 vs les autres

// 		if( Configure::read( 'Cg.departement' ) == 66 ){
// 			$avisdecisions = 'avis';
// 		}
// 		else{
// 			$avisdecisions = 'décisions';
// 		}
		$avisdecisions = '';
		$buttonName = '';

		echo '<div class="submit">';
			echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );
			if ( $commissionep['Commissionep']['etatcommissionep'] == 'decisionep' ) {
				if( Configure::read( 'Cg.departement' ) == 66 ) {
					$avisdecisions = 'clôturer les avis';
					$buttonName = 'Clôturer la séance';
				}
				else {
					$avisdecisions = 'valider les décisions';
					$buttonName = 'Valider';
				}
				echo '<br/><br/>'.$this->Form->submit( $buttonName, array( 'name' => 'Valider', 'div' => false, 'onclick' => 'return confirm( \'Êtes-vous sûr de vouloir '.$avisdecisions.' ?\' );' ) );
			}
		echo '</div>';
		echo $this->Form->end();

		if( Configure::read( 'Cg.departement' ) == 66 ) {
			echo $this->Default->button(
				'back',
				array(
					'controller' => 'commissionseps',
					'action'     => 'view',
					$commissionep['Commissionep']['id']
				),
				array(
					'id' => 'Back'
				)
			);
		}
		else {
			echo $this->Default->button(
				'back',
				array(
					'controller' => 'commissionseps',
					'action'     => 'arbitrageep'
				),
				array(
					'id' => 'Back'
				)
			);
		}
	?>
</div>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );

		$validationErrors = $this->validationErrors;
		$validationErrors = Hash::filter( $validationErrors );
		if( !empty( $validationErrors ) ) {
			debug( $validationErrors );
		}
	}
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>