<?php $this->start( 'custom_search_filters' );?>
<?php
	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __d( $this->request->params['controller'], 'Search.Contratinsertion.df_ci' ) )
		.$this->Xform->input( 'Search.Contratinsertion.df_ci', array( 'type' => 'hidden', 'value' => true ) )
		.$this->Xform->input( 'Search.Contratinsertion.df_ci_from', array( 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 3, 'minYear' => 2009, 'domain' => $this->request->params['controller'] ) )
		.$this->Xform->input( 'Search.Contratinsertion.df_ci_to', array( 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 3, 'minYear' => 2009, 'domain' => $this->request->params['controller'] ) )
	);
?>
<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'modelName' => 'Orientstruct',
			'beforeSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);

	if( isset( $results ) ) {
		echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => 'return toutCocher();' ) );
		echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => 'return toutDecocher();' ) );
	}
?>