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
	$etatdossierpdo = Set::enum( $pdo['Propopdo']['etatdossierpdo'], $options['etatdossierpdo'] );
	$complet = Set::enum( $pdo['Propopdo']['iscomplet'], $options['iscomplet'] );
	$categoriegeneral = Set::enum( $pdo['Propopdo']['categoriegeneral'], $categoriegeneral );
	$categoriedetail = Set::enum( $pdo['Propopdo']['categoriedetail'], $categoriedetail );
	$service = Set::enum( $pdo['Propopdo']['serviceinstructeur_id'], $serviceinstructeur );
	$user = Set::enum( $pdo['Propopdo']['user_id'], $gestionnaire );
	$origpdo = Set::enum( $pdo['Propopdo']['originepdo_id'], $originepdo );

	echo $this->Default2->view(
		$pdo,
		array(
			'Propopdo.etatdossierpdo' => array( 'type' => 'text', 'value' => $etatdossierpdo ),
			'Typepdo.libelle',
			'Propopdo.datereceptionpdo',
			'Propopdo.originepdo_id' => array( 'type' => 'text', 'value' => $origpdo ),
			'Propopdo.orgpayeur',
			'Propopdo.serviceinstructeur_id'=> array( 'type' => 'text', 'value' => $service ),
			'Propopdo.user_id' => array( 'type' => 'text', 'value' => $user ),
			'Situationpdo.libelle',
			'Statutpdo.libelle',
			'Propopdo.categoriegeneral' => array( 'type' => 'text', 'value' => $categoriegeneral ),
			'Propopdo.categoriedetail' => array( 'type' => 'text', 'value' => $categoriedetail ),
			'Propopdo.iscomplet' => array( 'type' => 'text', 'value' => $complet ),
		),
		array(
			'class' => 'aere'
		)
	);

	echo "<h2>Pièces jointes</h2>";
	echo $this->Fileuploader->results( Set::classicExtract( $pdo, 'Fichiermodule' ) );
?>
<hr />
<div>
	<?php
		echo $this->Xhtml->tag( 'h2', 'Traitements' );

		echo $this->Default2->index(
			$traitements,
			array(
				'Descriptionpdo.name',
				'Traitementtypepdo.name',
				'Traitementpdo.datereception' => array( 'type' => 'date' ),
				'Traitementpdo.datedepart' => array( 'type' => 'date' )
			),
			array(
				'actions' => array(
					'Traitementspdos::view'
				),
				'options' => $options,
				'id' => 'traitementpdoview'
			)
		);
	?>
</div>
<hr />
<div>
	<?php
		echo $this->Xhtml->tag( 'h2', 'Propositions de décisions' );

		echo $this->Default2->index(
			$propositions,
			array(
				'Decisionpdo.libelle',
				'Decisionpropopdo.datedecisionpdo',
				'Decisionpropopdo.avistechnique' => array( 'type' => 'boolean' ),
				'Decisionpropopdo.dateavistechnique' => array( 'type' => 'date' ),
				'Decisionpropopdo.commentaireavistechnique',
				'Decisionpropopdo.validationdecision' => array( 'type' => 'boolean' ),
				'Decisionpropopdo.datevalidationdecision' => array( 'type' => 'date' ),
				'Decisionpropopdo.commentairedecision'
			),
			array(
				'actions' => array(
					'Decisionspropospdos::edit'
				),
				'options' =>  $options,
				'id' => 'propositionpdoview'
			)
		);
	?>
</div>
</div>
<div class="submit">
	<?php
		echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Form->end();?>