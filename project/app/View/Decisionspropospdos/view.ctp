<?php
	$this->pageTitle =  __d( 'decisionpropopdo', "Decisionspropospdos::{$this->action}" );

	echo $this->element( 'dossier_menu', array( 'id' => $dossier_id ) );
?>

<div class="with_treemenu">
	<?php
		echo $this->Xhtml->tag( 'h1', $this->pageTitle );
		echo $this->Form->create( 'Decisionpropopdo', array( 'type' => 'post', 'id' => 'decisionpropopdoform', 'novalidate' => true ) );

		$decisionreponseep = Set::enum( Set::classicExtract( $decisionpropopdo, 'Decisionpropopdo.decisionreponseep' ), $options['Decisionpropopdo']['decisionreponseep'] );

		echo $this->Default2->view(
			$decisionpropopdo,
			array(
				'Decisionpropopdo.datedecisionpdo',
				'Decisionpdo.libelle',
				'Decisionpropopdo.commentairepdo',
				'Decisionpropopdo.hasreponseep' => array( 'type' => 'boolean' ),
				'Decisionpropopdo.decisionreponseep' => array( 'value' => $decisionreponseep ),
				'Decisionpropopdo.accordepaudition' => array( 'type' => 'boolean' ),
				'Decisionpropopdo.commentairereponseep',
				'Decisionpropopdo.datereponseep',
				'Decisionpropopdo.avistechnique' => array( 'type' => 'boolean' ),
				'Decisionpropopdo.dateavistechnique',
				'Decisionpropopdo.commentaireavistechnique',
				'Decisionpropopdo.validationdecision' => array( 'type' => 'boolean' ),
				'Decisionpropopdo.datevalidationdecision',
				'Decisionpropopdo.commentairedecision'
			)
		);
	?>
</div>
	<div class="submit">
		<?php

			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>
	<?php echo $this->Form->end();?>
<div class="clearer"><hr /></div>