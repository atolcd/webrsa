<?php require_once( dirname( __FILE__ ).DS.'search.ctp' ); ?>
<?php if( !empty( $this->request->data ) ): ?>
	<h2>1 - Nombre et type de contrats RSA en cours de validité au 31 décembre de l'année</h2>
	<?php $annee = Hash::get( $this->request->data, 'Search.annee' ); ?>
	<table>
		<caption>Nombre et type de contrats RSA en cours de validité au 31 décembre de l'année <?php echo $annee;?></caption>
		<thead>
			<tr>
				<th></th>
				<th>Total</th>
				<th>dont signataire du contrat dans le champ des droits et devoirs au 31 décembre (1)</th>
				<th>dont signataire du contrat hors du champ des droits et devoirs au 31 décembre (1)</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( array( 'cer' ) as $categorie ):?>
			<tr>
				<th><strong><?php echo __d( 'statistiquesministerielles', "Indicateurcaracteristique.{$categorie}" );?></strong></th>
				<td class="number"><strong><?php
					$value = Hash::get( $results, "Indicateurcaracteristique.{$categorie}_total" );
					if( is_null( $value ) ) {
						echo 'ND';
					}
					else {
						echo $this->Locale->number( $value );
					}
				?></strong></td>
				<td class="number"><strong><?php
					$value = Hash::get( $results, "Indicateurcaracteristique.{$categorie}_droitsdevoirs" );
					if( is_null( $value ) ) {
						echo 'ND';
					}
					else {
						echo $this->Locale->number( $value );
					}
				?></strong></td>
				<td class="number"><strong><?php
					$value = Hash::get( $results, "Indicateurcaracteristique.{$categorie}_horsdroitsdevoirs" );
					if( is_null( $value ) ) {
						echo 'ND';
					}
					else {
						echo $this->Locale->number( $value );
					}
				?></strong></td>
			</tr>
			<?php endforeach;?>

			<?php foreach( array( 'ppae', 'cer_pro', 'cer_social_pro' ) as $categorie ):?>
			<tr>
				<th><?php echo __d( 'statistiquesministerielles', "Indicateurcaracteristique.{$categorie}" );?></th>
				<td class="number"><?php
					$value = Hash::get( $results, "Indicateurcaracteristique.{$categorie}_total" );
					if( is_null( $value ) ) {
						echo 'ND';
					}
					else {
						echo $this->Locale->number( $value );
					}
				?></td>
				<td class="number"><?php
					$value = Hash::get( $results, "Indicateurcaracteristique.{$categorie}_droitsdevoirs" );
					if( is_null( $value ) ) {
						echo 'ND';
					}
					else {
						echo $this->Locale->number( $value );
					}
				?></td>
				<td class="number"><?php
					$value = Hash::get( $results, "Indicateurcaracteristique.{$categorie}_horsdroitsdevoirs" );
					if( is_null( $value ) ) {
						echo 'ND';
					}
					else {
						echo $this->Locale->number( $value );
					}
				?></td>
			</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<br />
	<table>
		<caption>Nombre et type de contrats RSA en cours de validité au 31 décembre de l'année <?php echo $annee;?></caption>
		<thead>
			<tr>
				<th></th>
				<th>Total</th>
				<th>dont signataire du contrat dans le champ des droits et devoirs au 31 décembre (1)</th>
				<th>dont signataire du contrat hors du champ des droits et devoirs au 31 décembre (1)</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( array( 'cer_pro', 'cer_social_pro' ) as $categorie ):?>
				<tr>
					<th><strong><?php echo __d( 'statistiquesministerielles', "Indicateurcaracteristique.{$categorie}_rappel" );?></strong></th>
					<td><strong><?php echo $this->Locale->number( Hash::get( $results, "Indicateurcaracteristique.{$categorie}_total" ) );?></strong></td>
					<td><strong><?php echo $this->Locale->number( Hash::get( $results, "Indicateurcaracteristique.{$categorie}_droitsdevoirs" ) );?></strong></td>
					<td><strong><?php echo $this->Locale->number( Hash::get( $results, "Indicateurcaracteristique.{$categorie}_horsdroitsdevoirs" ) );?></strong></td>
				</tr>
				<?php foreach( $durees_cers as $duree_cer ):?>
				<tr>
					<th><?php echo __d( 'statistiquesministerielles', "Indicateurcaracteristique.{$categorie}_{$duree_cer}" );?></th>
					<td><?php echo $this->Locale->number( Hash::get( $results, "Indicateurcaracteristique.{$categorie}_{$duree_cer}_total" ) );?></strong></td>
					<td><?php echo $this->Locale->number( Hash::get( $results, "Indicateurcaracteristique.{$categorie}_{$duree_cer}_droitsdevoirs" ) );?></strong></td>
					<td><?php echo $this->Locale->number( Hash::get( $results, "Indicateurcaracteristique.{$categorie}_{$duree_cer}_horsdroitsdevoirs" ) );?></strong></td>
				</tr>
				<?php endforeach;?>
			<?php endforeach;?>
		</tbody>
	</table>

	<p>(1) Selon la loi, une personne relève du périmètre des <strong>droits et devoirs</strong> (L262-28) lorsqu'elle appartient à un foyer ayant un droit ouvert
		au RSA socle et si elle est sans emploi ou a un revenu d'activité professionnelle inférieur à 500 euros par mois. La définition des droits et
		devoirs à retenir est celle des organismes payeurs.</p>
	<p>(2) Selon la loi, le contrat concerne une personne, et non un foyer. Les <strong>personnes</strong> sont définies comme les adultes du foyer, c'est-à-dire
		les allocataires et conjoints appartenant à un foyer ayant un droit ouvert au RSA.<br/>
		<strong>Un contrat aidé ne vaut pas contrat RSA, même s'il est financé par le conseil général.</strong></p>
	<p>(3) La personne bénéficiaire du RSA orientée vers <strong>Pôle emploi</strong> signe un <strong>PPAE</strong> (L262-34). Les PPAE signés par une personne
		bénéficiaire du RSA mais dont le référent unique n'appartient pas à Pôle emploi ne sont pas à prendre en compte.<br/>
		<strong>Un contrat aidé ne vaut pas PPAE.</strong></p>
	<p>(4) Selon la loi, un <strong>Contrat d'Engagement Réciproque en matière d'insertion professionnelle</strong> (L262-35) est signé par la personne
		bénéficiaire du RSA orientée vers un <strong>organisme participant au service public de l'emploi (SPE) autre que Pôle emploi</strong> : autres
		organismes publics de placement professionnel (PLIE, AFPA, maison de l'emploi, mission locale, etc.), organismes d'appui à la création et
		au développement d'entreprise, entreprises de travail temporaire, agences privées de placement, insertion par l'activité économique (IAE),
		autres organismes publics ou privés de placement professionnel. Le <strong>SPE</strong> est compris au sens large.<br/>
		<strong>Un contrat aidé ne vaut pas CER (même si le référent unique appartient à l'IAE).</strong></p>
	<p>(5) Selon la loi, un <strong>Contrat d'Engagement Réciproque en matière d'insertion sociale ou professionnelle </strong>(L262-36) est signé par la
		personne bénéficiaire du RSA orientée vers un <strong>autre organisme</strong> : Conseil général, Caf, Msa, CCAS/CIAS, associations d'insertion, autres
		organismes d'insertion, Agence départementale d'insertion dans les DOM.<br/>
		<strong>Un contrat aidé ne vaut pas CER.</strong></p>
<?php endif; ?>