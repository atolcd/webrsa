<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php $this->pageTitle = __d( 'ajoutdossier', "Ajoutdossiers::{$this->action}" );?>
<?php echo $this->Form->create( 'Ajoutdossiers', array( 'id' => 'SignupForm' ) );?>
	<h1>Insertion d'une nouvelle demande de RSA</h1>
	<h2>Étape 2: adresse complète</h2>

	<?php echo $this->Form->input( 'Adressefoyer.rgadr', array( 'type' => 'hidden', 'value' => '01' ) );?>
	<?php echo $this->Form->input( 'Adresse.numvoie', array( 'label' =>   __d( 'adresse', 'Adresse.numvoie', true ) ) );?>
	<?php echo $this->Form->input( 'Adresse.libtypevoie', array( 'label' => ValidateAllowEmptyUtility::label( 'Adresse.libtypevoie', 'adresse' ), 'type' => 'select', 'options' => $libtypevoie, 'empty' => true ) );?>
	<?php echo $this->Form->input( 'Adresse.nomvoie', array( 'label' =>  required( __d( 'adresse', 'Adresse.nomvoie', true ) ) ) );?>
	<?php echo $this->Form->input( 'Adresse.complideadr', array( 'label' =>  __d( 'adresse', 'Adresse.complideadr', true ) ) );?>
	<?php echo $this->Form->input( 'Adresse.compladr', array( 'label' =>  __d( 'adresse', 'Adresse.compladr', true ) ) );?>
	<?php echo $this->Form->input( 'Adresse.lieudist', array( 'label' =>  __d( 'adresse', 'Adresse.lieudist', true ) ) );?>
	<?php echo $this->Form->input( 'Adresse.numcom', array( 'label' =>  __d( 'adresse', 'Adresse.numcom', true ) ) );?>
	<?php echo $this->Form->input( 'Adresse.codepos', array( 'label' =>  required( __d( 'adresse', 'Adresse.codepos', true ) ) ) );?>
	<?php echo $this->Form->input( 'Adresse.nomcom', array( 'label' =>  required( __d( 'adresse', 'Adresse.nomcom', true ) ) ) );?>
	<?php echo $this->Form->input( 'Adresse.pays', array( 'label' =>  required( __d( 'adresse', 'Adresse.pays', true ) ), 'type' => 'select', 'options' => $pays, 'empty' => true ) );?>
	<?php echo $this->Form->input( 'Adresse.canton', array( 'label' =>  __d( 'adresse', 'Adresse.canton', true ) ) );?>
	<?php echo $this->Form->input( 'Adressefoyer.dtemm', array( 'label' =>  __d( 'adressefoyer', 'Adressefoyer.dtemm', true ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => ( date( 'Y' ) - 100 ), 'empty' => true ) );?>
	<?php echo $this->Form->input( 'Adressefoyer.typeadr', array( 'label' => required( __d( 'adressefoyer', 'Adressefoyer.typeadr', true ) ), 'type' => 'select', 'options' => $typeadr, 'empty' => true ) );?>

	<div class="submit">
		<?php echo $this->Form->submit( '< Précédent', array( 'name' => 'Previous', 'div'=>false ) );?>
		<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
		<?php echo $this->Form->submit( 'Suivant >', array( 'div'=>false ) );?>
	</div>
<?php echo $this->Form->end();?>