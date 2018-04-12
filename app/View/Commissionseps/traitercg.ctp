<h1><?php echo $this->pageTitle = '3. Traitement de la commission du '.date('d/m/Y à H:i', strtotime($commissionep['Commissionep']['dateseance'])).' par le CD '; ?></h1>
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

		echo $this->Form->create( null, array( 'novalidate' => true ) );
		echo '<div>'.$this->Form->input( 'Commissionep.save', array( 'type' => 'hidden', 'value' => true ) ).'</div>';

		foreach( array_keys( $dossiers ) as $theme ) {
			// S'il s'agit d'une ancienne thématique pour laquelle il n'existe pas de dossier, on n'affiche pas l'onglet
			if( !in_array( Inflector::tableize( $theme ), $options['Dossierep']['vx_themeep'] ) || !empty( $dossiers[$theme]['liste'] ) ) {
				$modeleDecision = Inflector::classify( 'Decision'.Inflector::underscore( $theme ) );
				$errorClass = ( !empty( $this->validationErrors[$modeleDecision] ) ? 'error' : '' );

				$file = sprintf( 'traitercg.%s.liste.ctp', Inflector::underscore( $theme ) );
				echo '<div id="'.$theme.'" class="'.$errorClass.'"><h2 class="title '.$errorClass.'">'.__d( 'dossierep', 'ENUM::THEMEEP::'.Inflector::tableize( $theme ) ).'</h2>';
				if( !empty( $dossiers[$theme]['liste'] ) ) {
					include_once  $file ;
				}
				else {
					echo '<p class="notice">Aucun dossier à traiter pour cette thématique.</p>';
				}
				echo '</div>';
			}
		}

		echo '<div class="submit">';
			echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );
			if ( $commissionep['Commissionep']['etatcommissionep'] == 'decisioncg' ) {
				echo '<br/><br/>'.$this->Form->submit( 'Valider', array( 'name' => 'Valider', 'div' => false, 'onclick' => 'return confirm( \'Êtes-vous sûr de vouloir valider les décisions ?\' );' ) );
			}
		echo '</div>';
		echo $this->Form->end();

		echo $this->Default->button(
		    'back',
		    array(
		        'controller' => 'commissionseps',
		        'action'     => 'arbitragecg'
		    ),
		    array(
		        'id' => 'Back'
		    )
		);

	?>
</div>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );

		$validationErrors = $this->validationErrors;
		$validationErrors = Hash::filter( $validationErrors );
	}
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>
