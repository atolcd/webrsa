<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'traitementpcg66', "Traitementspcgs66::{$this->action}" ).' '.$nompersonne
	);

	echo $this->Xform->create( 'Traitementpcg66', array( 'id' => 'traitementpcg66form' ) );
	if( Set::check( $this->request->data, 'Traitementpcg66.id' ) ){
		echo $this->Xform->input( 'Traitementpcg66.id', array( 'type' => 'hidden' ) );
	}

	echo $this->Default2->subform(
		array(
			'Traitementpcg66.personnepcg66_id' => array( 'type' => 'hidden', 'value' => $personnepcg66_id ),
			'Traitementpcg66.personne_id' => array( 'type' => 'hidden', 'value' => $personne_id ),
			'Traitementpcg66.user_id' => array( 'type' => 'hidden', 'value' => $userConnected ),
			'Traitementpcg66.clos' => array( 'type' => 'hidden', 'value' => 'N' ),
			'Traitementpcg66.annule' => array( 'type' => 'hidden', 'value' => 'N' )
		),
		array(
			'options' => $options
		)
	);
?>

<?php
	// Liste des types de traitement
	echo $this->Xform->input( 'Traitementpcg66.typetraitement', array(  'type' => 'radio', 'options' => $options['Traitementpcg66']['typetraitement'], 'legend' => required( 'Type de traitement' ) ) );
?>
    <script type="text/javascript">
		function showAjaxValidationErrors() {
			<?php if( isset( $this->validationErrors['Modeletraitementpcg66'] ) ) :?>
				<?php foreach( $this->validationErrors['Modeletraitementpcg66'] as $field => $errors ):?>
					var div = $( '<?php echo Inflector::camelize( "Modeletraitementpcg66_{$field}" );?>' );
					$( div ).addClassName( 'error' );
					var errorMessage = new Element( 'div', { 'class': 'error-message' } ).update( '<?php echo $errors[0];?>' );
					$( div ).insert( { 'bottom' : errorMessage } );
				<?php endforeach;?>
			<?php endif;?>
		}

		document.observe("dom:loaded", function() {
			<?php
				// FIXME: une fonction générique
				$dataModeletraitementpcg66 = array();
				foreach( array( 'Modeletraitementpcg66', 'Piecemodeletypecourrierpcg66' ) as $M ) {
					if( isset( $this->request->data[$M] ) && !empty( $this->request->data[$M] ) ) {
						foreach( $this->request->data[$M] as $field => $value ) {
							if( !is_array( $value ) ) {
								$dataModeletraitementpcg66["data[{$M}][{$field}]"] = js_escape( $value );
							}
							else {
								foreach( $value as $k => $v ) {
									$dataModeletraitementpcg66["data[{$M}][{$field}][{$k}]"] = js_escape( $v );
								}
							}
						}
					}
				}

				echo $this->Ajax->remoteFunction(
					array(
						'update' => 'Typecourrierpcg66Modeletypecourrierpcg66Id',
						'url' => array( 'action' => 'ajaxpiece' ),
 						'with' => 'Object.extend( Form.serialize( $( \'traitementpcg66form\' ), true ), '.php_associative_array_to_js( $dataModeletraitementpcg66 ).' )',
						'complete' => 'showAjaxValidationErrors()'
					)
				);
			?>
		} );
    </script>
	<?php
		// Courriers
		if ( isset( $typescourrierspcgs66 ) && !empty( $typescourrierspcgs66 ) ) { ?>
		<fieldset id="filecontainer-courrier" class="noborder invisible">
			<?php
				echo $this->Default->subform(
					array(
						'Traitementpcg66.typecourrierpcg66_id' => array( 'required' => true, 'type' => 'select', 'options' =>$typescourrierspcgs66 ),
						'Traitementpcg66.affiche_couple' => array( 'type' => 'checkbox' ),
						'Traitementpcg66.imprimer' => array( 'type' => 'hidden', 'value' => $imprimer )
					)
				).'<br />';

				echo $this->Ajax->observeField(
					'Traitementpcg66Typecourrierpcg66Id',
					array(
						'update' => 'Typecourrierpcg66Modeletypecourrierpcg66Id',
						'url' => array( 'action' => 'ajaxpiece' ),
						'with' => 'Form.serialize( $(\'traitementpcg66form\') )',
						'complete' => 'showAjaxValidationErrors()',
						'evalScripts' => true
					)
				);

				echo $this->Xhtml->tag(
						'div',
						' ',
						array(
								'id' => 'Typecourrierpcg66Modeletypecourrierpcg66Id'
						)
				);
			?>
		</fieldset>
	<?php } ?>


<?php
		// Début fiche de calcul
		echo "<fieldset id='fichecalcul' class='noborder invisible'><table>";

			echo $this->Default->subform(
				array(
					'Traitementpcg66.nbmoisactivite' => array( 'type' => 'hidden' ),
					'Traitementpcg66.mnttotalpriscompte' => array( 'type' => 'hidden' ),
					'Traitementpcg66.revenus' => array( 'type' => 'hidden' ),
					'Traitementpcg66.benefpriscompte' => array( 'type' => 'hidden' )
				),
				array(
					'options' => $options
				)
			);

			// Si on modifie ou que l'on revient sur le formulaire
			if( !empty( $this->request->data ) ) {
				$regime = Hash::get( $this->request->data, 'Traitementpcg66.regime' );
			}
			// Sinon, on prend la valeur dans la dernière fiche de calcul enregistrée
			else {
				$regime = Hash::get( $infoDerniereFicheCalcul, 'Traitementpcg66.regime' );
			}

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.regime' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.regime', array('label'=>false, 'type'=>'select', 'options'=>$options['Traitementpcg66']['regime'], 'empty'=>true, 'value' => $regime))
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.saisonnier' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.saisonnier', array('label'=>false, 'type'=>'checkbox'))
				)
			);

            $numsiret = Set::check( $infoDerniereFicheCalcul, 'Traitementpcg66.nrmrcs' ) ? Set::extract( $infoDerniereFicheCalcul, 'Traitementpcg66.nrmrcs' ) : Hash::get( $this->request->data, 'Traitementpcg66.nrmrcs' );
			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.nrmrcs' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.nrmrcs', array('label'=>false, 'type'=>'text', 'value' => $numsiret))
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => 2
					)
				)
			);

            $dtdebutactivite = Set::check( $infoDerniereFicheCalcul, 'Traitementpcg66.dtdebutactivite' ) ? Set::extract( $infoDerniereFicheCalcul, 'Traitementpcg66.dtdebutactivite' ) : Hash::get( $this->request->data, 'Traitementpcg66.dtdebutactivite' );
            $raisonsociale = Set::check( $infoDerniereFicheCalcul, 'Traitementpcg66.raisonsocial' ) ? Set::extract( $infoDerniereFicheCalcul, 'Traitementpcg66.raisonsocial' ) : Hash::get( $this->request->data, 'Traitementpcg66.raisonsocial' );
			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.dtdebutactivite' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.dtdebutactivite',
						array(
							'label'=>false,
							'type'=>'date',
							'empty'=>true,
							'dateFormat' => 'DMY',
							'minYear' => date('Y') - 40,
							'maxYear' => date('Y'),
                            'selected' => $dtdebutactivite
						)
					)
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.raisonsocial' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.raisonsocial', array('label'=>false, 'type'=>'text', 'value' => $raisonsociale))
				)
			);

			//Date de début de prise en compte = au 01-01-(n-1)
			$datedebutperiode = Set::check( $this->request->data, 'Traitementpcg66.dtdebutperiode' ) ? Set::extract( $this->request->data, 'Traitementpcg66.dtdebutperiode' ) : date("Y-01-01", strtotime("-1 year") );
			//Date de fin de prise en compte = au 31-12-(n-1)
			$datefinperiode = Set::check( $this->request->data, 'Traitementpcg66.datefinperiode' ) ? Set::extract( $this->request->data, 'Traitementpcg66.datefinperiode' ) : date("Y-12-31", strtotime("-1 year") );

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.dtdebutperiode' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.dtdebutperiode',
						array(
							'label'=>false,
							'type'=>'date',
							'dateFormat' => 'DMY',
							'minYear' => date('Y') - 10,
							'maxYear' => date('Y'),
							'empty' => true,
							'selected' => $datedebutperiode
						)
					)
				).
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.datefinperiode' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.datefinperiode',
						array(
							'label'=>false,
							'type'=>'date',
							'selected' => $datefinperiode,
							'dateFormat' => 'DMY',
							'minYear' => date('Y') - 10,
							'maxYear' => date('Y') + 4
						)
					)
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.nbmoisactivite' )
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'id' => 'nbmoisactivite'
					)
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => 2
					)
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.forfait' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.forfait', array('label'=>false, 'type'=>'text'))
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'fagri'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.coefannee1' )
				).
				$this->Xhtml->tag(
					'td',
					Configure::read('Traitementpcg66.fichecalcul_coefannee1').' %',
					array(
						'id' => 'coefannee1'
					)
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.aidesubvreint' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Default->subform(
						array(
							'Traitementpcg66.aidesubvreint' => array( 'type' => 'select', 'label' => false, 'empty' => true )
						),
						array(
							'options' => $options
						)
					)
				),
				array(
					'class' => 'fagri'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.coefannee2' )
				).
				$this->Xhtml->tag(
					'td',
					Configure::read('Traitementpcg66.fichecalcul_coefannee2').' %',
					array(
						'id' => 'coefannee2'
					)
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.mtaidesub' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.mtaidesub', array('label'=>false, 'type'=>'text'))
				),
				array(
					'class' => 'fagri'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.chaffvnt' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.chaffvnt', array('label'=>false, 'type'=>'text')).
					$this->Xhtml->tag(
						'p',
						'Attention CA dépassant '.Configure::read('Traitementpcg66.fichecalcul_cavntmax').' €',
						array(
							'class' => 'notice',
							'id' => 'infoChaffvnt'
						)
					)
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.abattement' ),
					array(
						'class' => 'microbic microbicauto'
					)
				).
				$this->Xhtml->tag(
					'td',
					Configure::read('Traitementpcg66.fichecalcul_abattbicvnt').' %',
					array(
						'class' => 'microbic microbicauto',
						'id' => 'abattbicvnt'
					)
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => '2',
						'class' => 'fagri ragri reel microbnc'
					)
				),
				array(
					'class' => 'ragri reel microbic microbicauto'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.chaffsrv' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.chaffsrv', array('label'=>false, 'type'=>'text')).
					$this->Xhtml->tag(
						'p',
						'Attention CA dépassant '.Configure::read('Traitementpcg66.fichecalcul_casrvmax').' €',
						array(
							'class' => 'notice',
							'id' => 'infoChaffsrv'
						)
					)
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.abattement' ),
					array(
						'class' => 'microbic microbicauto microbnc'
					)
				).
				$this->Xhtml->tag(
					'td',
					Configure::read('Traitementpcg66.fichecalcul_abattbicsrv').' %',
					array(
						'class' => 'microbic microbicauto',
						'id' => 'abattbicsrv'
					)
				).
				$this->Xhtml->tag(
					'td',
					Configure::read('Traitementpcg66.fichecalcul_abattbncsrv').' %',
					array(
						'class' => 'microbnc',
						'id' => 'abattbncsrv'
					)
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => '2',
						'class' => 'fagri ragri reel'
					)
				),
				array(
					'class' => 'ragri reel microbic microbicauto microbnc'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.benefpriscompte' ) )
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'id' => 'benefpriscompte'
					)
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'microbic microbicauto microbnc'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.benefoudef' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.benefoudef', array('label'=>false, 'type'=>'text'))
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'ragri reel'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.correction' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.ammortissements' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.ammortissements', array('label'=>false, 'type'=>'text')),
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'ragri reel'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					''
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.salaireexploitant' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.salaireexploitant', array('label'=>false, 'type'=>'text')),
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'ragri reel'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					''
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.provisionsnonded' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.provisionsnonded', array('label'=>false, 'type'=>'text')),
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'ragri reel'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					''
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.moinsvaluescession' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.moinsvaluescession', array('label'=>false, 'type'=>'text')),
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'ragri reel'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					''
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.autrecorrection' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.autrecorrection', array('label'=>false, 'type'=>'text')),
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'ragri reel'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.mnttotal' )
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'id' => 'mnttotal'
					)
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => 2
					)
				),
				array(
					'class' => 'fagri ragri reel'
				)
			);

			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.revenus' )
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'id' => 'revenus'
					)
				).
				$this->Xhtml->tag(
					'td',
					'',
					array(
						'colspan' => 2
					)
				)
			);

			//Date de début de prise en compte = date de demande RSA
			$datepriseencompte = Set::check( $this->request->data, 'Traitementpcg66.dtdebutprisecompte' ) ? Set::extract( $this->request->data, 'Traitementpcg66.dtdebutprisecompte' ) : $dtdemrsa;
			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.dtdebutprisecompte' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.dtdebutprisecompte',
						array(
							'label'=>false,
							'type'=>'date',
							'selected' => $datepriseencompte,
							'dateFormat' => 'DMY',
							'minYear' => date('Y') - 4,
							'maxYear' => date('Y') + 1
						)
					),
					array(
						'colspan' => 1
					)
				).
				$this->Xhtml->tag(
					'td',
					$this->Xform->required( __d( 'traitementpcg66', 'Traitementpcg66.datefinprisecompte' ) )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.datefinprisecompte',
						array(
							'label'=>false,
							'type'=>'date',
							'empty'=>true,
							'dateFormat' => 'DMY',
							'minYear' => date('Y') - 4,
							'maxYear' => date('Y') + 4
						)
					),
					array(
						'colspan' => 1
					)
				)
			);
			echo $this->Xhtml->tag(
				'tr',
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.dureefinprisecompte' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.dureefinprisecompte', array('label'=>false, 'type'=>'select', 'options'=>$options['Traitementpcg66']['dureefinprisecompte'], 'empty'=>true)),
					array(
						'colspan' => 1
					)
				).
				$this->Xhtml->tag(
					'td',
					__d( 'traitementpcg66', 'Traitementpcg66.daterevision' )
				).
				$this->Xhtml->tag(
					'td',
					$this->Form->input('Traitementpcg66.daterevision',
						array(
							'label'=>false,
							'type'=>'date',
							'empty'=>true,
							'dateFormat' => 'DMY',
							'minYear' => date('Y') - 4,
							'maxYear' => date('Y') + 4
						)
					),
					array(
						'colspan' => 1
					)
				)
			);

		echo "</table></fieldset>";

		// Fiche d'analyse
		echo $this->Xhtml->tag(
			'fieldset',
			$this->Default->subform(
				array(
					'Traitementpcg66.dossierarevoir' => array( 'type' => 'textarea' ),
				),
				array(
					'options' => $options
				)
			),
			array(
				'id'=>'fieldsetficheanalyse',
				'class'=>'noborder invisible'
			)
		);
		// Fin fiche de calcul
	?>

	<fieldset>
		<legend><?php echo required( $this->Default2->label( 'Traitementpcg66.haspiecejointe' ) );?></legend>

		<?php echo $this->Form->input( 'Traitementpcg66.haspiecejointe', array( 'type' => 'radio', 'options' => $options['Traitementpcg66']['haspiecejointe'], 'legend' => false, 'fieldset' => false ) );?>
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
		<?php echo $this->Fileuploader->validation( 'traitementpcg66form', 'Traitementpcg66', 'Pièce jointe' );?>
	</fieldset>


	<?php
		echo $this->Default->subform(
			array(
				'Traitementpcg66.serviceinstructeur_id' => array( 'type' => 'select', 'options' => $services, 'empty' => true )
			),
			array(
				'options' => $options
			)
		);
		// Liste des motifs concernant la personne
		echo $this->Default->subform(
			array(
				'Traitementpcg66.situationpdo_id' => array( 'type' => 'select', 'options' => $listeMotifs, 'empty' => true, 'required' => true )
			),
			array(
				'options' => $options
			)
		);
		echo $this->Default->subform(
			array(
				'Traitementpcg66.descriptionpdo_id' => array( 'type' => 'select', 'required' => true )
			),
			array(
				'options' => $options
			)
		);

		echo $this->Xhtml->tag(
			'fieldset',
			$this->Default->subform(
				array(
					'Traitementpcg66.datedepart' => array( 'dateFormat' => 'DMY', 'maxYear' => date('Y') + 2, 'minYear' => date('Y' ) - 5, 'empty' => false )
				),
				array(
					'options' => $options
				)
			),
			array(
				'id'=>'dateDepart',
				'class'=>'noborder invisible'
			)
		);

		echo $this->Xhtml->tag(
			'fieldset',
			$this->Default->subform(
				array(
					'Traitementpcg66.datereception' => array( 'dateFormat' => 'DMY', 'empty' => false, 'maxYear' => date('Y') + 2, 'minYear' => date('Y' ) - 5 )
				),
				array(
					'options' => $options
				)
			),
			array(
				'id'=>'dateReception',
				'class'=>'noborder invisible'
			)
		);

		echo $this->Xhtml->tag(
			'fieldset',
			$this->Default->subform(
				array(
					'Traitementpcg66.dureeecheance' => array( 'required' => true ),
					'Traitementpcg66.dateecheance' => array( 'dateFormat' => 'DMY','required' => true, 'empty' => true, 'maxYear' => date('Y') + 2, 'minYear' => date('Y' ) - 5 )
				),
				array(
					'options' => $options
				)
			),
			array(
				'class'=>'noborder invisible'
			)
		);

		//Liste des traitements autre que moi non clos, appartenant à d'autres dossiers
		if( !empty( $listeTraitementsNonClos ) ) {
//			echo $this->Form->input( 'Traitementpcg66.traitementnonclos', array( 'label' => 'Traitement d\'un autre dossier à clôturer ?', 'type' => 'select', 'options' => $listeTraitementsNonClos['Traitementpcg66']['traitementnonclos'], 'empty' => true ) );
            echo $this->Default2->subform(
                array(
                    'Traitementpcg66.Traitementpcg66' => array( 'type' => 'select', 'label' => 'Traitement d\'un autre dossier à clôturer ?', 'multiple' => 'checkbox', 'empty' => false, 'options' => $listeTraitementsNonClos['Traitementpcg66']['traitementnonclos'] )
                ),
                array(
                    'options' => $options
                )
            );
		}
        else {
            echo '<p class="notice">Aucun traitement non clos appartenant à un autre dossier</p>';
        }


		if( !empty( $traitementspcgsouverts ) ) {
			echo "<table>";

			echo $this->Default2->thead(
				array(
					'Traitementpcg66.situationpdo_id',
					'Traitementpcg66.descriptionpdo_id' => array( 'type'=>'string' ),
					'Traitementpcg66.datereception',
					'Traitementpcg66.datedepart',
					'Traitementpcg66.dateecheance',
					'Traitementpcg66.questionclore' => array( 'type'=>'string' )
				)
			);
			echo "<tbody>";

			foreach( $traitementspcgsouverts as $traitementpcgouvert ) {
				echo $this->Xhtml->tag(
					'tr',
					$this->Xhtml->tag(
						'td',
						Set::enum( Set::classicExtract($traitementpcgouvert, 'Traitementpcg66.situationpdo_id' ), $listeMotifs )
					).
					$this->Xhtml->tag(
						'td',
						Set::classicExtract($traitementpcgouvert, 'Descriptionpdo.name')
					).
					$this->Xhtml->tag(
						'td',
						$this->Locale->date( 'Date::short', Set::classicExtract($traitementpcgouvert, 'Traitementpcg66.datereception') )
					).
					$this->Xhtml->tag(
						'td',
						$this->Locale->date( 'Date::short', Set::classicExtract($traitementpcgouvert, 'Traitementpcg66.datedepart') )
					).
					$this->Xhtml->tag(
						'td',
						$this->Locale->date( 'Date::short', Set::classicExtract($traitementpcgouvert, 'Traitementpcg66.dateecheance') )
					).
					$this->Xhtml->tag(
						'td',
						$this->Form->input(
							'Traitementpcg66.traitmentpdoIdClore.'.Set::classicExtract($traitementpcgouvert, 'Traitementpcg66.id'),
							array(
								'type' => 'radio',
								'legend' => false,
								'options' => $options['Traitementpcg66']['clos']
							)
						)
					)
				);
			}

			echo "</tbody></table>";
		}
		else{
			echo '<p class="notice">Aucun traitement en cours.</p>';
		}

		echo "<div class='submit'>";
			echo $this->Form->submit( 'Enregistrer', array( 'div'=>false ) );
			echo $this->Form->submit( 'Retour', array( 'name' => 'Cancel', 'div'=>false ) );
		echo "</div>";

		echo $this->Form->end();
		echo $this->Observer->disableFormOnSubmit( 'traitementpcg66form' );
?>
<script type="text/javascript">
		document.observe( "dom:loaded", function() {
			observeDisableFieldsetOnRadioValue(
				'traitementpcg66form',
				'data[Traitementpcg66][haspiecejointe]',
				$( 'filecontainer-piecejointe' ),
				'1',
				false,
				true
			);

			observeDisableFieldsetOnRadioValue(
				'traitementpcg66form',
				'data[Traitementpcg66][typetraitement]',
				$( 'fieldsetficheanalyse' ),
				'dossierarevoir',
				false,
				true
			);

			observeDisableFieldsetOnRadioValue(
				'traitementpcg66form',
				'data[Traitementpcg66][typetraitement]',
				$( 'fichecalcul' ),
				'revenu',
				false,
				true
			);

			observeDisableFieldsetOnRadioValue(
				'traitementpcg66form',
				'data[Traitementpcg66][typetraitement]',
				$( 'filecontainer-courrier' ),
				'courrier',
				false,
				true
			);


			//**//
// 			observeDisableFieldsOnRadioValue(
// 				'traitementpcg66form',
// 				'data[Traitementpcg66][typetraitement]',
// 				[ 'Traitementpcg66Dureeecheance' ],
// 				'revenu',
// 				false,
// 				true
// 			);


			observeDisableFieldsOnRadioValue(
				'traitementpcg66form',
				'data[Traitementpcg66][typetraitement]',
				[
					'Traitementpcg66DateecheanceDay',
					'Traitementpcg66DateecheanceMonth',
					'Traitementpcg66DateecheanceYear',
					'Traitementpcg66Dureeecheance'
				],
				'revenu',
				false
			);

			observeDisableFieldsOnValue(
				'Traitementpcg66Dureeecheance',
				[
					'Traitementpcg66DateecheanceDay',
					'Traitementpcg66DateecheanceMonth',
					'Traitementpcg66DateecheanceYear'
				],
				['0', '', undefined],
				true
			);

			//
		} );
	</script>
<script type="text/javascript">
	//<![CDATA[

	function checkDatesToExpiration( dateDonnee, dateAChanger, operateur ) {
		var duree = $F( 'Traitementpcg66Duree'+dateDonnee ).split( '.' );

		var duree_entier = duree[0];
		var duree_decimales = duree[1];

		if ( $F( 'Traitementpcg66Date'+dateDonnee+'Day' ) != "" && $F( 'Traitementpcg66Date'+dateDonnee+'Month' ) != "" && $F( 'Traitementpcg66Date'+dateDonnee+'Year' ) != "" && $F( 'Traitementpcg66Duree'+dateDonnee ) != "" ) {
			var dateDepart = new Date( $F( 'Traitementpcg66Date'+dateDonnee+'Year' ), $F( 'Traitementpcg66Date'+dateDonnee+'Month' ) - 1, $F( 'Traitementpcg66Date'+dateDonnee+'Day' ) );

            var timestampDepart = dateDepart.getTime();
            var delai = operateur + parseInt( duree_entier ) + ' months';
            if ( duree_decimales != undefined ) {
                delai = delai + ' '  + operateur + '15 days';
            }

            var timestampArrivee = strtotime( delai, timestampDepart / 1000 )  * 1000;
            dateDepart = new Date( timestampArrivee );

			var newday = dateDepart.getDate();
			var newmonth = dateDepart.getMonth() + 1;
			var newyear = dateDepart.getFullYear();

			$( 'Traitementpcg66Date'+dateAChanger+'Day' ).value = ( newday < 10 ) ? '0' + newday : newday;
			$( 'Traitementpcg66Date'+dateAChanger+'Month' ).value = ( newmonth < 10 ) ? '0' + newmonth : newmonth;
			$( 'Traitementpcg66Date'+dateAChanger+'Year' ).value = newyear;
		}
	}

	function checkDatesEcheance( dateDonnee, dateAChanger, operateur ) {
		var duree = $F( 'Traitementpcg66Dureeecheance' ).split( '.' );
		var month = duree[0];
		var day = duree[1];
		if ( $F( 'Traitementpcg66Date'+dateDonnee+'Day' ) != "" && $F( 'Traitementpcg66Date'+dateDonnee+'Month' ) != "" && $F( 'Traitementpcg66Date'+dateDonnee+'Year' ) != "" && $F( 'Traitementpcg66Dureeecheance' ) != "" ) {
			var dateDepart = new Date( $F( 'Traitementpcg66Date'+dateDonnee+'Year' ), $F( 'Traitementpcg66Date'+dateDonnee+'Month' )-1, $F( 'Traitementpcg66Date'+dateDonnee+'Day' ) );
			if ( day != undefined ) {
				if ( operateur == '+' ) {
					dateDepart.setDate( 15 + dateDepart.getDate() );
				}
				else {
					dateDepart.setDate( 15 - dateDepart.getDate() );
				}
			}
			if ( operateur == '+' ) {
				month = parseInt( dateDepart.getMonth() ) + parseInt( month );
			}
			else {
				month = parseInt( dateDepart.getMonth() ) - parseInt( month );
			}
			dateDepart.setMonth( month );
			var newday = dateDepart.getDate();
			var newmonth = dateDepart.getMonth()+1;
			var newyear = dateDepart.getFullYear();
			$( 'Traitementpcg66DateecheanceDay' ).value = ( newday < 10 ) ? '0' + newday : newday;
			$( 'Traitementpcg66DateecheanceMonth' ).value = ( newmonth < 10 ) ? '0' + newmonth : newmonth;
			$( 'Traitementpcg66DateecheanceYear' ).value = newyear;
		}
	}

	document.observe("dom:loaded", function() {
		//Calcul automatique de la date de révision selon la date de fin de prise en compte
		[ 'Traitementpcg66DatefinprisecompteDay', 'Traitementpcg66DatefinprisecompteMonth', 'Traitementpcg66DatefinprisecompteYear', 'Traitementpcg66Dureefinprisecompte' ].each( function( id ) {
			$( id ).observe( 'change', function() {
				checkDatesToExpiration( 'finprisecompte', 'revision', '-' );
			});
		});

		//Calcul automatique de la date d'échéance selon la date de fin de prise en compte
		[ 'Traitementpcg66DatefinprisecompteDay', 'Traitementpcg66DatefinprisecompteMonth', 'Traitementpcg66DatefinprisecompteYear', 'Traitementpcg66Dureeecheance' ].each( function( id ) {
			$( id ).observe( 'change', function() {
				checkDatesEcheance( 'finprisecompte', 'echeance', '+' );
			});
		});

		<?php
			$datesreception = array();
			$datesdepart = array();
			foreach($options['Traitementpcg66']['listeDescription'] as $description) {
				if ($description['Descriptionpdo']['dateactive'] == 'datedepart') {
					$datesreception[] = $description["Descriptionpdo"]["id"];
				}
				else {
					$datesdepart[] = $description["Descriptionpdo"]["id"];
				}
			}

		?>

		<?php if( !empty( $datesreception ) ): ?>
			observeDisableFieldsOnValue(
				'Traitementpcg66DescriptionpdoId',
				[
					'Traitementpcg66DatereceptionDay',
					'Traitementpcg66DatereceptionMonth',
					'Traitementpcg66DatereceptionYear'
				],
				[ '<?php echo implode( "', '", $datesreception ); ?>' ],
				true
			);
		<?php endif; ?>

		<?php if( !empty( $datesdepart ) ): ?>
			observeDisableFieldsOnValue(
				'Traitementpcg66DescriptionpdoId',
				[
					'Traitementpcg66DatedepartDay',
					'Traitementpcg66DatedepartMonth',
					'Traitementpcg66DatedepartYear'
				],
				[ '<?php echo implode( "', '", $datesdepart ); ?>' ],
				true
			);
		<?php endif; ?>

		var descriptionspdos = <?php echo php_associative_array_to_js( $descriptionspdos );?>;
		$( 'Traitementpcg66DescriptionpdoId' ).observe( 'change', function (event) {
			var descriptionpdoId = $F( 'Traitementpcg66DescriptionpdoId' );
			if ( descriptionpdoId != null ) {
				$( 'Traitementpcg66Dureeecheance' ).setValue( descriptionspdos[descriptionpdoId] );
				fireEvent( $( 'Traitementpcg66Dureeecheance' ),'change');
			}
		});

		//Calcul automatique de la date d'échéance selon la date de fin de prise en compte
		[ 'Traitementpcg66DatedepartDay', 'Traitementpcg66DatedepartMonth', 'Traitementpcg66DatedepartYear', 'Traitementpcg66Dureeecheance' ].each( function( id ) {
			$( id ).observe( 'change', function() {
				checkDatesEcheance( 'depart', 'echeance', '+' );
			});
		});

		<?php foreach ( $options['Traitementpcg66']['regime'] as $enumname => $enumvalue ): ?>
			$$('tr.<?php echo $enumname; ?>').each(function (element) {
				element.hide();
			});
			$$('td.<?php echo $enumname; ?>').each(function (element) {
				element.hide();
			});
		<?php endforeach; ?>

		$('Traitementpcg66Regime').observe( 'change', function (event) {
			loadFiche();
		});
		loadFiche();

		[ $('Traitementpcg66DtdebutperiodeDay'), $('Traitementpcg66DtdebutperiodeMonth'), $('Traitementpcg66DtdebutperiodeYear'), $('Traitementpcg66DatefinperiodeDay'), $('Traitementpcg66DatefinperiodeMonth'), $('Traitementpcg66DatefinperiodeYear') ].each( function(element) {
			element.observe( 'change', function (event) {
				recalculnbmoisactivite();
			});
		});

		[ $('Traitementpcg66Forfait'), $('Traitementpcg66Mtaidesub'), $('Traitementpcg66Benefoudef'), $('Traitementpcg66Ammortissements'), $('Traitementpcg66Salaireexploitant'), $('Traitementpcg66Provisionsnonded'), $('Traitementpcg66Moinsvaluescession'), $('Traitementpcg66Autrecorrection'),  ].each( function (element) {
			element.observe( 'change', function (event) {
				recalculmnttotal();
			});
		});

		$('Traitementpcg66Chaffvnt').observe( 'change', function (event) {
			recalculbenefpriscompte();
			// Infobulle
			infobulle('vnt');
		});
		$('Traitementpcg66Chaffsrv').observe( 'change', function (event) {
			recalculbenefpriscompte();
			// Infobulle
			infobulle('srv');
		});
	} );

	function loadFiche() {
		var value = $F('Traitementpcg66Regime');
		$$('#fichecalcul tr').each(function (element) {
			var classes = $( element ).classNames();
			if( classes.size() > 0 ) {
				if( $( element ).hasClassName( value ) ) {
					element.show();

					// Réactiver les champs
					$( element ).getElementsBySelector( 'select', 'input' ).each( function( input ) {
						input.disabled = '';
					} );
				}
				else {
					element.hide();

					// Désactiver les champs
					$( element ).getElementsBySelector( 'select', 'input' ).each( function( input ) {
						input.disabled = 'disabled';
					} );
				}
			}
		});
		$$('#fichecalcul tr td').each(function (element) {
			var classes = $( element ).classNames();
			if( classes.size() > 0 ) {
				if( $( element ).hasClassName( value ) ) {
					element.show();
				}
				else {
					element.hide();
				}
			}
		});
		recalculnbmoisactivite();
		recalculmnttotal();
		recalculbenefpriscompte();
		infobulle('vnt');
		infobulle('srv');
	}

	function recalculnbmoisactivite() {
		var nbmois = 0;
		if ($F('Traitementpcg66DatefinperiodeYear') >= $F('Traitementpcg66DtdebutperiodeYear')) {
			nbmois += 12 * ($F('Traitementpcg66DatefinperiodeYear') - $F('Traitementpcg66DtdebutperiodeYear'));
			if (
				($F('Traitementpcg66DatefinperiodeMonth') >= $F('Traitementpcg66DtdebutperiodeMonth'))
				||
				(
					($F('Traitementpcg66DatefinperiodeMonth') < $F('Traitementpcg66DtdebutperiodeMonth'))
					&&
					($F('Traitementpcg66DatefinperiodeYear') > $F('Traitementpcg66DtdebutperiodeYear'))
				)
			)
				nbmois += $F('Traitementpcg66DatefinperiodeMonth') - $F('Traitementpcg66DtdebutperiodeMonth');
			if ($F('Traitementpcg66DatefinperiodeDay') > $F('Traitementpcg66DtdebutperiodeDay'))
				nbmois++;
		}
		if (nbmois < 0)
			nbmois = 0;

		$('nbmoisactivite').innerHTML = ''+nbmois+' mois';
		$('Traitementpcg66Nbmoisactivite').setValue(nbmois);
		recalculrevenus();
	}

	function recalculmnttotal() {
		var mttotal = 0;

		if ($F('Traitementpcg66Regime')=='fagri') {
			var coefannee1 = $('coefannee1').innerHTML.split(' ');
			var valuecoefannee1 = coefannee1[0].replace(',', '.');
			valuecoefannee1 = parseFloat(valuecoefannee1)/100;
			var coefannee2 = $('coefannee2').innerHTML.split(' ');
			var valuecoefannee2 = coefannee2[0].replace(',', '.');
			valuecoefannee2 = parseFloat(valuecoefannee2)/100;
			var forfait = parseFloat($F('Traitementpcg66Forfait').replace(',', '.'));
			var mtaidesub = parseFloat($F('Traitementpcg66Mtaidesub').replace(',', '.'));

			if (!isNaN(valuecoefannee1) && !isNaN(valuecoefannee2) && !isNaN(forfait) && forfait!=0)
				var X = ( forfait * valuecoefannee1 );
				var Y = ( ( forfait + X  ) * valuecoefannee2 );
				mttotal += parseFloat( ( forfait + X + Y ).toFixed( 2 ) );
				mttotal = ( isNaN( mttotal ) ? 0 : mttotal );
			if (!isNaN(mtaidesub) && mtaidesub!=0)
				mttotal += Math.round( mtaidesub * 100 ) / 100;
		}
		else if ($F('Traitementpcg66Regime')=='ragri' || $F('Traitementpcg66Regime')=='reel') {
			var benefoudef = parseFloat($F('Traitementpcg66Benefoudef').replace(',', '.'));
			var ammortissements = parseFloat($F('Traitementpcg66Ammortissements').replace(',', '.'));
			var salaireexploitant = parseFloat($F('Traitementpcg66Salaireexploitant').replace(',', '.'));
			var provisionsnonded = parseFloat($F('Traitementpcg66Provisionsnonded').replace(',', '.'));
			var moinsvaluescession = parseFloat($F('Traitementpcg66Moinsvaluescession').replace(',', '.'));
			var autrecorrection = parseFloat($F('Traitementpcg66Autrecorrection').replace(',', '.'));

			if (!isNaN(benefoudef))
				mttotal += Math.round( ( benefoudef ) * 100 ) / 100;
			if (!isNaN(ammortissements))
				mttotal += Math.round( ( ammortissements ) * 100 ) / 100;
			if (!isNaN(salaireexploitant))
				mttotal += Math.round( ( salaireexploitant ) * 100 ) / 100;
			if (!isNaN(provisionsnonded))
				mttotal += Math.round( ( provisionsnonded ) * 100 ) / 100;
			if (!isNaN(moinsvaluescession))
				mttotal += Math.round( ( moinsvaluescession ) * 100 ) / 100;
			if (!isNaN(autrecorrection))
				mttotal += Math.round( ( autrecorrection ) * 100 ) / 100;
		}

		$('Traitementpcg66Mnttotalpriscompte').setValue(mttotal);
		mttotal = mttotal.toString().replace('.', ',');
		$('mnttotal').innerHTML = mttotal+' €';
		recalculrevenus();
	}

	function recalculbenefpriscompte() {
		var benefpriscompte = 0;

		if ($F('Traitementpcg66Regime')=='microbic' || $F('Traitementpcg66Regime')=='microbicauto') {
			var chaffvnt = parseFloat($F('Traitementpcg66Chaffvnt').replace(',', '.'));
			var chaffsrv = parseFloat($F('Traitementpcg66Chaffsrv').replace(',', '.'));
			var abattbicvnt = $('abattbicvnt').innerHTML.split(' ');
			var valueabattbicvnt = abattbicvnt[0].replace(',', '.');
			valueabattbicvnt = 1 - parseFloat(valueabattbicvnt)/100;
			var abattbicsrv = $('abattbicsrv').innerHTML.split(' ');
			var valueabattbicsrv = abattbicsrv[0].replace(',', '.');
			valueabattbicsrv = 1 - parseFloat(valueabattbicsrv)/100;

			if (!isNaN(chaffsrv) && !isNaN(valueabattbicsrv))
				benefpriscompte += Math.round( (chaffsrv * valueabattbicsrv ) * 100 ) / 100;
			if (!isNaN(chaffvnt) && !isNaN(valueabattbicvnt))
				benefpriscompte += Math.round( ( chaffvnt * valueabattbicvnt ) * 100 ) / 100;
		}
		else if ($F('Traitementpcg66Regime')=='microbnc') {
			var chaffsrv = parseFloat($F('Traitementpcg66Chaffsrv').replace(',', '.'));
			var abattbncsrv = $('abattbncsrv').innerHTML.split(' ');
			var valueabattbncsrv = abattbncsrv[0].replace(',', '.');
			valueabattbncsrv = 1 - parseFloat(valueabattbncsrv)/100;

			if (!isNaN(chaffsrv) && !isNaN(valueabattbncsrv))
				benefpriscompte = Math.round( ( chaffsrv * valueabattbncsrv ) * 100 ) / 100;
		}

		$('Traitementpcg66Benefpriscompte').setValue(benefpriscompte);
		benefpriscompte = benefpriscompte.toString().replace('.', ',');
		$('benefpriscompte').innerHTML = benefpriscompte + ' €';
		recalculrevenus();
	}

	function infobulle(champ) {
		var p = $('infoChaff'+champ);
		if ($F('Traitementpcg66Regime')=='reel' || $F('Traitementpcg66Regime')=='microbic' || $F('Traitementpcg66Regime')=='microbicauto' || $F('Traitementpcg66Regime')=='microbnc') {
			var valuemax = 0;
			if (champ=='srv')
				valuemax = <?php echo Configure::read( 'Traitementpcg66.fichecalcul_casrvmax' ) ?>;
			else if (champ=='vnt')
				valuemax = <?php echo Configure::read( 'Traitementpcg66.fichecalcul_cavntmax' ) ?>;
			if( $F('Traitementpcg66Chaff'+champ) > valuemax )
				p.show();
			else
				p.hide();
		}
		else
			p.hide();
	}

	function recalculrevenus() {
		var revenus = 0;

		if ($F('Traitementpcg66Regime')=='fagri' || $F('Traitementpcg66Regime')=='ragri' || $F('Traitementpcg66Regime')=='reel') {
			var mnttotal = $('mnttotal').innerHTML.split(' ');
			var valuemnttotal = mnttotal[0].replace(',', '.');
			valuemnttotal = parseFloat(valuemnttotal);
			var nbmois = $('nbmoisactivite').innerHTML.split(' ');
			var valuenbmois = parseFloat(nbmois[0]);

			if (!isNaN(valuemnttotal) && !isNaN(valuenbmois) && valuemnttotal!=0 && valuenbmois!=0)
				revenus = Math.round( parseFloat( valuemnttotal ) / parseFloat( valuenbmois ) * 100 ) / 100;
		}
		else if ($F('Traitementpcg66Regime')=='microbic' || $F('Traitementpcg66Regime')=='microbicauto' || $F('Traitementpcg66Regime')=='microbnc') {
			var benefpriscompte = $('benefpriscompte').innerHTML.split(' ');
			var valuebenefpriscompte = benefpriscompte[0].replace(',', '.');
			valuebenefpriscompte = parseFloat(valuebenefpriscompte);
			var nbmois = $('nbmoisactivite').innerHTML.split(' ');
			var valuenbmois = parseFloat(nbmois[0]);

			if (!isNaN(valuebenefpriscompte) && !isNaN(valuenbmois) && valuebenefpriscompte!=0 && valuenbmois!=0)
				revenus = Math.round( parseFloat( valuebenefpriscompte ) / parseFloat( valuenbmois ) * 100 ) / 100;
		}

		$('Traitementpcg66Revenus').setValue(revenus);
		revenus = revenus.toString().replace('.', ',');
		$('revenus').innerHTML = revenus + ' € par mois';
	}
	//]]>
</script>