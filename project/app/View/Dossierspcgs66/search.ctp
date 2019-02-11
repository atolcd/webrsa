<?php
	$departement = Configure::read( 'Cg.departement' );
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
	
	echo '<fieldset><legend>' . __m( 'Dossierpcg66.search' ) . '</legend>'
		. $this->Allocataires->SearchForm->dateRange( 'Search.Dossierpcg66.datereceptionpdo', $paramDate )
		. $this->Default3->subform(
			array(
				'Search.Dossierpcg66.originepdo_id' => array( 'empty' => true ),
				'Search.Dossierpcg66.typepdo_id' => array( 'empty' => true ),
				'Search.Dossierpcg66.orgpayeur' => array( 'empty' => true ),
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. $this->Xform->multipleCheckbox( 'Search.Dossierpcg66.poledossierpcg66_id', $options )
		. $this->Xform->multipleCheckbox( 'Search.Dossierpcg66.user_id', $options, 'divideInto3Columns' )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Dossierpcg66.dateaffectation', $paramDate )
		. $this->Xform->multipleCheckbox( 'Search.Dossierpcg66.etatdossierpcg', $options, 'divideInto2Columns' )
		. $this->Xform->multipleCheckbox( 'Search.Decisiondossierpcg66.org_id', $options, 'divideInto2Columns' )
		. $this->Xform->multipleCheckboxToutCocher( 'Search.Traitementpcg66.situationpdo_id', $options, 'divideInto2Columns' )
		. $this->Xform->multipleCheckboxToutCocher( 'Search.Traitementpcg66.statutpdo_id', $options, 'divideInto2Columns' )
		. $this->Default3->subform(
			array(
				'Search.Decisiondossierpcg66.useravistechnique_id' => array( 'empty' => true, 'options' => $options['Dossierpcg66']['user_id'] ),
				'Search.Decisiondossierpcg66.userproposition_id' => array( 'empty' => true, 'options' => $options['Dossierpcg66']['user_id'] ),
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. $this->Xform->multipleCheckbox( 'Search.Decisiondossierpcg66.decisionpdo_id', $options, 'divideInto2Columns' )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Decisiondossierpcg66.datevalidation', $paramDate )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Decisiondossierpcg66.datetransmissionop', $paramDate )
		. $this->Default3->subform(
			array(
				'Search.Decisiondossierpcg66.nbproposition',
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. '</fieldset>'
		. $this->Romev3->fieldset( 'Categorieromev3', array( 'options' => $options, 'prefix' => 'Search' ) )
	;

	$this->end();
	
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' )
		)
	);
?>

<div id="progressBarContainer123" style="display: none;">
	<div id="popups2" style="z-index: 1000;">
		<div id="popup_1">
			<div class="hideshow">
				<div class="fade" style="z-index: 31"></div>
				<div class="popup_block">
					<div class="popup">
						<a href="#" onclick="$('progressBarContainer123').hide(); return false;"><?php echo $this->Xhtml->image('icon_close.png', array('class' => 'cntrl', 'alt' => 'close')); ?></a>
						<div id="popup-content123"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$$('a.ajax_view_decisions').each(function(a){
		a.observe('click', function(event){
			event.preventDefault();
			new Ajax.Updater(
				'popup-content123',
				a.getAttribute('href'),
				{
					asynchronous:true,
					evalScripts:true,
					requestHeaders:['X-Update', 'popup-content123'],
					onSuccess: function(){
						$('progressBarContainer123').show();
					}
				}
			);
		});
	});
</script>