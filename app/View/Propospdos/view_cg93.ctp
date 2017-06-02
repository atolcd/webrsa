<?php
	$this->pageTitle = 'Détails demande PDO';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1>Détails demande PDO</h1>
<ul class="actionMenu">
	<?php
		if( $this->Permissions->checkDossier( 'propospdos', 'edit', $dossierMenu ) ) {
			echo '<li>'.$this->Xhtml->editLink(
				'Éditer PDO',
				array( 'controller' => 'propospdos', 'action' => 'edit', Set::classicExtract( $pdo, 'Propopdo.id' ) )
			).' </li>';
		}
	?>
</ul>

<?php
	echo $this->Form->create( 'Propopdo', array( 'type' => 'post' ) );

	$complet = Set::enum( $pdo['Propopdo']['iscomplet'], $options['iscomplet'] );
	$origpdo = Set::enum( $pdo['Propopdo']['originepdo_id'], $originepdo );
	$motifpdo = Set::enum( $pdo['Propopdo']['motifpdo'], $motifpdo );
	$structure = Set::enum( $pdo['Propopdo']['structurereferente_id'], $structs );
	$decision = Set::enum( $pdo['Decisionpropopdo'][0]['decisionpdo_id'], $decisionpdo );
	echo $this->Default2->view(
		$pdo,
		array(
			'Structurereferente.lib_struc' => array( 'type' => 'text', 'value' => $structure ),
			'Typepdo.libelle',
			'Propopdo.datereceptionpdo',
			'Propopdo.originepdo_id' => array( 'type' => 'text', 'value' => $origpdo ),
			'Decisionpropopdo.decisionpdo_id' => array( 'type' => 'text', 'value' => $decision ),
			'Propopdo.motifpdo' => array( 'type' => 'text', 'value' => $motifpdo ),
			'Decisionpropopdo.0.datedecisionpdo',
			'Decisionpropopdo.0.commentairepdo',
			'Propopdo.iscomplet' => array( 'type' => 'text', 'value' => $complet ),
		),
		array(
			'class' => 'aere'
		)
	);

	echo "<h2>Pièces jointes</h2>";
	echo $this->Fileuploader->results( Set::classicExtract( $pdo, 'Fichiermodule' ) );
?>

</div>
<div class="submit">
	<?php echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) ); ?>
</div>
<?php echo $this->Form->end();?>