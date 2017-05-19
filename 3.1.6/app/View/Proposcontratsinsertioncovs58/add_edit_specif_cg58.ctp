<?php
	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'un CER';
	}
	else {
		$this->pageTitle = 'Édition d\'un CER';
	}

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js', 'prototype.maskedinput.js' ) );
	}
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'Propocontratinsertioncov58ReferentId', 'Propocontratinsertioncov58StructurereferenteId' );
	});
</script>

<script type="text/javascript">
	function checkDatesToRefresh() {
		if( ( $F( 'Propocontratinsertioncov58DdCiMonth' ) ) && ( $F( 'Propocontratinsertioncov58DdCiYear' ) ) && ( $F( 'Propocontratinsertioncov58DureeEngag' ) ) ) {
			setDateIntervalCer( 'Propocontratinsertioncov58DdCi', 'Propocontratinsertioncov58DfCi', $F( 'Propocontratinsertioncov58DureeEngag' ), false );
		}
	}

	document.observe( "dom:loaded", function() {
		new MaskedInput( '#Propocontratinsertioncov58DureeEngag', '9?9' );

		Event.observe( $( 'Propocontratinsertioncov58DdCiDay' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'Propocontratinsertioncov58DdCiMonth' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'Propocontratinsertioncov58DdCiYear' ), 'change', function() {
			checkDatesToRefresh();
		} );

		Event.observe( $( 'Propocontratinsertioncov58DureeEngag' ), 'change', function() {
			checkDatesToRefresh();
		} );
	});

</script>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {

		<?php
		$ref_id = Set::extract( $this->request->data, 'Propocontratinsertioncov58.referent_id' );
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'StructurereferenteRef',
					'url' => array(
						'action' => 'ajaxstruct',
						Set::extract( $this->request->data, 'Propocontratinsertioncov58.structurereferente_id' )
					)
				)
			).';';
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'ReferentRef',
					'url' => array(
						'action' => 'ajaxref',
						Set::extract( $this->request->data, 'Propocontratinsertioncov58.referent_id' )
					)
				)
			).';';
		?>
	} );
</script>

<h1><?php echo $this->pageTitle;?></h1>
<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Propocontratinsertioncov58', array( 'type' => 'post', 'id' => 'testform' ) );
		echo '<div>';
		echo $this->Form->input( 'Propocontratinsertioncov58.id', array( 'type' => 'hidden', 'value' => '' ) );

		echo $this->Form->input( 'Propocontratinsertioncov58.personne_id', array( 'type' => 'hidden', 'value' => Set::classicExtract( $personne, 'Personne.id' ) ) );
		echo $this->Form->input( 'Propocontratinsertioncov58.rg_ci', array( 'type' => 'hidden'/*, 'value' => '' */) );
		if ( isset( $avenant_id ) && !empty( $avenant_id ) ) {
			echo $this->Form->input( 'Propocontratinsertioncov58.avenant_id', array( 'type' => 'hidden', 'value' => $avenant_id ) );
		}
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Propocontratinsertioncov58', array( 'type' => 'post', 'id' => 'testform' ) );
		echo '<div>';
		echo $this->Form->input( 'Propocontratinsertioncov58.id', array( 'type' => 'hidden' ) );

		echo $this->Form->input( 'Propocontratinsertioncov58.personne_id', array( 'type' => 'hidden', 'value' => Set::classicExtract( $personne, 'Personne.id' ) ) );

		echo $this->Form->input( 'Propocontratinsertioncov58.dossiercov58_id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
?>
<fieldset>

<fieldset>
	<legend>RÉFÉRENT UNIQUE</legend>
	<table class="wide noborder">
		<tr>
			<td class="noborder">
				<strong>Organisme chargé de l'instruction du dossier :</strong>
				<?php echo $this->Xform->input( 'Propocontratinsertioncov58.structurereferente_id', array( 'label' => false, 'type' => 'select', 'options' => $structures, 'selected' => $struct_id, 'empty' => true ) );?>
				<?php echo $this->Ajax->observeField( 'Propocontratinsertioncov58StructurereferenteId', array( 'update' => 'StructurereferenteRef', 'url' => array( 'action' => 'ajaxstruct' ) ) ); ?>
			</td>
			<td class="noborder">
				<strong>Nom du référent unique :</strong>
				<?php echo $this->Xform->input( 'Propocontratinsertioncov58.referent_id', array('label' => false, 'type' => 'select', 'options' => $referents, 'empty' => true, 'selected' => $struct_id.'_'.$referent_id ) );?>
				<?php echo $this->Ajax->observeField( 'Propocontratinsertioncov58ReferentId', array( 'update' => 'ReferentRef', 'url' => array( 'action' => 'ajaxref' ) ) ); ?>
			</td>
		</tr>
		<tr>
			<td class="wide noborder"><div id="StructurereferenteRef"></div></td>
			<td class="wide noborder"><div id="ReferentRef"></div></td>
		</tr>
	</table>
</fieldset>

<fieldset>
	<legend>CARACTÉRISTIQUES DU PRÉSENT CONTRAT</legend>
	<?php
		if ( isset( $avenant_id ) && !empty( $avenant_id ) ) {
			echo $this->Xhtml->tag(
				'div',
				$this->Xform->label( __d( 'contratsinsertion', 'Propocontratinsertioncov58.num_contrat' ) ).
				'Avenant',
				array(
					'class' => 'input select'
				)
			);
			echo $this->Xform->input( 'Propocontratinsertioncov58.num_contrat', array( 'type' => 'hidden', 'value' => $tc ) );
		}
		else {
			echo $this->Xform->input( 'Propocontratinsertioncov58.num_contrat', array( 'label' => 'Type de contrat' , 'type' => 'select', 'options' => $options['num_contrat'], 'empty' => true, 'value' => $tc ) );
		}
	?>

	<table class="nbrCi wide noborder">
		<tr class="nbrCi">
			<td class="noborder">Nombre de renouvellements </td>
			<td class="noborder"> <?php echo $nbrCi;?> </td>
		</tr>
	</table>

	<?php echo $this->Xform->input( 'Propocontratinsertioncov58.dd_ci', array( 'label' => __d( 'propocontratinsertioncov58', 'Propocontratinsertioncov58.dd_ci' ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+3, 'minYear'=>date('Y')-2 , 'empty' => false ) );?>
	<?php echo $this->Xform->input( 'Propocontratinsertioncov58.duree_engag', array( 'label' => __d( 'propocontratinsertioncov58', 'Propocontratinsertioncov58.duree_engag' ), 'type' => 'text' ) );?>
	<?php echo $this->Xform->input( 'Propocontratinsertioncov58.df_ci', array( 'label' => __d( 'propocontratinsertioncov58', 'Propocontratinsertioncov58.df_ci' ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+3, 'minYear'=>date('Y')-2 , 'empty' => true ) ) ;?>

</fieldset>
	<?php echo $this->Xform->input( 'Propocontratinsertioncov58.datedemande', array( 'label' => __d( 'propocontratinsertioncov58', 'Propocontratinsertioncov58.date_saisi_ci' ), 'type' => 'hidden' ) ) ;?>
</fieldset>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>