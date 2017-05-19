<?php
	$title_for_layout = 'Modification du CER';
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
		echo $this->Html->script( 'prototype.livepipe.js' );
		echo $this->Html->script( 'prototype.tabs.js' );
	}

	echo $this->Html->tag( 'h1', $title_for_layout );
?>
<br />
<div id="tabbedWrapper" class="tabs">
	<div id="modification">
		<h2 class="title"><?php echo $title_for_layout;?></h2>
		<?php
			echo $this->Xform->getExtraValidationErrorMessages();

			echo $this->Xform->create( null, array( 'inputDefaults' => array( 'domain' => 'contratinsertion' ), 'id' => 'contratinsertion' ) );

			echo $this->Xform->inputs(
				array(
					'fieldset' => false,
					'legend' => false,
					'Contratinsertion.id',
					'Cer93.id',
					'Cer93.duree' => array( 'legend' => required( 'Ce contrat est proposé pour une durée de ' ), 'domain' => 'cer93', 'type' => 'radio', 'options' => $options['Cer93']['duree'] ),
					'Contratinsertion.dd_ci' => array( 'domain' => 'contratinsertion', 'type' => 'date', 'empty' => true, 'dateFormat' => 'DMY', 'required' => true ),
					'Contratinsertion.df_ci' => array( 'domain' => 'contratinsertion','type' => 'date', 'empty' => true, 'dateFormat' => 'DMY', 'required' => true )
				)
			);

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
		<?php require  dirname( __FILE__ ).'/_view.ctp' ; ?>
	</div>
</div>

<script type="text/javascript">
    function checkDatesToRefresh() {
        if( ( $F( 'ContratinsertionDdCiMonth' ) ) && ( $F( 'ContratinsertionDdCiYear' ) ) && ( radioValue( 'contratinsertion', 'data[Cer93][duree]' ) !== undefined ) ) {
            setDateIntervalCer( 'ContratinsertionDdCi', 'ContratinsertionDfCi', radioValue( 'contratinsertion', 'data[Cer93][duree]' ), false );
        }
    }

    document.observe( "dom:loaded", function() {
        Event.observe( $( 'ContratinsertionDdCiDay' ), 'change', function() {
            checkDatesToRefresh();
        } );
        Event.observe( $( 'ContratinsertionDdCiMonth' ), 'change', function() {
            checkDatesToRefresh();
        } );
        Event.observe( $( 'ContratinsertionDdCiYear' ), 'change', function() {
            checkDatesToRefresh();
        } );

		<?php foreach( $options['Cer93']['duree'] as $duree ): ?>
        Event.observe( $( 'Cer93Duree<?php echo str_replace( ' mois', '' ,$duree );?>' ), 'change', function() {
            checkDatesToRefresh();
        } );
		<?php endforeach;?>

		makeTabbed( 'tabbedWrapper', 2 );
	} );
</script>
<?php echo $this->Observer->disableFormOnSubmit( 'contratinsertion' );?>