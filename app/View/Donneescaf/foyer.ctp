<?php
	$defaultParams = array('paginate' => false, 'options' => $options);
	$noData = $this->Xhtml->tag('p', 'Pas de données.', array('class' => 'notice'));

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	
	echo $this->element('ancien_dossier' );
	
	?>

<ul id="" class="ui-tabs-nav">
	<li class="tab">
<?php echo $this->Xhtml->link('FOYER', array('controller' => 'donneescaf', 'action' => 'foyer', $foyers[0]['Foyer']['id']), array('class' => 'active'));?>
	</li>
<?php foreach ($personnes_list as $personne) {
	$label = '(&nbsp;'.Hash::get($personne, 'Prestation.rolepers').'&nbsp;) '.$personne['Personne']['nom_complet'];
	echo '<li class="tab">'.$this->Xhtml->link(
		$label, array('controller' => 'donneescaf', 'action' => 'personne', $personne['Personne']['id']), array(), false, false
	).'</li>';
} ?>
</ul>

<div id="tabbedWrapper" class="tabs">
	<div style="" class="tab">

<?php

	echo '<br/><br/><h2>Organisme<div class="information" for="matricule"></div></h2>';
	echo '<div class="info-data remarque" id="matricule" style="display: none;">'
		."<p>Contient les informations d'une demande (dossier) de rSa.</p>"
		."<p>Identification de la demande (dossier) de Rsa</p>"
		.'</div>'
	;
	if (!empty($foyers[0]['Dossier']['matricule'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.fonorg',
				'Dossier.numorg',
				'Dossier.matricule',
			),
			array('domain' => 'dossier') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Partenaire<div class="information" for="ideparte"></div></h2>';
	echo '<div class="info-data remarque" id="ideparte" style="display: none;">'
		."<p>Identification de la collectivité territoriale récepteur ou émetteur du flux.</p>"
		."<p>Pour le flux rSa, il s'agit de l'identification du Conseil Général effectuant l'échange avec la Caf ou la Msa.</p>"
		.'</div>'
	;
	if (!empty($foyers[0]['Dossier']['ideparte'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.typeparte',
				'Dossier.ideparte',
			),
			array('domain' => 'dossier') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Demande RSA</h2>';
	if (!empty($foyers[0]['Dossier']['numdemrsa'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.dtdemrsa',
				'Dossier.numdemrsa',
			),
			array('domain' => 'dossier') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Demande RMI<div class="information" for="numdepinsrmi"></div></h2>';
	echo '<div class="info-data remarque" id="numdepinsrmi" style="display: none;">'
		."<p>Identification de la demande de Rmi</p>"
		."<p>Ce bloc ne sera présent que pour les demandes de RSA qui relevaient auparavant du RMI. Il contient les identifiants de la demande de Rmi précédent la bascule du Rmi vers le Rsa</p>"
		. "<p>En cas de mutation, ce bloc n'est plus alimenté.</p>"
		.'</div>'
	;
	if (!empty($foyers[0]['Dossier']['numdepinsrmi'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.dtdemrmi',
				'Dossier.numdepinsrmi',
				'Dossier.typeinsrmi',
				'Dossier.numcominsrmi',
				'Dossier.numagrinsrmi',
				'Dossier.numdosinsrmi' => array('type' => 'text'),
				'Dossier.numcli',
			),
			array('domain' => 'dossier') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Organisme cédent<div class="information" for="numorgcedmut"></div></h2>';
	echo '<div class="info-data remarque" id="numorgcedmut" style="display: none;">'
		."<p>Ce bloc est présent uniquement s'il s'agit d'un dossier allocataire en provenance d'une Caf ou d'une MSA (mutation entrante) dont la date début arrivée mutation est postérieure à la demande RSA et antérieure ou égale à la date de clôture de la demande ou à la date de radiation du dossier si celle-ci est antérieure à la date de clôture de la demande.<br/>"
		."Ce bloc est transmis dans les flux quotidiens et dans le flux mensuel du mois dans lequel la mutation entrante est effectuée.</p>"
		.'</div>'
	;
	if (!empty($foyers[0]['Dossier']['numorgcedmut'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.fonorgcedmut',
				'Dossier.numorgcedmut',
				'Dossier.matriculeorgcedmut',
				'Dossier.ddarrmut',
			),
			array('domain' => 'dossier') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Organisme prenant<div class="information" for="numorgprenmut"></div></h2>';
	echo '<div class="info-data remarque" id="numorgprenmut" style="display: none;">'
		."<p>Ce bloc est présent uniquement s'il s'agit d'un dossier allocataire qui est transmit à une autre CAF ou à une MSA (mutation sortante) dont la date de mutation est postérierure à la date de demande RSA et antérieure ou égale à la date de clôture de la demande RSA ou à la date de radiation du dossier si celle-ci est antérieure à la date de clôture de la demande.<br/>"
		."Ce bloc est transmis dans les flux quotidiens et dans le flux mensuel du mois dans lequel la mutation sortante est effectuée.</p>"
		.'</div>'
	;
	if (!empty($foyers[0]['Dossier']['numorgprenmut'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.fonorgprenmut',
				'Dossier.numorgprenmut',
				'Dossier.dddepamut',
			),
			array('domain' => 'dossier') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Situation famille</h2>';
	if (!empty($foyers[0]['Dossier']['numorgprenmut'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Foyer.sitfam',
				'Foyer.ddsitfam',
				'Foyer.regagrifam',
			),
			array('domain' => 'foyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Tronc commun Adresse<div class="information" for="adressefoyer"></div></h2>';
	echo '<div class="info-data remarque" id="adressefoyer" style="display: none;">'
		."<p>Information commune à une adresse française ou étrangère</p>"
		.'</div>'
	;
	if (!empty($adressesfoyers[0]['Adressefoyer']['id'])) {
		echo $this->Default3->index(
			$adressesfoyers,
			array(
				'Adressefoyer.id' => array('type' => 'text', 'class' => 'highlight'),
				'Adressefoyer.rgadr',
				'Adressefoyer.dtemm',
				'Adressefoyer.Adresse.pays' => array('label' => __d('adresse', 'Adresse.pays')),
				'Adressefoyer.typeadr',
				'Adressefoyer.etatadr',
			),
			array('domain' => 'adressefoyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Adresse détaillée France<div class="information" for="adresse"></div></h2>';
	echo '<div class="info-data remarque" id="adresse" style="display: none;">'
		."<p>Adresse détaillée française</p>"
		."<p>Ce bloc est présent si la balise <PAYS> = 'FRA' , absent sinon</p>"
		.'</div>'
	;
	if (!empty($adressesfoyers[0]['Adressefoyer']['Adresse']['codepos'])) {
		echo $this->Default3->index(
			$adressesfoyers,
			array(
				'Adressefoyer.id' => array('type' => 'text', 'class' => 'highlight'),
				'Adressefoyer.Adresse.complideadr' => array('label' => __d('adresse', 'Adresse.complideadr')),
				'Adressefoyer.Adresse.compladr' => array('label' => __d('adresse', 'Adresse.compladr')),
				'Adressefoyer.Adresse.numvoie' => array('label' => __d('adresse', 'Adresse.numvoie')),
				'Adressefoyer.Adresse.libtypevoie' => array('label' => __d('adresse', 'Adresse.libtypevoie')),
				'Adressefoyer.Adresse.nomvoie' => array('label' => __d('adresse', 'Adresse.nomvoie')),
				'Adressefoyer.Adresse.lieudist' => array('label' => __d('adresse', 'Adresse.lieudist')),
				'Adressefoyer.Adresse.numcom' => array('label' => __d('adresse', 'Adresse.numcom')),
				'Adressefoyer.Adresse.codepos' => array('label' => __d('adresse', 'Adresse.codepos')),
				'Adressefoyer.Adresse.nomcom' => array('label' => __d('adresse', 'Adresse.nomcom')),
			),
			array('domain' => 'adressefoyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Données complémentaire adresse<div class="information" for="adressecomp"></div></h2>';
	echo '<div class="info-data remarque" id="adressecomp" style="display: none;">'
		."<p>Données complémentaires à l'adresse</p>"
		."<p>Cet ensemble de données caractérise le logement ou l'adresse propre à un échange métier.</p>"
		.'</div>'
	;
	if (!empty($adressesfoyers[0]['Adressefoyer']['Adresse']['codepos'])) {
		echo $this->Default3->index(
			$adressesfoyers,
			array(
				'Adressefoyer.id' => array('type' => 'text', 'class' => 'highlight'),
				'Adressefoyer.Adresse.typeres' => array('label' => __d('adresse', 'Adresse.typeres')),
				'Adressefoyer.Adresse.topresetr' => array('label' => __d('adresse', 'Adresse.topresetr')),
			),
			array('domain' => 'adressefoyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Adresse hors France<div class="information" for="liblig2adr"></div></h2>';
	echo '<div class="info-data remarque" id="liblig2adr" style="display: none;">'
		."<p>Restitution des adresses hors de France</p>"
		."<p>Ce bloc est présent uniquement pour les adresses hors de France (<PAYS> = HOR), les lignes d'adresse de 2 à 6 sont libres, la ligne 7 contient le pays.</p>"
		.'</div>'
	;
	if (!empty($adressesfoyers[0]['Adressefoyer']['Adresse']['liblig2adr'])) {
		echo $this->Default3->index(
			$adressesfoyers,
			array(
				'Adressefoyer.id' => array('type' => 'text', 'class' => 'highlight'),
				'Adressefoyer.Adresse.liblig2adr' => array('label' => __d('adresse', 'Adresse.liblig2adr')),
				'Adressefoyer.Adresse.liblig3adr' => array('label' => __d('adresse', 'Adresse.liblig3adr')),
				'Adressefoyer.Adresse.liblig4adr' => array('label' => __d('adresse', 'Adresse.liblig4adr')),
				'Adressefoyer.Adresse.liblig5adr' => array('label' => __d('adresse', 'Adresse.liblig5adr')),
				'Adressefoyer.Adresse.liblig6adr' => array('label' => __d('adresse', 'Adresse.liblig6adr')),
				'Adressefoyer.Adresse.liblig7adr' => array('label' => __d('adresse', 'Adresse.liblig7adr')),
			),
			array('domain' => 'adressefoyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Etat dossier RSA</h2>';
	if (!empty($foyers[0]['Dossier']['Situationdossierrsa']['etatdosrsa'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.Situationdossierrsa.etatdosrsa' => array('label' => __d('situationdossierrsa', 'Situationdossierrsa.etatdosrsa')),
			),
			array('domain' => 'situationdossierrsa') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	if (!empty($foyers[0]['Dossier']['Situationdossierrsa']['dtrefursa'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.Situationdossierrsa.dtrefursa' => array('label' => __d('situationdossierrsa', 'Situationdossierrsa.dtrefursa')),
				'Dossier.Situationdossierrsa.motirefursa' => array('label' => __d('situationdossierrsa', 'Situationdossierrsa.motirefursa')),
			),
			array('domain' => 'situationdossierrsa') + $defaultParams
		);
	}
	
	if (!empty($suspensionsdroits[0]['Suspensiondroit']['ddsusdrorsa'])) {
		echo $this->Default3->index(
			$suspensionsdroits,
			array(
				'Suspensiondroit.motisusdrorsa',
				'Suspensiondroit.ddsusdrorsa',
				'Suspensiondroit.natgroupfsus',
			),
			array('domain' => 'suspensiondroit') + $defaultParams
		);
	}
	
	if (!empty($suspensionsversements[0]['Suspensionversement']['ddsusversrsa'])) {
		echo $this->Default3->index(
			$suspensionsversements,
			array(
				'Suspensionversement.motisusversrsa',
				'Suspensionversement.ddsusversrsa',
			),
			array('domain' => 'suspensionversement') + $defaultParams
		);
	}
	
	if (!empty($foyers[0]['Dossier']['Situationdossierrsa']['dtclorsa'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.Situationdossierrsa.moticlorsa' => array('label' => __d('situationdossierrsa', 'Situationdossierrsa.moticlorsa')),
				'Dossier.Situationdossierrsa.dtclorsa' => array('label' => __d('situationdossierrsa', 'Situationdossierrsa.dtclorsa')),
			),
			array('domain' => 'situationdossierrsa') + $defaultParams
		);
	}
	
	echo '<br/><br/><h2>Droits RSA</h2>';
	if (!empty($foyers[0]['Dossier']['Detaildroitrsa']['have_tronccommun'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.Detaildroitrsa.topsansdomfixe' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.topsansdomfixe')),
				'Dossier.Detaildroitrsa.nbenfautcha' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.nbenfautcha')),
				'Dossier.Detaildroitrsa.oridemrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.oridemrsa')),
				'Dossier.Detaildroitrsa.dtoridemrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.dtoridemrsa')),
				'Dossier.Detaildroitrsa.topfoydrodevorsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.topfoydrodevorsa')),
			),
			array('domain' => 'detaildroitrsa') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Montant calcul droit RSA<div class="information" for="detaildroitrsa"></div></h2>';
	echo '<div class="info-data remarque" id="detaildroitrsa" style="display: none;">'
		."<p>Bloc absent si aucun droit calculé. Si bloc présent correspond au dernier droit calculé</p>"
		.'</div>'
	;
	if (!empty($foyers[0]['Dossier']['Detaildroitrsa']['have_mntcalculdroit'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.Detaildroitrsa.ddelecal' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.ddelecal')),
				'Dossier.Detaildroitrsa.dfelecal' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.dfelecal')),
				'Dossier.Detaildroitrsa.mtrevminigararsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtrevminigararsa')),
				'Dossier.Detaildroitrsa.mtpentrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtpentrsa')),
				'Dossier.Detaildroitrsa.mtlocalrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtlocalrsa')),
				'Dossier.Detaildroitrsa.mtrevgararsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtrevgararsa')),
				'Dossier.Detaildroitrsa.mtpfrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtpfrsa')),
				'Dossier.Detaildroitrsa.mtalrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtalrsa')),
				'Dossier.Detaildroitrsa.mtressmenrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtressmenrsa')),
				'Dossier.Detaildroitrsa.mtsanoblalimrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtsanoblalimrsa')),
				'Dossier.Detaildroitrsa.mtredhosrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtredhosrsa')),
				'Dossier.Detaildroitrsa.mtredcgrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtredcgrsa')),
				'Dossier.Detaildroitrsa.mtcumintegrsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtcumintegrsa')),
				'Dossier.Detaildroitrsa.mtabaneursa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mtabaneursa')),
				'Dossier.Detaildroitrsa.mttotdrorsa' => array('label' => __d('detaildroitrsa', 'Detaildroitrsa.mttotdrorsa')),
			),
			array('domain' => 'detaildroitrsa') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Droits RSA<div class="information" for="detailcalculdroitrsa"></div></h2>';
	echo '<div class="info-data remarque" id="detailcalculdroitrsa" style="display: none;">'
		."<p>Détail du calcul du versement du rSa</p>"
		."<p>Restitution du détail par nature de prestation et sous nature prestation.<br/>"
		."Attention présence aussi des droits clos, qui  ne sont transmis qu'une seule fois.<br/>"
		."!B9440 Si le type de bascule CDCTYBAS = 'E', ce bloc est absent!<br/>"
		."!C6345 Exclusion des droits réels RSA socle à zéro non inférieur au seuil de verst!</p>"
		.'</div>'
	;
	if (!empty($detailscalculsdroitsrsa)) {
		echo $this->Default3->index(
			$detailscalculsdroitsrsa,
			array(
				'Detailcalculdroitrsa.natpf',
				'Detailcalculdroitrsa.sousnatpf',
				'Detailcalculdroitrsa.ddnatdro',
				'Detailcalculdroitrsa.dfnatdro',
				'Detailcalculdroitrsa.mtrsavers',
				'Detailcalculdroitrsa.dtderrsavers',
			),
			array('domain' => 'detailcalculdroitrsa') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Avis PCG Droit RSA<div class="information" for="avispcg"></div></h2>';
	echo '<div class="info-data remarque" id="avispcg" style="display: none;">'
		."<p>Regroupement des décisions du CD liées à au dossier RSA</p>"
		."<p>Il s'agit de la restitution des informations présentes dans le modèle de gestion Caf/Msa. Les demandes d'avis sont isolées dans la suite du flux.</p>"
		.'</div>'
	;
	echo '<br/><br/><h2>Avis PCG Droit RSA - Condition administrative<div class="information" for="condadmin"></div></h2>';
	echo '<div class="info-data remarque" id="condadmin" style="display: none;">'
		."<p>Présent en cas de conditions d'admission</p>"
		.'</div>'
	;
	if (!empty($condsadmins)) {
		echo $this->Default3->index(
			$condsadmins,
			array(
				'Condadmin.aviscondadmrsa',
				'Condadmin.moticondadmrsa',
				'Condadmin.comm1condadmrsa',
				'Condadmin.comm2condadmrsa',
				'Condadmin.dteffaviscondadmrsa',
			),
			array('domain' => 'condadmin') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Avis PCG Droit RSA - Réduction RSA<div class="information" for="reducrsa"></div></h2>';
	echo '<div class="info-data remarque" id="reducrsa" style="display: none;">'
		."<p>Pénalité sur l'ensemble du droit</p>"
		."<p>Présence d'une réduction demandée par le CD. Cette réduction s'applique après calcul du droit</p>"
		.'</div>'
	;
	if (!empty($reducsrsa)) {
		echo $this->Default3->index(
			$reducsrsa,
			array(
				'Reducrsa.mtredrsa',
				'Reducrsa.ddredrsa',
				'Reducrsa.dfredrsa',
			),
			array('domain' => 'reducrsa') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Avis PCG Droit RSA - Avis pour paiement du RSA à un tiers<div class="information" for="avispcgdroitrsa"></div></h2>';
	echo '<div class="info-data remarque" id="avispcgdroitrsa" style="display: none;">'
		."<p>Avis pour paiement du RSA à un tiers</p>"
		."<p>Présence du bloc seulement si il y a une demande de versement du RSA à un tiers autre que les tutelles</p>"
		.'</div>'
	;
	if (!empty($foyers[0]['Dossier']['Avispcgdroitrsa']['dtavisdestpairsa'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Dossier.Avispcgdroitrsa.avisdestpairsa' => array('label' => __d('avispcgdroitrsa', 'Avispcgdroitrsa.avisdestpairsa')),
				'Dossier.Avispcgdroitrsa.dtavisdestpairsa' => array('label' => __d('avispcgdroitrsa', 'Avispcgdroitrsa.dtavisdestpairsa')),
				'Dossier.Avispcgdroitrsa.nomtie' => array('label' => __d('avispcgdroitrsa', 'Avispcgdroitrsa.nomtie')),
				'Dossier.Avispcgdroitrsa.typeperstie' => array('label' => __d('avispcgdroitrsa', 'Avispcgdroitrsa.typeperstie')),
			),
			array('domain' => 'avispcgdroitrsa') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Evenements<div class="information" for="evenement"></div></h2>';
	echo '<div class="info-data remarque" id="evenement" style="display: none;">'
		."<p>Ce bloc est uniquement alimenté s'il s'agit d'un échange infra-mensuel.<br/>"
		."S'il existe au sein d'une même liquidation plusieurs fois le même FGE, ne le transmettre qu'une seule fois.<br/>"
		."Par contre, s'il existe plusieurs fois le même FGE avec des dates et heures</p>"
		.'</div>'
	;
	if (!empty($evenements)) {
		echo $this->Default3->index(
			$evenements,
			array(
				'Evenement.dtliq',
				'Evenement.heuliq',
				'Evenement.fg',
			),
			array('domain' => 'evenement') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Contrôles administratifs<div class="information" for="controleadministratif"></div></h2>';
	echo '<div class="info-data remarque" id="controleadministratif" style="display: none;">'
		."<p>Regroupement des contrôles clôturés sur le mois de référence pour le dossier RSA</p>"
		."<p>Ce bloc est uniquement alimenté s'il s'agit d'un échange mensuel.<br/>"
		."Le contrôle peut porté sur un dossier clos sur un mois antérieur</p>"
		.'</div>'
	;
	if (!empty($controlesadministratifs)) {
		echo $this->Default3->index(
			$controlesadministratifs,
			array(
				'Controleadministratif.dteffcibcontro',
				'Controleadministratif.cibcontro',
				'Controleadministratif.cibcontromsa',
				'Controleadministratif.dtdeteccontro',
				'Controleadministratif.dtclocontro',
				'Controleadministratif.libcibcontro',
				'Controleadministratif.famcibcontro',
				'Controleadministratif.natcibcontro',
				'Controleadministratif.commacontro',
				'Controleadministratif.typecontro',
				'Controleadministratif.typeimpaccontro',
				'Controleadministratif.mtindursacgcontro',
				'Controleadministratif.mtraprsacgcontro',
			),
			array('domain' => 'controleadministratif') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Créances<div class="information" for="creance"></div></h2>';
	echo '<div class="info-data remarque" id="creance" style="display: none;">'
		."<p>Liste des créances communiquées au Conseil général</p>"
		."<p>Description de la créance transférée</p>"
		."<p>Il y a un bloc par créance à transférer au CD. La créance est transferée immédiatement après sa constation par la chaine mensuel de gestion des créances ou manuellement par le service agence comptable</p>"
		.'</div>'
	;
	if (!empty($creances)) {
		echo $this->Default3->index(
			$creances,
			array(
				'Creance.dtimplcre',
				'Creance.natcre',
				'Creance.rgcre',
				'Creance.motiindu',
				'Creance.oriindu',
				'Creance.respindu',
				'Creance.ddregucre',
				'Creance.dfregucre',
				'Creance.dtdercredcretrans',
				'Creance.mtsolreelcretrans',
				'Creance.mtinicre',
			),
			array('domain' => 'creance') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	// Dégroupage des infos financières
	$types = array(
		'AutresAnnulations' => array(
			'title' => 'Autres Annulations',
			'info' => array(
				"Opération comptable d'annulation pour d'autres motifs que les annulations de faibles montants.",
				"Ce bloc vient augmenter le montant total de l'acompte demandé par la Caf ou la Msa au Conseil Général"
			)
		),
		'IndusConstates' => array(
			'title' => 'Indus Constates',
			'info' => array(
				'Opération comptable de constation d\'implantation d\'un indu au cours du moirs comptable de référence',
				'Ce bloc vient diminuer le montant total de l\'acompte demandé par la Caf ou la Msa au Conseil Général'
			)
		),
		'IndusTransferesCG' => array(
			'title' => 'Indus Transferes CD',
			'info' => array(
				'Opération comptable de transfert de la créance de la Caf au Conseil général suite au constat de non recouvrement par la caf de celle et à la cloture du droit au rSa',
				'Ce bloc vient augmenter le montant total de l\'acompte demandé par la Caf ou la Msa au Conseil Général.',
				'Le détail des créances transférées se trouve dans le flux mensuel bénéficiaires.'
			)
		),
		'RemisesIndus' => array(
			'title' => 'Remises Indus',
			'info' => array(
				'Opération comptable de remise d\'indu effectuée par la Caf ou par le Conseil général',
				'Ce bloc vient augmenter le montant total de l\'acompte demandé par la Caf ou la Msa au Conseil Général',
				'Les remises d\'indus peuvent être totale ou partielle'
			)
		),
		'AllocationsComptabilisees' => array(
			'title' => 'Allocations Comptabilisees',
			'info' => array(
				'Opération comptable de constation du montant du droit au rSa à comptabiliser pour ce dossier allocataire.',
				'Il s\'agit du montant de rSa soit au titre du paiement mensuel ou au titre d\'un rappel sur période antérieure.',
			)
		),
		'AnnulationsFaibleMontant' => array(
			'title' => 'Annulations Faible Montant',
			'info' => array(
				'Opération comptable d\'annulation des créances de faible montant',
				"Ce bloc vient augmenter le montant total de l'acompte demandé par la Caf ou la Msa au Conseil Général",
				"L'annulation pour faible montant intervient lorsque la somme des soldes d'indus Département et Etat à recouvrer pour le rSa est inférieure au seuil réglementaire (77 euros) ou fixé par le département.",
				'Lorsque le département fait une annulation de faible montant supérieur au seuil réglementaire, le montants des indus"Etat" annulés sont à la charge du département.'
			)
		),
	);
	foreach ($infosfinancieres as $info) {
		$type = Hash::get($info, 'Infofinanciere.type_allocation');
		$types[$type][] = $info;
	}
	
	foreach ($types as $name => $datas) {
		
		echo '<br/><br/><h3>Accompte RSA - '.$datas['title'].'<div class="information" for="'.$name.'"></div></h3>';
		echo '<div class="info-data remarque" id="'.$name.'" style="display: none;">'
			."<p>".implode('</p><p>', (array)Hash::get($datas, 'info'))."</p>"
			.'</div>'
		;
		unset($datas['title'], $datas['info']);
		
		if (!empty($datas)) {
			echo $this->Default3->index(
				$datas,
				array(
					'Infofinanciere.moismoucompta',
					'Infofinanciere.natpfcre',
					'Infofinanciere.rgcre',
					'Infofinanciere.numintmoucompta',
					'Infofinanciere.typeopecompta',
					'Infofinanciere.sensopecompta',
					'Infofinanciere.mtmoucompta',
					'Infofinanciere.ddregu',
					'Infofinanciere.dttraimoucompta',
					'Infofinanciere.heutraimoucompta',
				),
				array('domain' => 'infofinanciere') + $defaultParams
			);
		} else {
			echo $noData;
		}
	}
	
	
	echo '<br/><br/><h2>Suivi Instruction DSP<div class="information" for="suiviinstruction"></div></h2>';
	echo '<div class="info-data remarque" id="suiviinstruction" style="display: none;">'
		."<p>Permet de présicer les données recueillies et l'identification de l'instructeur ayant effectué le nouveau  recueil/parcours/orientation pour chaque étape du déroulement d'@RSA.</p>"
		."<p>Ce bloc retrace les différentes étapes de l'instruction effectuée avec @RSA. Il y a autant de bloc SuiviInstruction que d'étapes effectuées.</p>"
		. "<p>Exemple 1 : 6 occurrences pour la restitution des données socio-professionnelles du demandeur, des données socio-professionnelles du conjoint, des données du parcours du demandeur, des données du parcours du conjoint, des données d'orientation du demandeur, des données d'orientation du conjoint.<br/>"
		. "Exemple 2 : 3 occurrences pour la restitution des données socio-professionnelles du demandeur, des données du parcours du demandeur, des données d'orientation du demandeur.<br/>"
		. "Exemple 3 : 4 occurrences pour la restitution des données socio-professionnelles du demandeur, des données socio-professionnelles du conjoint, des données du parcours du demandeur, les données du parcours du conjoint.</p>"
		.'</div>'
	;
	if (!empty($suivisinstruction)) {
		echo $this->Default3->index(
			$suivisinstruction,
			array(
				'Suiviinstruction.suiirsa',
				'Suiviinstruction.nomins',
				'Suiviinstruction.prenomins',
				'Suiviinstruction.numdepins',
				'Suiviinstruction.typeserins',
				'Suiviinstruction.numcomins',
				'Suiviinstruction.numagrins',
			),
			array('domain' => 'suiviinstruction') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Election Domicile</h2>';
	if (!empty($foyers[0]['Foyer']['raisoctieelectdom'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Foyer.raisoctieelectdom',
			),
			array('domain' => 'foyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Modes Contacts</h2>';
	if (!empty($modescontact)) {
		echo $this->Default3->index(
			$modescontact,
			array(
				'Modecontact.numtel',
				'Modecontact.numposte',
				'Modecontact.nattel',
				'Modecontact.matetel',
				'Modecontact.autorutitel',
				'Modecontact.adrelec',
				'Modecontact.autorutiadrelec',
			),
			array('domain' => 'modecontact') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Logement</h2>';
	if (!empty($foyers[0]['Foyer']['typeocclog'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Foyer.typeocclog',
				'Foyer.mtvallocterr',
				'Foyer.mtvalloclog',
			),
			array('domain' => 'foyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Paiement - Destinataire</h2>';
	if (!empty($paiementsfoyers)) {
		echo $this->Default3->index(
			$paiementsfoyers,
			array(
				'Paiementfoyer.topverstie',
				'Paiementfoyer.modepai',
				'Paiementfoyer.topribconj',
			),
			array('domain' => 'paiementfoyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Paiement - RIB<div class="information" for="numcomptban"></div></h2>';
	echo '<div class="info-data remarque" id="numcomptban" style="display: none;">'
		."<p>Rib ou IBAN présenté au dépôt de la demande RSA</p>"
		.'</div>'
	;
	if (!empty($paiementsfoyers[0]['Paiementfoyer']['numcomptban'])) {
		echo $this->Default3->index(
			$paiementsfoyers,
			array(
				'Paiementfoyer.titurib',
				'Paiementfoyer.nomprenomtiturib',
				'Paiementfoyer.numdebiban',
				'Paiementfoyer.etaban',
				'Paiementfoyer.guiban',
				'Paiementfoyer.numcomptban',
				'Paiementfoyer.clerib',
				'Paiementfoyer.numfiniban',
				'Paiementfoyer.comban',
				'Paiementfoyer.bic',
			),
			array('domain' => 'paiementfoyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Fiche de liaison</h2>';
	if (!empty($foyers[0]['Foyer']['contefichliairsa'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Foyer.contefichliairsa',
			),
			array('domain' => 'foyer') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Prestation RSA</h2>';
	if (!empty($foyers[0]['Foyer']['contefichliairsa'])) {
		echo $this->Default3->index(
			$foyers,
			array(
				'Foyer.mtestrsa' => array('label' => __d('foyer', 'Foyer.mtestrsa')),
				'Detaildroitrsa.surfagridom',
				'Detaildroitrsa.ddsurfagridom',
				'Detaildroitrsa.nbtotaidefamsurfdom',
				'Detaildroitrsa.nbtotpersmajosurfdom',
			),
			array('domain' => 'detaildroitrsa') + $defaultParams
		);
	} else {
		echo $noData;
	}
?>
	</div>
</div>
<script>
	// Affichage des blocs d'informations
	$$('div.information').each(function(info){
		info.observe('click', function(event){
			$(event.target.getAttribute('for')).toggle();
		});
	});
	
	// Permet de faire le lien facilement au survol entre un foreign_key et l'id correspondant
	$$('table td.highlight').each(function(td){
		td.up('tr').observe('mouseover', function(event){
			var baseid = event.target.up('tr').select('td.highlight')[0].innerHTML,
				highlight = false,
				count = 0;
			
			$$('table td.highlight').each(function(td2){
				if (td2.up('tr').select('td.highlight')[0].innerHTML === baseid) {
					count++;
				}
			});
			if (count > 1) {
				$$('table td.highlight').each(function(td2){
					var tr = td2.up('tr');

					if (tr.select('td.highlight')[0].innerHTML === baseid) {
						tr.addClassName('highlighted');
					}
				});
			}
		});
		td.up('tr').observe('mouseout', function(event){
			var baseid = event.target.up('tr').select('td.highlight')[0].innerHTML;
			
			$$('table td.highlight').each(function(td2){
				var tr = td2.up('tr');
				
				if (tr.select('td.highlight')[0].innerHTML === baseid) {
					tr.removeClassName('highlighted');
				}
			});
		});
	});
</script>