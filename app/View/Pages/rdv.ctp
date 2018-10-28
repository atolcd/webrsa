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
		<li><a title="Affecter un référent à un allocataire" href="affectref">Affecter un référent</a>
		<!-- <ul><li id="activ"><a title="Remplacer un référent par un autre" href="cpaffectref">Cas pratique &nbsp;: Remplacer en masse un référent</a></li></ul> -->
		</li>
	</ul>
		<ul>
		<li  id="activation"><a href="rdv">Les rendez-vous</a></li>
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
		<li><a title="Annexe 3: Les éléments remplissant le TDB/PIE " href="ann3">Remplir le TDB&nbsp; /&nbsp; PIE</a></li>
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
		<h2>Les rendez-vous</h2>
<div class="nav">
<a href="#dosben">Depuis le dossier du bénéficiaire</a> <br/>
<a href="objetrdv#coll">Rendez-vous collectif</a> <br/>
<a href="objetrdv#ind">Rendez-vous individuel</a> <br/>
<a href="objetrdv#tel">Téléphonique</a> <br/>
<a href="vmsrdv">Voir, Modifier, Supprimer un Rendez-vous</a> <br/>
<a href="rdvcer#mcer">Depuis le menu CER</a> <br/>
<a href="rdvcohorte">Mise à jour des statuts des rendez-vous en masse</a> <br/>
<a href="vmsrdv#ficli">Lier un fichier à un RDV</a> <br/>
</div>
<p>Il existe deux chemins permettant d’enregistrer un rendez-vous &nbsp;: </p>
<ul>
<li>A partir du dossier -> depuis le lien Gestion des RDV</li>
<li>A partir du menu CER ->Saisie d’un CER.</li>
</ul>
<p>La création d'un rendez-vous se fait toujours sur le dossier de l'allocataire<br/>
Le référent doit être affecté au dossier pour permettre la prise de rendez-vous.
</p>
<p class="nb"><b><u>Néanmoins</u>&nbsp;:</b> Dans le cas d'une prise de rendez vous pour les informations collectives, il est possible de créer un rendez-vous sans affecter de référent au dossier (pour le  bénéficiaire).</p>	
<ol>
<li><a name="dosben">Depuis le dossier du bénéficiaire</a></li></ol>
<p>Sur un dossier, déployer la liste en cliquant sur le "+"&nbsp;<img class="bimg" src="/didac/images/bplus.jpg" alt="Bouton plus" width="20px" height="21px" />&nbsp;du dossier.</p>
<img src="/didac/images/ajoutrdv.jpg" alt="Ajouter un rendez-vous" width="605px" height="342px" />
<p><u>Veillez à sélectionner votre structure</u> afin de pouvoir sélectionner le référent : Les listes déroulantes sont interdépendantes.</p>
<img src="/didac/images/rdvform.jpg" alt="Ajouter un rendez-vous" width="690px" height="265px" />
<ul><li>Le choix de votre structure en tant que "Structure référente <span style="color:red">*</span>" est indispensable :</li></ul>
<p>Dans le cas d'un rendez-vous prit avec un bénéficiaire orienté emploi ou service
	social, il vous faut sélectionner votre structure, afin que le rendez-vous puisse être
	<b><i>comptabilisé pour votre activité</i></b> dans le cas ou il serait <b><i>honoré</b></i>.
	<i>Noter que ce choix n'a pas d'incidence sur l'orientation faite par le CG</i>.
</p>
<ul>
<li>Il est possible d’enregistrer un rendez-vous à une date antérieure à la date du jour. </li>
<li>Vous pouvez redimensionner les zones de saisie de commentaires et d'objectif du 	RDV en positionnant la souris dans l'angle inférieur droit de la zone de saisie.</li>
</ul>
<p>Certains champs sont obligatoires &nbsp;:<br/>
Les champs marqués d'un astérisque (<span style="color:red">*</span>) sont obligatoires.</p>
<img src="/didac/images/rdvoblig.jpg" alt="Champs obligatoires" width="605px" height="392px" />
<p>Renseigner les champs marqués en rouge pour que le rendez-vous puisse être validé.<br/>
Le champ "Permanence liée à la structure" ne concerne pas les Projets de ville.<br/>
Lorsque les champs sont renseignés, la validation du rendez-vous se fait en cliquant sur le bouton "Enregistrer"&nbsp;<img class="bimg" src="/didac/images/benr.jpg" alt="Bouton Enregistrer" width="94px" height="30px" /> .</p>
<img src="/didac/images/ajourdv.jpg" alt="Champs obligatoires" width="605px" height="311px" />
<p>L'Objet du rendez-vous comporte 4 Items  &nbsp;:</p>
<img src="/didac/images/objrdv.jpg" alt="Objets du rendez-vous" width="518px" height="107px" />
<br/><br/>
<a href="#haut">Haut de la page</a>
</div>
<div id="footer"><hr/><p>Crée par DEIAT/PSI</p></div>
</div>
</body>
</html>