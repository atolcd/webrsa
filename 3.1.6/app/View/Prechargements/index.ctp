<h1><?php echo $this->pageTitle = 'Préchargement du cache'; ?></h1>
<br />
<?php
	$models = array(
		'initialized' => array(
			'title' => 'Modèles initialisés',
			'elmts' => $initialized,
			'class' => null,
		),
		'prechargements' => array(
			'title' => 'Modèles préchargés',
			'elmts' => $prechargements,
			'class' => null,
		),
		'nonprechargements' => array(
			'title' => 'Erreur(s) de préchargement',
			'elmts' => $nonprechargements,
			'class' => ( !empty( $nonprechargements ) ? 'error' : null ),
		),
		'uninitialized' => array(
			'title' => 'Modèles non initialisés',
			'elmts' => $uninitialized,
			'class' => null,
		),
		'missing' => array(
			'title' => 'Tables sans modèle lié',
			'elmts' => $missing,
			'class' => null,
		),
	);
?>
<div id="tabbedWrapper" class="tabs">
	<div id="models">
		<h2 class="title">Modèles</h2>
		<div id="tabbedWrapperModels" class="tabs">
			<?php foreach( $models as $modelId => $model ): ?>
			<div id="<?php echo $modelId;?>">
				<h3 class="title <?php echo $model['class'];?>"><?php echo h( $model['title'] );?> (<?php echo count($model['elmts']);?>)</h3>
				<ol>
				<?php
					sort( $model['elmts'] );

					foreach( $model['elmts'] as $elmt ) {
						echo '<li>'.$elmt.'</li>';
					}
				?>
				</ol>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div id="domains">
		<h2 class="title">Traductions (<?php echo count($domaines);?>)</h2>
			<ol>
			<?php
				sort( $domaines );

				foreach( $domaines as $elmt ) {
					echo '<li>'.$elmt.'</li>';
				}
			?>
			</ol>
	</div>
</div>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>
<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
	makeTabbed( 'tabbedWrapperModels', 3 );
	makeErrorTabs();
</script>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'parametrages',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>