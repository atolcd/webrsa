<?php
	$this->pageTitle =  __d( 'traitementpcg66', "Traitementspcgs66::{$this->action}" );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>

<h1><?php echo $this->pageTitle;?></h1>
	<?php echo $this->Form->create( 'Traitementpcg66',array(  'id' => 'envoicourrierform' ) ); ?>

	<fieldset>
        <?php 
            echo $this->Form->input( 'Traitementpcg66.id', array( 'type' => 'hidden' ) );
            echo $this->Form->input( 'Traitementpcg66.personnepcg66_id', array( 'type' => 'hidden', 'value' => $traitementpcg66['Traitementpcg66']['personnepcg66_id'] ) );

            echo $this->Default2->subform(
                array(
                    'Traitementpcg66.dateenvoicourrier' => array( 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => false )
                )
            );
        ?>
	</fieldset>

<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
	<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
</div>
<?php echo $this->Form->end();?>