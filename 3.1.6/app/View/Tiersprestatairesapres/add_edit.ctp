<?php
	$this->pageTitle = 'Tiers prestataire APRE';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

	<h1><?php echo $this->pageTitle;?></h1>

	<?php
		if( $this->action == 'add' ) {
			echo $this->Xform->create( 'Tiersprestataireapre', array( 'type' => 'post' ) );
		}
		else {
			echo $this->Xform->create( 'Tiersprestataireapre', array( 'type' => 'post' ) );
			echo '<div>';
			echo $this->Xform->input( 'Tiersprestataireapre.id', array( 'type' => 'hidden' ) );
			echo '</div>';
		}
	?>

	<fieldset>
		<?php
			echo $this->Xform->input( 'Tiersprestataireapre.nomtiers', array( 'required' => true, 'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.siret', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.numvoie', array( 'domain' => 'apre' ) );
			echo $this->Xform->enum( 'Tiersprestataireapre.typevoie', array(  'domain' => 'apre', 'options' => $typevoie, 'empty' => true ) );
			echo $this->Xform->input( 'Tiersprestataireapre.nomvoie', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.compladr', array( 'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.codepos', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.ville', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.canton', array( 'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.numtel', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.adrelec', array( 'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.nomtiturib', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.etaban', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.guiban', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.numcomptban', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.nometaban', array(  'domain' => 'apre' ) );
			echo $this->Xform->input( 'Tiersprestataireapre.clerib', array(  'domain' => 'apre', 'maxlength' => 2 ) );
		?>
	</fieldset>

	<fieldset>
		<legend>Formations liées</legend>
			<?php
				echo $this->Xform->enum( 'Tiersprestataireapre.aidesliees', array( 'required' => true, 'domain' => 'apre', 'options' => $natureAidesApres, 'empty' => true ) );
			?>
	</fieldset>

		<div class="submit">
			<?php
				echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
				echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
			?>
		</div>
<?php echo $this->Xform->end();?>

<div class="clearer"><hr /></div>