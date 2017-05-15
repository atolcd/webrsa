<?php
	$this->pageTitle = 'Types d\'orientations';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php echo $this->Form->create( 'Typeorient', array( 'type' => 'post' ) );?>

	<fieldset>
		<?php echo $this->Form->input( 'Typeorient.id', array( 'type' => 'hidden' ) );?>
		<?php echo $this->Form->input( 'Typeorient.lib_type_orient', array( 'label' => required(  __d( 'structurereferente', 'Structurereferente.lib_type_orient' ) ), 'type' => 'text' ) );?>
		<?php echo $this->Form->input( 'Typeorient.parentid', array( 'label' =>  __( 'parentid' ), 'type' => 'select', 'options' => $parentid, 'empty' => true )  );?>
		<?php echo $this->Form->input( 'Typeorient.modele_notif', array( 'label' => required( __d( 'typeorient', 'Typeorient.modele_notif' ) ), 'type' => 'text' )  );?>
	<?php echo $this->Form->input( 'Typeorient.modele_notif_cohorte', array( 'label' => required( __d( 'typeorient', 'Typeorient.modele_notif_cohorte' ) ), 'type' => 'text' ) );?>
	<?php echo $this->Form->input( 'Typeorient.actif', array( 'label' => required( __d( 'typeorient', 'Typeorient.actif' ) ), 'type' => 'radio', 'options' => $options['actif'] ) ); ?>
	</fieldset>
	<table>
	<thead>
		<tr>
			<th>ID</th>
			<th>Type d'orientation</th>
			<th>Parent</th>
			<th>Modèle de notification</th>
			<th>Modèle de notification pour cohorte</th>
			<th>Actif</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach( $typesorients as $typeorient ):?>
			<?php echo $this->Xhtml->tableCells(
						array(
							h( $typeorient['Typeorient']['id'] ),
							h( $typeorient['Typeorient']['lib_type_orient'] ),
							h( $typeorient['Typeorient']['parentid'] ),
							h( $typeorient['Typeorient']['modele_notif'] ),
							h( $typeorient['Typeorient']['modele_notif_cohorte'] ),
							h( Set::enum( $typeorient['Typeorient']['actif'], $options['actif'] ) ),
						),
						array( 'class' => 'odd' ),
						array( 'class' => 'even' )
					);
			?>
		<?php endforeach;?>
		</tbody>
	</table>

	<div class="submit">
		<?php
			echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
			echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
		?>
	</div>

<?php echo $this->Form->end();?>
