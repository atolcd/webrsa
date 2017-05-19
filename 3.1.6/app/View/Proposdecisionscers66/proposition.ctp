<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'propodecisioncer66', "Proposdecisionscers66::{$this->action}" )
	);

	echo $this->Xform->create( 'Propodecisioncer66', array( 'id' => 'propodecisioncer66form' ) );
	if( Set::check( $this->request->data, 'Propodecisioncer66.id' ) ){
		echo $this->Xform->input( 'Propodecisioncer66.id', array( 'type' => 'hidden' ) );
	}
	echo $this->Xform->input( 'Propodecisioncer66.contratinsertion_id', array( 'type' => 'hidden', 'value' => $contratinsertion_id ) );
?>

<fieldset>

	<?php
		$formeci = Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci' ), $forme_ci );
		$ddci = date_short( $contratinsertion['Contratinsertion']['dd_ci'] );
		$dfci = date_short( $contratinsertion['Contratinsertion']['df_ci'] );
		$duree = value( $duree_engag, Hash::get( $contratinsertion, 'Contratinsertion.duree_engag' ) );
		$referent = $contratinsertion['Referent']['nom_complet'];

		echo $this->Xform->fieldValue( 'Contratinsertion.forme_ci', $formeci );
		if( $formeci == 'Particulier' ) {
			$typeDemande = Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.type_demande' ), $options['Contratinsertion']['type_demande'] );
			echo $this->Xform->fieldValue( 'Contratinsertion.type_demande', $typeDemande );
		}
		echo $this->Xform->fieldValue( 'Contratinsertion.dd_ci', $ddci );
		echo $this->Xform->fieldValue( 'Contratinsertion.df_ci', $dfci );
		echo $this->Xform->fieldValue( 'Contratinsertion.duree_engag', $duree );
		echo $this->Xform->fieldValue( 'Referent.nom_complet', $referent );

		echo $this->Form->input( 'Propodecisioncer66.isvalidcer', array( 'legend' => required( __d( 'propodecisioncer66', 'Propodecisioncer66.isvalidcer' ) ), 'type' => 'radio', 'options' => $options['Propodecisioncer66']['isvalidcer'] ) );
		echo $this->Form->input( 'Propodecisioncer66.datevalidcer', array( 'label' => __d( 'propodecisioncer66', 'Propodecisioncer66.datevalidcer' ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-2 ) );
	?>
	<fieldset id="motifcer" class="invisible">
		<?php
			echo $this->Default2->subform(
				array(
					'Motifcernonvalid66.Motifcernonvalid66' => array( 'type' => 'select', 'label' => 'Motif de non validation', 'multiple' => 'checkbox', 'empty' => false, 'options' => $listMotifs ),
					'Propodecisioncer66.motifficheliaison' => array( 'required' => true, 'type' => 'textarea' ),
					'Propodecisioncer66.motifnotifnonvalid' => array( 'required' => true, 'type' => 'textarea' )
				),
				array(
					'options' => $options
				)
			);

			$formeContrat = Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci' );
			if( $formeContrat == 'C' ) {
				echo $this->Default2->subform(
					array(
						'Propodecisioncer66.nonvalidationparticulier' => array( 'type' => 'radio', 'options' => $options['Propodecisioncer66']['nonvalidationparticulier'] )
					),
					array(
						'options' => $options
					)
				);
			}
		?>
	</fieldset>
	<?php if( $formeContrat == 'C' ):?>
		<fieldset class="invisible">
			<?php
				echo $this->Default2->subform(
					array(
						'Propodecisioncer66.decisionfinale' => array( 'type' => 'radio', 'options' => $options['Propodecisioncer66']['decisionfinale'] )
					),
					array(
						'options' => $options
					)
				);
			?>
		</fieldset>
	<?php endif;?>
	<?php if( $formeContrat == 'S' ):?>
		<?php echo $this->Xform->input( 'Propodecisioncer66.decisionfinale', array( 'type' => 'hidden', 'value' => 'O' ) ); ?>
	<?php endif;?>

</fieldset>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		observeDisableFieldsetOnRadioValue(
			'propodecisioncer66form',
			'data[Propodecisioncer66][isvalidcer]',
			$( 'motifcer' ),
			['N'],
			false,
			true
		);
	});
</script>