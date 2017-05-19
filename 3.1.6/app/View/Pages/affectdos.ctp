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
		<div><h1>Guide utilisateur</h1><span style="margin-left:450px;font-size:12pt;">Version 2.9.07&nbsp;</span><a href="http://www.seine-saint-denis.fr/" target="_blank"><img style="position:absolute;top:109px;right:10px;"src="/didac/images/logo_cg.jpg" width="178px" height="50px" alt="logo cg93"/></a></div>
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
<a href="affectref#masse">Affectation en masse d'un référent</a> <br/>
<a href="affectaffin#affiner">Affiner les recherches d'affectation</a> <br/>
<!-- <a href="#rem">Remplacer en masse un référent</a> <br/> -->
<a href="affectdos#affdoss">Affectation depuis le dossier</a> <br/>
<a href="#cloref">Clôturer un référent</a> <br/>
</div>
<ol start="3"><li><a name="affdoss">Affecter un référent directement depuis le dossier du bénéficiaire</a></li></ol>
<p>Il est possible d’affecter un référent à partir du dossier principalement pour les cas suivants  &nbsp;: dossier non orientés, dossiers orientés Pôle Emploi et dossiers orientés SSD.<br/>
Dans le dossier, cliquer sur Référent du parcours &nbsp;:</p>
<img src="/didac/images/refparc.jpg" alt="Référent du parcours" width="605px" height="280px" />
<p><u>Formulaire d'affectation du référent</u>&nbsp;:</p>
<p>Sélectionner le nom de votre structure dans la liste  &nbsp;:</p>
<img src="/didac/images/formaffedoss.jpg" alt="Formulaire d'affectation" width="605px" height="281px" />
<p>Les listes déroulantes sont interdépendantes  &nbsp;: </p>
<ul><li>La sélection de la structure référente permet l'affichage des noms des chargés d'insertion dans la liste qui suit. Ce sont les référents de la structure choisie qui apparaissent puisque ces listes sont interdépendantes.</li></ul>
<img src="/didac/images/refalloc.jpg" alt="Formulaire d'affectation" width="605px" height="281px" />
<p>Le référent est affecté une fois l'enregistrement effectué  &nbsp;:</p>
<img src="/didac/images/affectdoss.jpg" alt="Formulaire d'affectation" width="605px" height="257px" />
<p>La rubrique "Référent du parcours" permet de visualiser le référent affecté au bénéficiaire.</p>

<p class="nb"><b>NB</b>  &nbsp;: Dans le cas d'un foyer comportant un conjoint (CJT), le référent du demandeur peut-être différent de celui du conjoint.</p>

<p>Le lien "Modifier"&nbsp;<img class="bimg" src="/didac/images/lienmodif.jpg" alt="Lien modifier" width="76px" height="21px" />  permet de revenir sur la fenêtre d'affectation du référent et de le modifier à partir de la liste déroulante.</p>
<img src="/didac/images/refchg.jpg" alt="Modifier le référent" width="605px" height="150px" />
<p>Si l'on retourne sur la synthèse du dossier du bénéficiaire, on peut visualiser l’affectation du référent.</p>
<img src="/didac/images/resrefmodif.jpg" alt="Modifier le référent" width="605px" height="317px" /><br/>
<a href="#haut">Haut de la page</a>
<ol start="4"><li><a name="cloref">Clôturer un référent</a></li></ol>
<p>La clôture du référent sur le dossier se fait en cliquant sur le lien "référent du parcours", puis sur le lien "clôturer".</p>
<img src="/didac/images/refclo.jpg" alt="Clôturer un référent" width="700px" height="171px" />
<img src="/didac/images/valclo.jpg" alt="Clôturer un référent" width="700px" height="176px" />
<p>Renseigner la date de fin de désignation.<br/>Valider la clôture en cliquant sur le bouton "Enregistrer".</p>
<p>Le bouton "Ajouter" devient actif &nbsp;:&nbsp;<img class="bimg"src="/didac/images/bajouref.jpg" alt="Ajouter un référent" width="101px" height="62px" /></p>
<p>
Une fois clôturé, vous pouvez ajouter le nouveau référent et ainsi conserver l'historique des référents ayant suivi le bénéficiaire.<br/>
Cette fonctionnalité est accessible uniquement aux <u>secrétaires</u> et aux <u>responsables</u> des PDV.<br/><br/>

Le lien "modifier" permet de modifier un référent affecté en le remplaçant.<br/><br/>

Seule la clôture du référent permet de conserver l'historique.
</p>
<br/>
<a href="#haut">Haut de la page</a>
</div>
<div id="footer"><hr/><p>Crée par DPAS/CESDI</p></div>
</div>
</body>
</html>
