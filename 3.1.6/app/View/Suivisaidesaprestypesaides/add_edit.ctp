<?php
	$this->pageTitle = 'Type d\'aide en fonction des personnes chargées du suivi';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
	<h1><?php echo $this->pageTitle;?></h1>

	<?php
		echo $this->Xform->create( 'Suiviaideapretypeaide', array( 'type' => 'post' ) );
	?>

	<fieldset>
		<legend>Aides complémentaires</legend>
		<?php
			foreach( $aidesApres as $index => $model ) {
				$id = Set::classicExtract( $this->request->data, "Suiviaideapretypeaide.{$index}.id" );
				if( !empty( $id ) ) {
					echo '<div>'.$this->Xform->input( "Suiviaideapretypeaide.{$index}.id", array( 'type' => 'hidden' ) ).'</div>';
				}
				echo $this->Xform->input( 'Suiviaideapretypeaide.'.$index.'.suiviaideapre_id', array( 'label' => Set::enum( $model, $natureAidesApres ), 'type' => 'select', 'options' => $personnessuivis, 'empty' => true ) );

				echo $this->Xform->input( 'Suiviaideapretypeaide.'.$index.'.typeaide', array( 'label' => false, 'type' => 'hidden', 'value' => $model ) );
			}
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