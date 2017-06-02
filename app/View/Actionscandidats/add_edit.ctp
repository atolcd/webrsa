<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'actioncandidat', "Actionscandidats::{$this->action}" )
	);

	echo $this->Xform->create( null, array( 'id' => 'ActioncandidatAddEditForm' ) );

	if (isset($this->request->data['Actioncandidat']['id']))
		echo $this->Form->input('Actioncandidat.id', array('type'=>'hidden'));

	echo $this->Default2->subform(
		array(
			'Actioncandidat.name' => array( 'domain' => 'actioncandidat', 'required' => true ),
			'Actioncandidat.themecode' => array( 'domain' => 'actioncandidat', 'required' => true ),
			'Actioncandidat.codefamille' => array( 'domain' => 'actioncandidat', 'required' => true ),
			'Actioncandidat.numcodefamille' => array( 'domain' => 'actioncandidat', 'required' => true ),
			'Actioncandidat.naturecer' => array( 'domain' => 'actioncandidat', 'options' => $options['Actioncandidat']['naturecer'], 'empty' => true ),
		)
	);

	echo '<div class="notice">Dernier numéro du code famille trouvé : <strong id="lastcodefamille"></strong></div>';

	echo $this->Default2->subform(
		array(
			'Actioncandidat.hasfichecandidature' => array( 'domain' => 'actioncandidat', 'required' => true, 'type'=>'radio', 'options' => $options['Actioncandidat']['hasfichecandidature'] ),
			'Actioncandidat.actif' => array( 'label' => 'Active ?', 'type' => 'radio', 'options' => $options['Actioncandidat']['actif'] ),
			'Actioncandidat.modele_document' => array( 'label' => __d( 'actioncandidat', 'Actioncandidat.modele_document' ), 'required' => true, 'value' => isset( $this->request->data['Actioncandidat']['modele_document'] ) ? $this->request->data['Actioncandidat']['modele_document'] : 'fichecandidature' )
		)
	);
?>
<fieldset>
	<legend><?php echo required( $this->Default2->label( 'Actioncandidat.haspiecejointe' ) );?></legend>

	<?php echo $this->Form->input( 'Actioncandidat.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Actioncandidat']['haspiecejointe'], 'legend' => false, 'fieldset' => false ) );?>
	<fieldset id="filecontainer-piecejointe" class="noborder invisible">
		<?php
			echo $this->Fileuploader->create(
				$fichiers,
				array( 'action' => 'ajaxfileupload' )
			);

			if( !empty( $fichiersEnBase ) ) {
				echo $this->Fileuploader->results(
					$fichiersEnBase
				);
			}
		?>
	</fieldset>
</fieldset>

<script type="text/javascript">
	document.observe( "dom:loaded", function() {
		observeDisableFieldsetOnRadioValue(
			'ActioncandidatAddEditForm',
			'data[Actioncandidat][haspiecejointe]',
			$( 'filecontainer-piecejointe' ),
			'1',
			false,
			true
		);

		function lastCodeFamille() {
			if ($('ActioncandidatThemecode').getValue() && $('ActioncandidatCodefamille').getValue()) {
				new Ajax.Updater(
					'lastcodefamille',
					'<?php echo Router::url( array( "action" => "ajax_getLastNumcodefamille" ) ); ?>',
					{
						asynchronous:true,
						evalScripts:true,
						parameters:{
							'themecode': $('ActioncandidatThemecode').getValue(),
							'codefamille': $('ActioncandidatCodefamille').getValue()
						},
						requestHeaders:['X-Update', 'lastcodefamille']
					}
				);
			} else {
				$('lastcodefamille').innerHTML = '';
			}
		}

		$('ActioncandidatThemecode').observe('change', lastCodeFamille);
		$('ActioncandidatCodefamille').observe('change', lastCodeFamille);
	} );
</script>

<script type="text/javascript">
		document.observe( "dom:loaded", function() {
			observeDisableFieldsetOnCheckbox( 'ActioncandidatCorrespondantaction', 'filtre_referent', false );

			observeDisableFieldsOnRadioValue(
				'ActioncandidatAddEditForm',
				'data[Actioncandidat][hasfichecandidature]',
				[
					'ActioncandidatNbpostedispo',
					'ActioncandidatNbposterestant'
				],
				1,
				true
			);

			observeDisableFieldsetOnRadioValue(
				'ActioncandidatAddEditForm',
				'data[Actioncandidat][hasfichecandidature]',
				$( 'avecfichecandidature' ),
				1,
				false,
				true
			);

			observeDisableFieldsetOnRadioValue(
				'ActioncandidatAddEditForm',
				'data[Actioncandidat][typeaction]',
				$( 'nbposte' ),
				'poste',
				false,
				true
			);

			observeDisableFieldsetOnRadioValue(
				'ActioncandidatAddEditForm',
				'data[Actioncandidat][typeaction]',
				$( 'nbheure' ),
				'heure',
				false,
				true
			);
		} );
	</script>
	<fieldset class="invisible" id="avecfichecandidature">
			<?php
				echo $this->Default->subform(
					array(
						'Actioncandidat.correspondantaction' => array('type' => 'checkbox' )
					)
				);
			?>
			<fieldset class="col2" id="filtre_referent">
				<legend>Référent</legend>
				<?php
					echo $this->Default->subform(
						array(
							'Actioncandidat.referent_id' => array('domain' => 'actioncandidat', 'type'=>'select' ),
						),
						array(
							'options' => $options
						)
					);
				?>
			</fieldset>
		<?php

			echo $this->Default->subform(
				array(
					'Actioncandidat.chargeinsertion_id' => array( 'domain' => 'actioncandidat', 'required' => true, 'type' => 'select'),
					'Actioncandidat.secretaire_id' => array( 'domain' => 'actioncandidat', 'required' => true, 'type' => 'select'),
					'Actioncandidat.contractualisation' => array( 'domain' => 'actioncandidat', 'required' => true, 'type' => 'select'),
					'Actioncandidat.emailprestataire' => array( 'domain' => 'actioncandidat' ),
					'Actioncandidat.lieuaction' => array( 'domain' => 'actioncandidat', 'required' => true ),
					'Actioncandidat.cantonaction' => array( 'domain' => 'actioncandidat', 'required' => true, 'options' => $cantons )
				),
				array(
					'options' => $options
				)
			);

			echo $this->Default->subform(
				array(
					'Actioncandidat.ddaction' => array( 'domain' => 'actioncandidat', 'required' => true, 'minYear' => 2009, 'maxYear' => date( 'Y' ) + 5, 'dateFormat' => 'DMY' ),
					'Actioncandidat.dfaction' => array( 'domain' => 'actioncandidat', 'required' => true, 'minYear' => 2009, 'maxYear' => date( 'Y' ) + 5, 'dateFormat' => 'DMY' )
				),
				array(
					'options' => $options
				)
			);
		?>
		<?php
			echo $this->Default->subform(
				array(
					'Actioncandidat.typeaction' => array( 'domain' => 'actioncandidat', 'type' => 'radio', 'options' => $options['Actioncandidat']['typeaction'], 'required' => true )
				),
				array(
					'options' => $options
				)
			);
		?>
		<fieldset id="nbposte">
			<?php
				echo $this->Default->subform(
					array(
						'Actioncandidat.nbpostedispo' => array( 'domain' => 'actioncandidat', 'required' => true ),
						'Actioncandidat.nbposterestant' => array( 'domain' => 'actioncandidat')
						),
					array(
						'options' => $options
					)
				);
			?>
		</fieldset>
		<fieldset id="nbheure">
			<?php
				echo $this->Default->subform(
					array(
						'Actioncandidat.nbheuredispo' => array( 'type' => 'text', 'domain' => 'actioncandidat', 'required' => true ),
						'Actioncandidat.nbheurerestante' => array( 'type' => 'text', 'domain' => 'actioncandidat')
						),
					array(
						'options' => $options
					)
				);
			?>
		</fieldset>
		<script type="text/javascript">
			function toutCocherZonesgeographiques() {
				return toutCocher( 'input[name="data[Zonegeographique][Zonegeographique][]"]' );
			}
			function toutDecocherZonesgeographiques() {
				return toutDecocher( 'input[name="data[Zonegeographique][Zonegeographique][]"]' );
			}
		</script>
		<fieldset class="col2">
			<legend>Zones géographiques</legend>
			<?php

			/* NOTE : ActioncandidatFiltreZoneGeo n'existe pas


			<script type="text/javascript">
				document.observe( "dom:loaded", function() {
					observeDisableFieldsetOnCheckbox( 'ActioncandidatFiltreZoneGeo', 'filtres_zone_geo', false );
				} );
			</script>*/?>
				<?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocherZonesgeographiques();" ) );?>
				<?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocherZonesgeographiques();" ) );?>


			<?php
				echo $this->Form->input( 'Zonegeographique.Zonegeographique', array( 'label' => false, 'multiple' => 'checkbox' , 'options' => $options['Zonegeographique'] ) );
			?>
		</fieldset>
		<?php
			echo $this->Default->subform(
				array(
					'Actioncandidat.contactpartenaire_id' => array( 'type' => 'select', 'empty' => true, 'required' => true )
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>
	<script type="text/javascript">
		function toutCocherMotifs() {
			return toutCocher( 'input[name="data[Motifsortie][Motifsortie][]"]' );
		}
		function toutDecocherMotifs() {
			return toutDecocher( 'input[name="data[Motifsortie][Motifsortie][]"]' );
		}
	</script>
	<fieldset class="invisible">
		<?php echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocherMotifs();" ) );?>
		<?php echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocherMotifs();" ) );?>
		<?php
			echo $this->Default2->subform(
				array(
					'Motifsortie.Motifsortie' => array( 'label' => 'Liste des motifs de sortie liés à l\'action', 'multiple' => 'checkbox', 'empty' => false )
				),
				array(
					'options' => $motifssortie
				)
			);
		?>
	</fieldset>
<?php
	echo $this->Xform->end( __( 'Save' ) );
	echo $this->Default->button(
		'back',
		array('controller' => 'actionscandidats', 'action' => 'index'),
		array('id' => 'Back')
	);

/* NOTE: ActioncandidatContractualisation n'existe pas


<script type="text/javascript">
	document.observe( "dom:loaded", function() {c
		var v = $( 'ActioncandidatAddEditForm' ).getInputs( 'radio', 'data[Actioncandidat][hasfichecandidature]' );
		var currentSelectValue = $F('ActioncandidatContractualisation');
		$( v ).each( function( radio ) {
			$( radio ).observe( 'change', function( event ) {
				if( radio.value == 0 ){
					$( 'ActioncandidatContractualisation' ).setValue('internecg');
				}
				else{
					$( 'ActioncandidatContractualisation' ).setValue(currentSelectValue);
				}
			} );
		} );
	} );
</script>*/