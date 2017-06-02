<?php
	if( Configure::read( 'nom_form_ci_cg' ) == 'cg66' ){
		$this->pageTitle = 'Décision du CER '.Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci'), $forme_ci);
	}
	else{
		$this->pageTitle = 'Décision du CER';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>
<?php  echo $this->Form->create( 'Contratinsertion',array( 'novalidate' => true ) ); ?>
<fieldset>
	<legend> PARTIE RESERVEE AU DEPARTEMENT</legend>
		<?php
			echo $this->Form->input( 'Contratinsertion.id', array( 'type' => 'hidden' ) );
			echo $this->Form->input( 'Contratinsertion.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );

			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$formeci = Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci' ), $forme_ci );
				$ddci = date_short( $contratinsertion['Contratinsertion']['dd_ci'] );
				$dfci = date_short( $contratinsertion['Contratinsertion']['df_ci'] );
				$duree = Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.duree_engag' ), $duree_engag );
				$referent = $contratinsertion['Referent']['nom_complet'];
				$propodecision = Set::enum( $contratinsertion['Propodecisioncer66']['isvalidcer'], $options['Propodecisioncer66']['isvalidcer'] );
				$datevalidcer = date_short( $contratinsertion['Propodecisioncer66']['datevalidcer'] );

				echo $this->Xform->fieldValue( 'Contratinsertion.forme_ci', $formeci );
				echo $this->Xform->fieldValue( 'Contratinsertion.dd_ci', $ddci );
				echo $this->Xform->fieldValue( 'Contratinsertion.df_ci', $dfci );
				echo $this->Xform->fieldValue( 'Contratinsertion.duree_engag', $duree );
				echo $this->Xform->fieldValue( 'Referent.nom_complet', $referent );
// 						echo $this->Xform->fieldValue( 'Propodecisioncer66.isvalidcer', $propodecision );
// 						echo $this->Xform->fieldValue( 'Propodecisioncer66.datevalidcer', $datevalidcer );

				echo $this->Form->input( 'Contratinsertion.decision_ci', array( 'label' => 'Décision finale', 'type' => 'select', 'options' => $decision_ci ) );
				echo $this->Form->input( 'Contratinsertion.datedecision', array( 'label' => '', 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-3 , 'empty' => true)  );
				echo $this->Form->input( 'Contratinsertion.observ_ci', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.observ_ci' ), 'type' => 'textarea', 'rows' => 6, 'class' => 'aere')  );
			}
			else{
				echo 'CER '.Set::enum( Set::classicExtract( $contratinsertion, 'Contratinsertion.forme_ci' ), $forme_ci ).' du '.date_short( Set::classicExtract( $contratinsertion, 'Contratinsertion.dd_ci' ) ).' au '.date_short( Set::classicExtract( $contratinsertion, 'Contratinsertion.df_ci' ) );

				echo $this->Form->input( 'Contratinsertion.observ_ci', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.observ_ci' ), 'type' => 'textarea', 'rows' => 6, 'class' => 'aere')  );

				echo $this->Form->input( 'Contratinsertion.positioncer', array( 'type' => 'hidden' ) );

				echo $this->Form->input( 'Contratinsertion.decision_ci', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.decision_ci' ), 'type' => 'select', 'options' => $decision_ci ) );
				echo $this->Form->input( 'Contratinsertion.datevalidation_ci', array( 'label' => '', 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+2, 'minYear'=>date('Y')-3 , 'empty' => true)  );
			}
		?>
</fieldset>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>
<script>
	document.observe("dom:loaded", function() {

		observeDisableFieldsOnValue(
			'ContratinsertionDecisionCi',
			[
				'ContratinsertionDatedecisionDay',
				'ContratinsertionDatedecisionMonth',
				'ContratinsertionDatedecisionYear'
			],
			'E',
			true
		);

		observeDisableFieldsOnValue(
			'ContratinsertionDecisionCi',
			[
				'ContratinsertionDatevalidationCiDay',
				'ContratinsertionDatevalidationCiMonth',
				'ContratinsertionDatevalidationCiYear'
			],
			'V',
			false
		);
	});
</script>