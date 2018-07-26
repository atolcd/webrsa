<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta name="googlebot" content="noindex, nofollow" />
	<title>Didacticiel WebRSA</title>
	<link rel="stylesheet" type="text/css" href="/didac/layout.css" />
	<link href="favicon.png" type="image/png" rel="icon" />
	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
	</head>
	<body>
	<div id="container">
		<div id="header"></div>
		<a name="haut"></a>
		<div><h1>Guide utilisateur</h1><span style="margin-left:450px;font-size:12pt;">Version 3.1.6&nbsp;</span><a href="http://www.seine-saint-denis.fr/" target="_blank"><img style="position:absolute;top:109px;right:10px;"src="/didac/images/logo_cg.jpg" width="178px" height="50px" alt="logo cg93"/></a></div>
		<hr/>
<div id="menu">
		<ul>
		<li><a title="Page d'accueil" href="accueil">Accueil</a></li>
		<li><a title="Introduction à WebRSA" href="intro">Introduction</a></li>
		<li><a title="La provenances des données et des flux" href="donnees">Origine des données</a></li>
		<li><a title="Se connecter à WebRSA" href="connect">Se connecter</a></li>
		<li><a title="Vos habilitations" href="profil">Les profils d'habilitations</a></li>
		</ul>
	<ul><!--ici le sous menu géré avec CSS-->
	
		<li><a title="Naviguer dans le dossier d'un bénéficiaire" href="dossier">Le dossier allocataire&nbsp;+</a>
		<ul><li id="activ"><a title="Rechercher un bénéficiaire du RSA" href="cprechalloc">Cas pratique &nbsp;: Rechercher un dossier</a></li></ul>
		</li>
	</ul>
	<ul>	
		<li  id="activation"><a title="Affecter un référent à un allocataire" href="affectref">Affecter un référent</a>
		<!-- <ul><li id="activ"><a title="Remplacer un référent par un autre" href="cpaffectref">Cas pratique &nbsp;: Remplacer en masse un référent</a></li></ul> -->
		</li>
	</ul>
		<ul>
		<li><a href="rdv">Les rendez-vous</a></li>
		<li><a href="cer">Saisie du CER</a></li>
		<li><a href="tabsui">Le tableau de suivi</a></li>
		<li><a href="qd1">Le questionnaire D1</a></li>
		<li><a href="qd2">Le questionnaire D2</a></li>
		<li><a href="dsp">La DSP</a></li>
		<li><a href="fp">La Fiche de prescription&nbsp;+</a>
		<ul><li id="activ"><a title="Abandon de la formation" href="cpfpabd">Cas pratique &nbsp;: Abandon de la formation</a></li></ul></li>
		<li><a href="demorien">La demande de réorientation</a></li>
		<li><a href="transferts">Les transferts</a></li>
		<li><a href="stats">Les statistiques</a></li>
		</ul>
	<ul>
		<li><a title="Faire des requêtes" href="recherches">Les Recherches&nbsp;+</a>
	<ul>		
		<li id="activ"><a title="Les nouvelles orientations" href="cpnvorient">Cas pratique &nbsp;: Les nouvelles orientations</a></li>
		<li id="activ"><a title="La file active des CER" href="cpcerfa">Cas pratique &nbsp;: File active des CER</a></li>
		<li id="activ"><a title="Vos rendez-vous du jour" href="cprdvj">Cas pratique &nbsp;: Retrouver nos rendez-vous</a></li>
		<li id="activ"><a title="Informer les bénéficiaires"  href="cpinfoben">Cas pratique &nbsp;: Informer les bénéficiaires de leur RDV</a></li>
	</ul>
		</li>
	</ul>
		<ul>
		<li><a title="Notices métier" href="notices">Notices métier</a></li>
		<li><a title="Annexe 1: L'assistance Webrsa" href="assistance">L'Assistance</a></li>
		<li><a title="Annexe 2: Les procédures métiers" href="procme">Procédures métier</a></li>
		<li><a title="Annexe 3: Les éléments remplissant le TDB/PDV " href="ann3">Remplir le TDB&nbsp; /&nbsp; PDV</a></li>
		<li><a title="Annexe 4: Les listes métiers " href="ann4">Les listes métiers</a></li>
		<li><a title="Annexe 5: Informations sur les doublons" href="ann5">Informations sur les doublons</a></li>
		<li><a title="Annexe 6: L'enregistrement de la structure de parcours " href="ann6">La structure de parcours</a></li>
		<li><a title="Annexe 7: Vider le cache du navigateur" href="infosutils">Informations aux utilisateurs </a></li>
		<li><a title="Annexe 8: Pré-requis WebRSA" href="ann8">Pré-requis WebRSA </a></li>
		<li><a href="/didac/pdf/Guide_utilisateur_final.pdf" target="_blank">Guide utilisateur (pdf)</a></li>
		<li><a href="/didac/pdf/Bureautique.pdf" target="_blank">Guide Bureautique (pdf)</a></li>
		
		<li><a title="Didacticiel Office 2003" href="off2003">Office 2003</a></li>
		<li><a title="Didacticiel Office 2007/2010" href="off2007">Office 2007 / 2010</a></li>
		<li><a title="Didacticiel OpenOffice" href="openoff">OpenOffice</a></li>
		<li><a title="Foire aux questions" href="faq">FAQ</a></li>
		</ul>
		</div>
	<div id="main">
	<a style="text-align:left;" href="javascript:history.back()">&lsaquo;&lsaquo;&nbsp;Page précédente</a>&nbsp;
	<a style="text-align:right;padding-right:15px;float:right;" href="javascript:history.forward(+1)">Page suivante&nbsp;&rsaquo;&rsaquo;</a>
		<h2>Affecter un référent</h2>
<div class="nav">
<a href="#masse">Affectation en masse d'un référent</a> <br/>
<a href="affectaffin#affiner">Affiner les recherches d'affectation</a> <br/>
<!-- <a href="#rem">Remplacer en masse un référent</a> <br/> -->
<a href="affectdos#affdoss">Affectation depuis le dossier</a> <br/>
<a href="affectdos#cloref">Clôturer un référent</a> <br/>
<a href="cohcloref#clorefma">Clôturer un référent en masse</a> <br/>
<a href="cohcloref#reafma">Réaffecter les dossiers du référents clôturé</a> <br/>
</div>		
<p>Chaque bénéficiaire est suivi par un chargé d’insertion dans le cadre de son insertion professionnelle. <br/>
Les référents peuvent être affectés par  &nbsp;:
</p>
<ul>
<li>Le Responsable du Projet de Ville.</li>
<li>Les secrétaires.</li>
</ul>

<p>WebRSA permet d'affecter un référent de deux manières&nbsp;:</p>
<ul>
<li>Soit <b>à partir du menu CER => Affectation d’un référent.</b> Il est alors possible d'affecter un référent à une liste de dossiers pour lesquels il faut saisir un CER (Affectation en masse).</li>
<li>Soit <b>directement sur le dossier</b> d'un bénéficiaire.</li>
</ul>
<p class="nb"><b>NB</b> &nbsp;: L'affectation du référent est obligatoire notamment pour la saisie des CER. <br/>
	Si le référent n'est pas affecté lors de la saisie du CER, celui-ci ne sera pas 	présent dans la cohorte du responsable de Projet de Ville. <br/>
	Par extension, il n'ira pas non plus dans la cohorte des CER à valider au 	niveau du Conseil général.
</p>
	
<ol>
<li><a name="masse">Affectation en masse d'un référent</a></li></ol>
<p>WebRSA permet d'affecter en masse un référent sur tous les bénéficiaires orientés vers votre structure.<br/>
Dans le Menu CER / Affectation d'un référent &nbsp;:
</p>
<img src="/didac/images/affectmasseref.jpg" alt="Affectation en masse" width="534px" height="171px" />
<p>Les bénéficiaires qui sont dans l’une des situations suivantes apparaissent dans cette cohorte. Ceux ne possédant pas de référents s’affichent en premier.<br/>La cohorte "Affectation d'un référent comporte"&nbsp;:</p>
<img src="/didac/images/sitref.jpg" alt="Affectation en masse" width="370px" height="239px" /><div style="position:relative;float:right;right:55px;width:300px;"><p>Vous pouvez aussi filtrer ceux qui ont contractualisés ou non.</p></div>

<img src="/didac/images/sansref.jpg" alt="Affectation en masse" width="607px" height="185px" />

<ul>
<li>Activer la liste en cochant le bouton "Activer". </li>
<li>Cliquer sur le lien <u>Voir</u> pour consulter le dossier du bénéficiaire.</li>
</ul>
<p>Pour valider ensuite la liste d'affectation, cliquer sur le bouton "Validation de la liste". <img class="bimg" src="/didac/images/validliste.jpg" alt="Validation de la liste" width="147px" height="27px" /></p>

<p>Pour cocher tous les boutons radio,<!-- <img class="bimg" src="/didac/images/btradios.jpg" alt="Affectation en masse" width="67px" height="52px" />  -->cliquer sur le bouton "Tout activer". Sélectionner ensuite le référent à affecter dans la liste.</p>
<img src="/didac/images/listeres.jpg" alt="Résultat de la liste" width="605px" height="205px" />
<p>Une fois la liste validée, les affectations deviennent effectives sur chaque dossier.</p>
<p class="nb"><b>NB</b> &nbsp;: Les allocataires non orientés, orientés vers le SSD ou vers Pôle Emploi n’apparaissent pas dans la cohorte d’affectation en masse (voir procédure métier). 
L’affectation d’un référent se fait via le dossier.<br/> 
Cette dernière est <u>indispensable</u> pour la validation du CER.</p>
<a href="#haut">Haut de la page</a>

</div>
<div id="footer"><hr/><p>Crée par DEIAT/PSI</p></div>
</div>
</body>
</html>