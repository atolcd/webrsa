<?php
	$title_for_layout = 'Visualisation du Bilan de parcours';
	$this->set( 'title_for_layout', $title_for_layout );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}
?>
<?php echo $this->Html->tag( 'h1', $title_for_layout );?>

<fieldset><legend>BILAN DU PARCOURS</legend>
	<?php

		echo $this->Xform->fieldValue( 'Bilanparcours66.typeformulaire', Set::enum( Set::classicExtract( $bilanparcours66, 'Bilanparcours66.typeformulaire'), $options['Bilanparcours66']['typeformulaire'] ) );
		echo $this->Xform->fieldValue( 'Bilanparcours66.serviceinstructeur_id', Set::classicExtract( $bilanparcours66, 'Serviceinstructeur.lib_service' ) );
		echo $this->Xform->fieldValue( 'Bilanparcours66.structurereferente_id', $bilanparcours66['Structurereferente']['lib_struc'] );
		echo $this->Xform->fieldValue( 'Bilanparcours66.referent_id', $bilanparcours66['Referent']['nom_complet'] );
		echo $this->Xform->fieldValue( 'Bilanparcours66.presenceallocataire', Set::enum( Set::classicExtract( $bilanparcours66, 'Bilanparcours66.presenceallocataire'), $options['Bilanparcours66']['presenceallocataire'] ) );
	?>
	<fieldset>
		<legend>Situation de l'allocataire</legend>
		<table class="wide noborder">
			<tr>
				<td class="mediumSize noborder">
					<strong>Statut de la personne : </strong><?php echo Set::enum( Set::extract( $bilanparcours66, 'Prestation.rolepers' ), $options['Prestation']['rolepers'] ); ?>
					<br />
					<strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $bilanparcours66, 'Personne.qual') , $options['Personne']['qual'] ).' '.Set::classicExtract( $bilanparcours66, 'Personne.nom' );?>
					<br />
					<strong>Prénom : </strong><?php echo Set::classicExtract( $bilanparcours66, 'Personne.prenom' );?>
					<br />
					<strong>Date de naissance : </strong><?php echo date_short( Set::classicExtract( $bilanparcours66, 'Personne.dtnai' ) );?>
				</td>
				<td class="mediumSize noborder">
					<strong>N° demandeur : </strong><?php echo Set::classicExtract( $bilanparcours66, 'Dossier.numdemrsa' );?>
					<br />
					<strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $bilanparcours66, 'Dossier.matricule' );?>
					<br />
					<strong>Inscrit au Pôle emploi</strong>
					<?php
						$isPoleemploi = Set::classicExtract( $bilanparcours66, 'Historiqueetatpe.etat' );
						if( $isPoleemploi == 'inscription' )
							echo 'Oui';
						else
							echo 'Non';
					?>
					<br />
					<strong>N° identifiant : </strong><?php echo Set::classicExtract( $bilanparcours66, 'Historiqueetatpe.identifiantpe' );?>
				</td>
			</tr>
			<tr>
				<td class="mediumSize noborder">
					<strong>Adresse : </strong><br /><?php echo Set::classicExtract( $bilanparcours66, 'Adresse.numvoie' ).' '.Set::classicExtract( $bilanparcours66, 'Adresse.libtypevoie' ).' '.Set::classicExtract( $bilanparcours66, 'Adresse.nomvoie' ).'<br /> '.Set::classicExtract( $bilanparcours66, 'Adresse.codepos' ).' '.Set::classicExtract( $bilanparcours66, 'Adresse.nomcom' );?>
				</td>
			</tr>
		</table>
		<?php
			echo $this->Xhtml->tag(
				'p',
				'<strong>Orientation actuelle (au moment de la saisie du bilan) : </strong>'.Set::extract( $bilanparcours66, 'Typeorientorigine.lib_type_orient' )
			);

			echo $this->Xform->fieldValue( 'Bilanparcours66.sitfam', Set::enum( Set::classicExtract( $bilanparcours66, 'Bilanparcours66.sitfam'), $options['Bilanparcours66']['sitfam'] ) );

		?>
	</fieldset>
	<?php 
	
	if (Hash::get($bilanparcours66, 'Bilanparcours66.typeformulaire') === 'pe') :?>
		<fieldset><legend>BILAN DU PARCOURS (Pôle Emploi)</legend>
			<?php
			echo '<div class="input value textarea"><span class="label">'
				.__d('bilanparcours66','Bilanparcours66.textbilanparcours')
				.'</span><br /><span class="input">'
				.nl2br(hash::get($bilanparcours66, 'Bilanparcours66.textbilanparcours'))
				.'</span></div>';
			echo '<div class="input value textarea"><span class="label">'
				.__d('bilanparcours66','Bilanparcours66.observbenef')
				.'</span><br /><span class="input">'
				.nl2br(hash::get($bilanparcours66, 'Bilanparcours66.observbenef'))
				.'</span></div>';
			?>
		</fieldset>
	<?php endif;
	
	if( $bilanparcours66['Bilanparcours66']['bilanparcoursinsertion'] != '0' && $bilanparcours66['Bilanparcours66']['bilanparcoursinsertion'] !== null ) :?>
		<fieldset><legend>Bilan du parcours d'insertion</legend>
			<?php
				echo '<div class="input value textarea"><span class="label">' . __d('bilanparcours66','Bilanparcours66.situationperso') . '</span><br /><span class="input">' . nl2br(Set::classicExtract( $bilanparcours66, 'Bilanparcours66.situationperso' )) . '</span></div>';
			?>
		</fieldset>
	<?php endif;?>
	<?php if( $bilanparcours66['Bilanparcours66']['motifep'] != '0' && $bilanparcours66['Bilanparcours66']['motifep'] !== null ) :?>
		<fieldset><legend>Motif de la saisine</legend>
			<?php
				echo $this->Xform->fieldValue( 'Bilanparcours66.motifsaisine', Set::classicExtract( $bilanparcours66, 'Bilanparcours66.motifsaisine' ) );
			?>
		</fieldset>
	<?php endif;?>
	<fieldset>
		<?php
			echo $this->Xhtml->tag(
				'p',
				'Proposition du référent :',
				array(
					'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
				)
			);

			$structureOrigine = Set::classicExtract( $bilanparcours66, 'Typeorientorigine.lib_type_orient' );
			$structureNouvelle = Set::classicExtract( $bilanparcours66, 'Structurereferentenouvelle.lib_struc' );

			echo $this->Xform->fieldValue( 'Bilanparcours66.proposition', Set::enum( Set::classicExtract( $bilanparcours66 , 'Bilanparcours66.proposition' ), $options['Bilanparcours66']['proposition'] ) );

			// Affichage selon la proposition
			if( $bilanparcours66['Bilanparcours66']['proposition'] == 'aucun' ) {
				echo '';
			}
			else if( $bilanparcours66['Bilanparcours66']['proposition'] == 'traitement' ) {
				echo $this->Xform->fieldValue( 'Bilanparcours66.avecep_typeorientprincipale_id', Set::classicExtract( $bilanparcours66 , 'Typeorientprincipale.lib_type_orient' ) );
				echo $this->Xform->fieldValue( 'Bilanparcours66.nvtypeorient_id', Set::classicExtract( $bilanparcours66 , 'NvTypeorient.lib_type_orient' ) );
				echo $this->Xform->fieldValue( 'Bilanparcours66.nvstructurereferente_id', Set::classicExtract( $bilanparcours66 , 'NvStructurereferente.lib_struc' ) );

				$avecSansChangementRef = $bilanparcours66['Bilanparcours66']['changementrefsansep'];
				if( $avecSansChangementRef == 'N' ) {
					echo '<div class="input text"><span class="label">&nbsp;</span><span class="input">Sans changement de référent</span></div>';
				}
				else {
					echo '<div class="input text"><span class="label">&nbsp;</span><span class="input">Avec changement de référent</span></div>';
				}

				//En cas de maintien au sein de la même structure
				if( $structureNouvelle == $structureOrigine ) {
					echo '<div class="aere">';
					echo '<fieldset><legend>Reconduction du contrat librement débattu</legend>';
						// Reconduction du contrat librement débattu
						echo $this->Xform->fieldValue( 'Bilanparcours66.duree_engag', Set::enum( Set::classicExtract( $bilanparcours66 , 'Bilanparcours66.duree_engag' ), $options['Bilanparcours66']['duree_engag'] ) );
						echo $this->Xform->fieldValue( 'Bilanparcours66.ddreconductoncontrat', date_short( Set::classicExtract( $bilanparcours66 , 'Bilanparcours66.ddreconductoncontrat' ) ) );
						echo $this->Xform->fieldValue( 'Bilanparcours66.dfreconductoncontrat', date_short( Set::classicExtract( $bilanparcours66 , 'Bilanparcours66.dfreconductoncontrat' ) ) );
					echo '</fieldset>';
					echo '</div>';
				}

			}
			else if( $bilanparcours66['Bilanparcours66']['proposition'] == 'parcours' ) {
				echo $this->Xform->fieldValue( 'Bilanparcours66.choixparcours', Set::enum( Set::classicExtract( $bilanparcours66 , 'Bilanparcours66.choixparcours' ), $options['Bilanparcours66']['choixparcours'] ) );
				//Pour un accompagnement
				echo $this->Xform->fieldValue( 'Bilanparcours66.avecep_typeorientprincipale_id', Set::classicExtract( $bilanparcours66 , 'Typeorientprincipale.lib_type_orient' ) );
				echo $this->Xform->fieldValue( 'Bilanparcours66.nvtypeorient_id', Set::classicExtract( $bilanparcours66 , 'NvTypeorient.lib_type_orient' ) );
				echo $this->Xform->fieldValue( 'Bilanparcours66.nvstructurereferente_id', Set::classicExtract( $bilanparcours66 , 'NvStructurereferente.lib_struc' ) );

				$avecSansChangementRef = $bilanparcours66['Bilanparcours66']['changementrefsansep'];
				$choixparcours = $bilanparcours66['Bilanparcours66']['choixparcours'];
				if( $avecSansChangementRef == 'N' && $choixparcours == 'maintien' ) {
					echo '<div class="input text"><span class="label">&nbsp;</span><span class="input">Sans changement de référent</span></div>';
				}
				else {
					echo '<div class="input text"><span class="label">&nbsp;</span><span class="input">Avec changement de référent</span></div>';
				}
			}
			else if( $bilanparcours66['Bilanparcours66']['proposition'] == 'audition' ) {
				echo $this->Xform->fieldValue( 'Bilanparcours66.examenaudition', Set::enum( Set::classicExtract( $bilanparcours66 , 'Bilanparcours66.examenaudition' ), $options['Bilanparcours66']['examenaudition'] ) );

				echo $this->Xform->fieldValue( 'Bilanparcours66.observbenefcompterendu', Set::classicExtract( $bilanparcours66, 'Bilanparcours66.observbenefcompterendu' ) );
			}
		?>
	</fieldset>
	<fieldset>
		<?php
			echo $this->Xform->fieldValue( 'Bilanparcours66.infoscomplementaires', Set::classicExtract( $bilanparcours66, 'Bilanparcours66.infoscomplementaires' ) );
			echo $this->Xform->fieldValue( 'Bilanparcours66.observbenefrealisationbilan', Set::classicExtract( $bilanparcours66, 'Bilanparcours66.observbenefrealisationbilan' ) );

			echo $this->Xform->fieldValue( 'Bilanparcours66.datebilan', date_short( Set::classicExtract( $bilanparcours66, 'Bilanparcours66.datebilan' ) ) );
		?>
	</fieldset>
    <!-- Partie pour l'EP -->
    <?php if( !empty( $bilanparcours66['Passagecommissionep']['id'] ) ) :?>
    <fieldset>
        <legend></legend>
		<?php
            echo $this->Xhtml->tag(
				'p',
				'Avis de l\'EP ',
				array(
					'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
				)
			);
        ?>
        </fieldset>
        <fieldset class="invisible">
            <?php
            // Partie EP Audition
            if( !empty( $bilanparcours66['Decisiondefautinsertionep66ep']['id'] ) ) {
                echo '<fieldset><legend><strong>AVIS DE L\'EP Locale Commission Audition du '.date('d/m/Y', strtotime($bilanparcours66['Commissionep']['dateseance'])).'</strong></legend>';

                echo $this->Xhtml->tag(
                    'p',
                    '<strong>Avis :</strong>'
                );
                if ( isset( $bilanparcours66['Decisiondefautinsertionep66ep']['decisionsup'] ) && !empty( $bilanparcours66['Decisiondefautinsertionep66ep']['decisionsup'] ) ) {
                    echo $this->Xhtml->tag(
                        'p',
                        $options['Decisiondefautinsertionep66']['decisionsup'][$bilanparcours66['Decisiondefautinsertionep66ep']['decisionsup']],
                        array(
                            'style' => 'padding: 0 2em;'
                        )
                    );
                }

                $avisEPTypeorient = Set::enum( $bilanparcours66['Decisiondefautinsertionep66ep']['typeorient_id'], $typesorients );
                $avisEPStructure = Set::enum( $bilanparcours66['Decisiondefautinsertionep66ep']['structurereferente_id'], $structuresreferentes );
				$referent_id = $bilanparcours66['Decisiondefautinsertionep66ep']['referent_id'];
                $avisEPReferent = 
					isset($options['Bilanparcours66']['nvsansep_Type']) 
					? Set::enum($referent_id, $options['Bilanparcours66']['nvsansep_Type'])
					: Set::enum($referent_id, (array)Hash::get($options, 'Bilanparcours66.nvsansep_referent_id'))
				;

                echo $this->Xhtml->tag(
                    'p',
                    $options['Decisiondefautinsertionep66']['decision'][$bilanparcours66['Decisiondefautinsertionep66ep']['decision']],
                    array(
                        'style' => 'padding: 0 2em;'
                    )
                );

                if( !empty( $bilanparcours66['Decisiondefautinsertionep66ep']['typeorient_id'] ) ) {
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
                    $bilanparcours66['Decisiondefautinsertionep66ep']['commentaire']
                );
				if( !empty( $bilanparcours66['Decisiondefautinsertionep66ep']['commentairebeneficiaire'] ) ) {
					 echo $this->Xhtml->tag(
	                    'p',
	                    "Commentaire bénéficiaire :",
	                    array(
	                        'style' => 'font-weight:bold; text-decoration:underline'
	                    )
	                );
                    echo $this->Xhtml->tag(
	                    'p',
	                    $bilanparcours66['Decisiondefautinsertionep66ep']['commentairebeneficiaire']
	                );
                }
                echo '</fieldset>';
            }
            ?>
    </fieldset>
    <fieldset class="invisible">
        <?php
            // EP Audition se transformant en EP Parcours
            if ( isset( $bilanparcours66['Decisiondefautinsertionep66cg']['id'] ) && !empty( $bilanparcours66['Decisiondefautinsertionep66cg']['id'] ) && !empty( $bilanparcours66['Decisiondefautinsertionep66cg']['decisionsup'] ) ) {
                echo $this->Xhtml->tag(
                    'p',
                    'DECISION DU COORDONNATEUR TECHNIQUE',
                    array(
                        'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
                    )
                );

                $decisioncg = $bilanparcours66['Decisiondefautinsertionep66cg'];

                echo '<fieldset><legend><strong>Suite à l\'avis de l\'EP Locale Commission Audition du '.date('d/m/Y', strtotime($bilanparcours66['Commissionep']['dateseance'])).'</strong></legend>';
                    if ( $decisioncg['decision'] == 'reorientationprofverssoc' ) {
                        echo $this->Xhtml->tag(
                            'p',
                            '<strong>Décision : </strong>Réorientation du PROFESSIONNEL vers le SOCIAL'
                        );
                        // Réorientation choisie
                        if( !empty( $bilanparcours66['Decisiondefautinsertionep66cg']['typeorient_id'] ) ) {
                            $decisionCTTypeorient = Set::enum( $bilanparcours66['Decisiondefautinsertionep66cg']['typeorient_id'], $typesorients );
                            $decisionCTStructure = Set::enum( $bilanparcours66['Decisiondefautinsertionep66cg']['structurereferente_id'], $structuresreferentes );
                            $decisionCTReferent = Set::enum( $bilanparcours66['Decisiondefautinsertionep66cg']['referent_id'], $options['Bilanparcours66']['nvsansep_referent_id'] );

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
                        $accord = ( $bilanparcours66['Decisiondefautinsertionep66cg']['decision'] == $decisioncg['decision'] ) ? 'Oui' : 'Non';
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
            ?>
        </fieldset>
        <fieldset class="invisible">
            <?php
                // Partie Avis EP Parcours
                if( !empty( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['id'] ) ) {
                    $avisep = $bilanparcours66['Decisionsaisinebilanparcoursep66ep'];

                    $avisEPParcoursTypeorient = Set::enum( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['typeorient_id'], $typesorients );
                    $avisEPParcoursStructure = Set::enum( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['structurereferente_id'], $structuresreferentes );
                    $avisEPParcoursReferent = Set::enum( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['referent_id'], $options['Bilanparcours66']['nvsansep_referent_id'] );

                    if ( isset( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['id'] ) && !empty( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['id'] ) ) {
                        echo '<fieldset><legend><strong>AVIS DE L\'EP Locale Commission Parcours du '.date('d/m/Y', strtotime($bilanparcours66['Commissionep']['dateseance'])).'</strong></legend>';
                            if ( $avisep['decision'] == 'reorientation' ) {
                                echo $this->Xhtml->tag(
                                    'p',
                                    '<strong>Avis : </strong>Réorientation'
                                );

                            }
                            elseif ( $avisep['decision'] == 'maintien' ) {
                                echo $this->Xhtml->tag(
                                    'p',
                                    '<strong>Avis : </strong>Maintien de l\'orientation SOCIALE '
                                );

                                echo $this->Xhtml->tag(
                                    'p',
                                    $options['Decisionsaisinebilanparcoursep66']['changementrefparcours'][$bilanparcours66['Decisionsaisinebilanparcoursep66ep']['changementrefparcours']]
                                );
                            }
                             echo $this->Xhtml->tag(
                                'p',
                                '<strong>Type d\'orientation : </strong>'.$avisEPParcoursTypeorient
                            );

                            echo $this->Xhtml->tag(
                                'p',
                                '<strong>Structure référente : </strong>'.$avisEPParcoursStructure
                            );

                            echo $this->Xhtml->tag(
                                'p',
                                '<strong>Nom du prescripteur : </strong>'.$avisEPParcoursReferent
                            );

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
                ?>
        </fieldset>
         <fieldset class="invisible">
            <?php
                    // Partie Décision EP Parcours
                    if ( isset( $bilanparcours66['Decisionsaisinebilanparcoursep66cg']['id'] ) && !empty( $bilanparcours66['Decisionsaisinebilanparcoursep66cg']['id'] ) ) {
                        echo $this->Xhtml->tag(
                            'p',
                            'DECISION DU COORDONNATEUR TECHNIQUE',
                            array(
                                'style' => 'text-align: center; font-size: 14px; font-weight:bold;'
                            )
                        );
                        $decisioncg = $bilanparcours66['Decisionsaisinebilanparcoursep66cg'];
                        $decisionEPParcoursTypeorient = Set::enum( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['typeorient_id'], $typesorients );
                        $decisionEPParcoursStructure = Set::enum( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['structurereferente_id'], $structuresreferentes );
                        $decisionEPParcoursReferent = Set::enum( $bilanparcours66['Decisionsaisinebilanparcoursep66ep']['referent_id'], $options['Bilanparcours66']['nvsansep_referent_id'] );

                        echo '<fieldset><legend><strong>Suite à l\'avis de l\'EP Locale Commission Parcours du '.date('d/m/Y', strtotime($bilanparcours66['Commissionep']['dateseance'])).'</strong></legend>';
                            if ( $decisioncg['decision'] == 'reorientation' ) {
                                echo $this->Xhtml->tag(
                                    'p',
                                    '<strong>Décision : </strong>Réorientation'
                                );
                                $accord = ( $avisep['decision'] == $decisioncg['decision'] ) ? 'Oui' : 'Non';
                                echo $this->Xhtml->tag(
                                    'p',
                                    "<strong>En accord avec l'avis de l'EPL commission Parcours : </strong>".$accord
                                );
                            }
                            elseif ( $decisioncg['decision'] == 'maintien' ) {
                                 echo $this->Xhtml->tag(
                                    'p',
                                    '<strong>Avis : </strong>Maintien de l\'orientation SOCIALE '
                                );


                                echo $this->Xhtml->tag(
                                    'p',
                                    $options['Decisionsaisinebilanparcoursep66']['changementrefparcours'][$decisioncg['changementrefparcours']]
                                );

                                $accord = ( $avisep['decision'] == $decisioncg['decision'] ) ? 'Oui' : 'Non';
                                echo $this->Xhtml->tag(
                                    'p',
                                    "<strong>En accord avec l'avis de l'EPL commission Parcours : </strong>".$accord
                                );
                            }
                            echo $this->Xhtml->tag(
                                'p',
                                '<strong>Type d\'orientation : </strong>'.$decisionEPParcoursTypeorient
                            );

                            echo $this->Xhtml->tag(
                                'p',
                                '<strong>Structure référente : </strong>'.$decisionEPParcoursStructure
                            );

                            echo $this->Xhtml->tag(
                                'p',
                                '<strong>Nom du prescripteur : </strong>'.$decisionEPParcoursReferent
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
                                $decisioncg['commentaire']
                            );
                        echo '</fieldset>';
                    }
                }

            ?>
        </fieldset>
    <fieldset class="invisible">
        <?php
            if ( !empty( $bilanparcours66['Decisiondossierpcg66']['id'] ) ) {

                $decisiontechnicien = $bilanparcours66['Decisiondossierpcg66']['decisionpcg66_id'];
                $decisionValidation = $bilanparcours66['Decisiondossierpcg66']['validationproposition'];
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
                            $bilanparcours66['Decisionpdo']['libelle']
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
                            $bilanparcours66['Decisiondossierpcg66']['commentairetechnicien']
                        );

                        $datevalidation = date_short( $bilanparcours66['Decisiondossierpcg66']['datevalidation'] );
                        echo $this->Xform->fieldValue( 'Decisiondossierpcg66.datevalidation', $datevalidation );
                    echo '</fieldset>';
                }
            }
        ?>
        </fieldset>
    <?php endif;?>
</fieldset>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'bilansparcours66',
			'action'     => 'index',
			$bilanparcours66['Bilanparcours66']['personne_id']
		),
		array(
			'id' => 'Back'
		)
	);
//    debug($bilanparcours66);
//    debug($options);
?>