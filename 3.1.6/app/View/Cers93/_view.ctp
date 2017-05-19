<!-- Bloc 1  -->
<fieldset>
    <legend>Structure établissant le CER</legend>
    <table class="wide noborder cers93">

        <tr>
			<td class="wide noborder">
				<table class="wide noborder">
					<tr>
						<td class="wide noborder"><strong>Type d'orientation</strong></td>
						<td class="wide noborder"><strong>Structure référente</strong></td>
						<td class="wide noborder"><strong>Adresse</strong></td>
					</tr>
					<tr>
						<td class="wide noborder"><?php echo Set::classicExtract( $contratinsertion, 'Structurereferente.Typeorient.lib_type_orient' );?></td>
						<td class="wide noborder"><?php echo Set::classicExtract( $contratinsertion, 'Structurereferente.lib_struc' );?></td>
						<td class="wide noborder"><?php echo Set::classicExtract( $contratinsertion, 'Structurereferente.num_voie').' '.Set::enum( Set::classicExtract( $contratinsertion, 'Structurereferente.type_voie'), $options['Structurereferente']['type_voie'] ).' '.Set::classicExtract( $contratinsertion, 'Structurereferente.nom_voie').'<br /> '.Set::classicExtract( $contratinsertion, 'Structurereferente.code_postal').' '.Set::classicExtract( $contratinsertion, 'Structurereferente.ville');?></td>
					</tr>
				</table>
			</td>
			<td class="wide noborder">
			<?php if( !empty( $contratinsertion['Referent']['nom_complet'] ) ):?>
				<table class="wide noborder">
					<tr>
						<td class="wide noborder"><strong>Nom complet</strong></td>
						<td class="wide noborder"><strong>Fonction</strong></td>
						<td class="wide noborder"><strong>Email</strong></td>
						<td class="wide noborder"><strong>N° téléphone</strong></td>
					</tr>
					<tr>
						<td class="wide noborder"><?php echo Set::classicExtract( $contratinsertion, 'Referent.nom_complet' );?></td>
						<td class="wide noborder"><?php echo Set::classicExtract( $contratinsertion, 'Referent.fonction' );?></td>
						<td class="wide noborder"><?php echo Set::classicExtract( $contratinsertion, 'Referent.email' );?></td>
						<td class="wide noborder"><?php echo Set::classicExtract( $contratinsertion, 'Referent.numero_poste' );?></td>
					</tr>
				</table>
				<?php endif;?>
			</td>
        </tr>
        <tr>
            <td class="wide noborder">
				<?php echo $this->Html->tag( 'p', 'Rang du contrat: '.$contratinsertion['Contratinsertion']['rg_ci'] ); ?>
			</td>
        </tr>
    </table>
</fieldset>
<?php if( $contratinsertion['Contratinsertion']['decision_ci'] === 'A' ):?>
	<fieldset>
		<legend>CER annulé</legend>
		<table class="wide noborder cers93">
			<tr>
				<td class="wide noborder">
					<strong>Utilisateur ayant annulé : </strong><?php echo h( $contratinsertion['Cer93']['Annulateur']['nom_complet'] ); ?>
				</td>
				<td class="wide noborder">
					<strong>Date d'annulation : </strong><?php echo date_short( $contratinsertion['Cer93']['date_annulation'] ); ?>
				</td>
			</tr>
			<tr>
				<td class="wide noborder" colspan="2">
					<strong>Raison de l'annulation : </strong><br/>
					<?php echo nl2br( h( $contratinsertion['Contratinsertion']['motifannulation'] ) ); ?>
				</td>
			</tr>
		</table>
	</fieldset>
<?php endif;?>
<fieldset>
	<legend>État civil</legend>
	 <table class="wide noborder">
        <tr>
            <td class="mediumSize noborder">
                <strong>Statut de la personne : </strong><?php echo Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.rolepers' ), $options['Prestation']['rolepers'] ); ?>
                <br />
                <strong>Nom : </strong><?php echo Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.qual'), $options['Personne']['qual'] ).' '.Set::classicExtract( $contratinsertion, 'Cer93.nom' );?>
                <br />
                <?php if( $contratinsertion['Cer93']['qual'] != 'MR' ):?>
					<strong>Nom de naissance : </strong><?php echo Set::classicExtract( $contratinsertion, 'Cer93.nomnai' );?>
					<br />
                <?php endif;?>
                <strong>Prénom : </strong><?php echo Set::classicExtract( $contratinsertion, 'Cer93.prenom' );?>
                <br />
                <strong>Date de naissance : </strong><?php echo date_short( Set::classicExtract( $contratinsertion, 'Cer93.dtnai' ) );?>
                <br />
                <strong>Adresse : </strong>
				<br /><?php echo nl2br( Set::classicExtract( $contratinsertion, 'Cer93.adresse' ) ).'<br \>'.Set::classicExtract( $contratinsertion, 'Cer93.codepos' ).' '.Set::classicExtract( $contratinsertion, 'Cer93.nomcom' );?>
            </td>
            <td class="mediumSize noborder">
                <strong>N° Service instructeur : </strong>
                <?php
					$libservice = Set::enum( Set::classicExtract( $contratinsertion, 'Suiviinstruction.typeserins' ),  $options['Serviceinstructeur']['typeserins'] );
					if( isset( $libservice ) ) {
						echo $libservice;
					}
					else{
						echo 'Non renseigné';
					}
                ?>
                <br />
                <strong>N° demandeur : </strong><?php echo Set::classicExtract( $contratinsertion, 'Cer93.numdemrsa' );?>
                <br />
                <strong>N° CAF/MSA : </strong><?php echo Set::classicExtract( $contratinsertion, 'Cer93.matricule' );?>
                <br />
                <strong>Inscrit au Pôle emploi</strong>
                <?php echo ( !empty( $contratinsertion['Cer93']['identifiantpe'] ) ? 'Oui' : 'Non' );?>
				<br />
				<strong>N° identifiant : </strong><?php echo Set::classicExtract( $contratinsertion, 'Cer93.identifiantpe' );?>
				<br />
				 <strong>Situation familiale : </strong><?php echo Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.sitfam' ), $options['Foyer']['sitfam'] );?>
                <br />
                <strong>Conditions de logement : </strong><?php echo Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.natlog' ), $options['Dsp']['natlog'] );?>
            </td>
        </tr>
    </table>

<?php

	// Bloc 2 : Composition du foyer
	if( !empty( $contratinsertion['Cer93']['Compofoyercer93'] ) ) {
		// Affichage des informations sous forme de tableau
		echo '<table class="mediumSize aere">
			<thead>
				<tr>
					<th>Rôle</th>
					<th>Civilité</th>
					<th>Nom</th>
					<th>Prénom</th>
					<th>Date de naissance</th>
				</tr>
			</thead>
		<tbody>';
		foreach( $contratinsertion['Cer93']['Compofoyercer93'] as $index => $compofoyercer93 ){
			echo $this->Xhtml->tableCells(
				array(
					h( Set::enum( $compofoyercer93['rolepers'], $options['Prestation']['rolepers'] ) ),
					h( Set::enum( $compofoyercer93['qual'], $options['Personne']['qual'] ) ),
					h( $compofoyercer93['nom'] ),
					h( $compofoyercer93['prenom'] ),
					h( $this->Locale->date( 'Date::short', $compofoyercer93['dtnai'] ) )
				),
				array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
				array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
			);
		}
		echo '</tbody></table>';
	}


	echo $this->Xform->fieldValue( 'Cer93.incoherencesetatcivil', Set::classicExtract( $contratinsertion, 'Cer93.incoherencesetatcivil' ) );
?>
</fieldset>

<fieldset>
	<legend>Vérification des droits</legend>
	<?php
		echo $this->Xform->fieldValue( 'Cer93.inscritpe', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.inscritpe' ), $options['Cer93']['inscritpe'] ) );
		echo $this->Xform->fieldValue( 'Cer93.cmu', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.cmu' ), $options['Cer93']['cmu'] ) );
		echo $this->Xform->fieldValue( 'Cer93.cmuc', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.cmuc' ), $options['Cer93']['cmuc'] ) );
	?>
</fieldset>
<fieldset id="FormationEtExpérience">
	<legend>Formation et expérience</legend>
	<?php
		echo $this->Xform->fieldValue( 'Cer93.nivetu', value( $options['Cer93']['nivetu'], Hash::get( $contratinsertion, 'Cer93.nivetu' ) ) );
	?>

<table class="wide aere noborder">
	<tr>
		<td style="width:48%" class="noborder">
		<h3>Diplômes (scolaires, universitaires et/ou professionnels)</h3>
		<?php if( !empty( $contratinsertion['Cer93']['Diplomecer93'] ) ):?>
			<table id="Diplomecer93">
				<thead>
					<tr>
						<th>Intitulé du diplôme</th>
						<th>Année d'obtention</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if( !empty( $contratinsertion['Cer93']['Diplomecer93'] ) ) {
							foreach( $contratinsertion['Cer93']['Diplomecer93'] as $index => $diplomecer93 ) {
								echo $this->Xhtml->tableCells(
									array(
										h( $diplomecer93['name'] ),
										h( $diplomecer93['annee'] ),
									),
									array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
									array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
								);

							}
						}
					?>
				</tbody>
			</table>
		<?php else:?>
			<p class="notice">Aucun diplôme renseigné pour cet allocataire</p>
		<?php endif;?>
		</td>
		<td class="noborder">
			<h3>Expériences professionnelles significatives</h3>
			<?php if( !empty( $contratinsertion['Cer93']['Expprocer93'] ) ):?>
				<table id="Expprocer93" class="tooltips">
					<thead>
						<tr>
							<th>Code domaine</th>
							<th>Code famille</th>
							<th>Code métier</th>
							<th>Appellation métier</th>
							<th>Nature du contrat</th>
							<th>Année de début</th>
							<th>Durée</th>
							<th class="innerTableHeader noprint">Informations complémentaires</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if( !empty( $contratinsertion['Cer93']['Expprocer93'] ) ) {
								foreach( $contratinsertion['Cer93']['Expprocer93'] as $index => $expprocer93 ) {
									$innerTable = '<table id="innerTableExpprocer93'.$index.'" class="innerTable">
										<tbody>
											<tr>
												<th>'.__d( 'cer93', 'Cer93.secteuracti_id' ).'</th>
												<td>'.value( $options['Expprocer93']['secteuracti_id'], $expprocer93['secteuracti_id'] ).'</td>
											</tr>
											<tr>
												<th>'.__d( 'cer93', 'Cer93.metierexerce_id' ).'</th>
												<td>'.value( $options['Expprocer93']['metierexerce_id'], $expprocer93['metierexerce_id'] ).'</td>
											</tr>
										</tbody>
									</table>';

									$code = array(
										'famille' => Hash::get( $expprocer93, 'Entreeromev3.Familleromev3.code' ),
										'domaine' => Hash::get( $expprocer93, 'Entreeromev3.Domaineromev3.code' ),
										'metier' => Hash::get( $expprocer93, 'Entreeromev3.Metierromev3.code' )
									);

									echo $this->Html->tableCells(
										array(
											h( implode( ' - ', array( $code['famille'], Hash::get( $expprocer93, 'Entreeromev3.Familleromev3.name' ) ) ) ),
											h( implode( ' - ', array( "{$code['famille']}{$code['domaine']}", Hash::get( $expprocer93, 'Entreeromev3.Domaineromev3.name' ) ) ) ),
											h( implode( ' - ', array( "{$code['famille']}{$code['domaine']}{$code['metier']}", Hash::get( $expprocer93, 'Entreeromev3.Metierromev3.name' ) ) ) ),
											h( Hash::get( $expprocer93, 'Entreeromev3.Appellationromev3.name' ) ),
											h( value( $options['Naturecontrat']['naturecontrat_id'], $expprocer93['naturecontrat_id'] ) ),
											h( $expprocer93['anneedeb'] ),
											h( "{$expprocer93['nbduree']} {$expprocer93['typeduree']}" ),
											array( $innerTable, array( 'class' => 'innerTableCell noprint' ) )
										),
										array( 'class' => 'odd', 'id' => 'innerTableTrigger'.$index ),
										array( 'class' => 'even', 'id' => 'innerTableTrigger'.$index )
									);
								}
							}
						?>
					</tbody>
				</table>
			<?php else:?>
				<p class="notice">Aucune expérience renseignée pour cet allocataire</p>
			<?php endif;?>
			</td>
		</tr>
	</table>
	<?php
		echo $this->Xform->fieldValue( 'Cer93.autresexps', Set::classicExtract( $contratinsertion, 'Cer93.autresexps'), true, 'textarea', true );
		echo $this->Xform->fieldValue( 'Cer93.isemploitrouv', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.isemploitrouv'), $options['Cer93']['isemploitrouv'] ) );
		if( $contratinsertion['Cer93']['isemploitrouv'] == 'O' ) {
			// Emploi trouvé, ROME v.3
			echo $this->Romev3->fieldsetView( 'Emptrouvromev3', $contratinsertion['Cer93'], array( 'domain' => 'cers93' ) );

			// Emploi trouvé, INSEE
			echo $this->Html->tag(
				'fieldset',
				$this->Html->tag( 'legend', 'Emploi trouvé (INSEE)' )
				.$this->Xform->fieldValue( 'Cer93.secteuracti_id', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.secteuracti_id'), $options['Expprocer93']['secteuracti_id'] ) )
				.$this->Xform->fieldValue( 'Cer93.metierexerce_id', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.metierexerce_id'), $options['Expprocer93']['metierexerce_id'] ) )
			);

			echo $this->Xform->fieldValue( 'Cer93.dureehebdo', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.dureehebdo'), $options['dureehebdo'] ) );
			echo $this->Xform->fieldValue( 'Cer93.naturecontrat_id', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.naturecontrat_id'), $options['Naturecontrat']['naturecontrat_id'] ) );

			if( !empty( $contratinsertion['Cer93']['dureecdd'] ) ) {
				echo $this->Xform->fieldValue( 'Cer93.dureecdd', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.dureecdd'), $options['dureecdd'] ) );
			}
		}
	?>
	<!-- Fin bloc 4 -->
</fieldset>
<!-- Bloc 5 : Bilan du précédent contrat -->
<?php $sujetpcd = array();?>
<fieldset id="bilanpcd"><legend>Bilan du contrat précédent</legend>
	<h4>Le précédent contrat portait sur </h4>
	<?php if( !empty( $contratinsertion['Cer93']['sujetpcd'] ) ):?>
		<?php
			$sujetpcd = unserialize( $contratinsertion['Cer93']['sujetpcd'] );
			echo $this->Cer93->sujetspcds2( $sujetpcd );
		?>
	<?php else:?>
		<p class="notice">Aucune information renseignée</p>
	<?php endif;?>
	<?php
		// Sujet précédent, complément d'informations ROME v.3
		$sujetromev3 = (array)Hash::get( $sujetpcd, 'Sujetromev3' );
		if( !empty( $sujetromev3 ) ) {
			echo $this->Romev3->fieldsetView( 'Sujetromev3', $sujetpcd, array( 'legend' => 'Le précédent contrat portait sur l\'emploi (ROME v.3)' ) );
		}

		echo $this->Xform->fieldValue( 'Cer93.prevupcd', Set::classicExtract( $contratinsertion, 'Cer93.prevupcd' ), true, 'textarea', true );
		echo $this->Xform->fieldValue( 'Cer93.bilancerpcd', Set::classicExtract( $contratinsertion, 'Cer93.bilancerpcd'), true, 'textarea', true );
	?>
</fieldset>
<!-- Fin du Bloc 5-->
<!-- Bloc 6 : Projet pour ce nouveau contrat -->
<fieldset id="projetbilan"><legend>Projet pour ce nouveau contrat</legend>
	<?php echo $this->Xform->fieldValue( 'Cer93.prevu', Set::classicExtract( $contratinsertion, 'Cer93.prevu'), true, 'textarea', true );?>
	<h3>Votre contrat porte sur </h3>
	<?php if( !empty( $contratinsertion['Cer93']['Sujetcer93'] ) ):?>
		<?php
			echo $this->Cer93->sujetspcds2( $contratinsertion['Cer93'] );
			// Emploi trouvé, ROME v.3
			$sujetromev3 = (array)Hash::get( $contratinsertion['Cer93'], 'Sujetromev3' );
			if( !empty( $sujetromev3 ) ) {
				echo $this->Romev3->fieldsetView( 'Sujetromev3', $contratinsertion['Cer93'], array( 'domain' => 'cers93' ) );
			}
		?>
	<?php else:?>
		<p class="notice">Aucune information renseignée</p>
	<?php endif;?>
</fieldset>
<!-- Fin du Bloc 6 -->
<!-- Bloc 7-8-9 -->
<fieldset>
	<?php
		//Bloc 7 : Durée proposée
		echo $this->Xform->fieldValue( 'Cer93.duree', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.duree'), $options['Cer93']['duree'] ) );

		//Bloc 8 : Projet pour ce nouveau contrat
		echo $this->Xform->fieldValue( 'Cer93.pointparcours', Set::enum( Set::classicExtract( $contratinsertion, 'Cer93.pointparcours'), $options['Cer93']['pointparcours'] ) );
		if( !empty( $contratinsertion['Cer93']['datepointparcours'] ) ) {
			echo $this->Xform->fieldValue( 'Cer93.datepointparcours', date_short( Set::classicExtract( $contratinsertion, 'Cer93.datepointparcours') ) );
		}

		//Bloc 9 : Partie réservée au professionnel en charge du contrat
		echo $this->Xform->fieldValue( 'Cer93.structureutilisateur', Set::classicExtract( $contratinsertion, 'Cer93.structureutilisateur' ), 'cers93' );
		echo $this->Xform->fieldValue( 'Cer93.nomutilisateur', Set::classicExtract( $contratinsertion, 'Cer93.nomutilisateur' ) );

		echo $this->Xform->fieldValue( 'Cer93.pourlecomptede', Set::classicExtract( $contratinsertion, 'Cer93.pourlecomptede' ) );
		echo $this->Xform->fieldValue( 'Cer93.observpro', Set::classicExtract( $contratinsertion, 'Cer93.observpro' ), true, 'textarea', true );

		echo $this->Xform->fieldValue( 'Contratinsertion.dd_ci', date_short( Set::classicExtract( $contratinsertion, 'Contratinsertion.dd_ci') ) );
		echo $this->Xform->fieldValue( 'Contratinsertion.df_ci', date_short( Set::classicExtract( $contratinsertion, 'Contratinsertion.df_ci') ) );
		echo $this->Xform->fieldValue( 'Contratinsertion.date_saisi_ci', date_short( Set::classicExtract( $contratinsertion, 'Contratinsertion.date_saisi_ci') ) );
	?>
</fieldset>