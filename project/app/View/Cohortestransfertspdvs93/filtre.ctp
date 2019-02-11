<?php
	// Filtre
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<script type="text/javascript">
    document.observe("dom:loaded", function() {
        observeDisableFieldsetOnCheckbox( 'SearchTransfertpdv93Created', $( 'SearchTransfertpdv93CreatedFromDay' ).up( 'fieldset' ), false );
    });
</script>
<?php
	echo '<ul class="actionMenu"><li>'.$this->Xhtml->link(
		$this->Xhtml->image(
			'icons/application_form_magnify.png',
			array( 'alt' => '' )
		).' Formulaire',
		'#',
		array( 'escape' => false, 'title' => 'Visibilité formulaire', 'onclick' => "var form = $$( 'form' ); form = form[0]; $( form ).toggle(); return false;" )
	).'</li></ul>';

	// Filtre
	echo $this->Form->create( null, array( 'type' => 'post', 'url' => array( 'controller' => $this->request->params['controller'], 'action' => $this->request->params['action'] ), 'id' => 'Search', 'class' => ( ( is_array( $this->request->data ) && !empty( $this->request->data ) && isset( $this->request->data['Search']['active'] ) ) ? 'folded' : 'unfolded' ), 'novalidate' => true ) );

	echo $this->Form->input( 'Search.active', array( 'type' => 'hidden', 'value' => true ) );

	echo $this->Search->blocAllocataire( array(), array(), 'Search' );
	echo $this->Search->toppersdrodevorsa( $options['toppersdrodevorsa'], 'Search.Calculdroitrsa.toppersdrodevorsa' );

	echo $this->Search->date( 'Search.Orientstruct.date_valid' );
	echo $this->Allocataires->blocAdresse( array( 'options' => $options ) );

	echo $this->Search->blocDossier( $options['etatdosrsa'], 'Search' );

    if( $this->action == 'transferes' ) {
        echo $this->Form->input( 'Search.Transfertpdv93.created', array( 'label' => 'Filtrer par dates de transfert', 'type' => 'checkbox' ) );
		echo '<fieldset><legend>Dates de transfert</legend>';
            $created_from = Set::check( $this->request->data, 'Search.Transfertpdv93.created_from' ) ? Set::extract( $this->request->data, 'Search.Transfertpdv93.created_from' ) : strtotime( '-1 week' );
            $created_to = Set::check( $this->request->data, 'Search.Transfertpdv93.created_to' ) ? Set::extract( $this->request->data, 'Search.Transfertpdv93.created_to' ) : strtotime( 'now' );
            echo $this->Form->input( 'Search.Transfertpdv93.created_from', array( 'label' => 'Du (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120, 'selected' => $created_from ) );
            echo $this->Form->input( 'Search.Transfertpdv93.created_to', array( 'label' => 'Au (inclus)', 'type' => 'date', 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ), 'minYear' => date( 'Y' ) - 120,  'maxYear' => date( 'Y' ) + 5, 'selected' => $created_to ) );
        echo '</fieldset>';
    }

	echo $this->Form->input( 'Search.Orientstruct.typeorient_id', array( 'label' => 'Type d\'orientation', 'type' => 'select', 'empty' => true, 'options' => $options['typesorients'] ) );

	echo $this->Search->referentParcours( $structuresreferentesparcours, $referentsparcours, 'Search' );
	echo $this->Search->paginationNombretotal( 'Search.Pagination.nombre_total' );
	echo $this->Search->observeDisableFormOnSubmit( 'Search' );

	echo $this->Html->tag( 'div', $this->Form->button( __( 'Search' ) ), array( 'class' => 'submit' ) );
	echo $this->Form->end();
?>