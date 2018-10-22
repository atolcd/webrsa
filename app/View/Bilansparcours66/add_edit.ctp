<?php
    if(!isset($messages))
        $messages   =   array();
    if(!$alertNbOrientation) {
        $messages[__d('bilansparcours66.po', 'AucuneOrientationAvantSaisineEPL')] = 'error';
    }
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
	$domain = 'bilanparcours66';
?>

<?php
	if( $this->action == 'add'  ) {
		$this->pageTitle = 'Ajout d\'un bilan de parcours';
	}
	else {
		$this->pageTitle = 'Édition du bilan de parcours';
	}

	function radioBilan( $view, $path, $value, $label ) {
		$name = 'data['.implode( '][', explode( '.', $path ) ).']';
		$storedValue = Set::classicExtract( $view->request->data, $path );
		$checked = ( ( $storedValue == $value ) ? 'checked="checked"' : '' );
		return "<label><input type=\"radio\" id=\"radio{$value}\" name=\"{$name}\" value=\"{$value}\" {$checked} />{$label}</label>";
	}
?>
	<h1><?php echo $this->pageTitle;?></h1>

	<?php
		if( $this->action == 'add' ) {
			echo $this->Form->create( 'Bilanparcours66', array( 'type' => 'post',  'id' => 'Bilan', 'novalidate' => true ) );
		}
		else {
			echo $this->Form->create( 'Bilanparcours66', array( 'type' => 'post', 'id' => 'Bilan', 'novalidate' => true ) );
			echo '<div>';
			echo $this->Form->input( 'Bilanparcours66.id', array( 'type' => 'hidden' ) );
			echo $this->Form->input( 'Pe.Bilanparcours66.id', array( 'type' => 'hidden' ) );
			echo '</div>';
		}
		echo '<div>';
		echo $this->Form->input( 'Bilanparcours66.personne_id', array( 'type' => 'hidden', 'value' => Set::classicExtract( $personne, 'Personne.id') ) );
		echo $this->Form->input( 'Bilanparcours66.user_id', array( 'type' => 'hidden', 'value' => $userConnected ) );
		echo '</div>';
	?>

	<div class="aere">
		<?php
			echo $this->Default->subform(
				array(
					'Bilanparcours66.typeformulaire' => array( 'type' => 'radio', 'value' => $typeformulaire/*, 'disabled' => true*/ )
				),
				array(
					'options' => $options
				)
			);
//			echo $this->Xform->input( 'Bilanparcours66.typeformulaire', array( 'type' => 'hidden', 'value' => $typeformulaire, 'id' => 'Bilanparcours66TypeformulaireHidden' ) );
		?>

<fieldset id="bilanparcourscg">
	<legend>BILAN DU PARCOURS</legend>
		<?php
			echo $this->Default->subform(
				array(
					'Bilanparcours66.orientstruct_id' => array( 'type' => 'hidden' ),
					'Bilanparcours66.serviceinstructeur_id' => array( 'label' => 'Maison sociale', 'value' => isset( $this->request->data['Bilanparcours66']['serviceinstructeur_id'] ) ? $this->request->data['Bilanparcours66']['serviceinstructeur_id'] : $serviceinstruceteurUser ),
					'Bilanparcours66.structurereferente_id',
					'Bilanparcours66.referent_id',
					'Bilanparcours66.presenceallocataire' => array('required'=>true)
				),
				array(
					'options' => $options
				)
			);

		?>

	<fieldset>
		<legend>Situation de l'allocataire</legend>
		<table class="wide noborder">
			<tr>
				<td class="mediumSize noborder">
					<strong>Statut de la personne : </strong><?php echo Set::extract( $rolepers, Set::extract( $personne, 'Prestation.rolepers' ) ); ?>
					<br />
					<strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $personne, 'Personne.qual') , $qual ).' '.Set::classicExtract( $personne, 'Personne.nom' );?>
					<br />
					<strong>Prénom : </strong><?php echo Set::classicExtract( $personne, 'Personne.prenom' );?>
					<br />
					<strong>Date de naissance : </strong><?php echo date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) );?>
				</td>
				<td class="mediumSize noborder">
					<strong>N° demandeur : </strong><?php echo Set::classicExtract( $personne, 'Foyer.Dossier.numdemrsa' );?>
					<br />
					<strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $personne, 'Foyer.Dossier.matricule' );?>
					<br />
					<strong>Inscrit au Pôle emploi</strong>
					<?php
						$isPoleemploi = Set::classicExtract( $personne, 'Activite.0.act' );
						if( $isPoleemploi == 'ANP' )
							echo 'Oui';
						else
							echo 'Non';
					?>
					<br />
					<strong>N° identifiant : </strong><?php echo Set::classicExtract( $personne, 'Personne.idassedic' );?>
				</td>
			</tr>
			<tr>
				<td class="mediumSize noborder">
					<strong>Adresse : </strong><br /><?php echo Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.numvoie' ).' '.Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.libtypevoie' ).' '.Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.codepos' ).' '.Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.nomcom' );?>
				</td>
				<td class="mediumSize noborder">
					<?php if( Set::extract( $personne, 'Foyer.Modecontact.0.autorutitel' ) == 'A' ):?>
							<strong>Numéro de téléphone 1 : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.0.numtel' );?>
					<?php endif;?>
					<?php if( Set::extract( $personne, 'Foyer.Modecontact.1.autorutitel' ) == 'A' ):?>
							<br />
							<strong>Numéro de téléphone 2 : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.1.numtel' );?>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="mediumSize noborder">
				<?php if( Set::extract( $personne, 'Foyer.Modecontact.0.autorutiadrelec' ) == 'A' ):?>
					<strong>Adresse mail : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.0.adrelec' );?> <!-- FIXME -->
				<?php endif;?>
				</td>
			</tr>
		</table>
		<?php
			if( !empty( $personne['Orientstruct'] ) ) {
				if ( $this->action == 'edit' ) {
					$defaultvaluetypeorient_id = $this->request->data['Bilanparcours66']['nvtypeorient_id'];
					$defaultvaluestructurereferente_id = implode( '_', array( $this->request->data['Bilanparcours66']['nvtypeorient_id'], $this->request->data['Bilanparcours66']['nvstructurereferente_id'] ) );

					echo $this->Xhtml->tag(
						'p',
						'<strong>Orientation (au moment de la création du bilan de parcours) : </strong>'.Set::extract( $this->request->data, 'Orientstruct.Typeorient.lib_type_orient' )
					);
				}
				else {
					$defaultvaluetypeorient_id = $personne['Orientstruct'][0]['typeorient_id'];
					$defaultvaluestructurereferente_id = implode( '_', array( $personne['Orientstruct'][0]['typeorient_id'], $personne['Orientstruct'][0]['structurereferente_id'] ) );

					echo $this->Xhtml->tag(
						'p',
						'<strong>Orientation actuelle : </strong>'.Set::extract( $personne, 'Orientstruct.0.Typeorient.lib_type_orient' )
					);
				}
			}

// 		debug( $personne );

			echo $this->Default->subform(
				array(
					'Bilanparcours66.sitfam' => array( 'type' => 'radio' )
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>
<fieldset>
	<?php
		echo $this->Xhtml->tag(
			'h3',
			$this->Form->input(
				'Bilanparcours66.bilanparcoursinsertion',
				array(
					'type'=>'checkbox',
					'label'=> 'Bilan du parcours d\'insertion'
				)
			)
		);//bilanparcoursinsertion
	?>
	<fieldset id="BilanparcoursinsertionCheckbox" class="invisible">
		<input type="button" id="cacheEntretien" value="<?php echo __d('bilansparcours66', 'Afficher/Cacher les entretiens');?>"/>
		<input type="button" id="cacheCER" value="<?php echo __d('bilansparcours66', 'Afficher/Cacher le dernier CER');?>"/>
		<?php
			echo '<div class="scrollable" id="listeEntretiens"><h3>' . __d('bilansparcours66', 'Entretiens') . '</h3>';
			foreach($entretiens as $i => $entretien){
				echo $this->Default3->view(
					$entretien,
					array(
						'Entretien.dateentretien',
						'Entretien.typeentretien',
						'Entretien.commentaireentretien',
					),
					array(
						'id' => 'TableEntretiens'.$i,
						'th' => true,
						'options' => $options,
					)
				);
			}
			echo '</div>';

			echo '<div class="scrollable" id="listeCERs"><h3>' . __d('bilansparcours66', 'Dernier CER') . '</h3>';
			echo $this->Default3->view(
				$contratsinsertion,
				array(
					'Contratinsertion.referent_id' => array( 'type' => 'select' ),
					'Contratinsertion.sitfam_ci',
					'Contratinsertion.sitpro_ci',
					'Contratinsertion.observ_benef',
					'Contratinsertion.nature_projet',
					'Contratinsertion.duree_engag',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
				),
				array(
					'th' => true,
					'options' => $options,
				)
			);
			echo '</div>';
		?>
	<?php
			echo $this->Default2->subform(
				array(
					'Bilanparcours66.situationperso',
					'Bilanparcours66.situationpro' => array( 'type' => 'hidden' )
				),
				array(
					'options' => $options
				)
			);

			/* INFO : situationpro, objinit, objatteint et objnew intégré dans situationperso
			echo $this->Xhtml->tag(
				'p',
				'Bilan du parcours d\'insertion :',
				array(
					'style' => ' font-size: 12px; font-weight:bold;'
				)
			);*/

			echo $this->Default2->subform(
				array(
					'Bilanparcours66.objinit' => array( 'type' => 'hidden' ),
					'Bilanparcours66.objatteint' => array( 'type' => 'hidden' ),
					'Bilanparcours66.objnew' => array( 'type' => 'hidden' )
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>
</fieldset>

<fieldset>
		<?php
			echo $this->Xhtml->tag(
				'h3',
				$this->Form->input(
					'Bilanparcours66.motifep',
					array(
						'type'=>'checkbox',
						'label'=> 'Motifs de la saisine de l\'équipe pluridisciplinaire ',
						'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
					)
				)
			);
		?>
		<fieldset id="motifsaisine" class="invisible">
		<?php
			echo $this->Default2->subform(
				array(
					'Bilanparcours66.motifsaisine'
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>
</fieldset>

<script type="text/javascript">
	document.observe("dom:loaded", function() {


	} );
</script>
	<?php
		echo $this->Xhtml->tag(
			'p',
			'Proposition du référent :',
			array(
				'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
			)
		);

		if (isset($this->validationErrors['Bilanparcours66']['proposition'])) {
			echo $this->Xhtml->tag(
				'div',
				$this->Xhtml->tag(
					'div',
					$this->validationErrors['Bilanparcours66']['proposition'][0],
					array(
						'class' => 'error-message'
					)
				),
				array(
					'class' => 'error'
				)
			);
		}

		if ( $this->action == 'edit' ){
			echo $this->Xform->input( 'Bilanparcours66.proposition', array( 'type' => 'hidden' ) );
			echo $this->Xform->input( 'Bilanparcours66.maintienorientation', array( 'type' => 'hidden' ) );
			echo $this->Xform->input( 'Bilanparcours66.positionbilan', array( 'type' => 'hidden' ) );
			echo $this->Xform->input( 'Bilanparcours66.nvtypeorient_id', array( 'type' => 'hidden' ) );
			echo $this->Xform->input( 'Bilanparcours66.nvstructurereferente_id', array( 'type' => 'hidden' ) );
		}
	?>

	<fieldset>
		<?php
			/// Aucune proposition requise pour le bilan
			$tmp = radioBilan( $this, 'Bilanparcours66.proposition', 'aucun', 'Bilan de parcours simple' );
			echo $this->Xhtml->tag( 'h3', $tmp );
			echo $this->Xform->input( 'Bilanparcours66.changementrefsansep', array( 'type' => 'hidden', 'value' => 'N' ) );
		?>
	</fieldset>

	<fieldset>
		<?php
			/// Traitement de l'orientation sans passage en EP locale
			$tmp = radioBilan( $this, 'Bilanparcours66.proposition', 'traitement', 'Traitement de l\'orientation du dossier sans passage en EP Locale' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
		<fieldset id="traitement" class="invisible">
            <?php
                echo $this->Default2->subform(
                    array(
                        'Bilanparcours66.choixsanspassageep' => array( 'type' => 'radio', 'required' => true )
                    ),
                    array(
                        'options' => $options
                    )
                );
            ?>
			<fieldset id="cgOrientationActuelle">
				<legend>Maintien de l'orientation SOCIALE</legend>
				<?php
						if ( $this->action == 'edit' ) {
							$defaultvaluetypeorient_id = $this->request->data['Bilanparcours66']['nvtypeorient_id'];
							$defaultvaluestructurereferente_id = implode( '_', array( $this->request->data['Bilanparcours66']['nvtypeorient_id'], $this->request->data['Bilanparcours66']['nvstructurereferente_id'] ) );

							echo $this->Xhtml->tag(
								'p',
								'Orientation SOCIALE (au moment de la création du bilan de parcours) : '.Set::extract( $this->request->data, 'Orientstruct.Typeorient.lib_type_orient' )
							);
						}
						else {
							if( !empty( $personne['Orientstruct'] ) ) {
								$defaultvaluetypeorient_id = ( isset( $personne['Orientstruct'][0]['typeorient_id'] ) ? $personne['Orientstruct'][0]['typeorient_id'] : null );
								$defaultvaluestructurereferente_id = implode( '_', array( $personne['Orientstruct'][0]['typeorient_id'], $personne['Orientstruct'][0]['structurereferente_id'] ) );

								echo $this->Xhtml->tag(
									'p',
									'Orientation SOCIALE actuelle : '.Set::extract( $personne, 'Orientstruct.0.Typeorient.lib_type_orient' )
								);
							}
							else{
								$defaultvaluestructurereferente_id = $defaultvaluetypeorient_id = null;
							}
						}


					echo $this->Default2->subform(
						array(
							'Bilanparcours66.sansep_typeorientprincipale_id' => array( 'type' => 'radio', 'options' => $options['Bilanparcours66']['typeorientprincipale_id'], 'required' => true, 'value' => ( ( isset( $this->request->data['Bilanparcours66']['typeorientprincipale_id'] ) ) ? $this->request->data['Bilanparcours66']['typeorientprincipale_id'] : null ) )
						),
						array(
							'options' => $options,
							'domain' => $domain
						)
					);

					foreach( $options['Bilanparcours66']['typeorientprincipale_id'] as $key => $value ) {
						echo "<div id='maintienOrientSansEp{$key}'>";
						echo $this->Default2->subform(
							array(
								'Bilanparcours66.nvtypeorient_id' => array( 'required'=> true, 'id' => 'Bilanparcours66NvtypeorientIdSansEp'.$key, 'options' => $options['Bilanparcours66']['nvtypeorient_id'][$key], 'value' => $defaultvaluetypeorient_id ),
								'Bilanparcours66.nvstructurereferente_id' => array( 'required'=> true, 'id' => 'Bilanparcours66NvstructurereferenteIdSansEp'.$key, 'options' => $options['Bilanparcours66']['nvstructurereferente_id'], 'value' => $defaultvaluestructurereferente_id )
							),
							array(
								'options' => $options,
								'domain' => $domain
							)
						);
						echo "</div>";
					}

					echo "<div id='cgMaintienOrientSansEpMemeRef' class='aere";
						if ( isset( $this->validationErrors['Bilanparcours66']['changementref'] ) && !empty( $this->validationErrors['Bilanparcours66']['changementref'] ) ) {
							echo " error";
						}
					echo "'>";
						echo $this->Xform->input( 'Bilanparcours66.changementrefsansep', array( 'type' => 'hidden', 'value' => 'N' ) );
						echo "Sans changement de référent.";
						if ( isset( $this->validationErrors['Bilanparcours66']['changementref'] ) && !empty( $this->validationErrors['Bilanparcours66']['changementref'] ) ) {
							echo $this->Xhtml->tag(
								'div',
								$this->validationErrors['Bilanparcours66']['changementref'][0],
								array(
									'class' => 'error-message'
								)
							);
						}
					echo "</div>";
					echo "<div id='cgMaintienOrientSansEpChangementRef' class='aere'>";
						echo $this->Xform->input( 'Bilanparcours66.changementrefsansep', array( 'type' => 'hidden', 'value' => 'O' ) );
						echo "Avec changement de référent.";
					echo "</div>";
				?>
				<fieldset id="cgContratReconduitSansEp">
					<legend>Reconduction du contrat librement débattu</legend>
					<?php
                        echo $this->Xhtml->tag( 'p', 'Etes-vous dans la limite des 24 mois pour la contractualisation ? <br /> Actuellement, '.$nbCumulDureeCER66.' mois de contractualisation sont effectifs', array( 'class' => 'notice' ) );

						echo $this->Default2->subform(
							array(
								'Bilanparcours66.duree_engag' => array( 'required' => true, 'id' => 'Bilanparcours66DureeEngagSansEp' ),
								'Bilanparcours66.ddreconductoncontrat' => array( 'required' => true, 'id' => 'Bilanparcours66DdreconductoncontratSansEp' ),
								'Bilanparcours66.dfreconductoncontrat' => array( 'required' => true, 'id' => 'Bilanparcours66DfreconductoncontratSansEp' ),
								'Bilanparcours66.nvcontratinsertion_id' => array( 'type' => 'hidden' )
							),
							array(
								'options' => $options,
								'domain' => $domain
							)
						);
					?>
				</fieldset>
			</fieldset>


            <fieldset id="cgReorientationEmploi">
                <legend>Réorientation vers PE</legend>
                <?php
                    if ( $this->action == 'edit' ) {
                        $defaultvaluetypeorient_id = $this->request->data['Bilanparcours66']['nvtypeorient_id'];
                        $defaultvaluestructurereferente_id = implode( '_', array( $this->request->data['Bilanparcours66']['nvtypeorient_id'], $this->request->data['Bilanparcours66']['nvstructurereferente_id'] ) );

                        echo $this->Xhtml->tag(
                            'p',
                            'Orientation (au moment de la création du bilan de parcours) : '.Set::extract( $this->request->data, 'Orientstruct.Typeorient.lib_type_orient' )
                        );
                    }
                    else {
                        if( !empty( $personne['Orientstruct'] ) ) {
                            $defaultvaluetypeorient_id = ( isset( $personne['Orientstruct'][0]['typeorient_id'] ) ? $personne['Orientstruct'][0]['typeorient_id'] : null );
                            $defaultvaluestructurereferente_id = implode( '_', array( $personne['Orientstruct'][0]['typeorient_id'], $personne['Orientstruct'][0]['structurereferente_id'] ) );

                            echo $this->Xhtml->tag(
                                'p',
                                'Orientation actuelle : '.Set::extract( $personne, 'Orientstruct.0.Typeorient.lib_type_orient' )
                            );
                        }
                        else{
                            $defaultvaluestructurereferente_id = $defaultvaluetypeorient_id = null;
                        }
                    }


                    echo "<div id='cgReorientationPESansEpChangementRef' class='aere'>";
						echo $this->Xform->input( 'Bilanparcours66.changementrefsansep', array( 'type' => 'hidden', 'value' => 'O' ) );

                        foreach( $options['Bilanparcours66']['orientationpro_id'] as $key => $value ) {
                            echo $this->Xform->input( 'Bilanparcours66.sansep_typeorientprincipale_id', array( 'type' => 'hidden', 'value' => $key ) );

                            echo $this->Default2->subform(
                                array(
                                    'Bilanparcours66.nvtypeorient_id' => array( 'required'=> true, 'id' => 'Bilanparcours66NvtypeorientIdPESansEp', 'options' => $options['Bilanparcours66']['nvtypeorient_id'][$key] ),
                                    'Bilanparcours66.nvstructurereferente_id' => array( 'required'=> true, 'id' => 'Bilanparcours66NvstructurereferenteIdPESansEp', 'options' => $options['Bilanparcours66']['nvstructurereferente_id'], 'value' => $defaultvaluestructurereferente_id )
                                ),
                                array(
                                    'options' => $options,
                                    'domain' => $domain
                                )
                            );
                        }
                    echo "Avec changement de référent.";

                    echo "</div>";
                ?>
            </fieldset>

		</fieldset>
	</fieldset>
	<fieldset>
		<?php
			/// "Commission Parcours": Examen du dossier avec passage en EP Locale
			$tmp = radioBilan( $this, 'Bilanparcours66.proposition', 'parcours', '"Commission Parcours": Examen du dossier avec passage en EP Locale' );
			echo $this->Xhtml->tag( 'h3', $tmp );

			if( $dossiersepsencours['saisinesbilansparcourseps66'] ) {
				echo $this->Xhtml->tag( 'p', 'Ce dossier est déjà en cours d\'examen par la Commission Parcours', array( 'class' => 'notice' ) );
			}

		?>
		<fieldset id="parcours" class="invisible">
			<?php
				echo $this->Default2->subform(
					array(
						'Bilanparcours66.choixparcours' => array( 'type' => 'radio', 'required' => true )
					),
					array(
						'options' => $options
					)
				);
			?>
			<fieldset id="cgMaintienOrientationAvecEp">
				<legend>Maintien de l'orientation SOCIALE</legend>
				<?php
						if ( $this->action == 'edit' ) {
							$defaultvaluetypeorient_id = $this->request->data['Bilanparcours66']['nvtypeorient_id'];
							$defaultvaluestructurereferente_id = implode( '_', array( $this->request->data['Bilanparcours66']['nvtypeorient_id'], $this->request->data['Bilanparcours66']['nvstructurereferente_id'] ) );

							echo $this->Xhtml->tag(
								'p',
								'Orientation SOCIALE (au moment de la création du bilan de parcours) : '.Set::extract( $this->request->data, 'Orientstruct.Typeorient.lib_type_orient' )
							);
						}
						else {
							if( !empty( $personne['Orientstruct'] ) ) {
								$defaultvaluetypeorient_id = $personne['Orientstruct'][0]['typeorient_id'];
								$defaultvaluestructurereferente_id = implode( '_', array( $personne['Orientstruct'][0]['typeorient_id'], $personne['Orientstruct'][0]['structurereferente_id'] ) );

								echo $this->Xhtml->tag(
									'p',
									'Orientation SOCIALE actuelle : '.Set::extract( $personne, 'Orientstruct.0.Typeorient.lib_type_orient' )
								);
							}
							else{
								$defaultvaluestructurereferente_id = $defaultvaluetypeorient_id = null;
							}
						}

					echo $this->Default2->subform(
						array(
							'Bilanparcours66.avecep_typeorientprincipale_id' => array( 'type' => 'radio', 'options' => $options['Bilanparcours66']['typeorientprincipale_id'], 'required' => true, 'value' => ( ( isset( $this->request->data['Bilanparcours66']['typeorientprincipale_id'] ) ) ? $this->request->data['Bilanparcours66']['typeorientprincipale_id'] : null ) )
						),
						array(
							'options' => $options,
							'domain' => $domain
						)
					);

					foreach( $options['Bilanparcours66']['typeorientprincipale_id'] as $key => $value ) {
						echo "<div id='cgMaintienOrientAvecEp{$key}'>";
						echo $this->Default2->subform(
							array(
								'Bilanparcours66.nvtypeorient_id' => array( 'required'=> true, 'id' => 'Bilanparcours66NvtypeorientIdAvecEp'.$key, 'options' => $options['Bilanparcours66']['nvtypeorient_id'][$key], 'value' => $defaultvaluetypeorient_id ),
								'Bilanparcours66.nvstructurereferente_id' => array( 'required'=> true, 'id' => 'Bilanparcours66NvstructurereferenteIdAvecEp'.$key, 'options' => $options['Bilanparcours66']['nvstructurereferente_id'], 'value' => $defaultvaluestructurereferente_id )
							),
							array(
								'options' => $options,
								'domain' => $domain
							)
						);
						echo "</div>";
					}
					echo "<div id='cgMaintienOrientAvecEpMemeRef' class='aere";
						if ( isset( $this->validationErrors['Bilanparcours66']['changementref'] ) && !empty( $this->validationErrors['Bilanparcours66']['changementref'] ) ) {
							echo " error";
						}
					echo "'>";
						echo $this->Xform->input( 'Bilanparcours66.changementrefavecep', array( 'type' => 'hidden', 'value' => 'N' ) );
						echo "Sans changement de référent.";
						if ( isset( $this->validationErrors['Bilanparcours66']['changementref'] ) && !empty( $this->validationErrors['Bilanparcours66']['changementref'] ) ) {
							echo $this->Xhtml->tag(
								'div',
								$this->validationErrors['Bilanparcours66']['changementref'][0],
								array(
									'class' => 'error-message'
								)
							);
						}
					echo "</div>";

					echo "<div id='cgMaintienOrientAvecEpChangementRef' class='aere'>";
						if( @$this->request->data['Bilanparcours66']['nvtypeorient_id'] != $defaultvaluetypeorient_id ) {
                            echo $this->Xform->input( 'Bilanparcours66.changementrefavecep', array( 'type' => 'hidden', 'value' => 'O' ) );
							echo "Avec changement de référent.";
						}
						else if( @$this->request->data['Bilanparcours66']['nvtypeorient_id'] != Set::extract( $this->request->data, 'Orientstruct.Typeorient.id' ) ) {
                            echo "Avec changement de référent.";
						}
						else {
                            echo $this->Xform->input( 'Bilanparcours66.changementrefavecep', array( 'type' => 'hidden', 'value' => 'N' ) );
							echo "Sans changement de référent.";
						}
					echo "</div>";
				?>

			</fieldset>
			<fieldset id="cgReorientationAvecEp">
				<legend>Réorientation du SOCIAL vers le professionnel</legend>
				<?php
					if ( $this->action == 'edit' ) {
						$defaultvaluetypeorient_id = $this->request->data['Bilanparcours66']['nvtypeorient_id'];
						$defaultvaluestructurereferente_id = implode( '_', array( $this->request->data['Bilanparcours66']['nvtypeorient_id'], $this->request->data['Bilanparcours66']['nvstructurereferente_id'] ) );
					}
					else {
						$defaultvaluetypeorient_id = null;
						$defaultvaluestructurereferente_id = null;
					}

					$typeorientprincipale = Configure::read( 'Orientstruct.typeorientprincipale' );
					$typeorientemploiId = $typeorientprincipale['Emploi'][0];

					echo $this->Default->subform(
						array(
							'Bilanparcours66.avecep_typeorientprincipale_id' => array( 'type' => 'hidden', 'value' => $typeorientemploiId ),
							'Bilanparcours66.nvtypeorient_id' => array( 'required' => true, 'options' => $options['Bilanparcours66']['nvtypeorient_id'][$typeorientemploiId], 'value' => $defaultvaluetypeorient_id, 'id' => 'Saisinebilanparcoursep66TypeorientId' ),
							'Bilanparcours66.nvstructurereferente_id' => array( 'required' => true, 'value' => $defaultvaluestructurereferente_id, 'id' => 'Saisinebilanparcoursep66StructurereferenteId' )
						),
						array(
							'options' => $options
						)
					);
					echo "<div id='cgReorientAvecEpChangementRef' class='aere'>";
						echo $this->Xform->input( 'Bilanparcours66.changementrefavecep', array( 'type' => 'hidden', 'value' => 'O' ) );
						echo "Avec changement de référent.";
					echo "</div>";
				?>
			</fieldset>
		</fieldset>
	</fieldset>
	<fieldset>
		<?php
			/// "Commission Audition": Examen du dossier par la commission EP Locale
			$tmp = radioBilan( $this, 'Bilanparcours66.proposition', 'audition', '"Commission Audition": Examen du dossier par la commission EP Locale' );
			echo $this->Xhtml->tag( 'h3', $tmp );

			if( $dossiersepsencours['defautsinsertionseps66'] ) {
				echo $this->Xhtml->tag( 'p', 'Ce dossier est déjà en cours d\'examen par la Commission Audition', array( 'class' => 'notice' ) );
			}
		?>
		<fieldset id="audition" class="invisible">
			<?php
				echo $this->Default2->subform(
					array(
						'Bilanparcours66.examenaudition' => array( 'type' => 'radio', 'required' => true )
					),
					array(
						'options' => $options
					)
				);
			?>
		</fieldset>
	</fieldset>


		<?php
			echo $this->Default2->subform(
				array(
					'Bilanparcours66.infoscomplementaires'
				),
				array(
					'options' => $options
				)
			);
			echo $this->Xhtml->tag(
				'p',
				'Observations du bénéficiaire :',
				array(
					'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
				)
			);
			echo $this->Default2->subform(
				array(
					'Bilanparcours66.observbenefrealisationbilan',
					'Bilanparcours66.observbenefcompterendu',
					'Bilanparcours66.datebilan' => array( 'dateFormat' => 'DMY', 'maxYear' => date('Y') +1, 'minYear' => 2009, 'empty' => true, 'required' => true ),
				),
				array(
					'options' => $options
				)
			);
		?>
		<div class="submit">
			<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
			<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
		</div>
</fieldset>

<fieldset id="bilanparcourspe">
	<legend>BILAN DU PARCOURS (Pôle Emploi)</legend>
		<?php
			echo $this->Default->subform(
				array(
					'Pe.Bilanparcours66.orientstruct_id' => array( 'type' => 'hidden' ),
					'Pe.Bilanparcours66.serviceinstructeur_id' => array( 'label' => 'Maison sociale' ),
					'Pe.Bilanparcours66.structurereferente_id'
				),
				array(
					'options' => $options
				)
			);

			echo '<div class ="input select';
				if (isset($this->validationErrors['Bilanparcours66']['referent_id'])) echo ' error';
			echo '">';
			echo $this->Default->subform(
				array(
					'Pe.Bilanparcours66.referent_id' => array('div'=>false)
				),
				array(
					'options' => $options
				)
			);
			if (isset($this->validationErrors['Bilanparcours66']['referent_id'])) {
				echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['referent_id'][0].'</div>';
			}
			echo '</div>';

			echo '<div class ="input select';
			if (isset($this->validationErrors['Bilanparcours66']['presenceallocataire'])) echo ' error';
			echo '">';
			echo $this->Default->subform(
				array(
					'Pe.Bilanparcours66.presenceallocataire' => array( 'required' => true, 'div' => false )
				),
				array(
					'options' => $options
				)
			);
			if (isset($this->validationErrors['Bilanparcours66']['presenceallocataire'])) echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['presenceallocataire'][0].'</div>';
			echo '</div>';
		?>

	<fieldset>
		<legend>Situation de l'allocataire</legend>
		<table class="wide noborder">
			<tr>
				<td class="mediumSize noborder">
					<strong>Statut de la personne : </strong><?php echo Set::extract( $rolepers, Set::extract( $personne, 'Prestation.rolepers' ) ); ?>
					<br />
					<strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $personne, 'Personne.qual') , $qual ).' '.Set::classicExtract( $personne, 'Personne.nom' );?>
					<br />
					<strong>Prénom : </strong><?php echo Set::classicExtract( $personne, 'Personne.prenom' );?>
					<br />
					<strong>Date de naissance : </strong><?php echo date_short( Set::classicExtract( $personne, 'Personne.dtnai' ) );?>
				</td>
				<td class="mediumSize noborder">
					<strong>N° demandeur : </strong><?php echo Set::classicExtract( $personne, 'Foyer.Dossier.numdemrsa' );?>
					<br />
					<strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $personne, 'Foyer.Dossier.matricule' );?>
					<br />
					<strong>Inscrit au Pôle emploi</strong>
					<?php
						$isPoleemploi = Set::classicExtract( $personne, 'Activite.0.act' );
						if( $isPoleemploi == 'ANP' )
							echo 'Oui';
						else
							echo 'Non';
					?>
					<br />
					<strong>N° identifiant : </strong><?php echo Set::classicExtract( $personne, 'Personne.idassedic' );?>
				</td>
			</tr>
			<tr>
				<td class="mediumSize noborder">
					<strong>Adresse : </strong><br /><?php echo Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.numvoie' ).' '.Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.libtypevoie' ).' '.Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.codepos' ).' '.Set::classicExtract( $personne, 'Foyer.Adressefoyer.0.Adresse.nomcom' );?>
				</td>
				<td class="mediumSize noborder">
					<?php if( Set::extract( $personne, 'Foyer.Modecontact.0.autorutitel' ) == 'A' ):?>
							<strong>Numéro de téléphone 1 : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.0.numtel' );?>
					<?php endif;?>
					<?php if( Set::extract( $personne, 'Foyer.Modecontact.1.autorutitel' ) == 'A' ):?>
							<br />
							<strong>Numéro de téléphone 2 : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.1.numtel' );?>
					<?php endif;?>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="mediumSize noborder">
				<?php if( Set::extract( $personne, 'Foyer.Modecontact.0.autorutiadrelec' ) == 'A' ):?>
					<strong>Adresse mail : </strong><?php echo Set::extract( $personne, 'Foyer.Modecontact.0.adrelec' );?> <!-- FIXME -->
				<?php endif;?>
				</td>
			</tr>
		</table>
	</fieldset>

	<?php
		if( $this->action == 'edit') {
			echo $this->Xform->input( 'Pe.Bilanparcours66.proposition', array( 'type' => 'hidden' ) );
			echo $this->Xform->input( 'Pe.Bilanparcours66.examenauditionpe', array( 'type' => 'hidden' ) );
		}
		echo $this->Default2->subform(
			array(
				'Pe.Bilanparcours66.textbilanparcours',
				'Pe.Bilanparcours66.observbenef',
// 				'Pe.Bilanparcours66.proposition' => array( 'type' => 'hidden', 'value' => 'parcours' )
			),
			array(
				'options' => $options
			)
		);
	?>


	<fieldset id="peAudition" >
		<?php
			/// "Commission Audition PE": Examen du dossier par la commission EP Locale
			$tmp = radioBilan( $this, 'Pe.Bilanparcours66.proposition', 'auditionpe', 'Saisine EPL Audition Défaut d\'insertion Public suivi par Pôle Emploi' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
		<fieldset id="auditionpe" class="invisible">
			<?php
			    echo $this->Default3->messages( $messages );//affichage de l'alerte si aucune orientation
				echo $this->Default2->subform(
					array(
						'Pe.Bilanparcours66.examenauditionpe' => array( 'type' => 'radio', 'required' => true )
					),
					array(
						'options' => $options
					)
				);
			?>
		</fieldset>
	</fieldset>

    <fieldset id="peEpParcours" >

        <?php
			/// "Commission Audition PE": Examen du dossier par la commission EP Locale
			$tmp = radioBilan( $this, 'Pe.Bilanparcours66.proposition', 'parcourspe', 'Saisine EPL Parcours Public suivi par Pôle Emploi' );
			echo $this->Xhtml->tag( 'h3', $tmp );
		?>
        <fieldset id="parcourspe" class="invisible">
            <?php
                echo '<div class ="input radio';
                    if (isset($this->validationErrors['Bilanparcours66']['choixparcours'])) echo ' error';
                echo '">';
                echo $this->Default->subform(
                    array(
//                        'Pe.Bilanparcours66.choixparcours' => array( 'div' => false, 'type' => 'radio', 'required' => true )
                        'Pe.Bilanparcours66.choixparcours' => array( 'div' => false, 'type' => 'hidden', 'value' => 'reorientation' )
                    ),
                    array(
                        'options' => $options
                    )
                );
                if (isset($this->validationErrors['Bilanparcours66']['choixparcours'])) {
                    echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['choixparcours'][0].'</div>';
                }
                echo '</div>';
            ?>
            <fieldset id="peMaintienOrientationAvecEp">
                <legend>Maintien de l'orientation PROFESSIONNELLE</legend>
                <?php
                    if( !empty( $personne['Orientstruct'] ) ) {
                        if ( $this->action == 'edit' && !isset( $this->request->data['Pe']['Bilanparcours66']['nvstructurereferente_id'] ) ) {
                            $defaultvaluetypeorient_id = $this->request->data['Bilanparcours66']['nvtypeorient_id'];
                            $defaultvaluestructurereferente_id = implode( '_', array( $this->request->data['Bilanparcours66']['nvtypeorient_id'], $this->request->data['Bilanparcours66']['nvstructurereferente_id'] ) );

                            echo $this->Xhtml->tag(
                                'p',
                                'Orientation PROFESSIONNELLE (au moment de la création du bilan de parcours) : '.Set::extract( $this->request->data, 'Orientstruct.Typeorient.lib_type_orient' )
                            );
                        }
                        elseif ( !isset( $this->request->data['Pe']['Bilanparcours66']['nvstructurereferente_id'] ) ) {
                            $defaultvaluetypeorient_id = $personne['Orientstruct'][0]['typeorient_id'];
                            $defaultvaluestructurereferente_id = implode( '_', array( $personne['Orientstruct'][0]['typeorient_id'], $personne['Orientstruct'][0]['structurereferente_id'] ) );

                            echo $this->Xhtml->tag(
                                'p',
                                'Orientation PROFESSIONNELLE actuelle : '.Set::extract( $personne, 'Orientstruct.0.Typeorient.lib_type_orient' )
                            );
                        }
                    }

                    $typeorientprincipale = Configure::read( 'Orientstruct.typeorientprincipale' );
                    $typeorientemploiId = $typeorientprincipale['Emploi'][0];

                    echo '<div class = "input">';
                    echo $this->Default->subform(
                        array(
                            'Pe.Bilanparcours66.avecep_typeorientprincipale_id' => array( 'type' => 'hidden', 'value' => $typeorientemploiId )
                        ),
                        array(
                            'options' => $options
                        )
                    );
                    echo '</div>';

                    echo '<div class ="input select';
                        if (isset($this->validationErrors['Bilanparcours66']['nvtypeorient_id'])) echo ' error';
                    echo '">';
                    echo $this->Default->subform(
                        array(
                            'Pe.Bilanparcours66.nvtypeorient_id' => array( 'required' => true, 'options' => $options['Bilanparcours66']['nvtypeorient_id'][$typeorientemploiId], 'value' => $defaultvaluetypeorient_id, 'id' => 'PeSaisinebilanparcoursep66TypeorientId' )
                        ),
                        array(
                            'options' => $options
                        )
                    );
                    if (isset($this->validationErrors['Bilanparcours66']['nvtypeorient_id'])) {
                        echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['nvtypeorient_id'][0].'</div>';
                    }
                    echo '</div>';

                    echo '<div class ="input select';
                        if (isset($this->validationErrors['Bilanparcours66']['nvstructurereferente_id'])) echo ' error';
                    echo '">';
                    echo $this->Default->subform(
                        array(
                            'Pe.Bilanparcours66.nvstructurereferente_id' => array( 'required' => true, 'value' => $defaultvaluestructurereferente_id, 'id' => 'PeSaisinebilanparcoursep66StructurereferenteId' )
                        ),
                        array(
                            'options' => $options
                        )
                    );
                    if (isset($this->validationErrors['Bilanparcours66']['nvstructurereferente_id'])) {
                        echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['nvstructurereferente_id'][0].'</div>';
                    }
                    echo '</div>';

                    echo "<div id='peMaintienOrientAvecEpMemeRef' class='aere";
                        if ( isset( $this->validationErrors['Bilanparcours66']['changementref'] ) && !empty( $this->validationErrors['Bilanparcours66']['changementref'] ) ) {
                            echo " error";
                        }
                    echo "'>";
                        echo $this->Xform->input( 'Pe.Bilanparcours66.changementrefavecep', array( 'type' => 'hidden', 'value' => 'N' ) );
                        echo "Sans changement de référent.";
                        if ( isset( $this->validationErrors['Bilanparcours66']['changementref'] ) && !empty( $this->validationErrors['Bilanparcours66']['changementref'] ) ) {
                            echo $this->Xhtml->tag(
                                'div',
                                $this->validationErrors['Bilanparcours66']['changementref'][0],
                                array(
                                    'class' => 'error-message'
                                )
                            );
                        }
                    echo "</div>";
                    echo "<div id='peMaintienOrientAvecEpChangementRef' class='aere'>";
                        echo $this->Xform->input( 'Pe.Bilanparcours66.changementrefavecep', array( 'type' => 'hidden', 'value' => 'O' ) );
                        echo "Avec changement de référent.";
                    echo "</div>";
                ?>
            </fieldset>
            <fieldset id="peReorientationAvecEp">
                <legend>Réorientation du PROFESSIONEL vers le SOCIAL</legend>
                <?php
                    if ( $this->action == 'edit' ) {
                        $defaultvaluetypeorient_id = $this->request->data['Bilanparcours66']['nvtypeorient_id'];
                        $defaultvaluestructurereferente_id = implode( '_', array( $this->request->data['Bilanparcours66']['nvtypeorient_id'], $this->request->data['Bilanparcours66']['nvstructurereferente_id'] ) );
                    }
                    else {
                        $defaultvaluetypeorient_id = null;
                        $defaultvaluestructurereferente_id = null;
                    }

                    // INFO: pour les cas où le champ caché n'apparaîtra pas (cf. FormHelper::radio )
                    if( isset( $this->request->data['Pe']['Bilanparcours66']['avecep_typeorientprincipale_id'] ) && !empty( $this->request->data['Pe']['Bilanparcours66']['avecep_typeorientprincipale_id'] ) ) {
                        echo $this->Xform->input( 'Pe.Bilanparcours66.avecep_typeorientprincipale_id', array( 'type' => 'hidden', 'value' => '' ) );
//                        echo $this->Xform->input( 'Pe.Bilanparcours66.avecep_typeorientprincipale_id', array( 'type' => 'hidden', 'value' => ( isset( $this->request->data['Bilanparcours66']['typeorientprincipale_id'] ) ? $this->request->data['Bilanparcours66']['typeorientprincipale_id'] : null ) ) );
                    }

                    echo '<div class ="input radio';
                        if (isset($this->validationErrors['Bilanparcours66']['avecep_typeorientprincipale_id'])) echo ' error';
                    echo '">';
                    echo $this->Default->subform(
                        array(
                            'Pe.Bilanparcours66.avecep_typeorientprincipale_id' => array( 'div' => false, 'options' => $options['Bilanparcours66']['typeorientprincipale_id'], 'type' => 'radio', 'required' => true, 'value' => ( isset( $this->request->data['Bilanparcours66']['typeorientprincipale_id'] ) ? $this->request->data['Bilanparcours66']['typeorientprincipale_id'] : null ) )
                        ),
                        array(
                            'options' => $options
                        )
                    );
                    if (isset($this->validationErrors['Bilanparcours66']['avecep_typeorientprincipale_id'])) {
                        echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['avecep_typeorientprincipale_id'][0].'</div>';
                    }
                    echo '</div>';

                    foreach( $options['Bilanparcours66']['typeorientprincipale_id'] as $key => $value ) {
                        echo "<div id='peMaintienOrientAvecEp{$key}'>";

                            echo '<div class ="input select';
                                if (isset($this->validationErrors['Bilanparcours66']['nvtypeorient_id'])) echo ' error';
                            echo '">';
                            echo $this->Default->subform(
                                array(
                                    'Pe.Bilanparcours66.nvtypeorient_id' => array( 'div' => false, 'required'=> true, 'id' => 'PeBilanparcours66NvtypeorientIdAvecEp'.$key, 'options' => $options['Bilanparcours66']['nvtypeorient_id'][$key], 'value' => $defaultvaluetypeorient_id )
                                ),
                                array(
                                    'options' => $options
                                )
                            );
                            if (isset($this->validationErrors['Bilanparcours66']['nvtypeorient_id'])) {
                                echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['nvtypeorient_id'][0].'</div>';
                            }
                            echo '</div>';

                            echo '<div class ="input select';
                                if (isset($this->validationErrors['Bilanparcours66']['nvstructurereferente_id'])) echo ' error';
                            echo '">';
                            echo $this->Default->subform(
                                array(
                                    'Pe.Bilanparcours66.nvstructurereferente_id' => array( 'div' => false, 'required'=> true, 'id' => 'PeBilanparcours66NvstructurereferenteIdAvecEp'.$key, 'options' => $options['Bilanparcours66']['nvstructurereferente_id'], 'value' => $defaultvaluestructurereferente_id )
                                ),
                                array(
                                    'options' => $options
                                )
                            );
                            if (isset($this->validationErrors['Bilanparcours66']['nvstructurereferente_id'])) {
                                echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['nvstructurereferente_id'][0].'</div>';
                            }
                            echo '</div>';

                        echo "</div>";
                    }
                    echo "<div id='peReorientAvecEpChangementRef' class='aere'>";
                        echo $this->Xform->input( 'Pe.Bilanparcours66.changementrefavecep', array( 'type' => 'hidden', 'value' => 'O' ) );
                        echo "Avec changement de référent.";
                    echo "</div>";
                ?>
            </fieldset>
        </fieldset>

        </fieldset>
            <?php
                echo '<div class ="input date';
                    if (isset($this->validationErrors['Bilanparcours66']['datebilan'])) echo ' error';
                echo '">';

                echo $this->Default->subform(
                    array(
                        'Pe.Bilanparcours66.datebilan' => array( 'dateFormat' => 'DMY', 'maxYear' => date('Y') +1, 'minYear' => 2009, 'empty' => true, 'required' => true, 'div' => false )
                    ),
                    array(
                        'options' => $options
                    )
                );
                if (isset($this->validationErrors['Bilanparcours66']['datebilan'])) {
                    echo '<div class="error-message">'.$this->validationErrors['Bilanparcours66']['datebilan'][0].'</div>';
                }
                echo '</div>';
            ?>
	<div class="submit">
		<?php echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );?>
		<?php echo $this->Form->submit('Annuler', array( 'name' => 'Cancel', 'div' => false ) );?>
	</div>
</fieldset>

<?php if ( $this->action == 'edit' && isset( $passagecommissionep['Decisionsaisinebilanparcoursep66'][0]['id'] ) && !empty( $passagecommissionep['Decisionsaisinebilanparcoursep66'][0]['id'] ) ) {
	$avisep = $passagecommissionep['Decisionsaisinebilanparcoursep66'][0];

	echo '<fieldset><legend><strong>AVIS DE L\'EP Locale Commission Parcours du '.date('d/m/Y', strtotime($passagecommissionep['Commissionep']['dateseance'])).'</strong></legend>';
		if ( $avisep['decision'] == 'reorientation' ) {
			echo $this->Xhtml->tag(
				'strong',
				'Réorientation du PROFESSIONNEL vers le SOCIAL'
			);
            echo $this->Xhtml->tag(
				'p',
				'Type d\'orientation : '.@$avisep['Typeorient']['lib_type_orient'].' <br /> Structure référente : '.@$avisep['Structurereferente']['lib_struc'].' <br /> Référent : '.@$avisep['Referent']['nom'].' '.@$avisep['Referent']['prenom']
			);
		}
		elseif ( $avisep['decision'] == 'maintien' ) {

			echo $this->Xhtml->tag(
				'strong',
				'Maintien de l\'orientation SOCIALE : '.$avisep['Typeorient']['lib_type_orient']//$options['Decisionsaisinebilanparcoursep66']['maintienorientparcours'][$avisep['maintienorientparcours']]
			);
			echo $this->Xhtml->tag(
				'p',
				$options['Decisionsaisinebilanparcoursep66']['changementrefparcours'][$avisep['changementrefparcours']]
			);
		}
		echo $this->Xhtml->tag(
			'p',
			"Argumentaire précis (avis motivé) de l'EP Locale :",
			array(
				'style' => 'font-weight:bold; text-decoration:underline'
			)
		);
		echo $this->Xhtml->tag(
			'p',
			$avisep['commentaire']
		);
	echo '</fieldset>';
}


elseif ( $this->action == 'edit' && isset( $passagecommissionep['Decisiondefautinsertionep66'][0]['id'] ) && !empty( $passagecommissionep['Decisiondefautinsertionep66'][0]['id'] ) ) {
	$avisep = $passagecommissionep['Decisiondefautinsertionep66'][0];
	echo '<fieldset><legend><strong>AVIS DE L\'EP Locale Commission Audition du '.date('d/m/Y', strtotime($passagecommissionep['Commissionep']['dateseance'])).'</strong></legend>';

        echo $this->Xhtml->tag(
            'p',
            '<strong>Avis :</strong>'
        );
		if ( isset( $passagecommissionep['Decisiondefautinsertionep66'][0]['decisionsup'] ) && !empty( $passagecommissionep['Decisiondefautinsertionep66'][0]['decisionsup'] ) ) {
			echo $this->Xhtml->tag(
				'p',
				$options['Decisiondefautinsertionep66']['decisionsup'][$avisep['decisionsup']],
                array(
                    'style' => 'padding: 0 2em;'
                )
			);
		}
		$avisEPTypeorient = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][0]['typeorient_id'], $typesorients );
		$avisEPStructure = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][0]['structurereferente_id'], $structuresreferentes );
		$avisEPReferent = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][0]['referent_id'], $options['Bilanparcours66']['nvsansep_referent_id'] );

		echo $this->Xhtml->tag(
			'p',
			$options['Decisiondefautinsertionep66']['decision'][$avisep['decision']],
            array(
                'style' => 'padding: 0 2em;'
            )
		);

		if( !empty( $passagecommissionep['Decisiondefautinsertionep66'][0]['typeorient_id'] ) ) {
			echo $this->Xhtml->tag(
				'p',
				'<strong>Type d\'orientation : </strong>'.$avisEPTypeorient
			);

			echo $this->Xhtml->tag(
				'p',
				'<strong>Structure référente : </strong>'.$avisEPStructure
			);

			echo $this->Xhtml->tag(
				'p',
				'<strong>Nom du prescripteur : </strong>'.$avisEPReferent
			);
		}

		echo $this->Xhtml->tag(
			'p',
			"Argumentaire précis (avis motivé) de l'EP Locale :",
			array(
				'style' => 'font-weight:bold; text-decoration:underline'
			)
		);
		echo $this->Xhtml->tag(
			'p',
			$avisep['commentaire']
		);
		if( !empty( $avisep['commentairebeneficiaire'] ) ) {
			 echo $this->Xhtml->tag(
	            'p',
	            "Commentaire bénéficiaire :",
	            array(
	                'style' => 'font-weight:bold; text-decoration:underline'
	            )
	        );
	        echo $this->Xhtml->tag(
	            'p',
	            $avisep['commentairebeneficiaire']
	        );
	    }
	echo '</fieldset>';
}

// EP Audition se transformant en EP Parcours
if ( $this->action == 'edit' && isset( $passagecommissionep['Decisiondefautinsertionep66'][1]['id'] ) && !empty( $passagecommissionep['Decisiondefautinsertionep66'][1]['id'] ) && !empty( $passagecommissionep['Decisiondefautinsertionep66'][1]['decisionsup'] ) ) {
	echo $this->Xhtml->tag(
		'p',
		'DECISION DU COORDONNATEUR TECHNIQUE',
		array(
			'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
		)
	);

	$decisioncg = $passagecommissionep['Decisiondefautinsertionep66'][1];

	echo '<fieldset><legend><strong>Suite à l\'avis de l\'EP Locale "Commission Audition"</strong></legend>';
		if ( $decisioncg['decision'] == 'reorientationprofverssoc' ) {
			echo $this->Xhtml->tag(
				'p',
				'<strong>Décision : </strong>Réorientation du PROFESSIONNEL vers le SOCIAL'
			);
            // Réorientation choisie
            if( !empty( $passagecommissionep['Decisiondefautinsertionep66'][1]['typeorient_id'] ) ) {
                $decisionCTTypeorient = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][1]['typeorient_id'], $typesorients );
                $decisionCTStructure = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][1]['structurereferente_id'], $structuresreferentes );
                $decisionCTReferent = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][1]['referent_id'], $options['Bilanparcours66']['nvsansep_referent_id'] );

                echo $this->Xhtml->tag(
                    'p',
                    '<strong>Type d\'orientation : </strong>'.$decisionCTTypeorient
                );

                echo $this->Xhtml->tag(
                    'p',
                    '<strong>Structure référente : </strong>'.$decisionCTStructure
                );

                echo $this->Xhtml->tag(
                    'p',
                    '<strong>Nom du prescripteur : </strong>'.$decisionCTReferent
                );
            }
			$accord = ( $avisep['decision'] == $decisioncg['decision'] ) ? 'Oui' : 'Non';
			echo $this->Xhtml->tag(
				'p',
				"<strong>En accord avec l'avis de l'EPL commission Audition : </strong>".$accord
			);
		}
		elseif ( $decisioncg['decision'] == 'reorientationsocverspro' ) {
			echo $this->Xhtml->tag(
				'strong',
				'<strong>Décision : </strong>Réorientation du SOCIAL vers le PROFESSIONNEL'
			);
            // Réorientation choisie
            if( !empty( $passagecommissionep['Decisiondefautinsertionep66'][1]['typeorient_id'] ) ) {
                $decisionCTTypeorient = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][1]['typeorient_id'], $typesorients );
                $decisionCTStructure = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][1]['structurereferente_id'], $structuresreferentes );
                $decisionCTReferent = Set::enum( $passagecommissionep['Decisiondefautinsertionep66'][1]['referent_id'], $options['Bilanparcours66']['nvsansep_referent_id'] );

                echo $this->Xhtml->tag(
                    'p',
                    '<strong>Type d\'orientation : </strong>'.$decisionCTTypeorient
                );

                echo $this->Xhtml->tag(
                    'p',
                    '<strong>Structure référente : </strong>'.$decisionCTStructure
                );

                echo $this->Xhtml->tag(
                    'p',
                    '<strong>Nom du prescripteur : </strong>'.$decisionCTReferent
                );
            }
			$accord = ( $avisep['decision'] == $decisioncg['decision'] ) ? 'Oui' : 'Non';
			echo $this->Xhtml->tag(
				'p',
				"En accord avec l'avis de l'EPL commission Parcours : ".$accord
			);
		}
		echo $this->Xhtml->tag(
			'p',
			"Commentaire :",
			array(
				'style' => 'font-weight:bold; text-decoration:underline'
			)
		);
		echo $this->Xhtml->tag(
			'p',
			$decisioncg['commentaire']
		);
	echo '</fieldset>';
}

//avis EP (index [0])
if ( $this->action == 'edit' && isset( $passagecommissionep['Decisionsaisinebilanparcoursep66'][0]['id'] ) && !empty( $passagecommissionep['Decisionsaisinebilanparcoursep66'][0]['id'] ) ) {
	echo $this->Xhtml->tag(
		'p',
		'AVIS EP',
		array(
			'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
		)
	);
	$decisioncg = $passagecommissionep['Decisionsaisinebilanparcoursep66'][0];
	echo '<fieldset><legend><strong>Suite à l\'avis de l\'EP Locale "Commission Parcours"</strong></legend>';
		if ( $decisioncg['decision'] == 'reorientation' ) {
			echo $this->Xhtml->tag(
				'strong',
				'Réorientation du PROFESSIONNEL vers le SOCIAL'
			);
			$accord = ( $avisep['decision'] == $decisioncg['decision'] ) ? 'Oui' : 'Non';
			echo $this->Xhtml->tag(
				'p',
				"En accord avec l'avis de l'EPL commission Parcours : ".$accord
			);
		}
		elseif ( $decisioncg['decision'] == 'maintien' ) {
			echo $this->Xhtml->tag(
				'strong',
				'Maintien de l\'orientation SOCIALE : '.$decisioncg['Typeorient']['lib_type_orient']//$options['Decisionsaisinebilanparcoursep66']['maintienorientparcours'][$decisioncg['maintienorientparcours']]
			);
			echo $this->Xhtml->tag(
				'p',
				$options['Decisionsaisinebilanparcoursep66']['changementrefparcours'][$decisioncg['changementrefparcours']]
			);
		}
		echo $this->Xhtml->tag(
			'p',
			"Commentaire :",
			array(
				'style' => 'font-weight:bold; text-decoration:underline'
			)
		);
		echo $this->Xhtml->tag(
			'p',
			$decisioncg['commentaire']
		);
	echo '</fieldset>';
}
//Decision coordonnateur technique (index [1])
if ( $this->action == 'edit' && isset( $passagecommissionep['Decisionsaisinebilanparcoursep66'][1]['id'] ) && !empty( $passagecommissionep['Decisionsaisinebilanparcoursep66'][1]['id'] ) ) {
	echo $this->Xhtml->tag(
		'p',
		'DECISION DU COORDONNATEUR TECHNIQUE',
		array(
			'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
		)
	);
	$decisioncg = $passagecommissionep['Decisionsaisinebilanparcoursep66'][1];
	echo '<fieldset><legend><strong>Suite à l\'avis de l\'EP Locale "Commission Parcours"</strong></legend>';
		if ( $decisioncg['decision'] == 'reorientation' ) {
			echo $this->Xhtml->tag(
				'strong',
				'Réorientation du PROFESSIONNEL vers le SOCIAL'
			);
			$accord = ( $avisep['decision'] == $decisioncg['decision'] ) ? 'Oui' : 'Non';
			echo $this->Xhtml->tag(
				'p',
				"En accord avec l'avis de l'EPL commission Parcours : ".$accord
			);
		}
		elseif ( $decisioncg['decision'] == 'maintien' ) {
			echo $this->Xhtml->tag(
				'strong',
				'Maintien de l\'orientation SOCIALE : '.$decisioncg['Typeorient']['lib_type_orient']//$options['Decisionsaisinebilanparcoursep66']['maintienorientparcours'][$decisioncg['maintienorientparcours']]
			);
			echo $this->Xhtml->tag(
				'p',
				$options['Decisionsaisinebilanparcoursep66']['changementrefparcours'][$decisioncg['changementrefparcours']]
			);
		}
		echo $this->Xhtml->tag(
			'p',
			"Commentaire :",
			array(
				'style' => 'font-weight:bold; text-decoration:underline'
			)
		);
		echo $this->Xhtml->tag(
			'p',
			$decisioncg['commentaire']
		);
	echo '</fieldset>';
}

// Affichage de la décision émise via le dossier pcg66
elseif ( $this->action == 'edit' && !empty( $dossierpcg66['Decisiondossierpcg66'][0]['id'] ) ) {

	$decisiontechnicien = $dossierpcg66['Decisiondossierpcg66'][0]['decisionpcg66_id'];

	$decisionValidation = $dossierpcg66['Decisiondossierpcg66'][0]['validationproposition'];
	if ( !empty( $decisionValidation ) && $decisionValidation == 'O' ) {
		echo $this->Xhtml->tag(
			'p',
			'DECISION DE LA CGA',
			array(
				'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
			)
		);

		echo '<fieldset><legend><strong>Suite à l\'avis de l\'EP Locale "Commission Audition"</strong></legend>';
			echo $this->Xhtml->tag(
				'p',
				$dossierpcg66['Decisiondossierpcg66'][0]['Decisionpdo']['libelle']
			);

			echo $this->Xhtml->tag(
				'p',
				"Commentaire :",
				array(
					'style' => 'font-weight:bold; text-decoration:underline'
				)
			);
			echo $this->Xhtml->tag(
				'p',
				$dossierpcg66['Decisiondossierpcg66'][0]['commentairetechnicien']
			);
			/*echo $this->Xhtml->tag(
				'p',
				$dossierpcg66['Decisiondossierpcg66'][0]['commentairevalidation']
			);*/
			$datevalidation = date_short( $dossierpcg66['Decisiondossierpcg66'][0]['datevalidation'] );
			echo $this->Xform->fieldValue( 'Decisiondossierpcg66.datevalidation', $datevalidation );
		echo '</fieldset>';
	}
}
?>

	</div>
	<?php echo $this->Form->end();?>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {
		if ( $('listeEntretiens') ) {
			$('listeEntretiens').hide();
			$('listeCERs').hide();
			$('cacheEntretien').observe('click', function(){
				$('listeEntretiens').toggle();
			});
			$('cacheCER').observe('click', function(){
				$('listeCERs').toggle();
			});
		}

        observeDisableFieldsetOnRadioValue(
			'Bilan',
			'data[Bilanparcours66][typeformulaire]',
			$( 'bilanparcourscg' ),
			'cg',
			false,
			true
		);

		observeDisableFieldsetOnRadioValue(
			'Bilan',
			'data[Bilanparcours66][typeformulaire]',
			$( 'bilanparcourspe' ),
			'pe',
			false,
			true
		);

		['traitement', 'parcours', 'audition' ].each( function( proposition ) {
			observeDisableFieldsetOnRadioValue(
				'Bilan',
				'data[Bilanparcours66][proposition]',
				$( proposition ),
				proposition,
				false,
				true
			);
		} );

        ['auditionpe', 'parcourspe' ].each( function( proposition ) {
			observeDisableFieldsetOnRadioValue(
				'Bilan',
				'data[Pe][Bilanparcours66][proposition]',
				$( proposition ),
				proposition,
				false,
				true
			);
		} );

      observeDisableFieldsetOnRadioValue(
            'Bilan',
            'data[Bilanparcours66][choixsanspassageep]',
            $( 'cgOrientationActuelle' ),
            'maintien',
            false,
            true
        );

        observeDisableFieldsetOnRadioValue(
            'Bilan',
            'data[Bilanparcours66][choixsanspassageep]',
            $( 'cgReorientationEmploi' ),
            'reorientation',
            false,
            true
        );

		observeDisableFieldsetOnCheckbox(
			'Bilanparcours66Bilanparcoursinsertion',
			'BilanparcoursinsertionCheckbox',
			false,
			true
		);

		observeDisableFieldsetOnCheckbox(
			'Bilanparcours66Motifep',
			'motifsaisine',
			false,
			true
		);

        observeDisableFieldsetOnRadioValue(
			'Bilan',
			'data[Pe][Bilanparcours66][proposition]',
			$( 'peParcours' ),
			'parcours',
			false,
			true
		);


		$( 'Bilan' ).getInputs( 'radio', 'data[Bilanparcours66][sansep_typeorientprincipale_id]' ).each( function ( observeRadio ) {
			$( observeRadio ).observe( 'change', function(event) {
				checkOrientstructTypeorientId( 'data[Bilanparcours66][sansep_typeorientprincipale_id]', 'maintienOrientSansEp' );
			} );
		} );
		checkOrientstructTypeorientId( 'data[Bilanparcours66][sansep_typeorientprincipale_id]', 'maintienOrientSansEp' );

		disableAndHideFormPart( 'cgContratReconduitSansEp' );
		disableAndHideFormPart( 'cgMaintienOrientSansEpChangementRef' );
		disableAndHideFormPart( 'cgMaintienOrientSansEpMemeRef' );


		<?php foreach( $options['Bilanparcours66']['typeorientprincipale_id'] as $key => $value ) { ?>
			dependantSelect( 'Bilanparcours66NvstructurereferenteIdSansEp<?php echo $key ?>', 'Bilanparcours66NvtypeorientIdSansEp<?php echo $key ?>' );
			try { $( 'Bilanparcours66NvstructurereferenteIdSansEp<?php echo $key ?>' ).onchange(); } catch(id) { }
			observeMemeReorientation( 'Bilanparcours66SansepTypeorientprincipaleId<?php echo $key ?>', 'Bilanparcours66NvstructurereferenteIdSansEp<?php echo $key ?>', 'Bilanparcours66NvtypeorientIdSansEp<?php echo $key ?>', 'cgContratReconduitSansEp', 'cgMaintienOrientSansEpMemeRef', 'cgMaintienOrientSansEpChangementRef' );
		<?php } ?>
		dependantSelect( 'Saisinebilanparcoursep66StructurereferenteId', 'Saisinebilanparcoursep66TypeorientId' );
		try { $( 'Saisinebilanparcoursep66StructurereferenteId' ).onchange(); } catch(id) { }

		// Dépendant select entre structure et référent pour le formulaire CG
		dependantSelect( 'Bilanparcours66ReferentId', 'Bilanparcours66StructurereferenteId' );

		dependantSelect( 'PeSaisinebilanparcoursep66StructurereferenteId', 'PeSaisinebilanparcoursep66TypeorientId' );
		try { $( 'PeSaisinebilanparcoursep66StructurereferenteId' ).onchange(); } catch(id) { }

		// Dépendant select entre structure et référent pour le formulaire Pôle Emploi
		dependantSelect( 'PeBilanparcours66ReferentId', 'PeBilanparcours66StructurereferenteId' );

		$( 'Bilanparcours66DdreconductoncontratSansEpYear' ).observe( 'change', function(event) {
			checkDatesToRefresh( '', 'SansEp' );
		} );
		$( 'Bilanparcours66DdreconductoncontratSansEpMonth' ).observe( 'change', function(event) {
			checkDatesToRefresh( '', 'SansEp' );
		} );
		$( 'Bilanparcours66DdreconductoncontratSansEpDay' ).observe( 'change', function(event) {
			checkDatesToRefresh( '', 'SansEp' );
		} );
		$( 'Bilanparcours66DureeEngagSansEp' ).observe( 'change', function(event) {
			checkDatesToRefresh( '', 'SansEp' );
		} );

		// ---------------------------------------------------------------------

		// Partie en cas de maintien ou  de réorientation
		observeDisableFieldsetOnRadioValue(
			'Bilan',
			'data[Bilanparcours66][choixparcours]',
			$( 'cgMaintienOrientationAvecEp' ),
			'maintien',
			false,
			true
		);

		observeDisableFieldsetOnRadioValue(
			'Bilan',
			'data[Bilanparcours66][choixparcours]',
			$( 'cgReorientationAvecEp' ),
			'reorientation',
			false,
			true
		);

		$( 'Bilan' ).getInputs( 'radio', 'data[Bilanparcours66][avecep_typeorientprincipale_id]' ).each( function ( observeRadio ) {
			$( observeRadio ).observe( 'change', function(event) {
				checkOrientstructTypeorientId( 'data[Bilanparcours66][avecep_typeorientprincipale_id]', 'cgMaintienOrientAvecEp' );
			} );
		} );
		checkOrientstructTypeorientId( 'data[Bilanparcours66][avecep_typeorientprincipale_id]', 'cgMaintienOrientAvecEp' );

//		disableAndHideFormPart( 'cgContratReconduitAvecEp' );
		disableAndHideFormPart( 'cgMaintienOrientAvecEpChangementRef' );
		disableAndHideFormPart( 'cgMaintienOrientAvecEpMemeRef' );
		<?php foreach( $options['Bilanparcours66']['typeorientprincipale_id'] as $key => $value ) { ?>
			dependantSelect( 'Bilanparcours66NvstructurereferenteIdAvecEp<?php echo $key ?>', 'Bilanparcours66NvtypeorientIdAvecEp<?php echo $key ?>' );
			try { $( 'Bilanparcours66NvstructurereferenteIdAvecEp<?php echo $key ?>' ).onchange(); } catch(id) { }
//			observeMemeReorientation( 'Bilanparcours66AvecepTypeorientprincipaleId<?php echo $key ?>', 'Bilanparcours66NvstructurereferenteIdAvecEp<?php echo $key ?>', 'Bilanparcours66NvtypeorientIdAvecEp<?php echo $key ?>', 'cgContratReconduitAvecEp', 'cgMaintienOrientAvecEpMemeRef', 'cgMaintienOrientAvecEpChangementRef' );
			observeMemeReorientationSansContractualisation( 'Bilanparcours66AvecepTypeorientprincipaleId<?php echo $key ?>', 'Bilanparcours66NvstructurereferenteIdAvecEp<?php echo $key ?>', 'Bilanparcours66NvtypeorientIdAvecEp<?php echo $key ?>', 'cgMaintienOrientAvecEpMemeRef', 'cgMaintienOrientAvecEpChangementRef' );
		<?php } ?>


		observeDisableFieldsOnRadioValue(
			'Bilan',
			'data[Bilanparcours66][proposition]',
			[
				'Bilanparcours66Observbenefcompterendu'
			],
			['parcours', 'traitement', undefined],
			false,
			true
		);

		// ---------------------------------------------------------------------

		observeDisableFieldsetOnRadioValue(
			'Bilan',
			'data[Pe][Bilanparcours66][choixparcours]',
			$( 'peMaintienOrientationAvecEp' ),
			'maintien',
			false,
			true
		);

//		observeDisableFieldsetOnRadioValue(
//			'Bilan',
//			'data[Pe][Bilanparcours66][choixparcours]',
//			$( 'peReorientationAvecEp' ),
//			'reorientation',
//			false,
//			true
//		);

		disableAndHideFormPart( 'peMaintienOrientAvecEpChangementRef' );
		disableAndHideFormPart( 'peMaintienOrientAvecEpMemeRef' );
//		observeMemeReorientation( 'PeBilanparcours66ChoixparcoursMaintien', 'PeSaisinebilanparcoursep66StructurereferenteId', 'PeSaisinebilanparcoursep66TypeorientId', 'peContratReconduitAvecEp', 'peMaintienOrientAvecEpMemeRef', 'peMaintienOrientAvecEpChangementRef' );
//        observeMemeReorientationSansContractualisation( 'PeBilanparcours66ChoixparcoursMaintien', 'PeSaisinebilanparcoursep66StructurereferenteId', 'PeSaisinebilanparcoursep66TypeorientId', 'peMaintienOrientAvecEpMemeRef', 'peMaintienOrientAvecEpChangementRef' );
//        observeMemeReorientationSansContractualisation( 'PeBilanparcours66ChoixparcoursReorientation', 'PeSaisinebilanparcoursep66StructurereferenteId', 'PeSaisinebilanparcoursep66TypeorientId', 'peMaintienOrientAvecEpMemeRef', 'peMaintienOrientAvecEpChangementRef' );

        <?php foreach( $options['Bilanparcours66']['typeorientprincipale_id'] as $key => $value ) { ?>
			dependantSelect( 'PeBilanparcours66NvstructurereferenteIdAvecEp<?php echo $key ?>', 'PeBilanparcours66NvtypeorientIdAvecEp<?php echo $key ?>' );
			try { $( 'PeBilanparcours66NvstructurereferenteIdAvecEp<?php echo $key ?>' ).onchange(); } catch(id) { }
			observeMemeReorientationSansContractualisation( 'PeBilanparcours66AvecepTypeorientprincipaleId<?php echo $key ?>', 'PeBilanparcours66NvstructurereferenteIdAvecEp<?php echo $key ?>', 'PeBilanparcours66NvtypeorientIdAvecEp<?php echo $key ?>', 'peMaintienOrientAvecEpMemeRef', 'peMaintienOrientAvecEpChangementRef' );
		<?php } ?>

		dependantSelect( 'PeSaisinebilanparcoursep66StructurereferenteId', 'PeSaisinebilanparcoursep66TypeorientId' );
		try { $( 'PeSaisinebilanparcoursep66StructurereferenteId' ).onchange(); } catch(id) { }

		// ---------------------------------------------------------------------

		$( 'Bilan' ).getInputs( 'radio', 'data[Pe][Bilanparcours66][avecep_typeorientprincipale_id]' ).each( function ( observeRadio ) {
			$( observeRadio ).observe( 'change', function(event) {
				checkOrientstructTypeorientId( 'data[Pe][Bilanparcours66][avecep_typeorientprincipale_id]', 'peMaintienOrientAvecEp' );
			} );
		} );
		checkOrientstructTypeorientId( 'data[Pe][Bilanparcours66][avecep_typeorientprincipale_id]', 'peMaintienOrientAvecEp' );


		// ---------------------------------------------------------------------
        // Si le formulaire est de type PE (orientation actuelle = 'emploi', alors on grise le bouton CG'
        <?php if($typeformulaire == 'pe') {?>
            ['Bilanparcours66TypeformulaireCg'].each( function ( elmt ) {
                $( elmt ).writeAttribute('disabled', 'disabled');
            } );
        <?php }?>
        // ---------------------------------------------------------------------

		<?php if ( isset( $passagecommissionep ) && !empty( $passagecommissionep ) ) { ?>
			['traitement', 'parcours', 'audition', 'auditionpe', 'radioaucun'].each( function( proposition ) {
				$( proposition ).up().getElementsBySelector( 'input', 'select' ).each( function( elmt ) {
					$( elmt ).writeAttribute('disabled', 'disabled');
				} );
			} );
			['Bilanparcours66TypeformulaireCg', 'Bilanparcours66TypeformulairePe', 'Bilanparcours66DatebilanDay', 'Bilanparcours66DatebilanMonth', 'Bilanparcours66DatebilanYear', 'PeSaisinebilanparcoursep66TypeorientId', 'PeSaisinebilanparcoursep66StructurereferenteId', 'PeBilanparcours66DatebilanDay', 'PeBilanparcours66DatebilanMonth', 'PeBilanparcours66DatebilanYear'].each( function ( elmt ) {
				$( elmt ).writeAttribute('disabled', 'disabled');
			} );
		<?php } ?>

		// Cas des dossiers provenant de la recherche par Pôle Emploi -> Radiés / non inscrits
		<?php if ( isset( $this->request->params['named']['Bilanparcours66__examenauditionpe'] ) && in_array( $this->request->params['named']['Bilanparcours66__examenauditionpe'], array( 'radiationpe', 'noninscriptionpe' ) ) ) { ?>
			$( 'Bilanparcours66TypeformulairePe' ).click();
			[  'radioparcourspe', 'Bilanparcours66TypeformulaireCg' ].each( function ( elmt ) {
				$( elmt ).writeAttribute( 'disabled', 'disabled');
			} );
			$( 'radioauditionpe' ).click();
			<?php if ( $this->request->params['named']['Bilanparcours66__examenauditionpe'] == 'radiationpe' ) { ?>
				$( 'PeBilanparcours66ExamenauditionpeRadiationpe' ).click();
				$( 'PeBilanparcours66ExamenauditionpeNoninscriptionpe' ).writeAttribute( 'disabled', 'disabled');
			<?php }
			else if ( $this->request->params['named']['Bilanparcours66__examenauditionpe'] == 'noninscriptionpe' ) { ?>
				$( 'PeBilanparcours66ExamenauditionpeNoninscriptionpe' ).click();
				$( 'PeBilanparcours66ExamenauditionpeRadiationpe' ).writeAttribute( 'disabled', 'disabled');
			<?php }
		} ?>

		// Cas des dossiers provenant de la recherche par Demande de maintien en social
		<?php if ( isset( $this->request->params['pass'][1]['Bilanparcours66__maintienensocial'] ) ) { ?>
			$( 'Bilanparcours66TypeformulaireCg' ).click();
			[ 'radiotraitement', 'radioaudition', 'radioauditionpe', 'Bilanparcours66TypeformulairePe' ].each( function ( elmt ) {
				$( elmt ).writeAttribute( 'disabled', 'disabled');
			} );
			$( 'Bilanparcours66Motifep' ).click();
			setInputValue( $( 'Bilanparcours66Motifsaisine' ), 'Demande de maintien en social depuis plus de 24 mois' );
			$( 'radioparcours' ).click();
		<?php } ?>

		// ----------------------------------------------------------------------------------------------------------
		//On désactive les boutons radio de la commission parcours et audition si un dossier EP
		// existe déjà (ou est déjà en cours de passage en EP) pour la thématique en question
		<?php if( $dossiersepsencours['saisinesbilansparcourseps66'] ) { ?>
			$( 'radioparcours' ).writeAttribute( 'disabled', 'disabled');
		<?php }?>
		<?php if( $dossiersepsencours['defautsinsertionseps66'] ) { ?>
			$( 'radioaudition' ).writeAttribute( 'disabled', 'disabled');
		<?php }?>

		// ----------------------------------------------------------------------------------------------------------

	});

	function setInputValue( input, value ) {
		input = $( input );
		if( ( input != undefined ) && ( $F( input ) == '' ) ) {
			$( input ).setValue( value );
		}
	}

	function checkDatesToRefresh( prefixe, suffixe ) {
		if( ( $F( prefixe+'Bilanparcours66Ddreconductoncontrat'+suffixe+'Month' ) ) && ( $F( prefixe+'Bilanparcours66Ddreconductoncontrat'+suffixe+'Year' ) ) && ( $F( prefixe+'Bilanparcours66DureeEngag'+suffixe ) ) ) {
			setDateIntervalCer(
				prefixe+'Bilanparcours66Ddreconductoncontrat'+suffixe,
				prefixe+'Bilanparcours66Dfreconductoncontrat'+suffixe,
				$F( prefixe+'Bilanparcours66DureeEngag'+suffixe ),
				false
			);
		}
	}

	function checkOrientstructTypeorientId( radioName, divFormPartId ) {
		var v = $( 'Bilan' ).getInputs( 'radio', radioName );
		var currentValue = undefined;
		$( v ).each( function( radio ) {
			if( radio.checked ) {
				currentValue = radio.value;
			}
		} );
		<?php foreach( $options['Bilanparcours66']['typeorientprincipale_id'] as $key => $value ) { ?>
			if ( currentValue == <?php echo $key ?> ) {
				enableAndShowFormPart( divFormPartId+'<?php echo $key ?>' );
			}
			else {
				disableAndHideFormPart( divFormPartId+'<?php echo $key ?>' );
			}
		<?php } ?>
	}

	function observeMemeReorientation( radioprecedente, structurereferenteId, typeorientId, contractualisationFieldset, maintienOrientMemeRefDiv, maintienOrientChangementRefDiv ) {
		[ radioprecedente, typeorientId, structurereferenteId ].each( function( elmt ) {
			$( elmt ).observe( 'change', function(event) {
				checkMemeReorientation( structurereferenteId, typeorientId, contractualisationFieldset, maintienOrientMemeRefDiv, maintienOrientChangementRefDiv );
			} );
		} );
		checkMemeReorientation( structurereferenteId, typeorientId, contractualisationFieldset, maintienOrientMemeRefDiv, maintienOrientChangementRefDiv );
	}

	function checkMemeReorientation( structurereferenteId, typeorientId, contractualisationFieldset, maintienOrientMemeRefDiv, maintienOrientChangementRefDiv ) {
		if ( ( $F( structurereferenteId ) != '' || $F( typeorientId ) != '' ) && $( structurereferenteId ).up(1).hasClassName( 'disabled' ) == false ) {
			<?php if ( $this->action == 'edit' && @$this->request->data['Bilanparcours66']['changementref'] == 'O' ) { ?>
				var typeorient_id = '<?php echo @$this->request->data['Orientstruct']['typeorient_id'] ?>';
				var structurereferente_id = '<?php echo @$this->request->data['Orientstruct']['structurereferente_id'] ?>';
			<?php } elseif ( $this->action == 'edit' ) { ?>
				var typeorient_id = '<?php echo @$this->request->data['Bilanparcours66']['nvtypeorient_id'] ?>';
				var structurereferente_id = '<?php echo @$this->request->data['Bilanparcours66']['nvstructurereferente_id'] ?>';
			<?php } else { ?>
				var typeorient_id = '<?php echo @$personne['Orientstruct'][0]['typeorient_id'] ?>';
				var structurereferente_id = '<?php echo @$personne['Orientstruct'][0]['structurereferente_id'] ?>';
			<?php } ?>
			var explose = $F( structurereferenteId ).split('_');
			if ( explose[1] == structurereferente_id ) {
				enableAndShowFormPart( contractualisationFieldset );
				enableAndShowFormPart( maintienOrientMemeRefDiv );
				disableAndHideFormPart( maintienOrientChangementRefDiv );
			}
			else if ( $F( typeorientId ) != typeorient_id || ( $F( typeorientId ) == typeorient_id && explose[1] != structurereferente_id && $F( structurereferenteId ) != '' ) ) {
				disableAndHideFormPart( contractualisationFieldset );
				enableAndShowFormPart( maintienOrientChangementRefDiv );
				disableAndHideFormPart( maintienOrientMemeRefDiv );
			}
			else {
				disableAndHideFormPart( contractualisationFieldset );
				disableAndHideFormPart( maintienOrientChangementRefDiv );
				disableAndHideFormPart( maintienOrientMemeRefDiv );
			}
		}
		else if ( $( structurereferenteId ).up(1).hasClassName( 'disabled' ) == false ) {
			disableAndHideFormPart( contractualisationFieldset );
			disableAndHideFormPart( maintienOrientChangementRefDiv );
			disableAndHideFormPart( maintienOrientMemeRefDiv );
		}
	}


    //test arnaud
    function observeMemeReorientationSansContractualisation( radioprecedente, structurereferenteId, typeorientId, maintienOrientMemeRefDiv, maintienOrientChangementRefDiv ) {
		[ radioprecedente, typeorientId, structurereferenteId ].each( function( elmt ) {
			$( elmt ).observe( 'change', function(event) {
				checkMemeReorientationSansContractualisation( structurereferenteId, typeorientId, maintienOrientMemeRefDiv, maintienOrientChangementRefDiv );
			} );
		} );
		checkMemeReorientationSansContractualisation( structurereferenteId, typeorientId, maintienOrientMemeRefDiv, maintienOrientChangementRefDiv );
	}

    function formRadioValue( formId, radioName ){
        var v = $( formId ).getInputs( 'radio', radioName );
        var currentValue = undefined;
        $( v ).each( function( radio ) {
            if( radio.checked ) {
                currentValue = radio.value;
            }
        } );
//        alert(currentValue);
        return currentValue;
    }


 	function checkMemeReorientationSansContractualisation( structurereferenteId, typeorientId, maintienOrientMemeRefDiv, maintienOrientChangementRefDiv ) {
//		if ( ( $F( structurereferenteId ) != '' || $F( typeorientId ) != '' ) && $( structurereferenteId ).up(1).hasClassName( 'disabled' ) == false ) {
        if( ( formRadioValue( 'Bilan', 'data[Bilanparcours66][proposition]' ) == 'parcours' ) || ( formRadioValue( 'Bilan', 'data[Pe][Bilanparcours66][proposition]' ) == 'parcourspe' ) )  {
			<?php if ( $this->action == 'edit' && @$this->request->data['Bilanparcours66']['changementref'] == 'O' ) { ?>
				var typeorient_id = '<?php echo @$this->request->data['Orientstruct']['typeorient_id'] ?>';
				var structurereferente_id = '<?php echo @$this->request->data['Orientstruct']['structurereferente_id'] ?>';
			<?php } elseif ( $this->action == 'edit' ) { ?>
				var typeorient_id = '<?php echo @$this->request->data['Bilanparcours66']['nvtypeorient_id'] ?>';
				var structurereferente_id = '<?php echo @$this->request->data['Bilanparcours66']['nvstructurereferente_id'] ?>';
			<?php } else { ?>
				var typeorient_id = '<?php echo @$personne['Orientstruct'][0]['typeorient_id'] ?>';
				var structurereferente_id = '<?php echo @$personne['Orientstruct'][0]['structurereferente_id'] ?>';
			<?php } ?>
			var explose = $F( structurereferenteId ).split('_');
			if ( explose[1] == structurereferente_id ) {
				enableAndShowFormPart( maintienOrientMemeRefDiv );
				disableAndHideFormPart( maintienOrientChangementRefDiv );
			}
			else if ( $F( typeorientId ) != typeorient_id || ( $F( typeorientId ) == typeorient_id && explose[1] != structurereferente_id && $F( structurereferenteId ) != '' ) ) {
				enableAndShowFormPart( maintienOrientChangementRefDiv );
				disableAndHideFormPart( maintienOrientMemeRefDiv );
			}
			else {
				disableAndHideFormPart( maintienOrientChangementRefDiv );
				disableAndHideFormPart( maintienOrientMemeRefDiv );
			}
		}
		else if ( $( structurereferenteId ).up(1).hasClassName( 'disabled' ) == false ) {
			disableAndHideFormPart( maintienOrientChangementRefDiv );
			disableAndHideFormPart( maintienOrientMemeRefDiv );
		}
	}

    dependantSelect( 'Bilanparcours66NvstructurereferenteIdPESansEp', 'Bilanparcours66NvtypeorientIdPESansEp' );
    try { $( 'Bilanparcours66NvstructurereferenteIdPESansEp' ).onchange(); } catch(id) { }
</script>