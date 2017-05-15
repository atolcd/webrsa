<?php $this->pageTitle = 'Liste des indus';?>

<h1><?php echo $this->pageTitle;?></h1>

<?php if( empty( $infofinanciere ) ):?>
	<p class="notice">Ce dossier ne possède pas d'indus.</p>
<?php else:?>
	<table id="searchResults" class="tooltips">
		<thead>
			<tr>
				<th>NIR</th>
				<th>Nom de l'allocataire</th>
				<th>Suivi</th>
				<th>Situation des droits</th>
				<th>Date indus</th>
				<th>Montant initial de l'indu</th>
				<th>Remise</th>
				<th>Montant remboursé</th>
				<th>Solde du</th>
				<th class="action">Action</th>
			</tr>
		</thead>
		<tbody>
				<?php
					echo $this->Xhtml->tableCells(
						array(
							h( $infofinanciere['Personne']['nir'] ),
							h( $infofinanciere['Personne']['nom'].' '.$infofinanciere['Personne']['prenom'] ),
							h( $infofinanciere['Dossier']['typeparte'] ),
							h( $etatdosrsa[$infofinanciere['Situationdossierrsa']['etatdosrsa']] ),
							$this->Locale->date( 'Date::short', $infofinanciere['Infofinanciere']['moismoucompta'] ),
							$this->Locale->money( $infofinanciere[0]['mt_indus_constate'] ),
							$this->Locale->money( $infofinanciere[0]['mt_remises_indus'] ),
							$this->Locale->money( $infofinanciere[0]['mt_indus_transferes_c_g'] ),
							$this->Locale->money( $infofinanciere[0]['mt_annulations_faible_montant'] ),
							$this->Xhtml->viewLink(
								'Détails d\'indu',
								array( 'controller' => 'indus', 'action' => 'view', $infofinanciere['Dossier']['id'] ),
								$this->Permissions->checkDossier( 'indus', 'view', $dossierMenu )
							),
						),
					array( 'class' => 'odd' ),
					array( 'class' => 'even' )
					);
				?>
		</tbody>
	</table>
<?php endif;?>