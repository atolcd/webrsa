<?php
	$this->pageTitle = 'Synthèse du parcours d\'insertion';

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	$cerControllerName = ( ( Configure::read( 'Cg.departement' ) == 93 ) ? 'cers93' : 'contratsinsertion' );
?>

<?php
	function thead( $pct = 10, $role = null ) {
		return '<thead>
				<tr>
					<th colspan="4" style="width: '.$pct.'%;">'.$role.'</th>
				</tr>
			</thead>';
	}
?>

<h1><?php
	echo $this->pageTitle;
	echo ' de '.$details['DEM']['Personne']['nom_complet'];
?></h1>
	<?php if( Configure::read( 'Cg.departement' ) == 93 ):?>
		<ul class="actionMenu">
			<li><?php
				echo $this->Xhtml->printLinkJs(
					'Imprimer l\'écran',
					array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
				);
			?></li>
		</ul>
	<?php endif;?>

<div id="resumeDossier">

<!-- Etape 1 : Affichage des instructions du Dossier (valable pour le Demandeur et le Conjoint) -->
	<h2>Etape 1: Instruction dossier</h2>
	<table>
		<?php echo thead( 100, 'Parcours Demandeur/Conjoint' );?>
		<tbody>
			<tr class="odd">
				<th>Service instructeur</th>
				<th>Date de demande</th>
				<th>Réalisé</th>
				<th class="action">Action</th>
			</tr>
			<tr>
				<td><?php echo value( $typeserins, Set::classicExtract( $details, 'Suiviinstruction.typeserins' ))?></td>
				<td><?php echo date_short( $details['Dossier']['dtdemrsa'] );?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['Dossier']['dtdemrsa'] ) );?></td>
				<td><?php echo $this->Xhtml->viewLink(
					'Voir le dossier',
					array( 'controller' => 'dossiers', 'action' => 'view', $details['Dossier']['id'] )
					);?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id="suiviInsertion">
	<!-- Etape 2 : Affichage de la dernière orientation du Demandeur et du Conjoint -->
	<h2>Etape 2: Orientation</h2>
	<table>
		<thead>
			<tr class="odd">
				<th colspan="5">Parcours Demandeur</th>
				<th colspan="5">Parcours Conjoint</th>
			</tr>
		</thead>
		<tbody>
			<tr class="odd">
				<th style="width: 200px">Structure référente</th>
				<th style="width: 200px">Date d'orientation</th>
				<th style="width: 200px">Date de relance</th>
				<th style="width: 200px">Réalisé</th>
				<th class="action">Action</th>

				<th style="width: 200px">Structure référente</th>
				<th style="width: 200px">Date d'orientation</th>
				<th style="width: 200px">Date de relance</th>
				<th style="width: 200px">Réalisé</th>
				<th class="action">Action</th>
			</tr>
			<tr>
				<td><?php echo value( $structuresreferentes, Set::extract( 'DEM.Orientstruct.derniere.structurereferente_id', $details ) );?></td>
				<td><?php echo date_short( Set::extract( 'DEM.Orientstruct.derniere.date_valid', $details ) );?></td>
				<td><?php echo date_short( Set::extract( 'DEM.Orientstruct.derniere.daterelance', $details ) );?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Orientstruct']['derniere']['date_valid'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Orientstruct']['derniere']['structurereferente_id'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir l\'orientation',
							array( 'controller' => 'orientsstructs', 'action' => 'index', Set::extract( 'DEM.Orientstruct.derniere.personne_id', $details ) ),
							$this->Permissions->checkDossier( 'orientsstructs', 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo value( $structuresreferentes, Set::extract( 'CJT.Orientstruct.derniere.structurereferente_id', $details ) );?></td>
				<td><?php echo date_short( Set::extract( 'CJT.Orientstruct.derniere.date_valid', $details ) );?></td>
				<td><?php echo date_short( Set::extract( 'CJT.Orientstruct.derniere.daterelance', $details ) );?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Orientstruct']['derniere']['date_valid'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Orientstruct']['derniere']['structurereferente_id'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir l\'orientation',
							array( 'controller' => 'orientsstructs', 'action' => 'index', Set::extract( 'CJT.Orientstruct.derniere.personne_id', $details ) ),
							$this->Permissions->checkDossier( 'orientsstructs', 'index', $dossierMenu )
						);
					}
				?></td>
			</tr>
		</tbody>
	</table>

	<!-- Etape 3 : Affichage des entretiens avec les structures référentes -->
	<h2>Etape 3: Rendez-vous référent</h2>
	<table>
		<thead>
			<tr class="odd">
				<th colspan="4">Parcours Demandeur</th>
				<th colspan="4">Parcours Conjoint</th>
			</tr>
		</thead>
		<tbody>
			<tr class="odd">
				<th>Date du dernier RDV</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>

				<th>Date du dernier RDV</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>
			</tr>
			<tr>
				<td><?php echo date_short( Set::extract( 'DEM.Rendezvous.dernier.daterdv', $details ) );?></td>
				<td colspan="2"><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Rendezvous']['dernier']['daterdv'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Rendezvous']['dernier']['daterdv'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir l\'entretien',
							array( 'controller' => 'rendezvous', 'action' => 'index', Set::extract( 'DEM.Rendezvous.dernier.personne_id', $details ) ),
							$this->Permissions->checkDossier( 'rendezvous', 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo date_short( Set::extract( 'CJT.Rendezvous.dernier.daterdv', $details ) );?></td>
				<td colspan="2" ><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Rendezvous']['dernier']['daterdv'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Rendezvous']['dernier']['daterdv'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir l\'entretien',
							array( 'controller' => 'rendezvous', 'action' => 'index', Set::extract( 'CJT.Rendezvous.dernier.personne_id', $details ) ),
							$this->Permissions->checkDossier( 'rendezvous', 'index', $dossierMenu )
						);
					}
				?></td>
			</tr>
		</tbody>
	</table>

	<!-- Etape 4 : Affichage des derniers enregistrements des contrats engagements -->
	<h2>Etape 4: Enregistrement Contrats ( CER / CUI )</h2>
	<table>
		<thead>
			<tr>
				<th  class="entete" colspan="8">CER</th>
				<th></th>
				<th  class="entete" colspan="8">CUI</th>
			</tr>
			<tr class="odd">
				<th colspan="4">Parcours Demandeur</th>
				<th colspan="4">Parcours Conjoint</th>

				<th></th>

				<th colspan="4">Parcours Demandeur</th>
				<th colspan="4">Parcours Conjoint</th>
			</tr>
		</thead>
		<tbody>
			<tr class="odd">
				<th>Date de signature</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>

				<th>Date de signature</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>

				<th></th>

				<th>Date de signature</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>

				<th>Date de signature</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>
			</tr>
			<tr>
				<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.date_saisi_ci', $details ) );?></td>
				<td colspan="2"><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Contratinsertion']['date_saisi_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Contratinsertion']['date_saisi_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => $cerControllerName, 'action' => 'index', Set::extract( 'DEM.Personne.id', $details ) ),
							$this->Permissions->checkDossier( $cerControllerName, 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.date_saisi_ci', $details ) );?></td>
				<td colspan="2" ><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Contratinsertion']['date_saisi_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Contratinsertion']['date_saisi_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => $cerControllerName, 'action' => 'index', Set::extract( 'CJT.Personne.id', $details ) ),
							$this->Permissions->checkDossier( $cerControllerName, 'index', $dossierMenu )
						);
					}
				?></td>

				<td></td>

				<!-- CUI -->
				<td><?php echo date_short( Set::extract( 'DEM.Cui.signaturele', $details ) );?></td>
				<td colspan="2"><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Cui']['signaturele'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Cui']['signaturele'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'cuis', 'action' => 'index', Set::extract( 'DEM.Personne.id', $details ) ),
							$this->Permissions->checkDossier( 'cuis', 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo date_short( Set::extract( 'CJT.Cui.signaturele', $details ) );?></td>
				<td colspan="2" ><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Cui']['signaturele'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Cui']['signaturele'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'cuis', 'action' => 'index', Set::extract( 'CJT.Personne.id', $details ) ),
							$this->Permissions->checkDossier( 'cuis', 'index', $dossierMenu )
						);
					}
				?></td>
			</tr>
		</tbody>
	</table>

	<!-- Etape 5 : Affichage des validations des contrats d'd'Engagement -->
	<h2>Etape 5: Validation Contrats ( CER / CUI )</h2>
	<table>
		<thead>
			<tr>
				<th  class="entete" colspan="8">CER</th>
				<th></th>
				<th  class="entete" colspan="8">CUI</th>
			</tr>
			<tr class="odd">
				<th colspan="4">Parcours Demandeur</th>
				<th colspan="4">Parcours Conjoint</th>
				<th></th>
				<th colspan="4">Parcours Demandeur</th>
				<th colspan="4">Parcours Conjoint</th>
			</tr>
		</thead>
		<tbody>
			<tr class="odd">
				<th>Date de validation</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>

				<th>Date de validation</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>
				<th></th>
				<th>Date de validation</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>

				<th>Date de validation</th>
				<th colspan="2">Réalisé</th>
				<th class="action">Action</th>
			</tr>
			<tr>
				<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.datevalidation_ci', $details ) );?></td>
				<td colspan="2"><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Contratinsertion']['datevalidation_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Contratinsertion']['datevalidation_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => $cerControllerName, 'action' => 'index', Set::extract( 'DEM.Personne.id', $details ) ),
							$this->Permissions->checkDossier( $cerControllerName, 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.datevalidation_ci', $details ) );?></td>
				<td colspan="2" ><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Contratinsertion']['datevalidation_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Contratinsertion']['datevalidation_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => $cerControllerName, 'action' => 'index', Set::extract( 'CJT.Personne.id', $details ) ),
							$this->Permissions->checkDossier( $cerControllerName, 'index', $dossierMenu )
						);
					}
				?></td>
				<td></td>
				<td><?php echo date_short( Set::extract( 'DEM.Cui.datevalidation_ci', $details ) );?></td>
				<td colspan="2"><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Cui']['datevalidation_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Cui']['datevalidation_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'cuis', 'action' => 'index', Set::extract( 'DEM.Personne.id', $details ) ),
							$this->Permissions->checkDossier( 'cuis', 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo date_short( Set::extract( 'CJT.Cui.datevalidation_ci', $details ) );?></td>
				<td colspan="2" ><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Cui']['datevalidation_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Cui']['datevalidation_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'cuis', 'action' => 'index', Set::extract( 'CJT.Personne.id', $details ) ),
							$this->Permissions->checkDossier( 'cuis', 'index', $dossierMenu )
						);
					}
				?></td>
			</tr>
		</tbody>
	</table>
	<?php if( Configure::read( 'Cg.departement' ) != 93 ): ?>
		<!-- Etape 6 : Affichage des Actions d'insertion engagées -->
		<h2>Etape 6: Actions d'insertion engagées</h2>
		<table>
			<thead>
				<tr class="odd">
					<th colspan="4">Parcours Demandeur</th>
					<th colspan="4">Parcours Conjoint</th>
				</tr>
			</thead>
			<tbody>
				<tr class="odd">
					<th>Actions engagées</th>
					<th>Date dernière action</th>
					<th>Réalisé</th>
					<th class="action">Action</th>

					<th>Actions engagées</th>
					<th>Date dernière action</th>
					<th>Réalisé</th>
					<th class="action">Action</th>
				</tr>
				<tr>
					<td><?php echo count( Set::extract( 'DEM.Actioninsertion', $details ) );?></td>
					<td><?php echo date_short( Set::extract( 'DEM.Actioninsertion.0.Actioninsertion.dd_action', $details ) );?></td>
					<td><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Contratinsertion']['datevalidation_ci'] ) );?></td>
					<td><?php
						if( !empty( $details['DEM']['Contratinsertion'] ) ){
							echo $this->Xhtml->viewLink(
								'Voir les actions d\'insertion',
								array( 'controller' => 'actionsinsertion', 'action' => 'index', Set::extract( 'DEM.Contratinsertion.id', $details ) ),
								$this->Permissions->checkDossier( 'actionsinsertion', 'index', $dossierMenu )
							);
						}
					?></td>

					<td><?php echo count( Set::extract( 'CJT.Actioninsertion', $details ) );?></td>
					<td><?php echo date_short( Set::extract( 'CJT.Actioninsertion.0.Actioninsertion.dd_action', $details ) );?></td>
					<td><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Actioninsertion']['dd_action'] ) );?></td>
					<td><?php
						if( !empty( $details['CJT']['Contratinsertion'] ) ){
							echo $this->Xhtml->viewLink(
								'Voir les actions d\'insertion',
								array( 'controller' => 'actionsinsertion', 'action' => 'index', Set::extract( 'CJT.Contratinsertion.id', $details ) ),
								$this->Permissions->checkDossier( 'actionsinsertion', 'index', $dossierMenu )
							);
						}
					?></td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>

	<?php if( Configure::read( 'Cg.departement' ) == 93 ): ?>
		<h2>Etape 6: Actions prescrites</h2>
		<table>
			<thead>
				<tr class="odd">
					<th colspan="5">Parcours Demandeur</th>
					<th colspan="5">Parcours Conjoint</th>
				</tr>
			</thead>
			<tbody>
				<tr class="odd">
					<th>Dernière action proposée</th>
					<th>Date d'effectivité</th>
					<th>A participé</th>
					<th>Catégorie d'action</th>
					<th class="action">Action</th>

					<th>Dernière action prroposée</th>
					<th>Date d'effectivité</th>
					<th>A participé</th>
					<th>Catégorie d'action</th>
					<th class="action">Action</th>
				</tr>
				<tr>
					<?php foreach( array( 'DEM', 'CJT' ) as $rolepers ): ?>
						<td><?php echo Hash::get( $details, "{$rolepers}.Ficheprescription93.Actionfp93.name" );?></td>
						<td><?php echo date_short( Hash::get( $details, "{$rolepers}.Ficheprescription93.Ficheprescription93.date_retour" ) );?></td>
						<td><?php echo $this->Xhtml->boolean( Hash::get( $details, "{$rolepers}.Ficheprescription93.Ficheprescription93.personne_a_integre" ) );?></td>
						<td><?php echo Hash::get( $details, "{$rolepers}.Ficheprescription93.Categoriefp93.name" );?></td>
						<td><?php
							$personne_id = Hash::get( $details, "{$rolepers}.Ficheprescription93.Ficheprescription93.personne_id" );
						if( !empty( $personne_id ) ){
							echo $this->Xhtml->viewLink(
								'Voir la fiche de positionnement',
								array( 'controller' => 'fichesprescriptions93', 'action' => 'index', $personne_id ),
								$this->Permissions->checkDossier( 'fichesprescriptions93', 'index', $dossierMenu )
							);
						}
					?></td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>

	<!-- Etape 7 : Affichage des bilans de fin de Contrat d'insertion -->
	<h2>Etape 7: Bilan de fin de Contrats ( CER / CUI )</h2>
	<table>
		<thead>
			<tr>
				<th  class="entete" colspan="8">CER</th>
				<th></th>
				<th  class="entete" colspan="8">CUI</th>
			</tr>
			<tr class="odd">
				<th colspan="4">Parcours Demandeur</th>
				<th colspan="4">Parcours Conjoint</th>
				<th></th>
				<th colspan="4">Parcours Demandeur</th>
				<th colspan="4">Parcours Conjoint</th>
			</tr>
		</thead>
		<tbody>
			<tr class="odd">
				<th>Date bilan</th>
				<th>Bilan</th>
				<th>Réalisé</th>
				<th class="action">Action</th>

				<th>Date bilan</th>
				<th>Bilan</th>
				<th>Réalisé</th>
				<th class="action">Action</th>

				<th></th>

				<th>Date bilan</th>
				<th>Bilan</th>
				<th>Réalisé</th>
				<th class="action">Action</th>

				<th>Date bilan</th>
				<th>Bilan</th>
				<th>Réalisé</th>
				<th class="action">Action</th>
			</tr>
			<tr>
				<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.df_ci', $details ) );?></td>
				<td><?php echo 'Non défini'?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Contratinsertion']['df_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Contratinsertion']['df_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => $cerControllerName, 'action' => 'index', Set::extract( 'DEM.Personne.id', $details ) ),
							$this->Permissions->checkDossier( $cerControllerName, 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.df_ci', $details ) );?></td>
				<td><?php echo 'Non défini'?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Contratinsertion']['df_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Contratinsertion']['df_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => $cerControllerName, 'action' => 'index', Set::extract( 'CJT.Personne.id', $details ) ),
							$this->Permissions->checkDossier( $cerControllerName, 'index', $dossierMenu )
						);
					}
				?></td>

				<td></td>

				<td><?php echo date_short( Set::extract( 'DEM.Cui.df_ci', $details ) );?></td>
				<td><?php echo 'Non défini'?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Cui']['df_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Cui']['df_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'cuis', 'action' => 'index', Set::extract( 'DEM.Personne.id', $details ) ),
							$this->Permissions->checkDossier( 'cuis', 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo date_short( Set::extract( 'CJT.Cui.df_ci', $details ) );?></td>
				<td><?php echo 'Non défini'?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Cui']['df_ci'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Cui']['df_ci'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'cuis', 'action' => 'index', Set::extract( 'CJT.Personne.id', $details ) ),
							$this->Permissions->checkDossier( 'cuis', 'index', $dossierMenu )
						);
					}
				?></td>
			</tr>
		</tbody>
	</table>
	<?php if( Configure::read( 'Cg.departement' ) == 93 ):?>
	<!-- Etape 8 : Affichage des dernières relances -->
	<h2>Etape 8: Dernière relance</h2>
	<table>
		<thead>
			<tr class="odd">
				<th colspan="5">Parcours Demandeur</th>
				<th colspan="5">Parcours Conjoint</th>
			</tr>
		</thead>
		<tbody>
			<tr class="odd">
				<th>Type de relance</th>
				<th>Date de relance</th>
				<th>Rang de passage</th>
				<th>Réalisé</th>
				<th class="action">Action</th>

				<th>Type de relance</th>
				<th>Date de relance</th>
				<th>Rang de passage</th>
				<th>Réalisé</th>
				<th class="action">Action</th>
			</tr>
			<tr>
				<td><?php echo Set::enum( Set::classicExtract( $details, 'DEM.Nonrespectsanctionep93.derniere.Nonrespectsanctionep93.origine' ), $relance['origine'] );?></td>
				<td><?php echo h( date_short( Set::extract( 'DEM.Nonrespectsanctionep93.derniere.Relancenonrespectsanctionep93.daterelance', $details ) ) );?></td>
				<td><?php
					$numrelance = Set::extract( 'DEM.Nonrespectsanctionep93.derniere.Relancenonrespectsanctionep93.numrelance', $details );
					if( !empty($numrelance) ){
						if( $numrelance == 1 ) {
							echo '1ère relance';
						}
						else {
							echo "{$numrelance}ème relance";
						}
					}
				?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['DEM']['Nonrespectsanctionep93']['derniere']['Nonrespectsanctionep93']['origine'] ) );?></td>
				<td><?php
					if( !empty( $details['DEM']['Nonrespectsanctionep93']['derniere']['Nonrespectsanctionep93']['origine'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'index', Set::extract( 'DEM.Personne.id', $details ) ),
							$this->Permissions->checkDossier( 'relancesnonrespectssanctionseps93', 'index', $dossierMenu )
						);
					}
				?></td>

				<td><?php echo Set::enum( Set::classicExtract( $details, 'CJT.Nonrespectsanctionep93.derniere.Nonrespectsanctionep93.origine' ), $relance['origine'] );?></td>
				<td><?php echo h( date_short( Set::extract( 'CJT.Nonrespectsanctionep93.derniere.Relancenonrespectsanctionep93.daterelance', $details ) ) );?></td>
				<td><?php
					$numrelance = Set::extract( 'CJT.Nonrespectsanctionep93.derniere.Relancenonrespectsanctionep93.numrelance', $details );
					if( !empty($numrelance) ){
						if( $numrelance == 1 ) {
							echo '1ère relance';
						}
						else {
							echo "{$numrelance}ème relance";
						}
					}
				?></td>
				<td><?php echo $this->Xhtml->boolean( !empty( $details['CJT']['Nonrespectsanctionep93']['derniere']['Nonrespectsanctionep93']['origine'] ) );?></td>
				<td><?php
					if( !empty( $details['CJT']['Nonrespectsanctionep93']['derniere']['Nonrespectsanctionep93']['origine'] ) ){
						echo $this->Xhtml->viewLink(
							'Voir le contrat',
							array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'index', Set::extract( 'CJT.Personne.id', $details ) ),
							$this->Permissions->checkDossier( 'relancesnonrespectssanctionseps93', 'index', $dossierMenu )
						);
					}
				?></td>
			</tr>
		</tbody>
	</table>
	<?php endif;?>

	<?php if( Configure::read( 'Cg.departement' ) == 93 ):?>
		<!-- Etape 9 : Affichage du dernier passage en EP -->
		<h2>Etape 9: Dernier passage en EP</h2>
			<?php
				$detailsEp = array();
				foreach( array( 'DEM', 'CJT' ) as $roleEp ) {
					if( isset( $details[$roleEp]['Dossierep']['derniere']['Dossierep'] ) ){
						$detailsEp[$roleEp]['dateEp'] = h( date_short( Set::extract( "{$roleEp}.Dossierep.derniere.Commissionep.dateseance", $details ) ) );
						$themeep = Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Dossierep.themeep" );
						$modeleDecision = 'Decision'.Inflector::singularize( $themeep );
						$detailsEp[$roleEp]['themeEp'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Dossierep.themeep" ), $dossierep['themeep'] );
						$detailsEp[$roleEp]['decisionEp'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.0.{$modeleDecision}.decision" ), $options[$modeleDecision]['decision'] );

						$detailsEp[$roleEp]['decisionCG'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.1.{$modeleDecision}.decision" ), $options[$modeleDecision]['decision'] );

						$detailsEp[$roleEp]['etatDossierep'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Passagecommissionep.etatdossierep" ), $options['Passagecommissionep']['etatdossierep'] );
					}
				}

			?>
			<?php if( isset($detailsEp) && !empty( $detailsEp ) ):?>
			<table>
				<thead>
					<tr class="odd">
						<th colspan="6">Parcours Demandeur</th>
						<th colspan="5">Parcours Conjoint</th>
					</tr>
				</thead>
				<tbody>
					<tr class="odd">
						<th>Date de la commission d'EP</th>
						<th>Motif de passage en EP</th>
						<th>État dossier EP</th>
						<th>Décision de l'EP</th>
						<th>Décision du CD</th>
						<th></th>

						<th>Date de la commission d'EP</th>
						<th>Motif de passage en EP</th>
						<th>État dossier EP</th>
						<th>Décision de l'EP</th>
						<th>Décision du CD</th>

					</tr>
						<td><?php echo @$detailsEp['DEM']['dateEp'];?></td>
						<td><?php echo @$detailsEp['DEM']['themeEp'];?></td>
						<td><?php echo @$detailsEp['DEM']['etatDossierep'];?></td>
						<td><?php echo @$detailsEp['DEM']['decisionEp'];?></td>
						<td><?php echo @$detailsEp['DEM']['decisionCG'];?></td>

						<td><?php echo @$detailsEp['CJT']['dateEp'];?></td>
						<td><?php echo @$detailsEp['CJT']['themeEp'];?></td>
						<td><?php echo @$detailsEp['CJT']['etatDossierep'];?></td>
						<td><?php echo @$detailsEp['CJT']['decisionEp'];?></td>
						<td><?php echo @$detailsEp['CJT']['decisionCG'];?></td>
					</tr>
				</tbody>
			</table>

		<?php else:?>
			<p class="notice">Aucun passage en EP présent.</p>
		<?php endif;?>
	<?php endif;?>

	<?php if( Configure::read( 'Cg.departement' ) != 93 ):?>
		<h2>Etape 8: Dernier passage en EP</h2>
		<?php
			$detailsEp = array();
			foreach( array( 'DEM', 'CJT' ) as $roleEp ) {
				if( isset( $details[$roleEp]['Dossierep']['derniere']['Dossierep'] ) ){
					$detailsEp[$roleEp]['dateEp'] = h( date_short( Set::extract( "{$roleEp}.Dossierep.derniere.Commissionep.dateseance", $details ) ) );
					$themeep = Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Dossierep.themeep" );
					$modeleDecision = 'Decision'.Inflector::singularize( $themeep );
					$detailsEp[$roleEp]['themeEp'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Dossierep.themeep" ), $dossierep['themeep'] );
					$detailsEp[$roleEp]['decisionEp'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.{$modeleDecision}.decision" ), $options[$modeleDecision]['decision'] );
					$detailsEp[$roleEp]['etatDossierep'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Passagecommissionep.etatdossierep" ), $options['Passagecommissionep']['etatdossierep'] );
				}
			}
		?>
		<?php if( isset($detailsEp) && !empty( $detailsEp ) ):?>
		<table>
			<thead>
				<tr class="odd">
					<th colspan="5">Parcours Demandeur</th>
					<th colspan="5">Parcours Conjoint</th>
				</tr>
			</thead>
			<tbody>
				<tr class="odd">
					<th>Date de la commission d'EP</th>
					<th>Motif de passage en EP</th>
					<th>État dossier EP</th>
					<th>Décision de l'EP</th>
					<th>Réalisé</th>

					<th>Date de la commission d'EP</th>
					<th>Motif de passage en EP</th>
					<th>État dossier EP</th>
					<th>Décision de l'EP</th>
					<th>Réalisé</th>
				</tr>
					<td><?php echo @$detailsEp['DEM']['dateEp'];?></td>
					<td><?php echo @$detailsEp['DEM']['themeEp'];?></td>
					<td><?php echo @$detailsEp['DEM']['etatDossierep'];?></td>
					<td><?php echo @$detailsEp['DEM']['decisionEp'];?></td>
					<td><?php echo $this->Xhtml->boolean( !empty( $detailsEp['DEM']['decisionCG'] ) );?></td>

					<td><?php echo @$detailsEp['CJT']['dateEp'];?></td>
					<td><?php echo @$detailsEp['CJT']['themeEp'];?></td>
					<td><?php echo @$detailsEp['CJT']['etatDossierep'];?></td>
					<td><?php echo @$detailsEp['CJT']['decisionEp'];?></td>
					<td><?php echo $this->Xhtml->boolean( !empty( $detailsEp['CJT']['decisionCG'] ) );?></td>
				</tr>
			</tbody>
		</table>

		<?php else:?>
			<p class="notice">Aucun passage en EP présent.</p>
		<?php endif;?>
	<?php endif;?>
</div>