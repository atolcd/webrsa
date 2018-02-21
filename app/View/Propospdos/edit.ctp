<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'une PDO';
	}
	else {
		$this->pageTitle = 'Édition de la PDO';
	}
?>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	if( $this->action == 'add' ) {
		echo $this->Xform->create( 'Propopdo', array( 'id' => 'propopdoform', 'type' => 'post' ) );
	}
	else {
		echo $this->Xform->create( 'Propopdo', array( 'id' => 'propopdoform', 'type' => 'post' ) );
		echo '<div>';
			echo $this->Xform->input( 'Propopdo.id', array( 'type' => 'hidden' ) );
			echo $this->Xform->input( 'Decisionpropopdo.id', array( 'type' => 'hidden' ) );
			echo $this->Xform->input( 'Decisionpropopdo.propopdo_id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	echo '<div>';
	echo $this->Xform->input( 'Propopdo.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );

	echo '</div>';
?>

<div class="aere">
	<fieldset>
		<legend>Détails PDO</legend>
		<?php
			echo $this->Xform->input( 'Propopdo.structurereferente_id', array( 'label' =>  $this->Xform->required( __( 'Structure gérant la PDO' ) ), 'type' => 'select', 'options' => $options['Propopdo']['structurereferente_id'], 'empty' => true ) );
			echo $this->Xform->input( 'Propopdo.typepdo_id', array( 'label' =>  $this->Xform->required( __d( 'propopdo', 'Propopdo.typepdo_id' ) ), 'type' => 'select', 'options' => $options['Propopdo']['typepdo_id'], 'empty' => true ) );
			echo $this->Xform->input( 'Propopdo.datereceptionpdo', array( 'label' =>  ( __( 'Date de réception de la PDO' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009, 'empty' => true ) );
			echo $this->Xform->input( 'Propopdo.originepdo_id', array( 'label' =>  $this->Xform->required( __( 'Origine' ) ), 'type' => 'select', 'options' => $options['Propopdo']['originepdo_id'], 'empty' => true ) );
			echo $this->Xform->input( 'Propopdo.decision', array( 'type' => 'hidden', 'value' => '1' ) )
				.$this->Xform->input( 'Decisionpropopdo.decisionpdo_id', array( 'label' =>  $this->Xform->required( __( 'Décision du Conseil Général' ) ), 'type' => 'select', 'options' => $options['Decisionpropopdo']['decisionpdo_id'], 'empty' => true ) );
			echo $this->Xform->input( 'Propopdo.motifpdo', array( 'label' =>  ( __( 'Motif de la décision' ) ), 'type' => 'select', 'options' => $options['Propopdo']['motifpdo'], 'empty' => true ) );
			echo $this->Xform->input( 'Propopdo.iscomplet', array( 'label' =>  __( 'Etat du dossier' ),  'type' => 'radio', 'options' => $options['Propopdo']['iscomplet'] ) );

			echo $this->Xform->input( 'Decisionpropopdo.datedecisionpdo', array( 'label' =>  ( __( 'Date de décision CD' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009, 'empty' => true ) );

			echo $this->Xform->input( 'Decisionpropopdo.commentairepdo', array( 'label' =>  'Observations', 'type' => 'textarea', 'empty' => true ) );

			echo $this->Default->view(
				$dossier,
				array(
					'Dossier.fonorg',
					'Suiviinstruction.typeserins',
				),
				array(
					'widget' => 'table',
					'id' => 'dossierInfosOrganisme',
					'options' => $options
				)
			);
		?>
		<table class="noborder" id="infosPdo">
			<tr>
				<td class="noborder">
					<?php
						echo $this->Xform->input( 'Situationpdo.Situationpdo', array( 'type' => 'select', 'label' => ( Configure::read( 'Cg.departement' ) == 58 ? 'Motifs de la PDO' : 'Situation de la PDO' ), 'multiple' => 'checkbox' , 'options' => $options['Situationpdo']['Situationpdo'] ) );
					?>
				</td>
				<td class="noborder">
					<?php
						echo $this->Xform->input( 'Statutpdo.Statutpdo', array( 'type' => 'select', 'label' => ( Configure::read( 'Cg.departement' ) == 58 ? 'Situation du demandeur' : 'Statut de la PDO' ), 'multiple' => 'checkbox' , 'options' => $options['Statutpdo']['Statutpdo'] ) );
					?>
				</td>

			</tr>
		</table>
	</fieldset>
	<?php if( $this->action == 'add' ):?>
	<?php echo $this->Form->input( 'Propopdo.haspiece', array( 'type' => 'hidden', 'value' => '0' ) );?>
	<?php endif;?>
	<?php if( $this->action == 'edit' ):?>
		<fieldset>
			<legend><?php echo required( $this->Default2->label( 'Propopdo.haspiece' ) );?></legend>
			<div style='display: none;'>
			<?php echo $this->Form->input( 'Propopdo.haspiece', array( 'type' => 'radio', 'options' => $options['Propopdo']['haspiece'], 'legend' => false, 'fieldset' => false, 'value' => 1 ) );?>
			</div>
			<fieldset id="filecontainer-piece" class="noborder invisible">
				<?php
					echo $this->Fileuploader->create(
                        $fichiers,
                        array( 'action' => 'ajaxfileupload' )
                    );

                    echo $this->Fileuploader->results(
                        $fichiersEnBase
                    );
				?>
			</fieldset>
			<?php echo $this->Fileuploader->validation( 'propopdoform', 'Propopdo.haspiece', 'Pièce jointe' );?>
		</fieldset>
	<?php endif;?>

</div>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Xform->end();?>