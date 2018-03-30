<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
        echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$this->pageTitle = 'Dossier PCG';
	$domain = 'dossierpcg66';

	App::uses('WebrsaAccess', 'Utility');
	if (!empty($dossierMenu)) {
		WebrsaAccess::init($dossierMenu);
	}
?>

<?php
	$charge = Set::enum( Set::classicExtract( $this->request->data, 'Dossierpcg66.user_id' ),  $options['Dossierpcg66']['user_id'] );

	if( $this->action == 'add' ) {
		$title = $this->pageTitle = 'Ajout d\'un dossier PCG concernant le '.Set::classicExtract( $rolepers, Set::classicExtract( $personneDem, 'Prestation.rolepers' ) ).' : '.Set::classicExtract( $personneDem, 'Personne.nom_complet');
	}
	else {
		if( !empty( $charge ) ) {
			$this->pageTitle = 'Édition du dossier PCG géré par '.$charge;

			$title = 'Édition du dossier PCG concernant le '.Set::classicExtract( $rolepers, Set::classicExtract( $personneDem, 'Prestation.rolepers' ) ).' : '.Set::classicExtract( $personneDem, 'Personne.nom_complet').'<br />'. 'géré par '.$charge;
		}
		else{
			$this->pageTitle = 'Édition du dossier PCG';

			$title = 'Édition du dossier PCG concernant le '.Set::classicExtract( $rolepers, Set::classicExtract( $personneDem, 'Prestation.rolepers' ) ).' : '.Set::classicExtract( $personneDem, 'Personne.nom_complet');
		}
	}
?>
<h1><?php echo $title;?></h1>

<?php
	echo $this->Xform->create( 'Dossierpcg66', array( 'id' => 'dossierpcg66form' ) );
	if( $this->action == 'add' ) {
	}
	else {
		echo '<div>';
		echo $this->Xform->input( 'Dossierpcg66.id', array( 'type' => 'hidden' ) );
		echo '</div>';
	}
	echo '<div>';
	echo $this->Xform->input( 'Dossierpcg66.foyer_id', array( 'type' => 'hidden', 'value' => $foyer_id ) );

	echo '</div>';
?>

<div class="aere">
<fieldset>
	<?php
		echo $this->Default3->subform(
			array(
				'Dossierpcg66.etatdossierpcg' => array( 'type' => 'hidden' ),
				'Dossierpcg66.typepdo_id' => array( 'label' => ( __d( 'dossierpcg66', 'Dossierpcg66.typepdo_id' ) ), 'type' => 'select', 'empty' => true ),
				'Dossierpcg66.datereceptionpdo' => array( 'label' =>  ( __d( 'dossierpcg66', 'Dossierpcg66.datereceptionpdo' ) ), 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear'=>date('Y')+1, 'minYear'=> 2009, 'empty' => false ),
				'Dossierpcg66.originepdo_id' => array( 'label' =>  ( __d( 'dossierpcg66', 'Dossierpcg66.originepdo_id' ) ), 'type' => 'select', 'empty' => true ),
				'Dossierpcg66.orgpayeur' => array( 'label' =>  __d( 'dossierpcg66', 'Dossierpcg66.orgpayeur' ), 'type' => 'select', 'empty' => true ),
				'Dossierpcg66.serviceinstructeur_id' => array( 'label' =>  ( __d( 'dossierpcg66', 'Dossierpcg66.serviceinstructeur_id' ) ), 'type' => 'select', 'empty' => true )
			),
			array(
				'domain' => $domain,
				'options' => $options
			)
		);
	?>
</fieldset>
<fieldset>
<legend><?php echo $this->Default2->label( 'Dossierpcg66.haspiecejointe' );?></legend>
<div style='display: none;'>
<?php echo $this->Form->input( 'Dossierpcg66.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Dossierpcg66']['haspiecejointe'], 'legend' => false, 'fieldset' => false, 'value' => 1 ) );?>
</div>
<fieldset id="filecontainer-piece" class="noborder invisible">
	<?php
		echo $this->Fileuploader->create(
			isset($fichiers) ? $fichiers : array(),
			array( 'action' => 'ajaxfileupload' )
		);

		if (!isset ($fichiersEnBase)) {
			$fichiersEnBase = array ();
		}

		echo $this->Fileuploader->results(
			$fichiersEnBase
		);
	?>
</fieldset>
<?php echo $this->Fileuploader->validation( 'dossierpcg66form', 'Dossierpcg66', 'Pièce jointe' );?>
</fieldset>
<fieldset>
	<legend><?php echo $this->Default2->label( 'Dossierpcg66.commentairepiecejointe' );?></legend>
		<?php
			echo $this->Default2->subform(
				array(
					'Dossierpcg66.commentairepiecejointe' => array( 'label' =>  false, 'type' => 'textarea' )
				),
				array(
					'domain' => $domain,
					'options' => $options
				)
			);
		?>
</fieldset>

<script type="text/javascript">
document.observe( "dom:loaded", function() {
    dependantSelect( 'Dossierpcg66UserId', 'Dossierpcg66Poledossierpcg66Id' );
} );
</script>

<?php if( $gestionnairemodifiable ):?>
	<fieldset>
		<?php
			echo $this->Default3->subform(
				array(
                    'Dossierpcg66.poledossierpcg66_id' => array( 'label' =>  ( __d( 'dossierpcg66', 'Dossierpcg66.poledossierpcg66_id' ) ), 'type' => 'select', 'empty' => true ),
					'Dossierpcg66.user_id' => array( 'label' =>  ( __d( 'dossierpcg66', 'Dossierpcg66.user_id' ) ), 'type' => 'select', 'empty' => true ),
					'Dossierpcg66.dateaffectation' => array( 'label' =>  ( __d( 'dossierpcg66', 'Dossierpcg66.dateaffectation' ) ), 'type' => 'date', 'dateFormat' => 'DMY', 'empty' => true, 'maxYear' => date( 'Y' ) + 1, 'minYear'=> 2009 ),
				),
				array(
					'domain' => $domain,
					'options' => $options
				)
			);
		?>
	</fieldset>
<?php endif;?>
<?php if( $personnedecisionmodifiable ):?>
	<fieldset>
		<legend>Personnes concernées</legend>

		<?php if( $this->Permissions->checkDossier( 'personnespcgs66', 'add', $dossierMenu ) ):?>
			<ul class="actionMenu">
				<?php
					echo '<li class="add">'.$this->Xhtml->addLink(
						'Ajouter une personne',
						array( 'controller' => 'personnespcgs66', 'action' => 'add', $dossierpcg66_id ),
						( !in_array( $etatdossierpcg, array( 'attaffect', 'transmisop' ) ) || $this->Permissions->checkDossier( 'personnespcgs66', 'add', $dossierMenu ) != "1" )
					).' </li>';
				?>
			</ul>
		<?php endif;?>

		<?php if( empty( $personnespcgs66 ) ):?>
			<p class="notice">Ce dossier ne possède pas de personne liée.</p>
		<?php endif;?>

		<?php if( !empty( $personnespcgs66 ) ):?>
			<table class="tooltips">
				<thead>
					<tr>
						<th>Personne concernée</th>
						<th>Motif(s)</th>
						<th>Statut(s)</th>
						<th>Nb de traitements</th>
						<th colspan="5" class="action">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach( $personnespcgs66 as $personnepcg66 ) {
							//Liste des différentes situations de la personne
							$listeSituations = Set::extract( $personnepcg66, '/Situationpdo/libelle' );
							$differentesSituations = '';
							foreach( $listeSituations as $key => $situation ) {
								if( !empty( $situation ) ) {
									$differentesSituations .= $this->Xhtml->tag( 'h3', '' ).'<ul><li>'.$situation.'</li></ul>';
								}
							}

							$blockWithoutTraitement = true;
							$nbTraitement = count( Set::extract( $personnepcg66, '/Traitementpcg66/id' ) );
							if( !empty( $nbTraitement ) ){
								$blockWithoutTraitement = true;
							}
							else{
								$blockWithoutTraitement = false;
							}

							//Liste des différents statuts de la personne
							$listeStatuts = Set::extract( $personnepcg66, '/Statutpdo/libelle' );
							$differentsStatuts = '';
							foreach( $listeStatuts as $key => $statut ) {
								if( !empty( $statut ) ) {
									$differentsStatuts .= $this->Xhtml->tag( 'h3', '' ).'<ul><li>'.$statut.'</li></ul>';
								}
							}
							echo $this->Xhtml->tableCells(
								array(
									h( Set::classicExtract( $personnepcg66, 'Personne.qual' ).' '.Set::classicExtract( $personnepcg66, 'Personne.nom' ).' '.Set::classicExtract( $personnepcg66, 'Personne.prenom' ) ),
									$differentesSituations,
									$differentsStatuts,
									h( $personnepcg66['Personnepcg66']['nbtraitements'] ),
									$this->Xhtml->viewLink(
										'Voir la personne concernée',
										array( 'controller' => 'personnespcgs66', 'action' => 'view', $personnepcg66['Personnepcg66']['id'] ),
										$this->Permissions->checkDossier( 'personnespcgs66', 'view', $dossierMenu )
									),
									$this->Xhtml->editLink(
										'Editer la personne concernée',
										array( 'controller' => 'personnespcgs66', 'action' => 'edit', $personnepcg66['Personnepcg66']['id'] ),
										$this->Permissions->checkDossier( 'personnespcgs66', 'edit', $dossierMenu )
									),
									$this->Xhtml->treatmentLink(
										'Traitements pour la personne',
										array( 'controller' => 'traitementspcgs66', 'action' => 'index', $personnepcg66['Personnepcg66']['personne_id'], $personnepcg66['Personnepcg66']['dossierpcg66_id'] ),
										$this->Permissions->checkDossier( 'traitementspcgs66', 'index', $dossierMenu )
									),
									$this->Xhtml->deleteLink(
										'Supprimer la personne',
										array( 'controller' => 'personnespcgs66', 'action' => 'delete', $personnepcg66['Personnepcg66']['id'] ),
										$this->Permissions->checkDossier( 'personnespcgs66', 'delete', $dossierMenu )
									),
								),
								array( 'class' => 'odd' ),
								array( 'class' => 'even' )
							);
						}
					?>
				</tbody>
			</table>
		<?php endif;?>
	</fieldset>
	<fieldset>
		<legend>Propositions de décision niveau foyer</legend>
	<?php
		echo $this->Default3->actions(
			WebrsaAccess::actionAdd("/Decisionsdossierspcgs66/add/{$dossierpcg66_id}", $ajoutDecisionPossible)
		);
		echo $this->Default3->index(
			$decisionsdossierspcgs66,
			$this->Translator->normalize(
				array(
					'Decisionpdo.libelle',
					'Decisiondossierpcg66.avistechnique',
					'Decisiondossierpcg66.dateavistechnique',
					'Decisiondossierpcg66.validationproposition',
					'Decisiondossierpcg66.datevalidation',
					'Fichiermodule.nb_fichiers_lies',
				) + WebrsaAccess::links(
					array(
						'/Decisionsdossierspcgs66/view/#Decisiondossierpcg66.id#',
						'/Decisionsdossierspcgs66/edit/#Decisiondossierpcg66.id#',
						'/Decisionsdossierspcgs66/avistechnique/#Decisiondossierpcg66.id#',
						'/Decisionsdossierspcgs66/validation/#Decisiondossierpcg66.id#',
						'/Dossierspcgs66/imprimer/#Decisiondossierpcg66.dossierpcg66_id#/#Decisiondossierpcg66.id#' => array(
							'class' => 'print ActionDecisionproposition'
						),
						'/Decisionsdossierspcgs66/transmitop/#Decisiondossierpcg66.id#',
						'/Decisionsdossierspcgs66/cancel/#Decisiondossierpcg66.id#',
						'/Decisionsdossierspcgs66/delete/#Decisiondossierpcg66.id#' => array('confirm' => true),
						'/Decisionsdossierspcgs66/filelink/#Decisiondossierpcg66.id#',
					)
				)
			),
			array(
				'innerTable' => $this->Translator->normalize(
					array(
						'Decisiondossierpcg66.motifannulation' => array(
							'condition' => "'#Decisiondossierpcg66.etatdossierpcg#' === 'annule'"
						),
					)
				),
				'options' => $options,
				'paginate' => false,
			)
		);
	?>
	</fieldset>
<?php endif;?>

<fieldset id="Etatpdo" class="invisible"></fieldset>

</div>
<div class="submit">
	<?php
		echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Form->submit( 'Retour', array( 'div' => false, 'name' => 'Cancel' ) );
	?>
</div>

<?php
	echo $this->Xform->end();
	echo $this->Observer->disableFormOnSubmit( 'dossierpcg66form' );
?>
<?php if ($this->action !== 'add') {?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		var impression = $$('a.ActionDecisionproposition').first();

		[ $('Dossierpcg66TypepdoId'), $('Dossierpcg66UserId') ].each(function(field) {
			if( field ) {
				field.observe('change', function(element, value) {
					fieldUpdater();
				});
			}
		});

		fieldUpdater();

		if (impression) {
			impression.observe('click', function() {
				var etatdossier = $('Etatpdo').select('strong').first();
				etatdossier.innerHTML = 'Calcul de la position...';
				setTimeout(fieldUpdater, 5000);
			});
		}
	});

	function fieldUpdater() {
		new Ajax.Updater(
			'Etatpdo',
			'<?php echo Router::url( array( "action" => "ajax_getetatdossierpcg66", Hash::get($this->request->data, 'Dossierpcg66.id' ) ) ); ?>',
			{
				asynchronous:true,
				evalScripts:true,
				parameters:{},
				requestHeaders:['X-Update', 'Etatpdo']
			}
		);
	}

	/**
	 * Lorsque on clic sur imprimer et qu'on a les droits, Transmettre OP se "dégrise"
	 */
	$$('a.dossierspcgs66.imprimer').each(function(element) {
		element.observe('click', function() {
			var td = element.up('tr').select('span.decisionsdossierspcgs66.transmitop').first().up('td'),
				trad = '<?php echo __m('/Decisionsdossierspcgs66/transmitop');?>',
				url = '<?php echo Router::url(array('controller' => 'decisionsdossierspcgs66', 'action' => 'transmitop'));?>',
				expl = element.getAttribute('href').split('/'),
				id = expl[expl.length -1],
				a = new Element('a', {href: url+'/'+id, class: 'decisionsdossierspcgs66 transmitop'}).insert(trad);

			if (<?php echo WebrsaPermissions::checkDossier('decisionsdossierspcgs66', 'transmitop', $dossierMenu)
				? 'true' : 'false';?>
			) {
				td.innerHTML = '';
				td.insert(a);
			}
		});
	});

/**
* 	Pré-remplissage de la date d'affectation à la date du jour:
*      Lorsqu'aucune date d'affectation n'est remplie
*      Et que l'on sélectionne un "Gestionnaire du dossier"
*      @fixme Sur /dossierspcgs66/edit/66777, le champ ne devrait pas être rempli
*/
$('<?php echo $this -> Html -> domId('Dossierpcg66.user_id'); ?>
	'
	).observe('change', function(event) {
	// On ne traite pas l'événement lorsque sa cause est dûe à l'initialisation des selects dépendants
	if( false === $(event).isTrusted) {
	return;
	}

	var elmt = $(event).target,
	user_id = $F(elmt),
	date_id = '
<?php echo $this -> Html -> domId('Dossierpcg66.dateaffectation'); ?>
	'
	;

	if('' !== user_id && CakeDateSelects.empty(date_id)) {
	CakeDateSelects.set(date_id, new Date());
	}
	});

</script>
<?php } ?>