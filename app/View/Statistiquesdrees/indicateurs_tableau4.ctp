<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>Tableau 4 - Actions inscrites dans les CER en cours de validité au 31/12 de l'année des personnes soumises aux droits et devoirs et orientées à cette même date vers un organisme autre que Pôle emploi</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
		$indicateurs = array( 'age', 'sexe', 'sitfam', 'anciennete', 'nivetu' );
	?>

	<table class="first">
		<caption>Personnes soumises aux droits et devoirs et orientées vers un organisme autre que Pôle emploi au 31/12 de l'année ayant un CER  en cours de validité à cette même date contenant (4) (6) (15) (16)…</caption>
		<thead>
			<tr class="main">
				<th></th>
				<th>… au moins une action visant à trouver des activités, stages ou formations destinés à acquérir des compétences professionnelles</th>
				<th>… au moins une action visant à s'inscrire dans un parcours de recherche d'emploi</th>
				<th>… au moins une action visant à s'inscrire dans une mesure d'insertion par l'activité économique (IAE)</th>
				<th>… au moins une action aidant à la réalisation d’un projet de création, de reprise ou de poursuite d’une activité non salariée</th>
				<th>… au moins une action visant à trouver un emploi aidé</th>
				<th>… au moins une action visant à trouver un emploi non aidé</th>
				<th>… au moins une action visant à faciliter le lien social (développement de l'autonomie sociale, activités collectives,…)</th>
				<th>… au moins une action visant la mobilité (permis de conduire, acquisition / location de véhicule, frais de transport…)</th>
				<th>… au moins une action visant l'accès à un logement, au relogement ou à l'amélioration de l'habitat</th>
				<th>… au moins une action visant l'accès aux soins</th>
				<th>… au moins une action visant l'autonomie financière (constitution d'un dossier de surendettement,...)</th>
				<th>… au moins une action visant la famille et la parentalité (soutien familial, garde d'enfant, …)</th>
				<th>… au moins une action visant la lutte contre l'illettrisme ou l'acquisition des savoirs de base</th>
				<th>… au moins une action visant l'accès aux droits ou l'aide dans les démarches administratives</th>
				<th>… au moins une action non classée dans les items précédents</th>
			</tr>
		</thead>
	<?php foreach( $indicateurs as $index => $indicateur ):?>
	<?php
		$name = "{$indicateur}";

		$acquerir_competences_pro = (array)Hash::get( $results, "{$name}.acquerir_competences_pro" );
		$parcours_recherche_emploi = (array)Hash::get( $results, "{$name}.parcours_recherche_emploi" );
		$iae = (array)Hash::get( $results, "{$name}.iae" );
		$activite_non_salariale = (array)Hash::get( $results, "{$name}.activite_non_salariale" ); 
		$emploi_aide = (array)Hash::get( $results, "{$name}.emploi_aide" );
		$emploi_non_aide = (array)Hash::get( $results, "{$name}.emploi_non_aide" );
		$lien_social = (array)Hash::get( $results, "{$name}.lien_social" );
		$mobilite = (array)Hash::get( $results, "{$name}.mobilite" );
		$acces_logement = (array)Hash::get( $results, "{$name}.acces_logement" );
		$acces_soins = (array)Hash::get( $results, "{$name}.acces_soins" );
		$autonomie_financiere = (array)Hash::get( $results, "{$name}.autonomie_financiere" );
		$famille_parentalite = (array)Hash::get( $results, "{$name}.famille_parentalite" );
		$illettrisme = (array)Hash::get( $results, "{$name}.illettrisme" );
		$demarches_administratives = (array)Hash::get( $results, "{$name}.demarches_administratives" ); 
		$autres = (array)Hash::get( $results, "{$name}.autres" );
?>
		<tbody>
			<tr class="total">
				<th colspan="16"><?php echo __d( 'statistiquesdrees', $name );?></th>
			</tr>
			<?php foreach( $tranches[$indicateur] as $tranche ):?>
			<tr>
				<th><?php echo __d( 'statistiquesdrees',  $tranche );?></th>
				<td class="number"><?php echo  isset( $acquerir_competences_pro[$tranche] ) ? $this->Locale->number( $acquerir_competences_pro[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $parcours_recherche_emploi[$tranche] ) ? $this->Locale->number( $parcours_recherche_emploi[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $iae[$tranche] ) ? $this->Locale->number( $iae[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $activite_non_salariale[$tranche] ) ? $this->Locale->number( $activite_non_salariale[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $emploi_aide[$tranche] ) ? $this->Locale->number( $emploi_aide[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $emploi_non_aide[$tranche] ) ? $this->Locale->number( $emploi_non_aide[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $lien_social[$tranche] ) ? $this->Locale->number( $lien_social[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $mobilite[$tranche] ) ? $this->Locale->number( $mobilite[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $acces_logement[$tranche] ) ? $this->Locale->number( $acces_logement[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $acces_soins[$tranche] ) ? $this->Locale->number( $acces_soins[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $autonomie_financiere[$tranche] ) ? $this->Locale->number( $autonomie_financiere[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $famille_parentalite[$tranche] ) ? $this->Locale->number( $famille_parentalite[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $illettrisme[$tranche] ) ? $this->Locale->number( $illettrisme[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $demarches_administratives[$tranche] ) ? $this->Locale->number( $demarches_administratives[$tranche] ) : 0 ;?></td>
				<td class="number"><?php echo  isset( $autres[$tranche] ) ? $this->Locale->number( $autres[$tranche] ) : 0 ;?></td>
			</tr>
			<?php endforeach;?>

			<tr>
				<th class="total">Effectif total</th>
				<td class="total"><?php echo $this->Locale->number( array_sum( $acquerir_competences_pro ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $parcours_recherche_emploi ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $iae ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $activite_non_salariale ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $emploi_aide ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $emploi_non_aide ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $lien_social ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $mobilite ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $acces_logement ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $acces_soins ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $autonomie_financiere ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $famille_parentalite ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $illettrisme ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $demarches_administratives ) );?></td>
				<td class="total"><?php echo $this->Locale->number( array_sum( $autres ) );?></td>
			</tr>
		</tbody>

	<?php endforeach;?>
	</table>

	<ul class="actionMenu">
	<li><?php
		echo $this->Xhtml->exportLink(
			'Télécharger le tableau',
			array( 'action' => 'exportcsv_tableau4', 'visualisation' ) + Hash::flatten( $this->request->data, '__' ),
			true
		);
	?></li>
	</ul>

	<?php
		include_once  dirname( __FILE__ ).DS.'precision.ctp' ;
	?>
<?php endif; ?>