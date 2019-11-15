<?php
	$departement = Configure::read( 'Cg.departement' );

	function thead( $pct = 10 ) {
		return '
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th style="width: '.$pct.'%;">'.__d ('dossiers', 'Dossier.demandeur').'</th>
					<th style="width: '.$pct.'%;">'.__d ('dossiers', 'Dossier.conjoint').'</th>
				</tr>
			</thead>';
	}

	function theadPastDossierDEM( $pctValue = 10, $pctAction = 8 ) {
		return '
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th style="width: '.$pctValue.'%;">'.__d ('dossiers', 'Dossier.demandeur').'</th>
					<th style="width: '.$pctAction.'%;">'.__d ('dossiers', 'Dossier.action').'</th>
				</tr>
			</thead>';
	}

	function theadPastDossierCJT( $pctValue = 10, $pctAction = 8 ) {
		return '
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th style="width: '.$pctValue.'%;">'.__d ('dossiers', 'Dossier.demandeur').'</th>
					<th style="width: '.$pctAction.'%;">'.__d ('dossiers', 'Dossier.action').'</th>
				</tr>
			</thead>';
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
			return __d ('dossiers', 'Dossier.non.defini');
		}
		else if( $calculdroitrsa['toppersdrodevorsa'] == 1 ) {
			return __d ('dossiers', 'Dossier.oui');
		}
		else {
			return __d ('dossiers', 'Dossier.non');
		}
	}

	function textPresenceDsp( $allocataire ) {
		if( !isset( $allocataire['Personne']['id'] ) ) {
			return null;
		}

		if( isset( $allocataire['Dsp']['id'] ) ) {
			return __d ('dossiers', 'Dossier.oui');
		}
		else {
			return __d ('dossiers', 'Dossier.non');
		}
	}

	/////  Récupération données du Contratinsertion pour le DEM et le CJT
	$DT = Set::extract( 'DEM.Contratinsertion.num_contrat', $details);
	$CT = Set::extract( 'CJT.Contratinsertion.num_contrat', $details);

	$deciD = Set::extract( 'DEM.Contratinsertion.decision_ci', $details);
	$deciC = Set::extract( 'CJT.Contratinsertion.decision_ci', $details);

?>
<?php $this->pageTitle = 'Dossier RSA '.$details['Dossier']['numdemrsa'];?>

<div id="resumeDossier">

	<ul class="actionMenu">
		<li>
		<?php
			echo $this->Xhtml->printLinkJs(
				__d ('dossiers', 'Dossier.imprimer.ecran'),
				array( 'onclick' => 'printit(); return false;', 'class' => 'noprint' )
			);
		?>
		</li>
	</ul>

	<br>

	<table  id="ficheDossier">
		<tbody>
			<tr>
				<td>
					<h1><?php echo __d ('dossiers', 'Dossier.dossier.rsa') . h( $details['Dossier']['numdemrsa'] );?></h1>
					<table>
						<tbody>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.numdemrsa')); ?></th>
								<td><?php echo h( $details['Dossier']['numdemrsa'] );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.dtdemrsa')); ?></th>
								<td><?php echo h( date_short( $details['Dossier']['dtdemrsa'] ) );?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.etatdemrsa')); ?></th>
								<td><?php echo h( value( $etatdosrsa, Set::extract( 'Situationdossierrsa.etatdosrsa', $details ) ) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('serviceinstructeur', 'Serviceinstructeur.lib_service')); ?></th>
								<td><?php echo h( value( $typeserins, Set::extract( 'Suiviinstruction.typeserins', $details ) ) );?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.statudemrsa')); ?></th>
								<td><?php echo value( $statudemrsa, Set::extract( 'Dossier.statudemrsa', $details ) );?></td>
							</tr>
						</tbody>
					</table>

					<h2><?php echo (__d ('search_plugin', 'Search.Referentparcours')); ?></h2>

					<table>
					<?php
						$rowCnt = 0;
						echo thead( 10 );
					?>
						<tbody>
							<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
								<th><?php echo (__d ('search_plugin_93', 'Structurereferenteparcours.lib_struc')); ?></th>
							<?php foreach( array( 'DEM', 'CJT' ) as $rolepers ):?>
								<td>
								<?php
									if( !empty( $details[$rolepers] ) ) {
										$struct = Set::extract( "{$rolepers}.Structurereferente", $details );
										if( empty( $struct ) ) {
											echo (__d ('dossiers', 'Dossier.aucune.orientstruct.active'));
										}
										else {
											echo implode( ' ', array( Set::classicExtract( $struct, 'lib_struc' ) ) );
										}
									}
								?>
								</td>
							<?php endforeach;?>
							</tr>
							<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
								<th><?php echo (__d ('dossiers_93', 'Referentparcours.nom_complet')); ?></th>
							<?php foreach( array( 'DEM', 'CJT' ) as $rolepers ):?>
								<td>
								<?php
									if( !empty( $details[$rolepers] ) ) {
										$referent = Set::extract( "{$rolepers}.Referent", $details );
										if( empty( $referent ) && isset( $details[$rolepers] ) ) {
											echo (__d ('dossiers', 'Dossier.aucun.referent.active'));
										}
										else {
											echo implode( ' ', array( Set::classicExtract( $referent, 'qual' ), Set::classicExtract( $referent, 'nom' ), Set::classicExtract( $referent, 'prenom' ) ) );
										}
									}
								?>
								</td>
							<?php endforeach;?>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<h2><?php echo (__d ('dossiers', 'Dossier.orientation')); ?></h2>
					<table>
					<?php
						$rowCnt = 0;
						echo thead( 10 );
					?>
						<tbody>
							<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
								<th><?php echo __d( 'orientstruct', 'Orientstruct.origine' ) ?></th>
								<td><?php echo value( $options['Orientstruct']['origine'], Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Orientstruct.origine' ) );?></td>
								<td><?php echo value( $options['Orientstruct']['origine'], Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Orientstruct.origine' ) );?></td>
							</tr>
							<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
								<th><?php echo (__d ('orientstruct', 'Orientstruct.typeorient_id')); ?></th>
								<td><?php echo Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Typeorient.lib_type_orient' );?></td>
								<td><?php echo Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Typeorient.lib_type_orient' );?></td>
							</tr>
							<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
								<th><?php echo (__d ('dossiers', 'Dossier.orientstruct.orientation')); ?></th>
								<td><?php echo Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Structurereferente.lib_struc' );?></td>
								<td><?php echo Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Structurereferente.lib_struc' );?></td>
							</tr>
							<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
								<th><?php echo __d( 'orientstruct', 'Orientstruct.date_valid' ) ?></th>
								<td><?php echo date_short( Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Orientstruct.date_valid' ) );?></td>
								<td><?php echo date_short( Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Orientstruct.date_valid' ) );?></td>
							</tr>
							<tr class="<?php echo ( ( $rowCnt++ ) % 2 ? 'even' : 'odd' )?>">
								<th><?php echo __d( 'orientstruct', 'Orientstruct.rgorient' ) ?></th>
								<td><?php echo Set::classicExtract( $details, 'DEM.Orientstruct.derniere.Orientstruct.rgorient' );?></td>
								<td><?php echo Set::classicExtract( $details, 'CJT.Orientstruct.derniere.Orientstruct.rgorient' );?></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<h2><?php echo (__d ('dossiers', 'Personne.personnes')); ?></h2>
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
								<th><?php echo (__d ('dossiers', 'Personne.depart.foyer')); ?></th>
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
								<th><?php echo __d( 'personnes_referents', 'Calculdroitrsa.toppersdrodevorsa' );?></th>
								<td><?php echo textToppersdrodevorsa( Set::extract( 'DEM.Calculdroitrsa', $details ) );?></td>
								<td><?php echo textToppersdrodevorsa( Set::extract( 'CJT.Calculdroitrsa', $details ) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Personne.dsp')); ?></th>
								<td><?php echo h( textPresenceDsp( @$details['DEM'] ) );?></td>
								<td><?php echo h( textPresenceDsp( @$details['CJT'] ) );?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Personne.nir')); ?></th>
								<td><?php echo h( Set::extract( 'DEM.Personne.nir', $details ) );?></td>
								<td><?php echo h( Set::extract( 'CJT.Personne.nir', $details ) );?></td>
							</tr>
							<tr class="even">
								<th colspan="3" class="center"><?php echo (__d ('dossiers', 'Personne.coordonnees')); ?></th>
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
				</td>
				<td>
					<h2><?php echo (__d ('dossiers', 'Cer.cer')); ?></h2>
					<table>
					<?php echo thead( 10 );?>
						<tbody>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Cer.rang')); ?></th>
								<td><?php echo Set::classicExtract( $details, 'DEM.Contratinsertion.rg_ci' );?></td>
								<td><?php echo Set::classicExtract( $details, 'CJT.Contratinsertion.rg_ci' );?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Cer.date.debut')); ?></th>
								<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.dd_ci', $details) );?></td>
								<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.dd_ci', $details) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Cer.date.fin')); ?></th>
								<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.df_ci', $details) );?></td>
								<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.df_ci', $details) );?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Cer.decision')); ?></th>
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
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Cer.date.decision')); ?></th>
								<td><?php echo date_short( Set::extract( 'DEM.Contratinsertion.datevalidation_ci', $details) );?></td>
								<td><?php echo date_short( Set::extract( 'CJT.Contratinsertion.datevalidation_ci', $details) );?></td>
							</tr>
						</tbody>
					</table>

				</td>
			</tr>
			<tr>
				<td>
					<h2><?php echo (__d ('dossiers', 'Dossier.info.caf')); ?></h2>
					<table >
						<tbody>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.date.obtention')); ?></th>
								<td><?php echo h( date_short( Set::extract( 'Dossier.dtdemrsa', $details ) ) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.date.entree')); ?></th>
								<td><?php echo h( date_short( Set::extract( 'Detaildroitrsa.dtoridemrsa', $details ) ) );?></td>
							</tr>
							<?php if (
								Set::extract( 'Situationdossierrsa.etatdosrsa', $details )  == 5
								|| Set::extract( 'Situationdossierrsa.etatdosrsa', $details )  == 6
							){?>
								<tr class="odd">
									<th><?php echo (__d ('dossiers', 'Dossier.info.caf.date.fin')); ?></th>
									<td><?php echo h( date_short( Set::extract( 'Situationdossierrsa.dtclorsa', $details ) ) );?></td>
								</tr>
								<tr class="even">
									<th><?php echo (__d ('dossiers', 'Dossier.info.caf.motif.fin')); ?></th>
									<td><?php echo h( value( $moticlorsa, Set::extract( 'Situationdossierrsa.moticlorsa', $details ) ) );?></td>
								</tr>
							<?php }?>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.numdemrsa')); ?></th>
								<td><?php echo Set::extract( 'Dossier.numdemrsa', $details );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.dtdermontant')); ?></th>
								<td><?php echo date_short( Set::extract( 'Detailcalculdroitrsa.0.dtderrsavers', $details ) );?></td>
							</tr>
							<?php if( isset( $details['Detailcalculdroitrsa'] ) ):?>
								<?php foreach( $details['Detailcalculdroitrsa'] as $detailcalculdroitrsa ):?>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.motif.montant')); ?></th>
								<td><?php echo value( $natpf, Set::extract( 'natpf', $detailcalculdroitrsa ) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.montant.rsa')); ?></th>
								<td><?php echo $this->Locale->money( Set::extract( 'mtrsavers', $detailcalculdroitrsa ) ); ?></td>
							</tr>
								<?php endforeach;?>
							<?php endif;?>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.indu.montant')); ?></th>
								<td><?php echo $this->Locale->money( Set::extract( 'Infofinanciere.mtmoucompta', $details ) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.indu.motif')); ?></th>
								<td><?php echo h( Set::extract( 'Creance.motiindu', $details ) );/*FIXME: traduction, manque dans Option*/?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.debut.traitement')); ?></th>
								<td><?php echo $this->Locale->date( 'Date::short', Set::extract( 'DEM.Dossiercaf.ddratdos', $details ) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.info.caf.fin.traitement')); ?></th>
								<td><?php echo h(  date_short( Set::extract( 'DEM.Dossiercaf.dfratdos', $details ) ) );?></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<h2><?php echo (__d ('dossiers', 'Dossier.pe')); ?></h2>
					<table>
					<?php echo thead( 10 );?>
						<tbody>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.id')); ?></th>
								<td><?php echo Set::extract( 'DEM.Fluxpoleemploi.Historiqueetatpe.0.identifiantpe', $details);?></td>
								<td><?php echo Set::extract( 'CJT.Fluxpoleemploi.Historiqueetatpe.0.identifiantpe', $details);?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.etat')); ?></th>
								<td><?php echo Set::enum( Set::extract( 'DEM.Fluxpoleemploi.Historiqueetatpe.0.etat', $details ), $etatpe['etat'] );?></td>
								<td><?php echo Set::enum( Set::extract( 'CJT.Fluxpoleemploi.Historiqueetatpe.0.etat', $details ), $etatpe['etat'] );?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.date.derniere')); ?></th>
								<td><?php echo date_short( Set::extract( 'DEM.Fluxpoleemploi.Historiqueetatpe.0.date', $details) );?></td>
								<td><?php echo date_short( Set::extract( 'CJT.Fluxpoleemploi.Historiqueetatpe.0.date', $details) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.motif')); ?></th>
								<td>
									<?php
									if (in_array(Set::extract( 'DEM.Fluxpoleemploi.Historiqueetatpe.0.etat', $details), array ('cessation', 'radiation'))) {
										echo Set::extract( 'DEM.Fluxpoleemploi.Historiqueetatpe.0.motif', $details);
									}
									?>
								</td>
								<td>
									<?php
									if (in_array(Set::extract( 'CJT.Fluxpoleemploi.Historiqueetatpe.0.etat', $details), array ('cessation', 'radiation'))) {
										echo Set::extract( 'CJT.Fluxpoleemploi.Historiqueetatpe.0.motif', $details);
									}
									?>
								</td>
							</tr>

							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.dat.maj.flux')); ?></th>
								<td><?php echo h (date_short(Set::extract( 'DEM.Fluxpoleemploi.Historiqueetatpe.date_creation', $details)));?></td>
								<td><?php echo h (date_short(Set::extract( 'CJT.Fluxpoleemploi.Historiqueetatpe.date_creation', $details)));?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.agence')); ?></th>
								<td><?php echo Set::extract( 'DEM.Fluxpoleemploi.Historiqueetatpe.suivi_structure_principale_nom', $details);?></td>
								<td><?php echo Set::extract( 'CJT.Fluxpoleemploi.Historiqueetatpe.suivi_structure_principale_nom', $details);?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.modalite.acc')); ?></th>
								<td>
									<?php
										$accompagnement = Set::extract( 'DEM.Fluxpoleemploi.Informationpe.ppae_modalite_code', $details);
										if (!empty ($accompagnement)) {
											echo $modaliteaccompagnements[$accompagnement];
										}
									?>
								</td>
								<td>
									<?php
										$accompagnement = Set::extract( 'CJT.Fluxpoleemploi.Informationpe.ppae_modalite_code', $details);
										if (!empty ($accompagnement)) {
											echo $modaliteaccompagnements[$accompagnement];
										}
									?>
								</td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.date.debut.ide')); ?></th>
								<td><?php echo h (date_short(Set::extract( 'DEM.Fluxpoleemploi.Informationpe.inscription_date_debut_ide', $details)));?></td>
								<td><?php echo h (date_short(Set::extract( 'CJT.Fluxpoleemploi.Informationpe.inscription_date_debut_ide', $details)));?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.niveau.formation')); ?></th>
								<td><?php echo Set::extract( 'DEM.Fluxpoleemploi.Informationpe.formation_lib_niveau', $details );?></td>
								<td><?php echo Set::extract( 'CJT.Fluxpoleemploi.Informationpe.formation_lib_niveau', $details );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.date.signature.ppae')); ?></th>
								<td><?php echo date_short( Set::extract( 'DEM.Fluxpoleemploi.Informationpe.ppae_date_signature', $details) );?></td>
								<td><?php echo date_short( Set::extract( 'CJT.Fluxpoleemploi.Informationpe.ppae_date_signature', $details) );?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.pe.date.notification.ppae')); ?></th>
								<td><?php echo date_short( Set::extract( 'DEM.Fluxpoleemploi.Informationpe.ppae_date_notification', $details) );?></td>
								<td><?php echo date_short( Set::extract( 'CJT.Fluxpoleemploi.Informationpe.ppae_date_notification', $details) );?></td>
							</tr>

						</tbody>
					</table>
				</td>
				<td>

					<!-- Anciens dossiers dans lesquels la personne a toujours une prestation -->
					<h2><?php echo (__d ('dossiers', 'Dossier.autres')); ?></h2>

					<table>
					<?php echo theadPastDossierDEM( 50, 8 );?>
						<tbody>

							<?php
								$nbdem = count( Set::extract( 'DEM.Dossiermultiple', $details ) );
								$colspan = "2";
								if( $nbdem == 0 ):
							?>
							<tr class="odd">
								<!-- Partie Demandeur-->
								<th><?php echo (__d ('dossiers', 'Dossier.autres.numero')); ?></th>
								<td colspan= <?php echo $colspan;?>><?php
										echo (__d ('dossiers', 'Dossier.autres.aucun.dossier.demandeur'));
									?>
								</td>
							</tr>
							<?php else:?>
							<?php for( $iteration = 0; $iteration <= $nbdem-1; $iteration++ ):?>
								<tr class="odd">
									<!-- Partie Demandeur-->
									<th><?php echo (__d ('dossiers', 'Dossier.autres.numero')); ?></th>
									<td><?php
											echo Set::extract( 'DEM.Dossiermultiple.'.$iteration.'.Dossier.numdemrsa', $details ).' en date du '.date_short( Set::extract( 'DEM.Dossiermultiple.'.$iteration.'.Dossier.dtdemrsa', $details ) ).' avec un état à '.value( $etatdosrsa, Set::extract( 'DEM.Dossiermultiple.'.$iteration.'.Situationdossierrsa.etatdosrsa', $details ) );
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
								$nbcjt = count( (array)Set::extract( 'CJT.Dossiermultiple', $details ) );
								if( $nbcjt == 0 ):
							?>
							<tr class="odd">
								<!-- Partie Conjoint-->
								<th><?php echo (__d ('dossiers', 'Dossier.autres.numero')); ?></th>
								<td colspan= <?php echo $colspan;?>><?php
										echo (__d ('dossiers', 'Dossier.autres.aucun.dossier.conjoint'));
									?>
								</td>
							</tr>
							<?php else:?>
							<?php for( $iteration = 0; $iteration <= $nbcjt-1; $iteration++ ):?>
							<tr class="odd">
								<!-- Partie Conjoint-->
								<th><?php echo (__d ('dossiers', 'Dossier.autres.numero')); ?></th>
								<td><?php
										echo Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Dossier.numdemrsa', $details ).' en date du '.date_short( Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Dossier.dtdemrsa', $details ) ).' avec un état à '.value( $etatdosrsa, Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Situationdossierrsa.etatdosrsa', $details ) );
									?>
								</td>
								<td><?php
										echo $this->Xhtml->viewLink(
											__d ('dossiers', 'Dossier.autres.voir'),
											array( 'controller' => 'dossiers', 'action' => 'view', Set::extract( 'CJT.Dossiermultiple.'.$iteration.'.Dossier.id', $details) )
										);
									?>
								</td>
							</tr>
							<?php endfor;?>
							<?php endif;?>
						</tbody>
					</table>
					<?php if( Configure::read( 'AncienAllocataire.enabled' ) ): ?>

						<!-- Anciens dossiers dans lesquels la personne n'a plus de prestation -->
						<h2><?php echo (__d ('dossiers', 'Dossier.autres.sans.presta')); ?></h2>

						<table>
						<?php echo theadPastDossierDEM( 50, 8 );?>
							<tbody>
								<?php
									$nbdem = count( Set::extract( 'DEM.AncienDossier', $details ) );
									$colspan = "2";
									if( $nbdem == 0 ):
								?>
								<tr class="odd">
									<!-- Partie Demandeur-->
									<th><?php echo (__d ('dossiers', 'Dossier.autres.numero')); ?></th>
									<td colspan= <?php echo $colspan;?>><?php
											echo (__d ('dossiers', 'Dossier.autres.aucun.dossier.demandeur'));
										?>
									</td>
								</tr>
								<?php else:?>
								<?php for( $iteration = 0; $iteration <= $nbdem-1; $iteration++ ):?>
									<tr class="odd">
										<!-- Partie Demandeur-->
										<th><?php echo (__d ('dossiers', 'Dossier.autres.numero')); ?></th>
										<td><?php
												echo Set::extract( 'DEM.AncienDossier.'.$iteration.'.Dossier.numdemrsa', $details ).' en date du '.date_short( Set::extract( 'DEM.AncienDossier.'.$iteration.'.Dossier.dtdemrsa', $details ) ).' avec un état à '.value( $etatdosrsa, Set::extract( 'DEM.AncienDossier.'.$iteration.'.Situationdossierrsa.etatdosrsa', $details ) );
											?>
										</td>
										<td><?php
												echo $this->Xhtml->viewLink(
													__d ('dossiers', 'Dossier.autres.voir'),
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
									$nbcjt = count( (array)Set::extract( 'CJT.AncienDossier', $details ) );
									if( $nbcjt == 0 ):
								?>
								<tr class="odd">
									<!-- Partie Conjoint-->
									<th>Autre N° de demande RSA</th>
									<td colspan= <?php echo $colspan;?>><?php
											echo (__d ('dossiers', 'Dossier.autres.aucun.dossier.conjoint'));
										?>
									</td>
								</tr>
								<?php else:?>
								<?php for( $iteration = 0; $iteration <= $nbcjt-1; $iteration++ ):?>
								<tr class="odd">
									<!-- Partie Conjoint-->
									<th>Autre N° de demande RSA</th>
									<td>
										<?php
											echo Set::extract( 'CJT.AncienDossier.'.$iteration.'.Dossier.numdemrsa', $details ).' en date du '.date_short( Set::extract( 'CJT.AncienDossier.'.$iteration.'.Dossier.dtdemrsa', $details ) ).' avec un état à '.value( $etatdosrsa, Set::extract( 'CJT.AncienDossier.'.$iteration.'.Situationdossierrsa.etatdosrsa', $details ) );
										?>
									</td>
									<td>
										<?php
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
					<?php endif; ?>
				</td>
			</tr>
			<!-- Partie passage en EP-->

			<tr>
				<td>

					<h2><?php echo (__d ('dossiers', 'Dossier.relance')); ?></h2>
					<table >
					<?php echo thead( 10 );?>
						<tbody>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.relance.type')); ?></th>
								<td><?php echo Set::enum( Set::classicExtract( $details, 'DEM.Nonrespectsanctionep93.derniere.Nonrespectsanctionep93.origine' ), $relance['origine'] );?></td>
								<td><?php echo Set::enum( Set::classicExtract( $details, 'CJT.Nonrespectsanctionep93.derniere.Nonrespectsanctionep93.origine' ), $relance['origine'] );?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.relance.date')); ?></th>
								<td><?php echo h( date_short( Set::extract( 'DEM.Nonrespectsanctionep93.derniere.Relancenonrespectsanctionep93.daterelance', $details ) ) );?></td>
								<td><?php echo h( date_short( Set::extract( 'CJT.Nonrespectsanctionep93.derniere.Relancenonrespectsanctionep93.daterelance', $details ) ) );?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.relance.type')); ?></th>
								<td><?php
									$numrelance = Set::extract( 'DEM.Nonrespectsanctionep93.derniere.Relancenonrespectsanctionep93.numrelance', $details );
									if( !empty($numrelance) ){
										if( $numrelance == 1 ) {
											echo __d ('dossiers', 'Dossier.relance.premiere');
										}
										else {
											echo $numrelance.__d ('dossiers', 'Dossier.relance.enieme');
										}
									}
								?></td>
								<td><?php
									$numrelance = Set::extract( 'CJT.Nonrespectsanctionep93.derniere.Relancenonrespectsanctionep93.numrelance', $details );
									if( !empty($numrelance) ){
										if( $numrelance == 1 ) {
											echo __d ('dossiers', 'Dossier.relance.premiere');
										}
										else {
											echo $numrelance.__d ('dossiers', 'Dossier.relance.enieme');
										}
									}
								?></td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<h2><?php echo (__d ('dossiers', 'Dossier.derniere.ep')); ?></h2>
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
					<table>
					<?php echo thead( 10 );?>
						<tbody>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.derniere.ep.date')); ?></th>
								<td><?php echo @$detailsEp['DEM']['dateEp'];?></td>
								<td><?php echo @$detailsEp['CJT']['dateEp'];?></td>
							</tr>
							<tr class="odd">
								<th><?php echo (__d ('dossiers', 'Dossier.derniere.ep.motif')); ?></th>
								<td><?php echo @$detailsEp['DEM']['themeEp'];?></td>
								<td><?php echo @$detailsEp['CJT']['themeEp'];?></td>
							</tr>
							<tr class="even">
								<th><?php echo (__d ('dossiers', 'Dossier.derniere.ep.etat')); ?></th>
								<td><?php echo @$detailsEp['DEM']['etatDossierep'];?></td>
								<td><?php echo @$detailsEp['CJT']['etatDossierep'];?></td>
							</tr>
							<?php
								$avisDecisionEP = 'Décision';
							?>
							<tr class="odd">
								<th><?php echo $avisDecisionEP . ' ' . __d ('dossiers', 'Dossier.derniere.ep.avis'); ?></th>
								<td><?php echo @$detailsEp['DEM']['decisionEp'];?></td>
								<td><?php echo @$detailsEp['CJT']['decisionEp'];?></td>
							</tr>

						</tbody>
					</table>
				</td>
			</tr>

		</tbody>
	</table>
</div>