<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une adresse';
	}
	else {
		$title = implode(
			' ',
			array(
				$this->request->data['Adresse']['numvoie'],
				$this->request->data['Adresse']['libtypevoie'],
				$this->request->data['Adresse']['nomvoie'] )
		);

		$this->pageTitle = 'Édition de l\'adresse « '.$title.' »';
		$foyer_id = $this->request->data['Adressefoyer']['foyer_id'];
	}
?>
<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Adressefoyer', array( 'type' => 'post', 'novalidate' => true ) );
	}
	else {
		echo $this->Form->create( 'Adressefoyer', array( 'type' => 'post', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Adresse.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Adressefoyer.id', array( 'type' => 'hidden', 'value' => $this->request->data['Adressefoyer']['id'] ) );
		echo '</div>';
	}

	echo '<div>'.$this->Form->input( 'Adressefoyer.foyer_id', array( 'type' => 'hidden', 'value' => $foyer_id ) ).'</div>';

	echo $this->Form->input( 'Adresse.numvoie', array( 'label' =>   __d( 'adresse', 'Adresse.numvoie' ) ) );
	echo $this->Form->input( 'Adresse.libtypevoie', array( 'label' => ValidateAllowEmptyUtility::label( 'Adresse.libtypevoie', 'adresse' ), 'type' => 'select', 'options' => $options['Adresse']['libtypevoie'], 'empty' => true ) );
	echo $this->Form->input( 'Adresse.nomvoie', array( 'label' => ValidateAllowEmptyUtility::label( 'Adresse.nomvoie', 'adresse' ) ) );
	echo $this->Form->input( 'Adresse.complideadr', array( 'label' =>  __d( 'adresse', 'Adresse.complideadr' ) ) );
	echo $this->Form->input( 'Adresse.compladr', array( 'label' =>  __d( 'adresse', 'Adresse.compladr' ) ) );
	echo $this->Form->input( 'Adresse.lieudist', array( 'label' =>  __d( 'adresse', 'Adresse.lieudist' ) ) );
	echo $this->Form->input( 'Adresse.numcom', array( 'label' =>  __d( 'adresse', 'Adresse.numcom' ) ) );
	echo $this->Form->input( 'Adresse.codepos', array( 'label' =>  required( __d( 'adresse', 'Adresse.codepos' ) ) ) );
	echo $this->Form->input( 'Adresse.nomcom', array( 'label' =>  required( __d( 'adresse', 'Adresse.nomcom' ) ) ) );
	echo $this->Form->input( 'Adresse.pays', array( 'label' =>  required( __d( 'adresse', 'Adresse.pays' ) ), 'type' => 'select', 'options' => $pays, 'empty' => true ) );
	echo $this->Form->input( 'Adresse.canton', array( 'label' =>  __d( 'adresse', 'Adresse.canton' ) ) );

	if( $this->name == 'Adressesfoyers' ):
		echo $this->Form->input( 'Adressefoyer.rgadr', array( 'label' => required( __d( 'adressefoyer', 'Adressefoyer.rgadr' ) ), 'type' => 'select', 'options' => $rgadr, 'empty' => true ) );
	endif;
	echo $this->Form->input( 'Adressefoyer.dtemm', array( 'label' =>  __d( 'adressefoyer', 'Adressefoyer.dtemm' ), 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => ( date( 'Y' ) - 100 ), 'empty' => true ) );
	echo $this->Form->input( 'Adressefoyer.typeadr', array( 'label' => required( __d( 'adressefoyer', 'Adressefoyer.typeadr' ) ), 'type' => 'select', 'options' => $typeadr, 'empty' => true ) );
?>
<div class="submit">
	<?php
		echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Form->end(); ?>