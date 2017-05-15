<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	if( $this->action == 'add' ) {
		$this->pageTitle = 'Ajout d\'un dossier';
	}
	else {
		$this->pageTitle = 'Édition du dossier';
	}

	$domain = 'pdo';
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		//Utilisé en cas de motif de PDO non admissible
		observeDisableFieldsetOnCheckbox( 'PropopdoDecision', $( 'PropopdoDecisionpdoId' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'PropopdoIsvalidation', $( 'PropopdoDatevalidationdecisionDay' ).up( 'fieldset' ), false );
		observeDisableFieldsetOnCheckbox( 'PropopdoIsdecisionop', $( 'PropopdoObservationop' ).up( 'fieldset' ), false );
	});
</script>

<script type="text/javascript">
	function checkDatesToRefresh() {
		if( ( $F( 'PropopdoDaterevisionMonth' ) ) && ( $F( 'PropopdoDaterevisionYear' ) ) ) {
			setDateInterval2( 'PropopdoDaterevision', 'PropopdoDateecheance', 4, false );

		}
	}

	document.observe("dom:loaded", function() {
		setDateInterval2( 'PropopdoDaterevision', 'PropopdoDateecheance', 4, false );

		Event.observe( $( 'PropopdoDaterevisionMonth' ), 'change', function() {
			checkDatesToRefresh();
		} );
		Event.observe( $( 'PropopdoDaterevisionYear' ), 'change', function() {
			checkDatesToRefresh();
		} );

		observeDisableFieldsetOnRadioValue(
			'propopdoform',
			'data[Propopdo][iscomplet]',
			$( 'FicheCalcul' ),
			'COM',
			false,
			true
		);
	});
</script>

<h1><?php echo $this->pageTitle;?></h1>

<?php
	echo $this->Xform->create( 'Propopdo', array( 'id' => 'propopdoform' ) );
	if( $this->action == 'add' ) {
	}
	else {
		echo '<div>';
		echo $this->Xform->input( 'Propopdo.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	echo '<div>';
	echo $this->Xform->input( 'Propopdo.personne_id', array( 'type' => 'hidden', 'value' => $personne_id ) );

	echo '</div>';
?>

<div class="aere">

<fieldset>
	<?php
		echo $this->Default->subform(
			array(
				'Propopdo.etatdossierpdo' => array( 'type' => 'hidden' )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);

		echo $this->Default->subform(
			array(
				'Propopdo.typepdo_id' => array( 'label' => ( __d( 'propopdo', 'Propopdo.typepdo_id' ) ), 'type' => 'select', 'options' => $typepdo, 'empty' => true ),
				'Propopdo.datereceptionpdo' => array( 'label' =>  ( __( 'Date de réception du dossier' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009, 'empty' => false ),
				'Propopdo.originepdo_id' => array( 'label' =>  ( __( 'Origine' ) ), 'type' => 'select', 'options' => $originepdo, 'empty' => true ),
				'Propopdo.orgpayeur' => array( 'label' =>  __d( 'propopdo', 'Propopdo.orgpayeur' ), 'type' => 'select', 'options' => $orgpayeur, 'empty' => true ),
				'Propopdo.serviceinstructeur_id' => array( 'label' =>  ( __d( 'propopdo', 'Propopdo.serviceinstructeur_id' ) ), 'type' => 'select', 'options' => $serviceinstructeur, 'empty' => true )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);
	?>
</fieldset>
<fieldset>
<legend><?php echo required( $this->Default2->label( 'Propopdo.haspiece' ) );?></legend>

<?php echo $this->Form->input( 'Propopdo.haspiece', array( 'type' => 'radio', 'options' => $options['haspiece'], 'legend' => false, 'fieldset' => false ) );?>
<fieldset id="filecontainer-piece" class="noborder invisible">
	<?php
		echo $this->Fileuploader->create(
			$fichiers,
			array( 'action' => 'ajaxfileupload' )
		);
	?>
</fieldset>
</fieldset>

<script type="text/javascript">
document.observe( "dom:loaded", function() {
	observeDisableFieldsetOnRadioValue(
		'propopdoform',
		'data[Propopdo][haspiece]',
		$( 'filecontainer-piece' ),
		'1',
		false,
		true
	);
} );
</script>

<fieldset>
	<?php
		echo $this->Default->subform(
			array(
				'Propopdo.user_id' => array( 'label' =>  ( __d( 'propopdo', 'Propopdo.user_id' ) ), 'type' => 'select', 'options' => $gestionnaire )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);
	?>
</fieldset>

<fieldset>
	<table class="noborder" id="infosPdo">
		<tr>
			<td class="mediumSize noborder">
				<?php
					echo $this->Xform->input( 'Situationpdo.Situationpdo', array( 'type' => 'select', 'label' => 'Motif de la décision', 'multiple' => 'checkbox' , 'options' => $situationlist ) );
				?>
			</td>
			<td class="mediumSize noborder">
				<?php
					echo $this->Xform->input( 'Statutpdo.Statutpdo', array( 'type' => 'select', 'label' => 'Statut de la personne', 'multiple' => 'checkbox' , 'options' => $statutlist ) );
				?>
			</td>
		</tr>
	</table>
	<?php
		echo $this->Xhtml->tag(
			'p',
			'Catégories : '
		);

		echo $this->Default->subform(
			array(
				'Propopdo.categoriegeneral' => array( 'label' => __d( 'propopdo', 'Propopdo.categoriegeneral' ), 'type' => 'select', 'empty' => true, 'options' => $categoriegeneral ),
				'Propopdo.categoriedetail' => array( 'label' => __d( 'propopdo', 'Propopdo.categoriedetail' ), 'type' => 'select', 'empty' => true, 'options' => $categoriedetail ),
				'Propopdo.iscomplet' => array( 'legend' => false, 'type' => 'radio', 'options' => $options['iscomplet'] )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);

		/// FIXME: à corriger car pas bon
		//echo $this->Ajax->observeField( 'PropopdoIscompletCOM', array( 'update' => 'Etatpdo', 'url' => array( 'action' => 'ajaxetatpdo' ) ) );
		//echo $this->Ajax->observeField( 'PropopdoIscompletINC', array( 'update' => 'Etatpdo', 'url' => array( 'action' => 'ajaxetatpdo' ) ) );

	?>
</fieldset>

<?php
	if ($this->action=='edit') {

		echo $this->Xhtml->tag(
			'fieldset',
			$this->Xhtml->tag(
				'legend',
				'Traitements'
			).
			$this->Default2->index(
				$traitementspdos,
				array(
					'Descriptionpdo.name',
					'Traitementpdo.datereception' => array( 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009 ),
					'Traitementpdo.datedepart' => array( 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009 ),
					'Traitementtypepdo.name',
					'Traitementpdo.hascourrier' => array( 'label' => 'Courrier ?', 'type' => 'boolean' ),
					'Traitementpdo.hasrevenu' => array( 'label' => 'Fiche de calcul ?', 'type' => 'boolean' ),
					'Traitementpdo.haspiecejointe' => array( 'label' => 'Pièce jointe ?', 'type' => 'boolean' ),
					'Traitementpdo.hasficheanalyse' => array( 'label' => 'Fiche d\'analyse ?', 'type' => 'boolean' ),
				),
				array(
					'actions' => array(
						'Traitementspdos::view',
						'Traitementspdos::clore' => array( 'disabled' => ( '\'#Traitementpdo.clos#\' != 0' ) ),
						'Traitementspdos::delete' => array( 'disabled' => '( "'.$this->Permissions->checkDossier( 'traitementspdos', 'delete', $dossierMenu ).'" != "1" )' )
					),
					'add' => array( 'Traitementpdo.add' => array( 'controller'=>'traitementspdos', 'action'=>'add', $pdo_id ) ),
					'options' => $options
				)
			)
		);
		$block = true;
		foreach( $decisionspropospdos as $i => $decision ){
			if( isset( $decision ) ){
				$block = false;
			}
			else{
				$block = true;
			}
		}

		echo $this->Xhtml->tag(
			'fieldset',
			$this->Xhtml->tag(
				'legend',
				'Propositions de décision'
			).
			$this->Default2->index(
				$decisionspropospdos,
				array(
					'Decisionpropopdo.datedecisionpdo' => array( 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009 ),
					'Decisionpdo.libelle',
					'Decisionpropopdo.avistechnique' => array( 'type' => 'boolean' ),
					'Decisionpropopdo.dateavistechnique' => array( 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009 ),
					'Decisionpropopdo.validationdecision' => array( 'type' => 'boolean' ),
					'Decisionpropopdo.datevalidationdecision' => array( 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+5, 'minYear'=>2009 ),
				),
				array(
					'actions' => array(
						'Decisionspropospdos::view',
						'Decisionspropospdos::edit' => array( 'disabled' => ( '\'#Decisionpropopdo.id#\' != '.$lastDecisionId.' || \''.$etatdossierpdo.'\' == \'dossiertraite\' || \'#Decisionpropopdo.validationdecision#\' != NULL' ) ),
						'Decisionspropospdos::print' => array( 'label' => 'Imprimer', 'url' => array( 'controller' => 'decisionspropospdos', 'action'=>'decisionproposition' ), 'disabled' => ( '\'#Decisionpropopdo.validationdecision#\' == NULL || ( "'.$this->Permissions->checkDossier( 'decisionspropospdos', 'print', $dossierMenu ).'" != "1" )' ) ),
						'Decisionspropospdos::delete' => array( 'disabled' => '( "'.$this->Permissions->checkDossier( 'decisionspropospdos', 'delete', $dossierMenu ).'" != "1" )' )
					),
					'add' => array( 'Decisionpropopdo.add' => array( 'controller'=>'decisionspropospdos', 'action'=>'add', $pdo_id, 'disabled' => $ajoutDecision ) ),
					'options' => $options
				)
			)
		);

	}
?>

<fieldset id="Etatpdo" class="invisible"></fieldset>

</div>
<div class="submit">
	<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) ); ?>
	<?php echo $this->Form->button( 'Retour', array( 'type' => 'button', 'onclick'=>"location.replace('".array( 'controller' => 'propospdos', 'action' => 'index', $personne_id )."')" ) ); ?>
</div>

<?php echo $this->Xform->end();?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		[ $('PropopdoTypepdoId'), $('PropopdoUserId'), $('PropopdoIscompletCOM'), $('PropopdoIscompletINC') ].each(function(field) {
			field.observe('change', function(element, value) {
				fieldUpdater();
			});
		});

		fieldUpdater();
	});

	function fieldUpdater() {
		new Ajax.Updater(
			'Etatpdo',
			'<?php echo array( "action" => "ajaxetatpdo" ) ?>',
			{
				asynchronous:true,
				evalScripts:true,
				parameters:
				{
					'typepdo_id' : $F('PropopdoTypepdoId'),
					'user_id' : $F('PropopdoUserId'),
					'complet' : $F('PropopdoIscompletCOM'),
					'incomplet' : $F('PropopdoIscompletINC'),
					'propopdo_id' : <?php echo (isset($this->request->data['Propopdo']['id']) ?  "\$F('PropopdoId')" : '0' ); ?>
				},
				requestHeaders:['X-Update', 'Etatpdo']
			}
		);
	}
</script>