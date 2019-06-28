<?php $this->start( 'custom_search_filters' );?>
<fieldset>
	<legend><?php echo __m( 'Search.Rendezvous' ); ?></legend>
	<?php
		// FIXME: fieldset
		echo $this->Form->input( 'Search.Rendezvous.statutrdv_id', array( 'label' => __m( 'Search.Rendezvous.statutrdv_id' ), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $options['Rendezvous']['statutrdv_id'], 'empty' => false ) );
		echo $this->Allocataires->communautesr( 'Rendezvous', array( 'options' => array( 'Search' => $options ), 'hide' => false ) );
		echo $this->Form->input( 'Search.Rendezvous.structurereferente_id', array( 'label' => __m( 'Search.Rendezvous.structurereferente_id' ), 'type' => 'select', 'options' => $options['PersonneReferent']['structurereferente_id'], 'empty' => true ) );
		echo $this->Form->input( 'Search.Rendezvous.referent_id', array( 'label' => __m( 'Search.Rendezvous.referent_id' ), 'type' => 'select', 'options' => $options['PersonneReferent']['referent_id'], 'empty' => true ) );
		echo $this->Form->input( 'Search.Rendezvous.permanence_id', array( 'label' => __m( 'Search.Rendezvous.permanence_id' ), 'type' => 'select', 'options' => $options['Rendezvous']['permanence_id'], 'empty' => true ) );
		echo $this->Form->input( 'Search.Rendezvous.typerdv_id', array( 'label' => __m( 'Search.Rendezvous.typerdv_id' ), 'type' => 'select', 'options' => $options['Rendezvous']['typerdv_id'], 'empty' => true ) );

		// Thématiques du RDV
		if( Configure::read( 'Rendezvous.useThematique' ) ) {
			if( isset( $options['Rendezvous']['thematiquerdv_id'] ) && !empty( $options['Rendezvous']['thematiquerdv_id'] ) ) {
				foreach( $options['Rendezvous']['thematiquerdv_id'] as $typerdv_id => $thematiques ) {
					$input = $this->Xform->input(
						'Search.Rendezvous.thematiquerdv_id',
						array(
							'type' => 'select',
							'multiple' => 'checkbox',
							'options' => $thematiques,
							'label' => __m( 'Search.Rendezvous.thematiquerdv_id' )
						)
					);
					echo $this->Xhtml->tag( 'fieldset', $input, array( 'id' => "SearchRendezvousThematiquerdvId{$typerdv_id}", 'class' => 'invisible' ) );
				}
			}
		}

		echo $this->SearchForm->dateRange( 'Search.Rendezvous.daterdv', array(
			'domain' => 'rendezvous', // FIXME
			'minYear_from' => 2009,
			'minYear_to' => 2009,
			'maxYear_from' => date( 'Y' ) + 1,
			'maxYear_to' => date( 'Y' ) + 1,
		) );

		echo $this->Form->input('Search.Rendezvous.arevoirle', array( 'label' => __d( 'rendezvous', 'Rendezvous.arevoirle' ), 'type' => 'date', 'dateFormat' => 'MY', 'empty' => true, 'minYear' => date( 'Y' ) - 1, 'maxYear' => date( 'Y' ) + 3 ) );
	?>
</fieldset>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>
<script type="text/javascript">
	// TODO
	document.observe("dom:loaded", function() {
		dependantSelect( 'SearchRendezvousReferentId', 'SearchRendezvousStructurereferenteId' );

		<?php if( Configure::read( 'Rendezvous.useThematique' ) ) :?>
			<?php if( isset( $options['Rendezvous']['thematiquerdv_id'] ) && !empty( $options['Rendezvous']['thematiquerdv_id'] ) ):?>
				<?php foreach( $options['Rendezvous']['thematiquerdv_id'] as $typerdv_id => $thematiques ):?>
					observeDisableFieldsetOnValue(
						'SearchRendezvousTyperdvId',
						'SearchRendezvousThematiquerdvId<?php echo $typerdv_id;?>',
						[ '<?php echo $typerdv_id;?>' ],
						false,
						true
					);
				<?php endforeach;?>
			<?php endif;?>
		<?php endif;?>
	});
</script>