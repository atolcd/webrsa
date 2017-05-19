<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<script type="text/javascript">
        document.observe("dom:loaded", function() {
            dependantSelect(
                'Personnepcg66Categoriedetail',
                'Personnepcg66Categoriegeneral'
            );
	});
</script>

<?php
	$chargeDossier = Set::enum( Set::classicExtract( $dossierpcg66, 'Dossierpcg66.user_id' ), $gestionnaire );
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'personnepcg66', "Personnespcgs66::{$this->action}" ).' géré par '.$chargeDossier
	);

	echo $this->Xform->create( 'Personnepcg66', array( 'id' => 'personnepcg66form' ) );
	if( Set::check( $this->request->data, 'Personnepcg66.id' ) ){
		echo $this->Xform->input( 'Personnepcg66.id', array( 'type' => 'hidden' ) );
	}

	echo $this->Default2->subform(
		array(
			'Personnepcg66.dossierpcg66_id' => array( 'type' => 'hidden', 'value' => $dossierpcg66_id ),
			'Personnepcg66.personne_id' => array( 'type' => 'select', 'empty' => true, 'options' => $personnes, 'required' => true ),
			'Personnepcg66.user_id' => array( 'type' => 'hidden', 'value' => $userConnected ),
		),
		array(
			'options' => $options
		)
	);
?>
<table class="noborder" id="infosPdo">
	<tr>
		<td class="mediumSize noborder">
			<?php $validationErrorPersonnepcg66SituationpdoSituationpdo = ( isset( $this->validationErrors['Personnepcg66']['Situationpdo.Situationpdo'] ) && !empty( $this->validationErrors['Personnepcg66']['Situationpdo.Situationpdo'] ) );?>
			<div class="input checkboxes<?php if( $validationErrorPersonnepcg66SituationpdoSituationpdo ) { echo ' error'; }?>">
				<fieldset>
					<legend><?php echo required( $this->Default2->label( 'Situationpdo.Situationpdo', array( 'domain' => 'personnepcg66' ) ) );?></legend>
					<?php
						if( $validationErrorPersonnepcg66SituationpdoSituationpdo ) {
							echo $this->Xform->errorDiv($this->validationErrors['Personnepcg66']['Situationpdo.Situationpdo']);
						}
						echo $this->Xform->input( 'Situationpdo.Situationpdo', array( 'type' => 'select', 'label' => required( 'Motif de la décision' ), 'multiple' => 'checkbox' , 'options' => $situationlist, 'fieldset' => false ) );
					?>
				</fieldset>
			</div>
		</td>
		<td class="mediumSize noborder">
			<?php $validationErrorPersonnepcg66SituationpdoStatutpdo = ( isset( $this->validationErrors['Personnepcg66']['Statutpdo.Statutpdo'] ) && !empty( $this->validationErrors['Personnepcg66']['Statutpdo.Statutpdo'] ) );?>
			<div class="input checkboxes<?php if( $validationErrorPersonnepcg66SituationpdoStatutpdo ) { echo ' error'; }?>">
				<fieldset>
					<legend><?php echo required( $this->Default2->label( 'Statutpdo.Statutpdo', array( 'domain' => 'personnepcg66' ) ) );?></legend>
					<?php
						if ( isset( $this->validationErrors['Personnepcg66']['Statutpdo.Statutpdo'] ) && !empty( $this->validationErrors['Personnepcg66']['Statutpdo.Statutpdo'] ) ) {
							echo $this->Xform->errorDiv($this->validationErrors['Personnepcg66']['Statutpdo.Statutpdo']);
						}
						echo $this->Xform->input( 'Statutpdo.Statutpdo', array( 'type' => 'select', 'label' => required( 'Statut de la personne' ), 'multiple' => 'checkbox' , 'options' => $statutlist, 'fieldset' => false  ) );
					?>
				</fieldset>
			</div>
		</td>
	</tr>
</table>
<?php
	echo $this->Xhtml->tag(
		'p',
		'Catégories : '
	);

	$selected = null;
	if( $this->action == 'edit' ){
		$selected = preg_replace( '/^[^_]+_/', '', $this->request->data['Personnepcg66']['categoriegeneral'] ).'_'.$this->request->data['Personnepcg66']['categoriedetail'];
	}
?>
<fieldset>
	<legend><?php echo __d( 'dsps', 'Libpersonnespcgs66.categoriegeneral' ); ?></legend>
<?php
	echo $this->Xform->input(
			'Personnepcg66.categoriegeneral',
			array(
				'type' => 'hidden',
				'value' => Hash::get( $this->request->data, 'Personnepcg66.categoriegeneral' )
			)
		) .
		$this->Xform->fieldValue(
			'Personnepcg66.categoriegeneral',
			value(
				$options['Coderomesecteurdsp66'],
				Hash::get( $this->request->data, 'Personnepcg66.categoriegeneral' )
			)
		)
	;

	echo $this->Xform->input(
			'Personnepcg66.categoriedetail',
			array(
				'type' => 'hidden',
				'value' => Hash::get( $this->request->data, 'Personnepcg66.categoriedetail' )
			)
		) .
		$this->Xform->fieldValue(
			'Personnepcg66.categoriedetail',
			value(
				$options['Coderomemetierdsp66'],
				Hash::get( $this->request->data, 'Personnepcg66.categoriegeneral' )
				. '_'
				. Hash::get( $this->request->data, 'Personnepcg66.categoriedetail' )
			)
		)
	;

	echo '</fieldset>';

	echo $this->Romev3->fieldset( 'Categorieromev3', array( 'options' => $options ) );

	echo "<div class='submit'>";
		echo $this->Form->submit( 'Enregistrer', array( 'div'=>false ) );
		echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div'=>false ) );
	echo "</div>";

	echo $this->Form->end();
	echo $this->Observer->disableFormOnSubmit( 'personnepcg66form' );
?>
