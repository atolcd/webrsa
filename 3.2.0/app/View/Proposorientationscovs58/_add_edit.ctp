<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Orientation';
	}
	else {
		$this->pageTitle = 'Édition de l\'orientation';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'Propoorientationcov58StructurereferenteId', 'Propoorientationcov58TypeorientId' );
		try { $( 'Propoorientationcov58StructurereferenteId' ).onchange(); } catch(id) { }

		dependantSelect( 'Propoorientationcov58ReferentId', 'Propoorientationcov58StructurereferenteId' );
		try { $( 'Propoorientationcov58ReferentId' ).onchange(); } catch(id) { }
	});
</script>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Propoorientationcov58', array(  'type' => 'post', 'novalidate' => true  ) );
		echo '<div>';
		echo $this->Form->input( 'Propoorientationcov58.id', array( 'type' => 'hidden', 'value' => '' ) );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Propoorientationcov58', array( 'type' => 'post', 'novalidate' => true  ) );
		echo '<div>';
		echo $this->Form->input( 'Propoorientationcov58.id', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'Propoorientationcov58.dossiercov58_id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}

	$typeorient_id = null;
	if( !empty( $this->request->data['Structurereferente']['Typeorient']['id'] ) ) {
		$typeorient_id = $this->request->data['Structurereferente']['Typeorient']['id'];
	}
	$domain = 'orientstruct';
?>

<fieldset>
	<legend>Ajout d'une orientation</legend>
	<script type="text/javascript">
		document.observe("dom:loaded", function() {
			dependantSelect( 'Propoorientationcov58ReferentorientantId', 'Propoorientationcov58StructureorientanteId' );
			try { $( 'Propoorientationcov58ReferentorientantId' ).onchange(); } catch(id) { }
		});
	</script>
	<?php
		$selected = null;
		if( $this->action == 'edit' ){
			$selected = preg_replace( '/^[^_]+_/', '', $this->request->data['Propoorientationcov58']['structureorientante_id'] ).'_'.$this->request->data['Propoorientationcov58']['referentorientant_id'];
		}

		echo $this->Default2->subform(
			array(
				'Propoorientationcov58.structureorientante_id' => array( 'type' => 'select', 'options' => $structsorientantes, 'required' => true ),
				'Propoorientationcov58.referentorientant_id' => array(  'type' => 'select', 'options' => $refsorientants, 'selected' => $selected, 'required' => true )
			)
		);

	?>



	<?php echo $this->Form->input( 'Propoorientationcov58.typeorient_id', array( 'label' =>  required( __d( 'structurereferente', 'Structurereferente.lib_type_orient' ) ), 'type' => 'select', 'options' => $typesorients, 'empty' => true, 'value' => $typeorient_id ) );?>
	<?php
		if( $this->action == 'edit' ) {
			if( !empty( $this->request->data['Propoorientationcov58']['structurereferente_id'] ) ) {
				$this->request->data['Propoorientationcov58']['structurereferente_id'] = preg_replace( '/^[^_]+_/', '', $this->request->data['Propoorientationcov58']['typeorient_id'] ).'_'.$this->request->data['Propoorientationcov58']['structurereferente_id'];

				$this->request->data['Propoorientationcov58']['referent_id'] = preg_replace( '/^[^_]+_/', '', $this->request->data['Propoorientationcov58']['structurereferente_id'] ).'_'.$this->request->data['Propoorientationcov58']['referent_id'];
			}
		}
		else {
			if( !Set::check( $this->request->data, 'Propoorientationcov58.structurereferente_id', '' ) ) {
				$this->request->data = Hash::insert( $this->request->data, 'Propoorientationcov58.structurereferente_id', '' );
			}
			if( !Set::check( $this->request->data, 'Propoorientationcov58.referent_id', '' ) ) {
				$this->request->data = Hash::insert( $this->request->data, 'Propoorientationcov58.referent_id', '' );
			}
		}

		/// Rustine sinon 13_10_5_4
		$this->request->data['Propoorientationcov58']['structurereferente_id'] = preg_replace( '/^.*(?<![0-9])([0-9]+_[0-9]+)$/', '\1', $this->request->data['Propoorientationcov58']['structurereferente_id'] );
		$this->request->data['Propoorientationcov58']['referent_id'] = preg_replace( '/^.*(?<![0-9])([0-9]+_[0-9]+)$/', '\1', $this->request->data['Propoorientationcov58']['referent_id'] );

		echo $this->Form->input( 'Propoorientationcov58.structurereferente_id', array( 'label' => required(__d( 'structurereferente', 'Structurereferente.lib_struc' )), 'type' => 'select', 'options' => $structuresreferentes, 'empty' => true, 'selected' => $this->request->data['Propoorientationcov58']['structurereferente_id'] ) );
		echo $this->Form->input( 'Propoorientationcov58.referent_id', array(  'label' => __d( 'structurereferente', 'Structurereferente.nom_referent' ), 'type' => 'select', 'options' => $referents, 'empty' => true, 'selected' => $this->request->data['Propoorientationcov58']['referent_id'] ) );
		echo $this->Form->input( 'Propoorientationcov58.datedemande', array( 'type' => 'hidden', 'value' => date( 'Y-m-d' ) ) );
		echo $this->Form->input( 'Propoorientationcov58.user_id', array( 'type' => 'hidden', 'value' => $this->Session->read( 'Auth.User.id' ) ) );
	?>
</fieldset>

<div class="submit">
	<?php
		echo $this->Xform->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Xform->submit( 'Annuler', array( 'name' => 'Cancel', 'div' => false ) );
	?>
</div>
<?php echo $this->Form->end();?>