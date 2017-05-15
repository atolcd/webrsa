<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}
?>
<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'decisiondossierpcg66', "Decisionsdossierspcgs66::{$this->action}" )
	);

	echo $this->Xform->create( 'Decisiondossierpcg66', array( 'id' => 'decisiondossierpcg66form' ) );
	if( Set::check( $this->request->data, 'Decisiondossierpcg66.id' ) ){
		echo $this->Xform->input( 'Decisiondossierpcg66.id', array( 'type' => 'hidden' ) );
	}
	echo $this->Xform->input( 'Decisiondossierpcg66.dossierpcg66_id', array( 'type' => 'hidden', 'value' => $dossierpcg66_id ) );
	if( $this->action == 'add' ) {
		echo $this->Xform->input( 'Decisiondossierpcg66.user_id', array( 'type' => 'hidden', 'value' => $userConnected ) );
	}

?>

<?php if( !empty( $personnespcgs66 ) ):?>

	<table class="tooltips aere"><caption style="caption-side: top;">Informations concernant la (les) personne(s)</caption>
			<thead>
				<tr>
					<th>Personne concernée</th>
					<th>Motif(s)</th>
					<th>Statut(s)</th>
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
								h( Hash::get( $personnepcg66, 'Personne.qual' ).' '.Hash::get( $personnepcg66, 'Personne.nom' ).' '.Hash::get( $personnepcg66, 'Personne.prenom' ) ),
								$differentesSituations,
								$differentsStatuts
							),
							array( 'class' => 'odd' ),
							array( 'class' => 'even' )
						);
					}
				?>
			</tbody>
		</table>
<?php else:?>
	<p class="notice">Aucune personne n'est concernée par ce dossier.</p>
<?php  endif;?>

<?php
	echo "<h2>Pièces liées au dossier</h2>";
	echo $this->Fileuploader->results( array_merge(Hash::get( $dossierpcg66, 'Fichiermodule' ), Hash::extract($fichiersDocument, '{n}.Fichiermodule')) );
?>

<?php echo "<h2>Liste des traitements non clos</h2>";?>
<?php if( !empty( $listeTraitementsNonClos ) ):?>
    <?php
        foreach( $listeTraitementsNonClos as $key => $traitement ){
			foreach ($traitement['traitementnonclosdecision'] as $traitement_id => $option) {
				$link = $traitement['autorisations']['printFicheCalcul'][$traitement_id] ?
					$this->Default2->button(
						'Fiche de calcul',
						array(
							'controller' => 'traitementspcgs66',
							'action'     => 'printFicheCalcul',
							$traitement_id
						),
						array(
							'label' => 'Fiche&nbsp;de&nbsp;calcul',
							'class' => 'action_impression',
							'enable' => false
						)
					)
					: ($traitement['autorisations']['printModeleCourrier'][$traitement_id] ?
						$this->Default2->button(
							'Fiche de calcul',
							array(
								'controller' => 'traitementspcgs66',
								'action'     => 'printModeleCourrier',
								$traitement_id
							),
							array(
								'label' => 'Imprimer',
								'class' => 'action_impression',
								'enable' => false
							)
						)
					: '')
				;

				$traitement['traitementnonclosdecision'][$traitement_id] = $traitement['traitementnonclosdecision'][$traitement_id].' '.$link;
			}

//            echo $this->Form->input( 'Traitementpcg66.traitementnonclosdecision', array( 'label' => 'Traitement d\'un autre dossier à clôturer ?', 'type' => 'select', 'options' => $traitement['traitementnonclosdecision'], 'empty' => true ) );
            echo $this->Default2->subform(
				array(
					'Traitementpcg66.Traitementpcg66' => array( 'type' => 'select', 'label' => 'Traitement d\'un autre dossier à clôturer ?', 'multiple' => 'checkbox', 'empty' => false, 'options' => $traitement['traitementnonclosdecision'], 'escape' => false )
				),
				array(
					'options' => $options
				)
			);

        }
    ?>
    <?php else:?>
        <p class="notice"> Aucun traitement à clôturer</p>
<?php  endif;?>

<?php if( !empty( $listeFicheAReporter ) ):?>
	<?php echo "<h2>Fiche(s) de calcul à prendre en compte</h2>";?>
	<table class="tooltips aere"><caption style="caption-side: top;">Informations concernant la (les) fiche(s) de calcul</caption>
		<thead>
			<tr>
                <th>Personne concernée</th>
				<th>Régime</th>
                <th>Chiffre d'affaire Ventes</th>
                <th>Chiffre d'affaire Services</th>
				<th>Bénéfice pris en compte</th>
				<th>Montant des revenus arrêtés à</th>
				<th>Date de début de période</th>
				<th>Date de fin de période</th>
				<th>Date de révision</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach( $listeFicheAReporter as $i => $fichecalcul ){
				$regime = Hash::get( $fichecalcul, 'regime' );
				if( $regime == 'microbic' ) {
					$montanttotal = Hash::get( $fichecalcul, 'benefpriscompte' );
				}
				else{
					$montanttotal = Hash::get( $fichecalcul, 'mnttotalpriscompte' );
				}

				echo $this->Xhtml->tableCells(
					array(
                        h( Hash::get( $fichecalcul, 'Personnepcg66.Personne.nom_complet' ) ),
						h( Set::enum( Hash::get( $fichecalcul, 'regime' ), $options['Traitementpcg66']['regime'] ) ),
						h( $this->Locale->money( Hash::get( $fichecalcul, 'chaffvnt' ) ) ),
                        h( $this->Locale->money( Hash::get( $fichecalcul, 'chaffsrv' ) ) ),
                        h( $this->Locale->money( $montanttotal ) ),
						h( $this->Locale->money( Hash::get( $fichecalcul, 'revenus' ) ).' par mois' ),
						h( date_short( Hash::get( $fichecalcul, 'dtdebutperiode' ) ) ),
						h( date_short( Hash::get( $fichecalcul, 'datefinperiode' ) ) ),
						h( date_short( Hash::get( $fichecalcul, 'daterevision' ) ) )
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
			}
		?>
		</tbody>
	</table>
<?php  endif;?>


	<fieldset><legend>Proposition du technicien</legend>
		<?php if( !empty( $dossierpcg66['Decisiondossierpcg66'] ) ):?>
			<table class="aere"><caption style="caption-side: top;">Propositions passées</caption>
				<thead>
					<tr>
						<th>Proposition de décision</th>
						<th>Date de la proposition</th>
						<th>Avis technique</th>
						<th>Commentaire de l'avis technique</th>
						<th>Validation proposition</th>
						<th>Commentaire du décideur</th>
						<th>Commentaire technicien</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach( $dossierpcg66['Decisiondossierpcg66'] as $decision ):?>
						<?php
							echo $this->Xhtml->tableCells(
								array(
									h( Hash::get( $decision, 'Decisionpdo.libelle' ) ),
									h( date_short( Hash::get( $decision, 'datepropositiontechnicien' ) ) ),
									h( Set::enum( Hash::get( $decision, 'avistechnique' ), $options['Decisiondossierpcg66']['validationproposition'] ) ),
									h( Hash::get( $decision, 'commentaireavistechnique' ) ),
									h( Set::enum( Hash::get( $decision, 'validationproposition' ), $options['Decisiondossierpcg66']['validationproposition'] ) ),
									h( Hash::get( $decision, 'commentairevalidation' ) ),
									h( Hash::get( $decision, 'commentairetechnicien' ) )
								),
								array( 'class' => 'odd' ),
								array( 'class' => 'even' )
							);
					?>
				<?php endforeach;?>
			</tbody>
		</table>
	<?php else:?>
		<p class="notice">Aucune proposition passée n'a encore été émise par le technicien.</p>
	<?php  endif;?>

	<?php if( !empty( $dossierpcg66['Dossierpcg66']['contratinsertion_id'] ) ):?>
		<?php echo "<h2>Informations du CER Particulier lié</h2>";?>
		<table class="tooltips default2">
	<thead>
		<tr>
			<th>Forme du contrat</th>
			<th>Type de contrat</th>
			<th>Date de début de contrat</th>
			<th>Date de fin de contrat</th>
			<th>Contrat signé le</th>
			<th>Position du CER</th>
			<th colspan="11" class="action">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
				$positioncer = Set::enum( Hash::get( $dossierpcg66, 'Contratinsertion.positioncer' ), $options['Contratinsertion']['positioncer'] );

				echo $this->Xhtml->tableCells(
					array(
						h( Hash::get( $formeCi, Hash::get( $dossierpcg66, 'Contratinsertion.forme_ci' ) ) ),
						h( Hash::get( $options['Contratinsertion']['num_contrat'], Hash::get( $dossierpcg66, 'Contratinsertion.num_contrat' ) ) ),
						h( date_short( Hash::get( $dossierpcg66, 'Contratinsertion.dd_ci' ) ) ),
						h( date_short( Hash::get( $dossierpcg66, 'Contratinsertion.df_ci' ) ) ),
						h( date_short( Hash::get( $dossierpcg66, 'Contratinsertion.date_saisi_ci' ) ) ),
						h( $positioncer ),


						$this->Default2->button(
							'view',
							array( 'controller' => 'contratsinsertion', 'action' => 'view',
							$dossierpcg66['Contratinsertion']['id'] ),
							array(
								'enabled' => (
									$this->Permissions->checkDossier( 'contratsinsertion', 'view', $dossierMenu ) == 1
								),
								'class' => 'external'
							)
						),
						$this->Default2->button(
							'ficheliaisoncer',
							array( 'controller' => 'contratsinsertion', 'action' => 'ficheliaisoncer',
							$dossierpcg66['Contratinsertion']['id'] ),
							array(
								'enabled' => (
									( $this->Permissions->checkDossier( 'contratsinsertion', 'ficheliaisoncer', $dossierMenu ) == 1 )
									&& ( Hash::get( $dossierpcg66, 'Contratinsertion.positioncer' ) != 'annule' )
									&& ( !empty( $isvalidcer )  )
								)
							)
						),
						$this->Default2->button(
							'notifbenef',
							array( 'controller' => 'contratsinsertion', 'action' => 'notifbenef',
							$dossierpcg66['Contratinsertion']['id'] ),
							array(
								'enabled' => (
									( $this->Permissions->checkDossier( 'contratsinsertion', 'notifbenef', $dossierMenu ) == 1 )
									&& ( Hash::get( $dossierpcg66, 'Contratinsertion.positioncer' ) != 'annule' )
									&& ( !empty( $isvalidcer )  )
								)
							)
						),
						$this->Default2->button(
							'notifop',
							array( 'controller' => 'contratsinsertion', 'action' => 'notificationsop',
							$dossierpcg66['Contratinsertion']['id'] ),
							array(
								'enabled' => (
									( $this->Permissions->checkDossier( 'contratsinsertion', 'notificationsop', $dossierMenu ) == 1 )
									&& ( Hash::get( $dossierpcg66, 'Contratinsertion.positioncer' ) != 'annule' )
									&& ( !empty( $isvalidcer ) && ( $isvalidcer != 'N' ) )
								)
							)
						),
						$this->Default2->button(
							'print',
							array( 'controller' => 'contratsinsertion', 'action' => 'impression',
							$dossierpcg66['Contratinsertion']['id'] ),
							array(
								'enabled' => (
									( $this->Permissions->checkDossier( 'contratsinsertion', 'impression', $dossierMenu ) == 1 )
									&& ( Hash::get( $dossierpcg66, 'Contratinsertion.positioncer' ) != 'annule' )
								)
							)
						),

						$this->Default2->button(
							'notification',
							array( 'controller' => 'contratsinsertion', 'action' => 'notification',
							$dossierpcg66['Contratinsertion']['id'] ),
							array(
								'enabled' => (
									( $this->Permissions->checkDossier( 'contratsinsertion', 'notification', $dossierMenu ) == 1 )
									&& ( Hash::get( $dossierpcg66, 'Contratinsertion.positioncer' ) != 'annule' )
								)
							)
						),
						$this->Default2->button(
							'filelink',
							array( 'controller' => 'contratsinsertion', 'action' => 'filelink',
							$dossierpcg66['Contratinsertion']['id'] ),
							array(
								'enabled' => (
									$this->Permissions->checkDossier( 'contratsinsertion', 'filelink', $dossierMenu ) == 1
								)
							)
						),
						h( '('.Hash::get( $dossierpcg66, 'Fichiermodule.nbFichiersLies' ).')' )
					),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
				);
		?>
	</tbody>
</table>
	<?php endif;?>

	<fieldset id="Propositionpcg" class="invisible"></fieldset>

		<?php
			if( !empty( $dossierpcg66['Dossierpcg66']['contratinsertion_id'] ) ) {
				$listdecisionpdo = $listdecisionpcgCer;
			}

			echo $this->Default2->subform(
				array(
					'Typersapcg66.Typersapcg66' => array( 'type' => 'select', 'label' => 'Type de prestation', 'multiple' => 'checkbox', 'empty' => false, 'options' => $typersapcg66 ),
					'Decisiondossierpcg66.decisionpdo_id' => array( 'type' => 'select', 'empty' => true, 'options' => $listdecisionpdo )
				),
				array(
					'options' => $options
				)
			);
		?>
		<fieldset id="propononvalidcerparticulier" class="invisible">
			<?php
				if( !empty( $idsDecisionNonValidCer ) ) {
					if( Set::check( $this->request->data, 'Propodecisioncer66.id' ) ){
						echo $this->Xform->input( 'Propodecisioncer66.id', array( 'type' => 'hidden' ) );
					}
					echo $this->Xform->input( 'Propodecisioncer66.contratinsertion_id', array( 'type' => 'hidden', 'value' => $contratinsertion_id ) );
					echo $this->Xform->input( 'Propodecisioncer66.datevalidcer', array( 'label' => 'Date de la proposition du CER', 'type' => 'date', 'dateFormat' => 'DMY', 'minYear' => date( 'Y' ) - 4, 'maxYear' => date( 'Y' ) + 2, 'empty' => false ) );
					echo $this->Default2->subform(
						array(
							'Motifcernonvalid66.Motifcernonvalid66' => array( 'type' => 'select', 'label' => 'Motif de non validation', 'multiple' => 'checkbox', 'empty' => false, 'options' => $listMotifs ),
							'Propodecisioncer66.motifficheliaison' => array( 'type' => 'textarea' ),
							'Propodecisioncer66.motifnotifnonvalid' => array( 'type' => 'textarea' )
						),
						array(
							'options' => $options
						)
					);
				}
			?>
		</fieldset>
		<?php
            $commentaireTechnicienValue = null;
            if( isset( $decisiondossierpcg66['Decisiondossierpcg66']['commentairetechnicien'] ) && !empty( $decisiondossierpcg66['Decisiondossierpcg66']['commentairetechnicien'] ) ) {
                $commentaireTechnicienValue = $decisiondossierpcg66['Decisiondossierpcg66']['commentairetechnicien'];
            }
            else if( !empty( $this->request->data ) ) {
                $commentaireTechnicienValue = $this->request->data['Decisiondossierpcg66']['commentairetechnicien'];
            }
            else {
                $commentaireTechnicienValue = Hash::get( $dossierpcg66, 'Decisiondossierpcg66.0.commentairetechnicien' );
            }

			echo $this->Default2->subform(
				array(
					'Decisiondossierpcg66.datepropositiontechnicien' => array( 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+5, 'minYear' => date('Y')-1, 'empty' => false ),
//					'Decisiondossierpcg66.commentairetechnicien' => array( 'value' => isset( $dossierpcg66['Decisiondossierpcg66'][0]['commentairetechnicien'] ) ? ( $dossierpcg66['Decisiondossierpcg66'][0]['commentairetechnicien'] ) : null )
					'Decisiondossierpcg66.commentairetechnicien' => array( 'value' => $commentaireTechnicienValue ),
					'Decisiondossierpcg66.instrencours' => array( 'type' => 'checkbox', 'options' => null )
				),
				array(
					'options' => $options
				)
			);
		?>
	</fieldset>

	<?php if( !empty( $idsDecisionNonValidCer ) ):?>
		<script type="text/javascript">
			document.observe("dom:loaded", function() {
				observeDisableFieldsetOnValue(
					'Decisiondossierpcg66DecisionpdoId',
					$( 'propononvalidcerparticulier' ),
					['<?php echo implode( ',', $idsDecisionNonValidCer );?>'],
					false,
					true
				);

			});
		</script>
	<?php endif;?>

    <fieldset id="transmission"><legend>Information transmise à :</legend>
        <?php
            echo $this->Default2->subform(
                array(
                    'Decisiondossierpcg66.orgtransmisdossierpcg66_id' => array( 'legend' =>  false, 'type' => 'radio', 'class' => 'uncheckable', 'empty' => false, 'options' => $orgs )
                ),
                array(
                    'options' => $options
                )
            );
        ?>
        <fieldset class="noborder" id="infotransmise"><legend>Commentaire suite à la transmission</legend>
        <?php
            echo $this->Default2->subform(
                array(
                    'Decisiondossierpcg66.infotransmise' => array( 'type' => 'textarea', 'label' => false )
                )
            );
        ?>
        </fieldset>
    </fieldset>

	<?php if( $avistechniquemodifiable && !in_array( $this->action, array( 'add', 'edit' ) ) && ( $this->Permissions->checkDossier( 'decisionsdossierspcgs66', 'avistechnique', $dossierMenu ) || $this->Permissions->checkDossier( 'decisionsdossierspcgs66', 'validation', $dossierMenu ) ) ):?>
		<fieldset id="avtech"><legend><?php echo 'Avis technique'; ?></legend>
				<?php
					if( empty( $this->request->data['Decisiondossierpcg66']['useravistechnique_id'] ) ){
						echo $this->Xform->input( 'Decisiondossierpcg66.useravistechnique_id', array( 'type' => 'hidden', 'value' => $userConnected ) );
					}
					echo $this->Default2->subform(
						array(
							'Decisiondossierpcg66.avistechnique' => array( 'label' => false, 'type' => 'radio', 'options' => $options['Decisiondossierpcg66']['avistechnique'] ),
//                                'Decisiondossierpcg66.useravistechnique_id' => array( 'type' => 'hidden', 'value' => $userConnected )
						),
						array(
							'options' => $options
						)
					);
				?>
				<fieldset id="avistech" class="noborder">
					<?php
						echo $this->Default2->subform(
							array(
								'Decisiondossierpcg66.vuavistechnique' => array( 'type' => 'checkbox', 'options' => null ),
								'Decisiondossierpcg66.commentaireavistechnique',
								'Decisiondossierpcg66.dateavistechnique' => array( 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+5, 'minYear' => date('Y')-1, 'empty' => false )
							),
							array(
								'options' => $options
							)
						);

						if( !empty( $decisiondossierpcg66['Decisiondossierpcg66']['avistechnique'] ) ) {
							echo $this->Xform->fieldValue( 'Decisiondossierpcg66.useravistechnique_id', Set::enum( Hash::get( $decisiondossierpcg66, 'Decisiondossierpcg66.useravistechnique_id'), $gestionnaire ) );
						}

						if ($this->action === 'avistechnique'
							&& Hash::get($decisiondossierpcg66, 'Decisiondossierpcg66.retouravistechnique')
						) {
							echo $this->Xform->fieldValue(
								'Commentaire du validateur',
								Hash::get($decisiondossierpcg66, 'Decisiondossierpcg66.commentairevalidation'),
								false,
								'textarea'
							);
						}
					?>
				</fieldset>
		</fieldset>
	<?php endif;?>

	<?php if( $validationmodifiable && !in_array( $this->action, array( 'add', 'edit', 'avistechnique' ) ) && ( $this->Permissions->checkDossier( 'decisionsdossierspcgs66', 'avistechnique', $dossierMenu ) || $this->Permissions->checkDossier( 'decisionsdossierspcgs66', 'validation', $dossierMenu ) ) ):?>
		<fieldset id="propovalid"><legend>Validation de la proposition</legend>
				<?php
					echo $this->Default2->subform(
						array(
							'Decisiondossierpcg66.validationproposition' => array( 'label' => false, 'type' => 'radio', 'options' => $options['Decisiondossierpcg66']['validationproposition'] ),
							'Decisiondossierpcg66.userproposition_id' => array( 'type' => 'hidden', 'value' => $userConnected )
						),
						array(
							'options' => $options
						)
					);
				?>
				<fieldset id="validpropo" class="noborder">
					<?php
						echo $this->Default2->subform(
						array(
							'Decisiondossierpcg66.retouravistechnique' => array( 'type' => 'checkbox', 'options' => null ),
							'Decisiondossierpcg66.commentairevalidation',
							'Decisiondossierpcg66.datevalidation' => array( 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+5, 'minYear' => date('Y')-1, 'empty' => false )
						),
						array(
							'options' => $options
						)
					);
						if( !empty( $decisiondossierpcg66['Decisiondossierpcg66']['validationproposition'] ) ) {
							echo $this->Xform->fieldValue( 'Decisiondossierpcg66.userproposition_id', Set::enum( Hash::get( $decisiondossierpcg66, 'Decisiondossierpcg66.userproposition_id'), $gestionnaire ) );
						}
					?>
				</fieldset>
		</fieldset>
	<?php endif;?>


    <?php
		echo $this->Default2->subform(
			array(
				'Decisiondossierpcg66.commentaire' => array( 'label' =>  'Observations : ', 'type' => 'textarea', 'rows' => 3 ),
			),
			array(
				'options' => $options
			)
		);
	?>
<?php
	echo "<div class='submit'>";
        echo $this->Form->submit( 'Enregistrer', array( 'div' => false ) );
		echo $this->Form->submit( 'Retour', array( 'div' => false, 'name' => 'Cancel' ) );
	echo "</div>";

	echo $this->Xform->end();
	echo $this->Observer->disableFormOnSubmit( 'decisiondossierpcg66form' );
?>

<script type="text/javascript">
	document.observe("dom:loaded", function() {

        var v = $( 'decisiondossierpcg66form' ).getInputs( 'radio', 'data[Decisiondossierpcg66][orgtransmisdossierpcg66_id]' );
        var poleSelectionneId = '<?php echo $dossierpcg66['Dossierpcg66']['poledossierpcg66_id'];?>';

        $( v ).each( function( radio ) {
            if( $(radio).value.match( new RegExp( '^' + poleSelectionneId + '_' ) ) ){
                $(radio).disabled = true;
            }
        } );

        // on affiche la zone de commentaire si un des boutons radio est coché
        observeDisableFieldsetOnRadioValue(
			'decisiondossierpcg66form',
			'data[Decisiondossierpcg66][orgtransmisdossierpcg66_id]',
			$( 'infotransmise' ),
			['<?php echo implode( '\', \'', array_keys( $orgs ) );?>'],
			false,
			true
		);

	<?php if( $avistechniquemodifiable ):?>
		observeDisableFieldsetOnRadioValue(
			'decisiondossierpcg66form',
			'data[Decisiondossierpcg66][avistechnique]',
			$( 'avistech' ),
			['O','N'],
			false,
			true
		);
	<?php endif;?>

	<?php if( $validationmodifiable ):?>
		observeDisableFieldsetOnRadioValue(
			'decisiondossierpcg66form',
			'data[Decisiondossierpcg66][validationproposition]',
			$( 'validpropo' ),
			['O','N'],
			false,
			true
		);

		observeDisableFieldsOnValue(
			'Decisiondossierpcg66Retouravistechnique',
			[
				'Decisiondossierpcg66Vuavistechnique'
			],
			'1',
			false
		);

		$( 'decisiondossierpcg66form' ).getInputs( 'radio', 'data[Decisiondossierpcg66][validationproposition]' ).each( function( radio ) {
			$( radio ).observe( 'change', function( event ) {
				disableFieldsOnValue(
					'Decisiondossierpcg66Retouravistechnique',
					[
						'Decisiondossierpcg66Vuavistechnique'
					],
					'1',
					false
				);
			} );
		} );
	<?php endif;?>

	} );

</script>