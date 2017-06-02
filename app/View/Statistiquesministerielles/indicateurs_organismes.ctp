<?php require_once  dirname( __FILE__ ).DS.'search.ctp' ; ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>2 - Organismes de prise en charge des personnes dans le champ des Droits et Devoirs au 31 décembre de l'année, dont le référent unique a été désigné</h2>
	<?php
		$annee = Hash::get( $this->request->data, 'Search.annee' );
	?>
	<table>
		<caption>Organismes de prise en charge des personnes dans le champ des Droits et Devoirs au 31 décembre de l'année <?php echo $annee;?>, dont le référent unique a été désigné</caption>
		<tbody>
			<tr>
				<th><strong>Nombre de personnes dans le champ des Droits et Devoirs au 31 décembre (1)</strong></th>
				<td><strong><?php echo $this->Locale->number( Hash::get( $results, 'Indicateurorganisme.total' ) );?></strong></td>
			</tr>
			<tr>
				<th colspan="2"><strong>dont le référent unique appartient à</strong></th>
			</tr>
			<?php foreach( array_keys( $results['Indicateurorganisme'] ) as $indicateur ):?>
				<?php if( !in_array( $indicateur, array( 'total', 'attente_orient' ) ) ):?>
				<tr class="<?php echo Inflector::slug( mb_strtolower( $indicateur ) );?>">
					<th> - <?php echo h( $indicateur );?></th>
					<td><?php
						// INFO: le nom de l'indicateur pouvant contenir des points, on n'utilisera pas Hash::get()
						$value = isset( $results['Indicateurorganisme'][$indicateur] ) ? $results['Indicateurorganisme'][$indicateur] : null;
						if( is_null( $value ) ) {
							echo 'ND';
						}
						else {
							echo $this->Locale->number( $value );
						}
					?></td>
				</tr>
				<?php endif;?>
			<?php endforeach;?>
			<tr>
				<th><strong>Nombre de personnes dans le champ des Droits et Devoirs <u>orientées au 31 décembre dont le référent unique n'avait pas encore été désigné ou n'était pas connu au 31 décembre</u> (2) :</strong></th>
				<td><strong>0</strong></td>
			</tr>
			<tr>
				<th><strong>Nombre de personnes dans le champ des Droits et Devoirs <u>non-orientées au 31 décembre</u></strong></th>
				<td><strong><?php echo $this->Locale->number( Hash::get( $results, 'Indicateurorganisme.attente_orient' ) );?></strong></td>
			</tr>
		</tbody>
	</table>
	<p>(1) Les <strong>personnes</strong> sont définies comme les adultes du foyer, c'est-à-dire les allocataires et conjoints appartenant à un foyer ayant un droit
		ouvert au RSA. La définition des droits et devoirs à retenir est celle des organismes payeurs. Pour mémoire, selon la loi, une personne
		relève du périmètre des <strong>droits et devoirs</strong> (L262-28) lorsqu'elle appartient à un foyer ayant un droit <u>ouvert</u> au RSA socle et si elle est sans
		emploi ou a un revenu d'activité professionnelle inférieur à 500 euros par mois.</p>
	<p>(2) Le <strong>référent unique</strong> accompagne la personne dans son parcours d'insertion. Il est notamment chargé d'élaborer le Contrat
		d'Engagement Réciproque (ou le PPAE en cas d'orientation vers Pôle emploi) et de coordonner sa mise en œuvre.<br/>
		Si plusieurs organismes interviennent dans le parcours d'insertion, le référent unique est la personne chargée de contractualiser.<br/>
		Selon la loi, le référent unique suit une personne, et non un foyer.</p>
	<p>(3) Les personnes suivies par un organisme financé par le conseil départemental/territorial ont pour référent unique la personne chargée
		de contractualiser, indépendamment du financement.<br/>
		Pour la Métropole de Lyon, « service du département » est à comprendre comme « service de la métropole de Lyon ».<br/>
		L'ADI existe dans certains DOM uniquement.<br/>
		Pour les COM, « service du département » est à comprendre comme « service de la collectivité ».</p>
	<p>(4) Msa uniquement en France métropolitaine. Caisse de prévoyance sociale (CPS) à Saint-Pierre-et-Miquelon uniquement.<br/>
		Établissement des allocations familiales à Mayotte uniquement. Pas de CCAS, CIAS à Mayotte.</p>
<?php endif;?>