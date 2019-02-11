<?php
	if( $this->action == 'attdecisioncpdv' ) {
		$title_for_layout = 'Décision du Référent';
	}
	else {
		$title_for_layout = 'Décision du Responsable';
	}
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}
?>

<?php
	// FIXME: liste de titres depuis le contrôleur
	echo $this->Html->tag( 'h1', $title_for_layout );
?>
<br />
<div id="tabbedWrapper" class="tabs">
	<div id="decisioncg">
		<h2 class="title"><?php echo $title_for_layout;?></h2>
		<?php
			echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'histochoixcer93' ) ) );

			echo $this->Xform->inputs(
				array(
					'fieldset' => false,
					'legend' => false,
					'Histochoixcer93.id' => array( 'type' => 'hidden' ),
					'Histochoixcer93.cer93_id' => array( 'type' => 'hidden' ),
					'Histochoixcer93.user_id' => array( 'type' => 'hidden' ),
					'Histochoixcer93.formeci' => array( 'type' => 'radio', 'options' => $options['Cer93']['formeci'] ),
					'Histochoixcer93.etape' => array( 'type' => 'hidden' )
				)
			);

			echo '<fieldset><legend>Commentaires</legend>';
			echo $this->Checkboxes->inputs(
				'Commentairenormecer93.Commentairenormecer93.%d',
				array(
					'fk_field' => 'commentairenormecer93_id',
					'autre_field' => 'commentaireautre',
					'autres_type' => 'textarea',
					'options' => $options['Commentairenormecer93']['commentairenormecer93_id'],
					'autres_ids' => (array)$commentairenormecer93_isautre_id
				)
			);
			echo '</fieldset>';
			echo $this->Xform->inputs(
				array(
					'fieldset' => false,
					'legend' => false,
					'Histochoixcer93.datechoix' => array( 'type' => 'date', 'dateFormat' => 'DMY' )
				)
			);

			if( $this->action == 'attdecisioncg' ) {
				echo $this->Xform->input( 'Histochoixcer93.isrejet', array( 'type' => 'checkbox' ) );
			}
		?>
		<?php
			echo $this->Html->tag(
				'div',
				$this->Xform->button( 'Enregistrer', array( 'type' => 'submit' ) )
				.$this->Xform->button( 'Annuler', array( 'type' => 'submit', 'name' => 'Cancel' ) ),
				array( 'class' => 'submit noprint' )
			);

			echo $this->Xform->end();
		?>
	</div>
	<div id="cerview">
		<h2 class="title">Visualisation du CER</h2>
		<?php
			require  dirname( __FILE__ ).'/../Cers93/_view.ctp' ;
		?>
	</div>
</div>
<script type="text/javascript">
	makeTabbed( 'tabbedWrapper', 2 );
</script>