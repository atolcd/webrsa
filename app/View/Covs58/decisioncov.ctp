<?php
	$this->pageTitle = 'COV du '.date('d-m-Y à h:i', strtotime($cov58['Cov58']['datecommission']));

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js', 'prototype.maskedinput.js' ) );
	}
?>

<h1><?php echo $this->pageTitle; ?></h1>
<br/>
<div id="tabbedWrapper" class="tabs">
	<?php
		echo $this->Form->create( null, array() );
		foreach( array_keys( $dossiers ) as $theme ) {
			// S'il s'agit d'une ancienne thématique pour laquelle il n'existe pas de dossier, on n'affiche pas l'onglet
			if( !in_array( Inflector::tableize( $theme ), $options['Dossiercov58']['vx_themecov58'] ) || !empty( $dossiers[$theme]['liste'] ) ) {
				$modeleDecision = Inflector::classify( 'Decision'.Inflector::underscore( $theme ) );
				$errorClass = ( !empty( $this->validationErrors[$modeleDecision] ) ? 'error' : '' );

				echo '<div id="'.$theme.'" class="'.$errorClass.'"><h2 class="title '.$errorClass.'">'.__d( 'dossiercov58', 'ENUM::THEMECOV::'.Inflector::tableize( $theme ) ).'</h2>';
				if( !empty( $dossiers[$theme]['liste'] ) ) {
					require_once( 'decisioncov.'.Inflector::tableize( $theme ).'.ctp' );
				}
				else {
					echo '<p class="notice">Aucun dossier à traiter pour cette thématique.</p>';
				}
				echo '</div>';
			}
		}

		echo $this->Form->submit( 'Enregistrer' );
		echo $this->Form->end();

		echo $this->Default->button(
		    'back',
		    array(
		        'controller' => 'covs58',
		        'action'     => 'view',
		        $cov58_id
		    ),
		    array(
		        'id' => 'Back'
		    )
		);
	?>
</div>

<?php
	echo $this->Html->script( 'prototype.livepipe.js' );
	echo $this->Html->script( 'prototype.tabs.js' );
?>

<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>