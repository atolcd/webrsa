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
		dependantSelect( 'ContratinsertionReferentId', 'ContratinsertionStructurereferenteId' );
	});
</script>

<script type="text/javascript">
	function checkDatesToRefresh() {
		if( ( $F( 'ContratinsertionDdCiMonth' ) ) && ( $F( 'ContratinsertionDdCiYear' ) ) && ( $F( 'ContratinsertionDureeEngag' ) ) ) {
			setDateIntervalCer( 'ContratinsertionDdCi', 'ContratinsertionDfCi', $F( 'ContratinsertionDureeEngag' ), false );
		}
	}

	document.observe( "dom:loaded", function() {
		new MaskedInput( '#ContratinsertionDureeEngag', '9?9' );

		Event.observe( $( 'ContratinsertionDdCiDay' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'ContratinsertionDdCiMonth' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'ContratinsertionDdCiYear' ), 'change', function() {
			checkDatesToRefresh();
		} );

		Event.observe( $( 'ContratinsertionDureeEngag' ), 'change', function() {
			checkDatesToRefresh();
		} );
	} );

</script>
<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		<?php
		$ref_id = Set::extract( $this->request->data, 'Contratinsertion.referent_id' );
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'StructurereferenteRef',
					'url' => array(
						'action' => 'ajaxstruct',
						Set::extract( $this->request->data, 'Contratinsertion.structurereferente_id' )
					)
				)
			).';';
			echo $this->Ajax->remoteFunction(
				array(
					'update' => 'ReferentRef',
					'url' => array(
						'action' => 'ajaxref',
						Set::extract( $this->request->data, 'Contratinsertion.referent_id' )
					)
				)
			).';';
		?>
	} );
</script>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Form->create( 'Contratinsertion', array( 'type' => 'post', 'id' => 'testform', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Contratinsertion.id', array( 'type' => 'hidden', 'value' => '' ) );

		echo $this->Form->input( 'Contratinsertion.personne_id', array( 'type' => 'hidden', 'value' => Set::classicExtract( $personne, 'Personne.id' ) ) );
		echo $this->Form->input( 'Contratinsertion.rg_ci', array( 'type' => 'hidden') );
		echo '</div>';
	}
	else {
		echo $this->Form->create( 'Contratinsertion', array( 'type' => 'post', 'id' => 'testform', 'novalidate' => true ) );
		echo '<div>';
		echo $this->Form->input( 'Contratinsertion.id', array( 'type' => 'hidden' ) );

		echo $this->Form->input( 'Contratinsertion.personne_id', array( 'type' => 'hidden', 'value' => Set::classicExtract( $personne, 'Personne.id' ) ) );
		echo $this->Form->input( 'Contratinsertion.decision_ci', array( 'type' => 'hidden' ) );
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
				<?php echo $this->Xform->input( 'Contratinsertion.structurereferente_id', array( 'label' => false, 'type' => 'select', 'options' => $structures, 'selected' => $struct_id, 'empty' => true ) );?>
				<?php echo $this->Ajax->observeField( 'ContratinsertionStructurereferenteId', array( 'update' => 'StructurereferenteRef', 'url' => array( 'action' => 'ajaxstruct' ) ) ); ?>
			</td>
			<td class="noborder">
				<strong>Nom du référent unique :</strong>
				<?php echo $this->Xform->input( 'Contratinsertion.referent_id', array('label' => false, 'type' => 'select', 'options' => $referents, 'empty' => true, 'selected' => $struct_id.'_'.$referent_id ) );?>
				<?php echo $this->Ajax->observeField( 'ContratinsertionReferentId', array( 'update' => 'ReferentRef', 'url' => array( 'action' => 'ajaxref' ) ) ); ?>
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
				$this->Xform->label( __d( 'contratinsertion', 'Contratinsertion.num_contrat' ) ).
				'Avenant',
				array(
					'class' => 'input select'
				)
			);
			echo $this->Xform->input( 'Contratinsertion.num_contrat', array( 'type' => 'hidden', 'value' => $tc ) );
		}
		else {
			echo $this->Xform->input( 'Contratinsertion.num_contrat', array( 'label' => 'Type de contrat' , 'type' => 'select', 'options' => $options['num_contrat'], 'empty' => true, 'value' => $tc ) );
		}
	?>

	<table class="nbrCi wide noborder">
		<tr class="nbrCi">
			<td class="noborder">Nombre de renouvellements </td>
			<td class="noborder"> <?php echo $nbrCi;?> </td>
		</tr>
	</table>

	<?php echo $this->Xform->input( 'Contratinsertion.dd_ci', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.dd_ci' ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+3, 'minYear'=>date('Y')-10 , 'empty' => false ) );?>
	<?php echo $this->Xform->input( 'Contratinsertion.duree_engag', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.duree_engag' ), 'type' => 'text' ) );?>
	<?php echo $this->Xform->input( 'Contratinsertion.df_ci', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.df_ci' ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+3, 'minYear'=>date('Y')-10, 'empty' => true ) ) ;?>

</fieldset>
	<?php echo $this->Xform->input( 'Contratinsertion.date_saisi_ci', array( 'label' => __d( 'contratinsertion', 'Contratinsertion.date_saisi_ci' ), 'type' => 'hidden'  ) ) ;?>
</fieldset>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>