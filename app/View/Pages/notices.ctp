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
<style type="text/css">
<!-- 
#notice a {
text-decoration:underline;
font-weight:bold;
font-size:1em;
padding:3px;
margin-top:5px;
margin-right:auto;
margin-bottom:0px;
margin-left:auto;
float:left;
color:#B0C4DE;
width:640px;
height:25px;
background-color:#CD5C5C;
border:solid 1px #6495ED;
border-bottom:none;
border-radius: 10px 10px 0 0;
}
#notice p {float:left; background-color:#CD5C5C;padding:3px;
margin-top:0px;
margin-right:auto;
margin-bottom:5px;
margin-left:auto;
width:640px;
height:25px;
border:solid 1px #6495ED;
border-top:none;
border-radius:0 0 10px 10px;
} 

#notice ul {float:left; background-color:#CD5C5C;padding:3px;
margin-top:0px;
margin-right:auto;
margin-bottom:5px;
margin-left:auto;
width:640px;
height:75px;
border:solid 1px #6495ED;
border-top:none;
border-radius:0 0 10px 10px;}

-->
</style> 
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
		
		<li id="activation"><a title="Notices métier" href="notices">Notices métier</a></li>
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
	<a title="Revenir à la page précédente" style="text-align:left;" href="javascript:history.back()">&lsaquo;&lsaquo;&nbsp;Page précédente</a>&nbsp;
	<a title="Aller à la page suivante" style="text-align:right;padding-right:15px;float:right;" href="javascript:history.forward(+1)">Page suivante&nbsp;&rsaquo;&rsaquo;</a>
		<h2>Les notices métier</h2>
<p>Cette page contient toutes les notices métier qui vous sont remises lors des formations, ou qui vous sont envoyées jointes aux infos flash.</p>
<div id="notice">
<a href="/didac/pdf/notices/n1.pdf" target="_blank">Notice 1 : Les secteurs d'activités</a><br/>

<p>Cette notice est encore utile pour requêter sur les anciens CER qui contenait les listes métiers.</p>


<a href="/didac/pdf/notices/n2.pdf" target="_blank">Notice 2 : Calcul des CERs</a><br/>
<p style="height:60px;">Cette notice convient pour : - le calcul de la moyenne annuelle des CER en cours de validité à la fin de chaque mois (article 4.2 de la convention CG93/PIE 2013et 19-2 de la conventionCG93/PIE 2014-2016)  
<br/>- le calcul des CER en cours de validité pour une période de votre choix </p>
<a href="/didac/pdf/notices/n3.pdf" target="_blank">Notice 3 : Les tableaux de bords</a><br/>
<p style="height:40px;">Cette notice vous inform des champs à renseigner dans webRSA pour mettre à jour les tableaux B3 (difficultés sociales), B4 et B5 (fiche de prescription) et B6 (Actions collectives).</p>
<a href="/didac/pdf/notices/n4.pdf" target="_blank">Notice 4 : Les tableaux B1 / B2</a><br/>
<p style="height:40px;">Les tableaux B1 et B2 ne figurent pas dans WebRSA. Ce sont ceux de vos bilans semestriels qui vous sont envoyés par FTP.</p>
<a href="/didac/pdf/notices/n5.pdf" target="_blank">Notice 5 : La fiche de prescription</a><br/>
<p style="height:60px;">Notice explicative pour l'utilisation de la fiche de prescription. Rappel sur les actions "en attente" de conventionnement (NFT et NFA): celles-ci seront comptabilisées dans les tableaux de bords, une fois qu'elles seront conventionnées.</p>
<a href="/didac/pdf/notices/n6.pdf" target="_blank">Notice 6: Les tableaux de bords B5</a><br/>
<p style="height:45px;">Ce tableau permet de recenser l’ensemble des prescriptions à caractère socio-professionnel et professionnel. Il est généré automatiquement à partir des informations saisies sur chaque fiche de prescription enregistrée.</p>
<a href="/didac/pdf/notices/n7.pdf" target="_blank">Notice 7: Les actions PDI 2016</a><br/>
<p style="border-bottom:none;border-radius:0 0 0 0;margin-bottom:0;">Catalogue du <b>PDI</b> (Janvier 2016), liste des actions actives sur WebRSA. </p>
<ul>
<li>Prescriptions professionnelles</li>
<li>Prescriptions socio professionnelles</li>
<li>Prescription vers les acteurs de la Santé</li>
<li>Culture loisirs vacances</li>
</ul>
<a href="/didac/pdf/notices/n8.pdf" target="_blank">Notice 8: Le sourcing</a><br/>
<p style="height:40px;">Cette notice est celle remise lors des ateliers "Sourcing", il s'agit de cibler et identifier des publics pour mieux les mobiliser dans leur parcours d’insertion. </p>
<a href="/didac/pdf/notices/n9.pdf" target="_blank">Notice 9: Le FTP </a><br/>
<p style="height:40px;">Cette notice vous permet d'Exploiter les données mises à disposition sur le FTP - Listing B2 partie 3 analyse  parcours - Indicateurs socio démographiques .</p>
<a href="/didac/pdf/notices/ftp.pdf" target="_blank">Procédure d'accès au FTP (SFTP)</a><br/>
<p style="height:60px;">Notice explicative permettant d'accéder au FTP : Celui-ci est sécurisé (SFTP). Si la connexion ne se fait pas voyez votre service informatique afin de savoir si le firewall autorise ce type d'accès et si l'envoi de fichiers en SFTP est autorisé. Sinon n'hésitez pas à revenir vers nous.</p>


<br/>

</div></div>
<div id="footer"><hr/><p>Crée par DEIAT/PSI</p></div>
</div>
</body>
</html>