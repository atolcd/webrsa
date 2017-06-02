<?php
	$cov58['Cov58']['datecommission'] = strftime( '%d/%m/%Y à %H:%M', strtotime( $cov58['Cov58']['datecommission'] ) );
	echo $this->Default3->titleForLayout( $cov58 );

	echo $this->Default3->actions(
		array(
			"/Covs58/ordredujour/{$cov58['Cov58']['id']}" => array(
				'disabled' => !$this->Permissions->check( 'Covs58', 'ordredujour' ),
				'class' => 'print'
			),
			"/Covs58/impressionpv/{$cov58['Cov58']['id']}" => array(
				'disabled' => !$this->Permissions->check( 'Covs58', 'impressionpv' ),
				'class' => 'print'
			),
		)
	);

	$themes = (array)Hash::get( $options, 'Dossiercov58.themecov58' );
	$anciennes_thematiques = (array)Hash::get( $options, 'Dossiercov58.anciennes_thematiques' );
?>
<div id="tabbedWrapper" class="tabs">
	<?php
		foreach( $themes as $theme => $label ) {
			if( !empty( $results[$theme] ) || !in_array( $theme, $anciennes_thematiques ) ) {
				echo '<div id="'.$theme.'"><h2 class="title">'.$label.'</h2>';
				if( !empty( $results[$theme] ) ) {
					echo $this->Default3->index(
						$results[$theme],
						$fields[$theme],
						array(
							'options' => $options,
							'paginate' => false
						)
					);
				}
				else {
					echo '<p class="notice">Aucun dossier traité pour cette thématique.</p>';
				}
				echo '</div>';
			}
		}
	?>
</div>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'covs58',
			'action'     => 'view',
			$cov58['Cov58']['id']
		),
		array(
			'id' => 'Back'
		)
	);

	echo $this->Html->script( 'prototype.livepipe.js' );
	echo $this->Html->script( 'prototype.tabs.js' );
?>
<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>