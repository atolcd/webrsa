<?php
	$departement = Configure::read( 'Cg.departement' );

	function thead( $pct = 10 ) {
		return '<thead>
				<tr>
					<th>&nbsp;</th>
					<th style="width: '.$pct.'%;">Demandeur</th>
					<th style="width: '.$pct.'%;">Conjoint</th>
				</tr>
			</thead>';
	}

	function theadPastDossierDEM( $pctValue = 10, $pctAction = 8 ) {

        if( Configure::read( 'Cg.departement' ) == 66 ) {
            return '<thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th style="width: '.$pctValue.'%;">Demandeur</th>
                        <th style="width: 4%;">Dossier PCG</th>
                        <th style="width: '.$pctAction.'%;">Action</th>
                    </tr>
                </thead>';
        }
        else {
            return '<thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th style="width: '.$pctValue.'%;">Demandeur</th>
                        <th style="width: '.$pctAction.'%;">Action</th>
                    </tr>
                </thead>';
        }
	}

	function theadPastDossierCJT( $pctValue = 10, $pctAction = 8 ) {
		if( Configure::read( 'Cg.departement' ) == 66 ) {
            return '<thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th style="width: '.$pctValue.'%;">Conjoint</th>
                        <th style="width: 4%;">Dossier PCG</th>
                        <th style="width: '.$pctAction.'%;">Action</th>
                    </tr>
                </thead>';
        }
        else {
            return '<thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th style="width: '.$pctValue.'%;">Demandeur</th>
                        <th style="width: '.$pctAction.'%;">Action</th>
                    </tr>
                </thead>';
        }
	}

	function linkedValue( $links, $details, $personne, $table, $field ) {
		$value = ( ( isset( $details[$personne][$table] ) && isset( $details[$personne][$table][$field] ) ) ? ( $details[$personne][$table][$field] ) : null );
		return ( isset( $links[$value] ) ? $links[$value] : null );
	}

	function textToppersdrodevorsa( $calculdroitrsa ) {
		if( !isset( $calculdroitrsa['toppersdrodevorsa'] ) ) {
			return null;
		}

		if( is_null( $calculdroitrsa['toppersdrodevorsa'] ) ) {
			return 'Non défini';
		}
		else if( $calculdroitrsa['toppersdrodevorsa'] == 1 ) {
			return 'Oui';
		}
		else {
			return 'Non';
		}
	}

	function textPresenceDsp( $allocataire ) {
		if( !isset( $allocataire['Personne']['id'] ) ) {
			return null;
		}

		if( isset( $allocataire['Dsp']['id'] ) ) {
			return 'Oui';
		}
		else {
			return 'Non';
		}
	}

	function etatdossiercui66( array $data, array $enums ) {
		$etatdossiercui66 = Hash::get( $data, 'Cui66.etatdossiercui66' );

		$insert = '';
		if ( in_array( $etatdossiercui66, array( 'contratsuspendu', 'rupturecontrat', 'dossierrelance' ) ) ){
			switch ( $etatdossiercui66 ){
				case 'contratsuspendu': $insert = new DateTime($data['Suspensioncui66']['datefin']); break;
				case 'rupturecontrat': $insert = new DateTime($data['Rupturecui66']['daterupture']); break;
				case 'dossierrelance': $insert = new DateTime($data['Emailcui']['dateenvoi']); break;
				default: $insert = '';
			}
			if( false === empty( $insert ) ) {
				$insert = date_format( $insert, 'd/m/Y' );
			}
		}

		return sprintf( value( $enums, $etatdossiercui66 ), $insert );
	}

	/////  Récupération données du Contratinsertion pour le DEM et le CJT
	$DT = Set::extract( 'DEM.Contratinsertion.num_contrat', $details);
	$CT = Set::extract( 'CJT.Contratinsertion.num_contrat', $details);

	$deciD = Set::extract( 'DEM.Contratinsertion.decision_ci', $details);
	$deciC = Set::extract( 'CJT.Contratinsertion.decision_ci', $details);

?>
<?php $this->pageTitle = 'Dossier RSA '.$details['Dossier']['numdemrsa'];?>

<div id="resumeDossier">
	<div style="clear:all;overflow: none;height: 0;">&nbsp;</div>
	<h1>Dossier RSA <?php echo h( $details['Dossier']['numdemrsa'] );?></h1>
	<table style="width:100%;">
		<tbody>
			<tr class="odd">
				<th>Numéro de dossier</th>
				<td><?php echo h( $details['Dossier']['numdemrsa'] );?></td>
			</tr>
			<tr class="even">
				<th>Date de demande</th>
				<td><?php echo h( date_short( $details['Dossier']['dtdemrsa'] ) );?></td>
			</tr>
			<tr class="odd">
				<th>État du dossier</th>
				<td><?php echo h( value( $etatdosrsa, Set::extract( 'Situationdossierrsa.etatdosrsa', $details ) ) );?></td>
			</tr>
			<tr class="even">
				<th>Service instructeur</th>
				<td><?php echo h( value( $typeserins, Set::extract( 'Suiviinstruction.typeserins', $details ) ) );?></td>
			</tr>
			<tr class="odd">
				<th>Statut du demandeur</th>
				<td><?php echo value( $statudemrsa, Set::extract( 'Dossier.statudemrsa', $details ) );?></td>
			</tr>
		</tbody>
	</table>

	<div class="col-2 gauche">
		<h2>Suivi du parcours</h2>
		<table>
			<?php
				$rowCnt = 0;
				echo thead( 10 );
			?>
			<tbody>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo $departement == 93 ? 'Structure de suivi' : 'Structure référente en cours';?></th>
					<?php foreach( array( 'DEM', 'CJT' ) as $rolepers ):?>
					<td><?php
						if( !empty( $details[$rolepers] ) ) {
							$struct = Set::extract( "{$rolepers}.Structurereferente", $details );
							if( empty( $struct ) ) {
								echo 'Aucune structure référente active';
							}
							else {
								echo implode( ' ', array( Set::classicExtract( $struct, 'lib_struc' ) ) );
							}
						}
					?></td>
					<?php endforeach;?>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th><?php echo $departement == 93 ? 'Personne chargée du suivi' : 'Référent en cours';?></th>
					<?php foreach( array( 'DEM', 'CJT' ) as $rolepers ):?>
					<td><?php
						if( !empty( $details[$rolepers] ) ) {
							$referent = Set::extract( "{$rolepers}.Referent", $details );
							if( empty( $referent ) && isset( $details[$rolepers] ) ) {
								echo 'Aucun référent actif';
							}
							else {
								echo implode( ' ', array( Set::classicExtract( $referent, 'qual' ), Set::classicExtract( $referent, 'nom' ), Set::classicExtract( $referent, 'prenom' ) ) );
							}
						}
					?></td>
					<?php endforeach;?>
				</tr>
			</tbody>
		</table>

		<h2>Personnes</h2>
		<table>
			<?php echo thead( 10 );?>
			<tbody>
				<tr class="even">
					<th><?php echo __d( 'personne', 'Personne.qual' );?></th>
					<td><?php echo value( $qual,  Set::extract( 'DEM.Personne.qual', $details ) );?></td>
					<td><?php echo value( $qual,  Set::extract( 'CJT.Personne.qual', $details ) );?></td>
				</tr>
				<tr class="odd">
					<th><?php echo __d( 'personne', 'Personne.nom' );?></th>
					<td><?php echo Set::extract( 'DEM.Personne.nom', $details );?></td>
					<td><?php echo Set::extract( 'CJT.Personne.nom', $details );?></td>
				</tr>
				<tr class="even">
					<th><?php echo __d( 'personne', 'Personne.prenom' );?></th>
					<td><?php echo Set::extract( 'DEM.Personne.prenom', $details );?></td>
					<td><?php echo Set::extract( 'CJT.Personne.prenom', $details );?></td>
				</tr>
				<tr class="odd">
					<th>Départ du foyer </th>
					<td><?php echo h( date_short( Set::extract( 'DEM.Dossiercaf.dfratdos', $details ) ) );?></td>
					<td><?php echo h( date_short( Set::extract( 'CJT.Dossiercaf.dfratdos', $details ) ) );?></td>
				</tr>
				<tr class="odd">
					<th><?php echo __d( 'personne', 'Personne.dtnai' );?></th>
					<td><?php echo date_short( Set::extract( 'DEM.Personne.dtnai', $details ) ).' ('.age( ( Set::extract( 'DEM.Personne.dtnai', $details ) ) ).' ans)';?></td>
					<td>
						<?php if( Hash::check( $details, 'CJT.Personne.dtnai' ) ):?>
							<?php echo date_short( Set::extract( 'CJT.Personne.dtnai', $details ) ).' ('.age( ( Set::extract( 'CJT.Personne.dtnai', $details ) ) ).' ans)';?>
						<?php endif;?>
					</td>
				</tr>
				<tr class="even">
					<th><?php echo __d( 'foyer', 'Foyer.sitfam' );?></th>
					<td colspan="2"><?php echo  isset( $sitfam[$details['Foyer']['sitfam']] ) ?  $sitfam[$details['Foyer']['sitfam']] : null ;?></td>
				</tr>
				<tr class="odd">
					<th><?php echo __( 'adresse' );?></th>
					<td colspan="2">
						<?php echo $details['Adresse']['numvoie'].' '.$details['Adresse']['libtypevoie'].' '. $details['Adresse']['nomvoie'];?>
					</td>
				</tr>
				<tr class="even">
					<th><?php echo __d( 'adresse', 'Adresse.nomcom' );?></th>
					<td colspan="2"><?php echo  isset( $details['Adresse']['nomcom'] ) ? $details['Adresse']['nomcom'] : null ;?></td>
				</tr>
				<tr class="odd">
					<th>Soumis à droits et devoirs</th>
					<td><?php echo textToppersdrodevorsa( Set::extract( 'DEM.Calculdroitrsa', $details ) );?></td>
					<td><?php echo textToppersdrodevorsa( Set::extract( 'CJT.Calculdroitrsa', $details ) );?></td>
				</tr>
				<tr class="even">
					<th>DSP</th>
					<td><?php echo h( textPresenceDsp( @$details['DEM'] ) );?></td>
					<td><?php echo h( textPresenceDsp( @$details['CJT'] ) );?></td>
				</tr>
				<tr class="odd">
					<th>NIR</th>
					<td><?php echo h( Set::extract( 'DEM.Personne.nir', $details ) );?></td>
					<td><?php echo h( Set::extract( 'CJT.Personne.nir', $details ) );?></td>
				</tr>
				<tr class="even">
						<th colspan="3" class="center">Coordonnées</th>
				</tr>
				<tr class="odd">
						<th><?php echo __d( 'personne', 'Personne.numfixe' );?></th>
						<td><?php echo Set::extract( 'DEM.Personne.numfixe', $details );?></td>
						<td><?php echo Set::extract( 'CJT.Personne.numfixe', $details );?></td>
				</tr>
				<tr class="even">
						<th><?php echo __d( 'personne', 'Personne.numport' );?></th>
						<td><?php echo Set::extract( 'DEM.Personne.numport', $details );?></td>
						<td><?php echo Set::extract( 'CJT.Personne.numport', $details );?></td>
				</tr>
				<tr class="odd">
						<th><?php echo __d( 'personne', 'Personne.email' );?></th>
						<td><?php echo Set::extract( 'DEM.Personne.email', $details );?></td>
						<td><?php echo Set::extract( 'CJT.Personne.email', $details );?></td>
				</tr>
			</tbody>
		</table>

		<h2>Informations CAF / MSA</h2>
		<table>
			<tbody>
				<tr class="even">
					<th><?php echo __d( 'dossier', 'Dossier.matricule.large' ); ?></th>
					<td><?php echo Set::extract( 'Dossier.matricule', $details );;?></td>
				</tr>
				<tr class="odd">
					<th>Date d'obtention du numéro de RSA</th>
					<td><?php echo h( date_short( Set::extract( 'Dossier.dtdemrsa', $details ) ) );?></td>
				</tr>
				<tr class="even">
					<th>Date d'entrée au RSA/RMI/API</th>
					<td><?php echo h( date_short( Set::extract( 'Detaildroitrsa.dtoridemrsa', $details ) ) );?></td>
				</tr>
				<?php if (
					Set::extract( 'Situationdossierrsa.etatdosrsa', $details )  == 5
					|| Set::extract( 'Situationdossierrsa.etatdosrsa', $details )  == 6
				){?>
					<tr class="odd">
						<th>Date de fin de droits</th>
						<td><?php echo h( date_short( Set::extract( 'Situationdossierrsa.dtclorsa', $details ) ) );?></td>
					</tr>
					<tr class="even">
						<th>Motif de fin de droits</th>
						<td><?php echo h( value( $moticlorsa, Set::extract( 'Situationdossierrsa.moticlorsa', $details ) ) );?></td>
					</tr>
				<?php }?>
				<tr class="odd">
					<th>Numéro de demande RSA</th>
					<td><?php echo Set::extract( 'Dossier.numdemrsa', $details );?></td>
				</tr>
				<tr class="even">
					<th>Date dernier(s) montant(s)</th>
					<td><?php echo date_short( Set::extract( 'Detailcalculdroitrsa.0.dtderrsavers', $details ) );?></td>
				</tr>
				<?php if( isset( $details['Detailcalculdroitrsa'] ) ):?>
					<?php foreach( $details['Detailcalculdroitrsa'] as $detailcalculdroitrsa ):?>
					<tr class="odd">
						<th>Motif montant</th>
						<td><?php echo value( $natpf, Set::extract( 'natpf', $detailcalculdroitrsa ) );?></td>
					</tr>
					<tr class="even">
						<th>Montant RSA</th>
						<td><?php echo $this->Locale->money( Set::extract( 'mtrsavers', $detailcalculdroitrsa ) ); ?></td>
					</tr>
					<?php endforeach;?>
				<?php endif;?>
				<tr class="odd">
					<th>Montant INDUS</th>
					<td><?php echo $this->Locale->money( Set::extract( 'Infofinanciere.mtmoucompta', $details ) );?></td>
				</tr>
				<tr class="even">
					<th>Motif INDUS</th>
					<td><?php echo h( Set::extract( 'Creance.motiindu', $details ) );/*FIXME: traduction, manque dans Option*/?></td>
				</tr>
				<tr class="odd">
					<th>Début du traitement CAF / MSA</th>
					<td><?php echo $this->Locale->date( 'Date::short', Set::extract( 'DEM.Dossiercaf.ddratdos', $details ) );?></td>
				</tr>
				<tr class="even">
					<th>Fin du traitement CAF / MSA</th>
					<td><?php echo h(  date_short( Set::extract( 'DEM.Dossiercaf.dfratdos', $details ) ) );?></td>
				</tr>
			</tbody>
		</table>

		<h2>Dernière Information Pôle Emploi</h2>
		<table>
		<?php echo thead( 10 );?>
			<tbody>
				<tr class="even">
					<th>Identifiant pôle-emploi</th>
					<td><?php echo Set::extract( 'DEM.Informationpe.0.identifiantpe', $details);?></td>
					<td><?php echo Set::extract( 'CJT.Informationpe.0.identifiantpe', $details);?></td>
				</tr>
				<tr class="odd">
					<th>Etat actuel Pôle Emploi</th>
					<td><?php echo Set::enum( Set::extract( 'DEM.Informationpe.0.etat', $details ), $etatpe['etat'] );?></td>
					<td><?php echo Set::enum( Set::extract( 'CJT.Informationpe.0.etat', $details ), $etatpe['etat'] );?></td>
				</tr>
				<tr class="even">
					<th>Dernière date</th>
					<td><?php echo date_short( Set::extract( 'DEM.Informationpe.0.date', $details) );?></td>
					<td><?php echo date_short( Set::extract( 'CJT.Informationpe.0.date', $details) );?></td>
				</tr>
				<tr class="odd">
					<th>Code état</th>
					<td><?php echo Set::enum( Set::extract( 'DEM.Informationpe.0.code', $details), $categorie );?></td>
					<td><?php echo Set::enum( Set::extract( 'CJT.Informationpe.0.code', $details), $categorie );?></td>
				</tr>
				<tr class="even">
					<th>Motif</th>
					<td><?php echo Set::extract( 'DEM.Informationpe.0.motif', $details);?></td>
					<td><?php echo Set::extract( 'CJT.Informationpe.0.motif', $details);?></td>
				</tr>
			</tbody>
		</table>
	</div><div class="col-2 droite">
		<h2>Orientation</h2>
		<table>
		<?php
			$rowCnt = 0;
			echo thead( 10 );
		?>
			<tbody>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th>Orientation en cours...</th>
					<?php foreach( array( 'DEM', 'CJT' ) as $rolepers ):?>
					<td><?php
						$nonoriente66 = Set::extract( "{$rolepers}.Nonoriente66.derniere", $details );
						$orientation = Set::extract( "{$rolepers}.Orientstruct.derniere", $details );
						if( empty( $orientation ) && !empty( $nonoriente66 ) ) {
							echo '<p class="error">Orientation en cours: Traitement DPS</p>';
						}
					?></td>
					<?php endforeach;?>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th>Référent ayant réalisé l'orientation</th>
					<td><?php echo Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Referentorientant.nom_complet' );?></td>
					<td><?php echo Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Referentorientant.nom_complet' );?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th>Type d'orientation</th>
					<td><?php echo Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Typeorient.lib_type_orient' );?></td>
					<td><?php echo Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Typeorient.lib_type_orient' );?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th>Structure référente de l'orientation<!--Type de structure--></th>
					<td><?php echo Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Structurereferente.lib_struc' );?></td>
					<td><?php echo Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Structurereferente.lib_struc' );?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th>Date de l'orientation</th>
					<td><?php echo date_short( Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Orientstruct.date_valid' ) );?></td>
					<td><?php echo date_short( Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Orientstruct.date_valid' ) );?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th>Statut de l'orientation</th>
					<td><?php echo Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Orientstruct.statut_orient' );?></td>
					<td><?php echo Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Orientstruct.statut_orient' );?></td>
				</tr>
				<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
					<th>Rang de l'orientation</th>
					<td><?php echo Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Orientstruct.rgorient' );?></td>
					<td><?php echo Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Orientstruct.rgorient' );?></td>
				</tr>
			</tbody>
		</table>

		<h2>Contrat d'Engagement Réciproque</h2>
		<table>
		<?php echo thead( 10 );?>
			<tbody>
				<tr class="even">
					<th>Type de contrat</th>
					<td><?php echo Set::enum( Set::classicExtract( $details, 'DEM.Contratinsertion.num_contrat' ), $numcontrat['num_contrat'] );?></td>
					<td><?php echo Set::enum( Set::classicExtract( $details, 'CJT.Contratinsertion.num_contrat' ), $numcontrat['num_contrat'] );?></td>
				</tr>
				<tr class="odd">
					<th>Date de début</th>
					<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.dd_ci', $details) );?></td>
					<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.dd_ci', $details) );?></td>
				</tr>
				<tr class="even">
					<th>Date de fin</th>
					<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.df_ci', $details) );?></td>
					<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.df_ci', $details) );?></td>
				</tr>
				<tr class="odd">
					<th>Décision</th>
					<td>
						<?php if(  Set::extract( 'DEM.Contratinsertion', $details) != null ):?>
							<?php echo ( !empty( $deciD )  ) ? $decision_ci[$deciD] : $decision_ci[''] ;?>
						<?php endif;?>
					</td>
					<td>
						<?php if( Set::extract( 'CJT.Contratinsertion', $details) != null ):?>
							<?php echo ( !empty( $deciC )  ) ? $decision_ci[$deciC] : $decision_ci[''] ;?>
						<?php endif;?>
					</td>
				</tr>
				<tr class="odd">
					<th><?php echo __d('contratsinsertion', 'Contratinsertion.positioncer');?></th>
					<td>
						<?php echo Hash::get($details, 'DEM.Contratinsertion.positioncer') ? __d('contratinsertion', 'ENUM::POSITIONCER::'.Hash::get($details, 'DEM.Contratinsertion.positioncer')) : '';?>
					</td>
					<td>
						<?php echo Hash::get($details, 'CJT.Contratinsertion.positioncer') ? __d('contratinsertion', 'ENUM::POSITIONCER::'.Hash::get($details, 'CJT.Contratinsertion.positioncer')) : '';?>
					</td>
				</tr>
				<tr class="even">
					<th>Date de décision</th>
					<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.datevalidation_ci', $details) );?></td>
					<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.datevalidation_ci', $details) );?></td>
				</tr>
			</tbody>
		</table>
		<h2>Contrat Unique d'Insertion</h2>
		<table>
		<?php echo thead( 10 );?>
			<tbody>
				<tr class="odd">
					<th>Date de début</th>
					<td><?php echo date_short( Set::classicExtract( $details, 'DEM.Cui.effetpriseencharge' ) );?></td>
					<td><?php echo date_short( Set::classicExtract( $details, 'CJT.Cui.effetpriseencharge' ) );?></td>
				</tr>
				<tr class="even">
					<th>Date de fin</th>
					<td><?php echo date_short( Set::classicExtract( $details, 'DEM.Cui.finpriseencharge' ) );?></td>
					<td><?php echo date_short( Set::classicExtract( $details, 'CJT.Cui.finpriseencharge' ) );?></td>
				</tr>
				<tr class="odd">
					<th>Type de CUI</th>
					<td><?php echo Set::enum( Set::classicExtract( $details, 'DEM.Cui66.typecontrat' ), $enumcui['Typecontratcui66'] );?></td>
					<td><?php echo Set::enum( Set::classicExtract( $details, 'CJT.Cui66.typecontrat' ), $enumcui['Typecontratcui66'] );?></td>
				</tr>
				<tr class="even">
					<th>Décision</th>
					<td><?php echo Set::enum( Set::classicExtract( $details, 'DEM.Decisioncui66.decision' ), $enumcui['Decisioncui66']['decision'] );?></td>
					<td><?php echo Set::enum( Set::classicExtract( $details, 'CJT.Decisioncui66.decision' ), $enumcui['Decisioncui66']['decision'] );?></td>
				</tr>
				<tr class="odd">
					<th>Etat du dossier</th>
					<td><?php echo etatdossiercui66( (array)Hash::get( $details, 'DEM' ), $enumcui['Cui66']['etatdossiercui66'] );?></td>
					<td><?php echo etatdossiercui66( (array)Hash::get( $details, 'CJT' ), $enumcui['Cui66']['etatdossiercui66'] );?></td>
				</tr>
			</tbody>
		</table>

		<h2>Autres demandes RSA</h2>
		<table>
		<?php echo theadPastDossierDEM( 50, 8 );?>
			<tbody>
				<?php
					$nbdem = count( Set::extract( 'DEM.Dossiermultiple', $details ) );
					$colspan = "3";
					if( $nbdem == 0 ):
				?>
				<tr class="odd">
					<!-- Partie Demandeur-->
					<th>Autre N° de demande RSA</th>
					<td colspan= <?php echo $colspan;?>><?php
							echo 'Aucun dossier passé pour le demandeur';
						?>
					</td>
				</tr>
				<?php else:?>
				<?php for( $iteration = 0; $iteration <= $nbdem-1; $iteration++ ):?>
					<tr class="odd">
						<!-- Partie Demandeur-->
						<th>Autre N° de demande RSA</th>
						<td><?php
								echo Set::extract( 'DEM.Dossiermultiple.'.$iteration.'.Dossier.numdemrsa', $details ).' en date du '.date_short( Set::extract( 'DEM.Dossiermultiple.'.$iteration.'.Dossier.dtdemrsa', $details ) ).' avec un état à '.value( $etatdosrsa, Set::extract( 'DEM.Dossiermultiple.'.$iteration.'.Situationdossierrsa.etatdosrsa', $details ) );
							?>
						</td>
						<td><?php
								echo Set::extract( 'DEM.Dossiermultiple.'.$iteration.'.Foyer.nbdossierspcgs', $details );
							?>
						</td>
						<td><?php
								echo $this->Xhtml->viewLink(
									'Voir',
									array( 'controller' => 'dossiers', 'action' => 'view', Set::extract( 'DEM.Dossiermultiple.'.$iteration.'.Dossier.id', $details) )
								);
							?>
						</td>
					</tr>
					<?php endfor;?>
					<?php endif;?>
				</tbody>
			</table>
			<table>
			<?php echo theadPastDossierCJT( 50, 8 );?>
			<tbody>
				<?php
					$nbcjt = count( Set::extract( 'CJT.Dossiermultiple', $details ) );
					if( $nbcjt == 0 ):
				?>
				<tr class="odd">
					<!-- Partie Conjoint-->
					<th>Autre N° de demande RSA</th>
					<td colspan= <?php echo $colspan;?>><?php
							echo 'Aucun dossier passé pour le conjoint';
						?>
					</td>
				</tr>
				<?php else:?>
				<?php for( $iteration = 0; $iteration <= $nbcjt-1; $iteration++ ):?>
				<tr class="odd">
					<!-- Partie Conjoint-->
					<th>Autre N° de demande RSA</th>
					<td><?php
							echo Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Dossier.numdemrsa', $details ).' en date du '.date_short( Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Dossier.dtdemrsa', $details ) ).' avec un état à '.value( $etatdosrsa, Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Situationdossierrsa.etatdosrsa', $details ) );
						?>
					</td>
					<?php if( $departement == 66 ):?>
						<td><?php
								echo Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Foyer.nbdossierspcgs', $details );
							?>
						</td>
						<?php endif;?>
					<td><?php
							echo $this->Xhtml->viewLink(
								'Voir',
								array( 'controller' => 'dossiers', 'action' => 'view', Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Dossier.id', $details) )
							);
						?>
					</td>
				</tr>
				<?php endfor;?>
				<?php endif;?>
			</tbody>
		</table>

		<h2>Autres demandes RSA sans prestation</h2>
		<table>
		<?php echo theadPastDossierDEM( 50, 8 );?>
			<tbody>
				<?php
					$nbdem = count( Set::extract( 'DEM.AncienDossier', $details ) );
					$colspan = "3";
					if( $nbdem == 0 ):
				?>
				<tr class="odd">
					<!-- Partie Demandeur-->
					<th>Autre N° de demande RSA</th>
					<td colspan= <?php echo $colspan;?>><?php
							echo 'Aucun dossier passé pour le demandeur';
						?>
					</td>
				</tr>
				<?php else:?>
				<?php for( $iteration = 0; $iteration <= $nbdem-1; $iteration++ ):?>
					<tr class="odd">
						<!-- Partie Demandeur-->
						<th>Autre N° de demande RSA</th>
						<td><?php
								echo Set::extract( 'DEM.AncienDossier.'.$iteration.'.Dossier.numdemrsa', $details ).' en date du '.date_short( Set::extract( 'DEM.AncienDossier.'.$iteration.'.Dossier.dtdemrsa', $details ) ).' avec un état à '.value( $etatdosrsa, Set::extract( 'DEM.AncienDossier.'.$iteration.'.Situationdossierrsa.etatdosrsa', $details ) );
							?>
						</td>
						<td><?php
								echo Set::extract( 'DEM.AncienDossier.'.$iteration.'.Foyer.nbdossierspcgs', $details );
							?>
						</td>
						<td><?php
								echo $this->Xhtml->viewLink(
									'Voir',
									array( 'controller' => 'dossiers', 'action' => 'view', Set::extract( 'DEM.AncienDossier.'.$iteration.'.Dossier.id', $details) )
								);
							?>
						</td>
					</tr>
					<?php endfor;?>
					<?php endif;?>
				</tbody>
			</table>
			<table>
			<?php echo theadPastDossierCJT( 50, 8 );?>
			<tbody>
				<?php
					$nbcjt = count( Set::extract( 'CJT.AncienDossier', $details ) );
					if( $nbcjt == 0 ):
				?>
				<tr class="odd">
					<!-- Partie Conjoint-->
					<th>Autre N° de demande RSA</th>
					<td colspan= <?php echo $colspan;?>><?php
							echo 'Aucun dossier passé pour le conjoint';
						?>
					</td>
				</tr>
				<?php else:?>
				<?php for( $iteration = 0; $iteration <= $nbcjt-1; $iteration++ ):?>
				<tr class="odd">
					<!-- Partie Conjoint-->
					<th>Autre N° de demande RSA</th>
					<td><?php
							echo Set::extract( 'CJT.AncienDossier.'.$iteration.'.Dossier.numdemrsa', $details ).' en date du '.date_short( Set::extract( 'CJT.AncienDossier.'.$iteration.'.Dossier.dtdemrsa', $details ) ).' avec un état à '.value( $etatdosrsa, Set::extract( 'CJT.AncienDossier.'.$iteration.'.Situationdossierrsa.etatdosrsa', $details ) );
						?>
					</td>
						<td><?php
								echo Set::extract( 'CJT.AncienDossier.'.$iteration.'.Foyer.nbdossierspcgs', $details );
							?>
						</td>
					<td><?php
							echo $this->Xhtml->viewLink(
								'Voir',
								array( 'controller' => 'dossiers', 'action' => 'view', Set::extract( 'CJT.AncienDossier.'.$iteration.'.Dossier.id', $details) )
							);
						?>
					</td>
				</tr>
				<?php endfor;?>
				<?php endif;?>
			</tbody>
		</table>

		<h2>APRE/ADREs Accordées par années</h2>
		<table>
			<thead>
				<tr>
					<th></th>
					<th>Demandeur</th>
					<th>Conjoin</th>
				</tr>
			</thead>
		<?php
			$class = 'odd';
			foreach ($montantApres as $annee => $value) {
				// On s'assure de la présence du DEM et du CJT
				$value['DEM'] = isset($value['DEM']) ? $value['DEM'] : 0;
				$value['CJT'] = isset($value['CJT']) ? $value['CJT'] : 0;

				echo "<tr class=\"{$class}\"><th>{$annee}</th>";
				$class = $class === 'odd' ? 'even' : 'odd';
				foreach($value as $role => $montant) {
					echo "<td>{$montant}</td>";
				}
				echo '</tr>';
			}
		?>
		</table>
	</div>

	<h2>Dernier passage en EP</h2>
<?php
	$detailsEp = array();
	if( $displayingInfoEp ) {
		foreach( array( 'DEM', 'CJT' ) as $roleEp ) {
			if( isset( $details[$roleEp]['Dossierep']['derniere']['Dossierep'] ) ){
				$detailsEp[$roleEp]['dateEp'] = h( date_short( Set::extract( "{$roleEp}.Dossierep.derniere.Commissionep.dateseance", $details ) ) );
				$themeep = Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Dossierep.themeep" );
				$modeleDecision = 'Decision'.Inflector::singularize( $themeep );
				$detailsEp[$roleEp]['themeEp'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Dossierep.themeep" ), $dossierep['themeep'] );
				$detailsEp[$roleEp]['decisionEp'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.{$modeleDecision}.decision" ), $optionsep[$modeleDecision]['decision'] );
				$detailsEp[$roleEp]['etatDossierep'] = Set::enum( Set::classicExtract( $details, "{$roleEp}.Dossierep.derniere.Passagecommissionep.etatdossierep" ), $optionsep['Passagecommissionep']['etatdossierep'] );
			}
		}
	}
	?>
	<table style="width:100%;">
	<?php echo thead( 10 );?>
		<tbody>
			<tr class="even">
				<th>Date de la commission d'EP</th>
				<td><?php echo @$detailsEp['DEM']['dateEp'];?></td>
				<td><?php echo @$detailsEp['CJT']['dateEp'];?></td>
			</tr>
			<tr class="odd">
				<th>Motif de passage en EP</th>
				<td><?php echo @$detailsEp['DEM']['themeEp'];?></td>
				<td><?php echo @$detailsEp['CJT']['themeEp'];?></td>
			</tr>
			<tr class="even">
				<th>État dossier EP</th>
				<td><?php echo @$detailsEp['DEM']['etatDossierep'];?></td>
				<td><?php echo @$detailsEp['CJT']['etatDossierep'];?></td>
			</tr>
			<?php
				$avisDecisionEP = 'Avis';
			?>
			<tr class="odd">
				<th><?php echo $avisDecisionEP;?> de la commission d'EP</th>
				<td><?php echo @$detailsEp['DEM']['decisionEp'];?></td>
				<td><?php echo @$detailsEp['CJT']['decisionEp'];?></td>
			</tr>
			<tr class="even">
				<th>Décision CGA</th>
				<td colspan="2"><?php echo @$details['DEM']['Dossierpcg66']['dernier']['Decisiondossierpcg66'][0]['Decisionpdo']['libelle'];?></td>
				<!-- <td><?php /*echo @$details['CJT']['Dossierpcg66']['dernier']['Decisiondossierpcg66'][0]['Decisionpdo']['libelle']; */?></td> -->
			</tr>
		</tbody>
	</table>
</div>
