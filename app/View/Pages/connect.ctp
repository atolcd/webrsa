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
		<li  id="activation"><a title="Se connecter à WebRSA" href="connect">Se connecter</a></li>
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
		<h2>Se connecter</h2>
<div class="nav">
<a href="#debut">Le première connexion</a> 
<a href="#pswd">Modifier son mot de passe</a> 
</div>	
	<ol>
	<li><a name="debut">La première connexion</a></li></ol>
<p>Pour une meilleure utilisation, il est recommandé d'utiliser Firefox.</p>
<img src="/didac/images/fx.jpg" alt="firefox" width="100px" height="74px"/><span style="position:relative;top:-70px;left:7px;">Utiliser la version 17 minimum.</span>
	<ul>
	<li>Le compte " Utilisateur " à utiliser est celui que l’administrateur de l’application vous a donné.</li>
	</ul>
	<ul style="margin-left:75px;">
	<li>Identifiant&nbsp;: 1ère lettre du prénom puis nom de famille en minuscule &nbsp;:pnomdefamille.</li>
	<li>Mot de passe&nbsp;: Le formateur vous communiquera le mot de passe, en principe &nbsp;: pnomdefamille93* (1ère lettre du prénom puis nom de famille en minuscule93*).</li>
	</ul>
<p>En <a href="https://rsa.cg93.fr" target="_blank">https://rsa.cg93.fr</a>&nbsp;(accès externe au Conseil Départemental).
Se connecter avec votre identifiant et votre mot de passe qui vous seront communiqués par le formateur lors des journées d’accompagnement.</p>
<img src="/didac/images/connexion.jpg" alt="connexion" width="605px" height="192px"/>
<ol start="2"><li><a name="pswd">Modification du mot de passe</a></li>
</ol>
<p>Après la première connexion, vous devez personnaliser votre mot de passe pour une meilleure sécurité des informations. 
<br/>
Pour modifier votre mot de passe, cliquer sur le lien de votre nom utilisateur en haut à droite de la page sur laquelle vous êtes situé. Ce lien est toujours disponible. </p>
<img src="/didac/images/psdw.jpg" alt="Modifier votre mot de passe" width="333px" height="165px"/>
<br/>
<p>Le formulaire de modification de votre mot de passe &nbsp;:</p>
<img src="/didac/images/chgpswd.jpg" alt="Modifier votre mot de passe" width="600px" height="123px"/>
<div class="nb"><b><u>NB</u></b>&nbsp;: Le format du mot de passe doit comporter 8 caractères minimum et doit être 	composé de lettres, au moins 2 chiffres et un caractère spécial.</div>
<p>Une fois connecté, certains champs sont réservés à l'identification. 
Les informations affichées sont propres au profil de l'utilisateur. Il existe trois profils utilisateurs &nbsp;: Chef de Projet Insertion Emploi, Secrétaire et Chargé d’insertion.
</p>
<img src="/didac/images/infos.jpg" alt="Vos informations utilisateur" width="600px" height="78px"/>
<img src="/didac/images/logout.jpg" alt="Se déconnecter" width="605px" height="108px"/><br/>
<div class="nb"><b><u>NB</u> &nbsp;:</b>&nbsp;N'oubliez pas de cliquer sur <b><u>le lien de déconnexion</u></b> pour quitter WebRSA. </div>
<p>Pour des raisons de sécurité, nous vous conseillons de modifier le mot de passe fourni par le CG, puis par la suite de le modifier régulièrement.</p>
<br/>
<a href="#haut" style="padding-bottom:10px;">Haut de la page</a>
</div>
<br/>
<div id="footer"><hr/><p>Crée par DEIAT/PSI</p></div>
</div>

</body>
</html>