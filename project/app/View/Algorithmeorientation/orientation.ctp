<?php if(!empty($bloquer)){
    echo '<p class="error">' . sprintf(__d('algorithmeorientation', 'Algorithmeorientation.bloque'), $bloquer) . '</p>';
} else {

?>
<div class="steps">
	<?php if(isset($pbAdresses)):?>
    <span class="steps-prog" style="width: 25%;"></span>
    <a class="step step-main step-passed" href="#">
        <span class="shape">
            <span class="number">1</span>
        </span>
        <span class="text"><?=__m('bandeau.recherche')?></span>
    </a>
    <a class="step step-optional step-active" href="#">
        <span class="shape"></span>
        <span class="text"><?=__m('bandeau.adresses')?></span>
    </a>
    <?php else:?>
	<span class="steps-prog" style="width: 0%;"></span>
    <a class="step step-main step-active" href="#">
        <span class="shape">
            <span class="number">1</span>
        </span>
        <span class="text"><?=__m('bandeau.recherche')?></span>
    </a>
    <a class="step step-optional" href="#">
        <span class="shape"></span>
        <span class="text"><?=__m('bandeau.adresses')?></span>
    </a>
    <?php endif;?>
	<a class="step step-main" href="#">
        <span class="shape">
            <span class="number">2</span>
        </span>
        <span class="text"><?=__m('bandeau.orientables')?></span>
    </a>
    <a class="step step-optional" href="#">
        <span class="shape"></span>
        <span class="text"><?=__m('bandeau.depassements')?></span>
    </a>
    <a class="step step-main" href="#">
        <span class="shape">
            <span class="number">3</span>
        </span>
        <span class="text"><?=__m('bandeau.simulation')?></span>
    </a>
</div>
<?php
    $controller = $this->params->controller;
    $action = $this->action;
    $formId = ucfirst($controller) . ucfirst($action) . 'Form';
    $availableDomains = WebrsaTranslator::domains();

    $domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
    $paramDate = array(
        'domain' => $domain,
        'minYear_from' => '2009',
        'maxYear_from' => date( 'Y' ) + 1,
        'minYear_to' => '2009',
        'maxYear_to' => date( 'Y' ) + 4
    );
    $paramAllocataire = array(
        'options' => $options,
        'prefix' => 'Search',
    );
    $dateRule = array(
        'date' => array(
            'rule' => array('date'),
            'message' => null,
            'required' => null,
            'allowEmpty' => true,
            'on' => null
        )
    );
    $this->start( 'custom_search_filters' );

    echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __m('parcours.search') )
		.$this->Default3->subform(
			array(
				'Search.Historiqueetatpe.identifiantpe' => array( /*'maxlength' => 11*/ ),
				'Search.Personne.has_contratinsertion' => array( 'empty' => true ),
				'Search.Personne.has_personne_referent' => array( 'empty' => true ),
				'Search.Personne.is_inscritpe' => array( 'empty' => true )
			),
			array( 'options' => array( 'Search' => $options ) )
		)
	);

    echo '<fieldset><legend>' . __m( 'Orientstruct.search' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Orientstruct.derniere' => array( 'type' => 'checkbox' )
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. $this->Default3->subform(
			array(
				'Search.Orientstruct.dernierevalid' => array( 'type' => 'checkbox' )
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. $this->SearchForm->dateRange( 'Search.Orientstruct.date_valid', $paramDate )
	;

    echo $this->Default3->subform(
        array(
            'Search.Orientstruct.origine' => array('empty' => true),
        ),
        array( 'options' => array( 'Search' => $options ) )
    );

	echo $this->Default3->subform(
			array(
				'Search.Orientstruct.typeorient_id' => array('empty' => true, 'required' => false),
			),
			array( 'options' => array( 'Search' => $options ) )
		);

	echo $this->Allocataires->communautesrSelect( 'Orientstruct', array( 'options' => array( 'Search' => $options ) ) );

	echo $this->Default3->subform(
			array(
				'Search.Orientstruct.structurereferente_id' => array('empty' => true, 'required' => false),
				'Search.Orientstruct.statut_orient' => array('empty' => true, 'required' => false)
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. '</fieldset>'
	;

    $this->end();

	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'paginate' => false,
			'exportcsv' => false,
            'noDisplay' => true
		)
	);

    if(isset($pbAdresses) && $pbAdresses != []){
        require_once('problemes_adresses.ctp');
    } else {
        if(!empty($resultats )) {
            $this->redirect('liste_orientables.ctp');

        } else if ($noresult){
            echo'<h2 class=center>'.__m('aucun_orientable').'</h2>';
        }

    }
}
?>
