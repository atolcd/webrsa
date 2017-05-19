<?php
	$departement = Configure::read( 'Cg.departement' );
	$this->pageTitle = $departement == 93 ? 'Clôture de la personne chargée du suivi' : 'Clôture du référent';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>
<?php echo $this->Form->create( 'PersonneReferent',array() );?>
	<fieldset>
		<?php

			echo $this->Default2->subform(
				array(
					'PersonneReferent.id' => array( 'type' => 'hidden' ),
					'PersonneReferent.personne_id' => array( 'type' => 'hidden' ),
					'PersonneReferent.structurereferente_id' => array( 'disabled' => true, 'value' => $personne_referent['PersonneReferent']['structurereferente_id'], 'label' => $departement == 93 ? 'Structure de suivi' : 'Structure référente' ),
					'PersonneReferent.referent_id' => array( 'type' => 'select', 'disabled' => true, 'value' => $personne_referent['PersonneReferent']['referent_id'], 'label' => $departement == 93 ? 'Personne chargée du suivi' : 'Référent' ),
					'PersonneReferent.dddesignation' => array( 'disabled' => true, 'value' => $personne_referent['PersonneReferent']['dddesignation'] )
				),
				array(
					'options' => $options,
					'domain' => 'personne_referent'
				)
			);

			echo $this->Default2->subform(
				array(
					'PersonneReferent.dddesignation' => array( 'type' => 'hidden' ),//Champ nécessaire pour la comparaison de date, sinon n'apparait pas dans $this->request->data
					'PersonneReferent.dfdesignation' => array( 'required' => true )
				),
				array(
					'options' => $options,
					'domain' => 'personne_referent'
				)
			);
		?>
	</fieldset>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>
</div>