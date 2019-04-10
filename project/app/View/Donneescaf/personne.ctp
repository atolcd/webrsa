<?php
	$defaultParams = array('paginate' => false, 'options' => $options);
	$noData = $this->Xhtml->tag('p', 'Pas de données.', array('class' => 'notice'));

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	
	echo $this->element('ancien_dossier');

?>

<ul id="" class="ui-tabs-nav">
	<li class="tab">
<?php echo $this->Xhtml->link('FOYER', array('controller' => 'donneescaf', 'action' => 'foyer', $personnes[0]['Personne']['foyer_id']), array('class' => ''));?>
	</li>
<?php foreach ($personnes_list as $personne) {
	$label = '(&nbsp;'.Hash::get($personne, 'Prestation.rolepers').'&nbsp;) '.$personne['Personne']['nom_complet'];
	$class = Hash::get($personne, 'Personne.id') === (integer)$this->request->params['pass'][0] ? 'active' : '';
	echo '<li class="tab">'.$this->Xhtml->link(
		$label, array('controller' => 'donneescaf', 'action' => 'personne', $personne['Personne']['id']), array('class' => $class), false, false
	).'</li>';
} ?>
</ul>

<div id="tabbedWrapper" class="tabs">
	<div style="" class="tab">

<?php
	echo '<br/><br/><h2>Identification<div class="information" for="personne"></div></h2>';
	echo '<div class="info-data remarque" id="personne" style="display: none;">'
		."<p>Etat Civil de la personne</p>"
		.'</div>'
	;
	if (!empty($personnes[0]['Personne']['id'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Personne.qual',
				'Personne.nom',
				'Personne.nomnai',
				'Personne.prenom',
				'Personne.prenom2',
				'Personne.prenom3',
				'Personne.nomcomnai',
				'Personne.dtnai',
				'Personne.rgnai',
				'Personne.typedtnai',
				'Personne.nir',
				'Personne.topvaliec' => array('label' => 'Certification de l\'état civil de la personne'),
				'Personne.sexe',
			),
			array('domain' => 'personne') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Dossier CAF<div class="information" for="dossiercaf"></div></h2>';
	echo '<div class="info-data remarque" id="dossiercaf" style="display: none;">'
		."<p>Caractérise la personne traitée au regard du dossier allocataire de la Caf ou de la Msa</p>"
		.'</div>'
	;
	if (!empty($personnes[0]['Dossiercaf']['id'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Dossiercaf.toprespdos',
				'Dossiercaf.ddratdos',
				'Dossiercaf.dfratdos',
				'Dossiercaf.numdemrsaprece',
			),
			array('domain' => 'dossiercaf') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Prestation<div class="information" for="prestation"></div></h2>';
	echo '<div class="info-data remarque" id="prestation" style="display: none;">'
		."<p>Description du rôle de la personne dans la prestation décrite</p>"
		."<p>Restitution uniquement des situations correspondant aux prestations familiales de base et au RSA.</p>"
		.'</div>'
	;
	if (!empty($personnes[0]['Prestation']['id'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Prestation.natprest',
				'Prestation.rolepers',
				'Prestation.topchapers' => array('label' => 'A au moins une personne à charge'),
			),
			array('domain' => 'prestation') + $defaultParams
		);
	} else {
		echo $noData;
	}

	echo '<br/><br/><h2>Rattachements<div class="information" for="rattachement"></div></h2>';
	echo '<div class="info-data remarque" id="rattachement" style="display: none;">'
		."<p>Ce bloc n'est présent que pour les enfants et autres presonnes. Il permet d'identifier le lien de parenté de la personne traitée avec le demandeur et le conjoint si il y en a un.</p>"
		.'</div>'
	;
	if (!empty($rattachements)) {
		echo $this->Default3->index(
			$rattachements,
			array(
				'Rattachement.nomnai',
				'Rattachement.prenom',
				'Rattachement.dtnai',
				'Rattachement.nir',
				'Rattachement.typepar',
			),
			array('domain' => 'rattachement') + $defaultParams
		);
	} else {
		echo $noData;
	}

	echo '<br/><br/><h2>Identifiant Pole emploi<div class="information" for="idassedic"></div></h2>';
	echo '<div class="info-data remarque" id="idassedic" style="display: none;">'
		."<p>Ce bloc contient les références de la personne chez Pôle Emploi.</p>"
		.'</div>'
	;
	if (!empty($personnes[0]['Personne']['idassedic'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Personne.idassedic',
				'Personne.numagenpoleemploi',
				'Personne.dtinscpoleemploi',
			),
			array('domain' => 'personne') + $defaultParams
		);
	} else {
		echo $noData;
	}

	echo '<br/><br/><h2>Ressources Trimestrielles<div class="information" for="ressource"></div></h2>';
	echo '<div class="info-data remarque" id="ressource" style="display: none;">'
		."<p>Ressources de la personne traitée pour le trimestre de référence correspondant au mois traité</p>"
		."<p>Ce bloc n'est pas présent en cas d'absence de la déclaration trimestrielle des ressources Rsa.<br/>Pour les dossiers API/RMI basculés en RSA, le bloc \"ressources\" sera alimenté après réception de la première DTR.</p>"
		."<p></p>"
		.'</div>'
	;
	if (!empty($ressources)) {
		echo $this->Default3->index(
			$ressources,
			array(
				'Ressource.topressnul',
				'Ressource.ddress',
				'Ressource.dfress',
			),
			array('domain' => 'ressource') + $defaultParams
		);
	} else {
		echo $noData;
	}
	echo '<br/><br/><h2>Ressources Mensuelles<div class="information" for="ressourcemensuelle"></div></h2>';
	echo '<div class="info-data remarque" id="ressourcemensuelle" style="display: none;">'
		."<p>Ressources de la personne traitée pour le trimestre de référence correspondant au mois traité, ventilées par mois</p>"
		."<p>Ce bloc n'est présent que s'il y a eu des ressources déclarées pour le mois concernée.<br/>Il est absent lorsque TOPRESSNUL a la valeur \"1\".</p>"
		.'</div>'
	;
	if (!empty($ressourcesmensuelles)) {
		echo $this->Default3->index(
			$ressourcesmensuelles,
			array(
				'Ressourcemensuelle.id' => array('type' => 'text', 'class' => 'highlight'),
				'Ressourcemensuelle.moisress',
				'Ressourcemensuelle.nbheumentra',
			),
			array('domain' => 'ressourcemensuelle') + $defaultParams
		);
	} else {
		echo $noData;
	}
	echo '<br/><br/><h2>Détails sur les Ressources Mensuelles<div class="information" for="detailressourcemensuelle"></div></h2>';
	echo '<div class="info-data remarque" id="detailressourcemensuelle" style="display: none;">'
		."<p>Détail des ressources que la personne a déclarer dans la DTR RSA</p>"
		.'</div>'
	;
	if (!empty($detailsressourcesmensuelles)) {
		echo $this->Default3->index(
			$detailsressourcesmensuelles,
			array(
				'Detailressourcemensuelle.ressourcemensuelle_id' => array('type' => 'text', 'class' => 'highlight'),
				'Detailressourcemensuelle.natress',
				'Detailressourcemensuelle.mtnatressmen',
				'Detailressourcemensuelle.abaneu',
				'Detailressourcemensuelle.dfpercress',
				'Detailressourcemensuelle.topprevsubsress',
			),
			array('domain' => 'detailressourcemensuelle') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Montant Calcul Droit RSA<div class="information" for="calculdroitrsa"></div></h2>';
	echo '<div class="info-data remarque" id="calculdroitrsa" style="display: none;">'
		."<p>Eléments résultant du calcul du droit au RSA pour le mois de référence traité</p>"
		.'</div>'
	;
	if (!empty($personnes[0]['Calculdroitrsa']['id'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Calculdroitrsa.toppersdrodevorsa' => array('type' => 'boolean'),
				'Calculdroitrsa.toppersentdrodevorsa' => array('type' => 'boolean'),
				'Calculdroitrsa.mtpersressmenrsa',
				'Calculdroitrsa.mtpersabaneursa',
			),
			array('domain' => 'calculdroitrsa') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Activités<div class="information" for="activite"></div></h2>';
	echo '<div class="info-data remarque" id="activite" style="display: none;">'
		."<p>Decrit les activités de la personne traitée</p>"
		."<p>Il y a autant de blocs que d'activités sur la période correspondant au mois de réfence</p>"
		.'</div>'
	;
	if (!empty($activites)) {
		echo $this->Default3->index(
			$activites,
			array(
				'Activite.reg',
				'Activite.act',
				'Activite.paysact',
				'Activite.ddact',
				'Activite.dfact',
				'Activite.natcontrtra',
				'Activite.topcondadmeti',
				'Activite.hauremusmic',
			),
			array('domain' => 'activite') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Allocations soutien familial<div class="information" for="allocationsoutienfamilial"></div></h2>';
	echo '<div class="info-data remarque" id="allocationsoutienfamilial" style="display: none;">'
		."<p>Informations sur les pensions alimentaire</p>"
		."<p>Restitutiton de la situation vis à vis de l'Asf</p>"
		."<p>Restitution des ernières informations relatives à la demande d'Allocation de Soutien Familiale vis à vis du père et de la mère.</p>"
		."<p>Bloc non restitué pour les dossiers ayant une Asf expérimentale.</p>"
		.'</div>'
	;
	if (!empty($allocationssoutienfamilial)) {
		echo $this->Default3->index(
			$allocationssoutienfamilial,
			array(
				'Allocationsoutienfamilial.sitasf',
				'Allocationsoutienfamilial.parassoasf',
				'Allocationsoutienfamilial.ddasf',
				'Allocationsoutienfamilial.dfasf',
				'Allocationsoutienfamilial.topasf' => array('type' => 'boolean'),
				'Allocationsoutienfamilial.topdemasf' => array('type' => 'boolean'),
				'Allocationsoutienfamilial.topenfreconn' => array('type' => 'boolean'),
			),
			array('domain' => 'allocationsoutienfamilial') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Créances alimentaires<div class="information" for="creancealimentaire"></div></h2>';
	echo '<div class="info-data remarque" id="creancealimentaire" style="display: none;">'
		."<p>Restitution des informations liées à la créance alimentaire</p>"
		.'</div>'
	;
	if (!empty($creancesalimentaires)) {
		echo $this->Default3->index(
			$creancesalimentaires,
			array(
				'Creancealimentaire.etatcrealim',
				'Creancealimentaire.ddcrealim',
				'Creancealimentaire.dfcrealim',
				'Creancealimentaire.orioblalim',
				'Creancealimentaire.motidiscrealim',
				'Creancealimentaire.commcrealim',
				'Creancealimentaire.mtsancrealim',
				'Creancealimentaire.topdemdisproccrealim',
				'Creancealimentaire.engproccrealim',
				'Creancealimentaire.verspa',
				'Creancealimentaire.topjugpa',
			),
			array('domain' => 'creancealimentaire') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Grossesse<div class="information" for="grossesse"></div></h2>';
	echo '<div class="info-data remarque" id="grossesse" style="display: none;">'
		."<p>Situation de la personne enceinte</p>"
		."<p>Présent seulement si la personne à déclarée à une grossesse encours ou qui s'est achevée dans le mois de référence.</p>"
		.'</div>'
	;
	if (!empty($grossesses)) {
		echo $this->Default3->index(
			$grossesses,
			array(
				'Grossesse.ddgro',
				'Grossesse.dfgro',
				'Grossesse.dtdeclgro',
				'Grossesse.natfingro',
			),
			array('domain' => 'grossesse') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Informations agricoles<div class="information" for="infoagricole"></div></h2>';
	echo '<div class="info-data remarque" id="infoagricole" style="display: none;">'
		."<p>Ce bloc n'est présent que pour les exploitants agricole (alimenté par les Msa)</p>"
		.'</div>'
	;
	if (!empty($infosagricoles)) {
		echo $this->Default3->index(
			$infosagricoles,
			array(
				'Infoagricole.mtbenagri',
				'Infoagricole.regfisagri',
				'Infoagricole.dtbenagri',
				'Infoagricole.mtbenagria1',
				'Infoagricole.dtbenagria1',
				'Infoagricole.dtcloexecompta',
				'Infoagricole.topressevaagri' => array('type' => 'boolean'),
			),
			array('domain' => 'infoagricole') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Aides agricoles</h2>';
	if (!empty($aidesagricoles)) {
		echo $this->Default3->index(
			$aidesagricoles,
			array(
				'Aideagricole.annrefaideagri',
				'Aideagricole.libnataideagri',
				'Aideagricole.mtaideagri',
			),
			array('domain' => 'aideagricole') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	// Dégroupage de la table avispcgpersonnes
	$avispcgnonsal = $avispcgexcl = array();
	if (!empty($avispcgpersonnes)) {
		foreach ($avispcgpersonnes as $avis) {
			if ($avis['Avispcgpersonne']['excl']) {
				$avispcgexcl[] = $avis;
			} else {
				$avispcgnonsal[] = $avis;
			}
		}
	}
	
	echo '<br/><br/><h2>Avis PCG - Décisions CD<div class="information" for="avispcg"></div></h2>';
	echo '<div class="info-data remarque" id="avispcg" style="display: none;">'
		."<p>Regroupement des décisions du CD liées à la personne traitée</p>"
		."<p>Il s'agit de la restitution des informations présentes dans le modèle de gestion Caf/Msa. Les demandes d'avis sont isolées dans la suite du flux.</p>"
		.'</div>'
	;
	echo '<br/><br/><h3>Décisions CD - Avis sur condition travailleur non salarié<div class="information" for="avispcgpersonne"></div></h3>';
	echo '<div class="info-data remarque" id="avispcgpersonne" style="display: none;">'
		."<p>Avis sur condition travailleur non salarié</p>"
		."<p>Il s'agit de la restitution des informations présentes dans le modèle de gestion Caf/Msa. Les demandes d'avis sont isolées dans la suite du flux.</p>"
		."<p>Le bloc \"ConditionETI\" est présent si activité de la personne est non salariale (ETI,MAR,EXP,EXS ou GSA ) et si les  données D710M.CDCREVAL  et D710M.CDDSOURE sont valorisées dans la base Cristal.</p>"
		.'</div>'
	;
	if (!empty($avispcgnonsal)) {
		echo $this->Default3->index(
			$avispcgnonsal,
			array(
				'Avispcgpersonne.id' => array('type' => 'text', 'class' => 'highlight'),
				'Avispcgpersonne.avisevaressnonsal',
				'Avispcgpersonne.dtsouressnonsal',
				'Avispcgpersonne.dtevaressnonsal',
				'Avispcgpersonne.mtevalressnonsal',
			),
			array('domain' => 'avispcgpersonne') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>Décisions CD - Avis sur dérogation liée à la personne traitée<div class="information" for="derogation"></div></h3>';
	echo '<div class="info-data remarque" id="derogation" style="display: none;">'
		."<p>Avis sur dérogation liée à la personne traitée</p>"
		."<p>Ce bloc est présent en cas de dérogation. Une même personne peut faire l'objet de plusieurs dérogations</p>"
		.'</div>'
	;
	if (!empty($derogations)) {
		echo $this->Default3->index(
			$derogations,
			array(
				'Derogation.avispcgpersonne_id' => array('type' => 'text', 'class' => 'highlight'),
				'Derogation.typedero',
				'Derogation.avisdero',
				'Derogation.ddavisdero',
				'Derogation.dfavisdero',
			),
			array('domain' => 'derogation') + $defaultParams
		);
	} else {
		echo $noData;
	}

	echo '<br/><br/><h3>Décisions CD - Avis pour exclusion dans le calcul du droit RSA<div class="information" for="avispcgexcl"></div></h3>';
	echo '<div class="info-data remarque" id="avispcgexcl" style="display: none;">'
		."<p>Avis pour exclusion de la personne traitée dans le calcul du droit RSA</p>"
		."<p>Ce bloc est présent si la personne est exclu du calcul du montant de prestation Rsa</p>"
		.'</div>'
	;
	if (!empty($avispcgexcl)) {
		echo $this->Default3->index(
			$avispcgexcl,
			array(
				'Avispcgpersonne.id' => array('type' => 'text', 'class' => 'highlight'),
				'Avispcgpersonne.excl',
				'Avispcgpersonne.ddexcl',
				'Avispcgpersonne.dfexcl',
			),
			array('domain' => 'avispcgpersonne') + $defaultParams
		);
	} else {
		echo $noData;
	}

	echo '<br/><br/><h3>Décisions CD - Libéralité non déclarée<div class="information" for="liberalite"></div></h3>';
	echo '<div class="info-data remarque" id="liberalite" style="display: none;">'
		."<p>Présence de libéralité non déclarée par la personne traitée</p>"
		."<p>Ce bloc fait suite à un contrôle où il a été constaté des pensions (ou assimilées) allouées en espèces à cette personne alors qu'elles n'ont pas été déclarées. Cette information a fait l'ojet d'une demande de prise en compte auprès du CD pour savoir si le montant doit être intégré dans les ressources de la personne.</p>"
		.'</div>'
	;
	if (!empty($liberalites)) {
		echo $this->Default3->index(
			$liberalites,
			array(
				'Liberalite.avispcgpersonne_id' => array('type' => 'text', 'class' => 'highlight'),
				'Liberalite.mtlibernondecl',
				'Liberalite.dtabsdeclliber',
			),
			array('domain' => 'liberalite') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>Suivi appui orientation<div class="information" for="suiviappuiorientation"></div></h3>';
	echo '<div class="info-data remarque" id="suiviappuiorientation" style="display: none;">'
		.'<p>Présent seulement<br/>'
		. 'pour le demandeur s\'il y a au moins une occurrence avec '.__d('suiviinstruction', 'Suiviinstruction.suiirsa').' = '.__d('suiviinstruction', 'ENUM::SUIIRSA::13').'<br/>'
		. 'pour le conjoint s\'il y a au moins une occurrence avec '.__d('suiviinstruction', 'Suiviinstruction.suiirsa').' = '.__d('suiviinstruction', 'ENUM::SUIIRSA::14').'</p>'
		.'</div>'
	;
	if (!empty($suivisappuisorientation)) {
		echo $this->Default3->index(
			$suivisappuisorientation,
			array(
				'Suiviappuiorientation.sitperssocpro',
				'Suiviappuiorientation.dtenrsocpro',
				'Suiviappuiorientation.dtenrparco',
				'Suiviappuiorientation.dtenrorie',
				'Suiviappuiorientation.topoblsocpro' => array('type' => 'boolean'),
				'Suiviappuiorientation.topsouhsocpro' => array('type' => 'boolean'),
			),
			array('domain' => 'suiviappuiorientation') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>DSPs<div class="information" for="dsp"></div></h2>';
	echo '<div class="info-data remarque" id="dsp" style="display: none;">'
		.'<p>Présent seulement<br/>'
		. 'pour le demandeur s\'il y a au moins une occurrence avec '.__d('suiviinstruction', 'Suiviinstruction.suiirsa').' = '.__d('suiviinstruction', 'ENUM::SUIIRSA::13').'<br/>'
		. 'pour le conjoint s\'il y a au moins une occurrence avec '.__d('suiviinstruction', 'Suiviinstruction.suiirsa').' = '.__d('suiviinstruction', 'ENUM::SUIIRSA::14').'</p>'
		.'</div>'
	;
	echo '<br/><br/><h3>DSPs - Généralités</h3>';
	if (!empty($personnes[0]['Dsp']['have_generalite'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Dsp.sitpersdemrsa',
				'Dsp.topisogroouenf',
				'Dsp.topdrorsarmiant',
				'Dsp.drorsarmianta2',
				'Dsp.topcouvsoc',
			),
			array('domain' => 'dsp') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Commun situation sociale</h3>';
	if (!empty($personnes[0]['Dsp']['have_comsitsoc'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Dsp.accosocfam',
				'Dsp.libcooraccosocfam',
				'Dsp.accosocindi',
				'Dsp.libcooraccosocindi',
				'Dsp.soutdemarsoc',
			),
			array('domain' => 'dsp') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Detail Difficulté Situation Sociale</h3>';
	if (!empty($detailsdifsocs)) {
		echo $this->Default3->index(
			$detailsdifsocs,
			array(
				'Detaildifsoc.difsoc',
				'Detaildifsoc.libautrdifsoc',
			),
			array('domain' => 'detaildifsoc') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Detail Accompagnement Social Familial</h3>';
	if (!empty($detailsaccosocfams)) {
		echo $this->Default3->index(
			$detailsaccosocfams,
			array(
				'Detailaccosocfam.nataccosocfam',
				'Detailaccosocfam.libautraccosocfam',
			),
			array('domain' => 'detailaccosocfam') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Detail Accompagnement Social Individuel</h3>';
	if (!empty($detailsaccosocindis)) {
		echo $this->Default3->index(
			$detailsaccosocindis,
			array(
				'Detailaccosocindi.nataccosocindi',
				'Detailaccosocindi.libautraccosocindi',
			),
			array('domain' => 'detailaccosocindi') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Detail Difficulté Disponibilité</h3>';
	if (!empty($detailsdifdisps)) {
		echo $this->Default3->index(
			$detailsdifdisps,
			array(
				'Detaildifdisp.difdisp',
			),
			array('domain' => 'detaildifdisp') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Niveau Etude</h3>';
	if (!empty($personnes[0]['Dsp']['have_nivetu'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Dsp.nivetu',
				'Dsp.nivdipmaxobt',
				'Dsp.annobtnivdipmax',
				'Dsp.topqualipro',
				'Dsp.libautrqualipro',
				'Dsp.topcompeextrapro',
				'Dsp.libcompeextrapro',
			),
			array('domain' => 'dsp') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Disponibilité Emploi</h3>';
	if (Hash::get($personnes, '0.Dsp.topengdemarechemploi') !== null) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Dsp.topengdemarechemploi',
			),
			array('domain' => 'dsp') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Situation proféssionnelle</h3>';
	if (!empty($personnes[0]['Dsp']['have_nivetu'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Dsp.hispro',
				'Dsp.libderact',
				'Dsp.libsecactderact',
				'Dsp.cessderact',
				'Dsp.topdomideract',
				'Dsp.libactdomi',
				'Dsp.libsecactdomi',
				'Dsp.duractdomi',
				'Dsp.inscdememploi',
				'Dsp.topisogrorechemploi',
				'Dsp.accoemploi',
				'Dsp.libcooraccoemploi',
				'Dsp.topprojpro',
				'Dsp.libemploirech',
				'Dsp.libsecactrech',
				'Dsp.topcreareprientre',
				'Dsp.concoformqualiemploi',
			),
			array('domain' => 'dsp') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Commun mobilité</h3>';
	if (!empty($personnes[0]['Dsp']['have_nivetu'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Dsp.topmoyloco',
				'Dsp.toppermicondub',
				'Dsp.topautrpermicondu',
				'Dsp.libautrpermicondu',
			),
			array('domain' => 'dsp') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Detail mobilité</h3>';
	if (!empty($detailsnatmobs)) {
		echo $this->Default3->index(
			$detailsnatmobs,
			array(
				'Detailnatmob.natmob',
			),
			array('domain' => 'detailnatmob') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Commun difficulté logement</h3>';
	if (!empty($personnes[0]['Dsp']['natlog'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Dsp.natlog',
				'Dsp.demarlog',
			),
			array('domain' => 'dsp') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h3>DSPs - Commun difficulté logement</h3>';
	if (!empty($detailsdiflogs)) {
		echo $this->Default3->index(
			$detailsdiflogs,
			array(
				'Detaildiflog.diflog',
				'Detaildiflog.libautrdiflog',
			),
			array('domain' => 'detaildiflog') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Parcours<div class="information" for="parcours"></div></h2>';
	echo '<div class="info-data remarque" id="parcours" style="display: none;">'
		.'<p>Présent seulement<br/>'
		. 'pour le demandeur s\'il y a au moins une occurrence avec '.__d('suiviinstruction', 'Suiviinstruction.suiirsa').' = '.__d('suiviinstruction', 'ENUM::SUIIRSA::23').'<br/>'
		. 'pour le conjoint s\'il y a au moins une occurrence avec '.__d('suiviinstruction', 'Suiviinstruction.suiirsa').' = '.__d('suiviinstruction', 'ENUM::SUIIRSA::24').'</p>'
		.'</div>'
	;
	if (!empty($parcours[0]['Parcours']['have_parcours'])) {
		echo $this->Default3->index(
			$parcours,
			array(
				'Parcours.natparcocal',
				'Parcours.natparcomod',
				'Parcours.toprefuparco' => array('type' => 'boolean'),
				'Parcours.motimodparco',
			),
			array('domain' => 'parcours') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Organisme du Parcours<div class="information" for="orgparcours"></div></h2>';
	echo '<div class="info-data remarque" id="orgparcours" style="display: none;">'
		.'<p>Référence de l\'organisme décidant de l\'orientation</p>'
		.'</div>'
	;
	if (!empty($parcours[0]['Parcours']['raisocorgdeciorie'])) {
		echo $this->Default3->index(
			$parcours,
			array(
				'Parcours.raisocorgdeciorie',
				'Parcours.numvoie',
				'Parcours.complideadr',
				'Parcours.compladr',
				'Parcours.libtypevoie',
				'Parcours.nomvoie',
				'Parcours.lieudist',
				'Parcours.codepos',
				'Parcours.nomcom',
				'Parcours.numtelorgdeciorie',
				'Parcours.dtrvorgdeciorie',
				'Parcours.hrrvorgdeciorie',
				'Parcours.libadrrvorgdeciorie',
				'Parcours.numtelrvorgdeciorie',
			),
			array('domain' => 'parcours') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Organisme Référent Orientation<div class="information" for="orgorientation"></div></h2>';
	echo '<div class="info-data remarque" id="orgorientation" style="display: none;">'
		.'<p>Présent seulement<br/>'
		. 'pour le demandeur s\'il y a au moins une occurrence avec  '.__d('suiviinstruction', 'Suiviinstruction.suiirsa').' = '.__d('suiviinstruction', 'ENUM::SUIIRSA::33').'<br/>'
		. 'pour le conjoint s\'il y a au moins une occurrence avec  '.__d('suiviinstruction', 'Suiviinstruction.suiirsa').' = '.__d('suiviinstruction', 'ENUM::SUIIRSA::34').'</p>'
		.'<p>Référence de l\'organisme désigné comme référent orientation</p>'
		.'</div>'
	;
	if (!empty($orientations[0]['Orientation']['raisocorgorie'])) {
		echo $this->Default3->index(
			$orientations,
			array(
				'Orientation.raisocorgorie',
				'Orientation.numvoie',
				'Orientation.complideadr',
				'Orientation.compladr',
				'Orientation.libtypevoie',
				'Orientation.nomvoie',
				'Orientation.lieudist',
				'Orientation.codepos',
				'Orientation.nomcom',
				'Orientation.numtelorgorie',
				'Orientation.dtrvorgorie',
				'Orientation.hrrvorgorie',
				'Orientation.libadrrvorgorie',
				'Orientation.numtelrvorgorie',
			),
			array('domain' => 'orientation') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Titre de séjour</h2>';
	if (!empty($titressejour)) {
		echo $this->Default3->index(
			$titressejour,
			array(
				'Titresejour.dtentfra',
				'Titresejour.nattitsej',
				'Titresejour.menttitsej',
				'Titresejour.ddtitsej',
				'Titresejour.dftitsej',
				'Titresejour.numtitsej',
				'Titresejour.numduptitsej',
			),
			array('domain' => 'titresejour') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Nationalité</h2>';
	if (!empty($personnes[0]['Personne']['have_nati'])) {
		echo $this->Default3->index(
			$personnes,
			array(
				'Personne.nati',
				'Personne.dtnati',
				'Personne.pieecpres',
			),
			array('domain' => 'personne') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Informations ETI</h2>';
	if (!empty($informationseti)) {
		echo $this->Default3->index(
			$informationseti,
			array(
				'Informationeti.topcreaentre',
				'Informationeti.topaccre',
				'Informationeti.acteti',
				'Informationeti.topempl1ax',
				'Informationeti.topstag1ax',
				'Informationeti.topsansempl',
				'Informationeti.ddchiaffaeti',
				'Informationeti.dfchiaffaeti',
				'Informationeti.mtchiaffaeti',
				'Informationeti.regfiseti',
				'Informationeti.topbeneti',
				'Informationeti.regfisetia1',
				'Informationeti.mtbenetia1',
				'Informationeti.mtamoeti',
				'Informationeti.mtplusvalueti',
				'Informationeti.topevoreveti',
				'Informationeti.libevoreveti',
				'Informationeti.topressevaeti',
			),
			array('domain' => 'informationeti') + $defaultParams
		);
	} else {
		echo $noData;
	}
	
	echo '<br/><br/><h2>Condition Activité préalable<div class="information" for="conditionactiviteprealable"></div></h2>';
	echo '<div class="info-data remarque" id="conditionactiviteprealable" style="display: none;">'
		.'<p>Présent seulement pour le demandeur  répondant aux critères du rSa jeunes : 18-25 ans,  sans enfants à charges, sans grossesse connue. (y compris si le demandeur a déjà au préalable remplit cette condition lors d\'une précédente demande).</p>'
		.'</div>'
	;
	if (!empty($conditionsactivitesprealables)) {
		echo $this->Default3->index(
			$conditionsactivitesprealables,
			array(
				'Conditionactiviteprealable.ddcondactprea',
				'Conditionactiviteprealable.dfcondactprea',
				'Conditionactiviteprealable.topcondactprea' => array('type' => 'boolean'),
				'Conditionactiviteprealable.nbheuacttot',
			),
			array('domain' => 'conditionactiviteprealable') + $defaultParams
		);
	} else {
		echo $noData;
	}

	echo '<br/><br/><h2>Informations Importé FRSA</h2>';
	if (!empty($personneslangues)) {
		echo $this->Default3->index(
			$personneslangues,
			array(
				'Personnelangue.maternelles',
				'Personnelangue.francais_niveau',
				'Personnelangue.francais_niveau_validation',
				'Personnelangue.niveaux_professionnels'
			),
			array('domain' => 'personnelangue') + $defaultParams
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