<?php
	$this->pageTitle =  __d( 'decisiondossierpcg66', "Decisionsdossierspcgs66::{$this->action}" );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>
	<?php echo $this->Form->create( 'Decisiondossierpcg66',array(  'id' => 'transmissionopdossierpcg66form' ) ); ?>

	<fieldset>
			<?php 
                echo $this->Form->input( 'Decisiondossierpcg66.id', array( 'type' => 'hidden' ) );
                echo $this->Form->input( 'Decisiondossierpcg66.dossierpcg66_id', array( 'type' => 'hidden', 'value' => $dossierpcg66_id ) );
                echo $this->Form->input( 'Decisiondossierpcg66.etatop', array(  'div' => false, 'legend' => required( __d( 'decisiondossierpcg66', 'Decisiondossierpcg66.etatop' ) ), 'type' => 'radio', 'options' => $options['Decisiondossierpcg66']['etatop'] )  ); 
            ?>
            <fieldset><legend><?php echo __d( 'orgtransmisdossierpcg66', 'Orgtransmisdossierpcg66.name' ); ?></legend>
                
            <?php
                echo $this->Form->input( 'Notificationdecisiondossierpcg66.Notificationdecisiondossierpcg66', array( 'type' => 'select', 'label' => false, 'multiple' => 'checkbox', 'empty' => false, 'options' => $listeOrgstransmisdossierspcgs66 ) );
            ?>
            </fieldset>

			<feildset id="etattransmission" class="noborder" >
				<?php
					echo $this->Default2->subform(
						array(
							'Decisiondossierpcg66.datetransmissionop' => array( 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>date('Y')-1, 'empty' => false )
						),
						array(
							'options' => $options
						)
					);
                    
                    
				?>
			</fieldset>
	</fieldset>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsetOnRadioValue(
			'transmissionopdossierpcg66form',
			'data[Decisiondossierpcg66][etatop]',
			$( 'etattransmission' ),
			'transmis',
			false,
			true
		);
	} );
</script>