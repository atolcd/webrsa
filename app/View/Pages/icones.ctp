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
	
	<script src="menu.js" type="text/javascript"></script>
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
	
		<li  id="activation"><a title="Naviguer dans le dossier d'un bénéficiaire" href="dossier">Le dossier allocataire&nbsp; +</a>
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
		<li><a title="Faire des requêtes" href="recherches">Les Recherches&nbsp; +</a>
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
		</ul>
		</div>
	<div id="main">
		<h2 style="display:inline;"> Le dossier allocataire&nbsp;:&nbsp;<i>Les icônes</i></h2>
	<br/>
<div class="nav">
<a href="dossier">Rechercher un allocataire</a> <br/>
<a href="dossier2#consulted">Consulter un dossier</a>
<a href="dossier2#syn">La synthèse du dossier</a>
<a href="dossier2#coord">Modifier les coordonnées d'un bénéficiaire</a>
<a href="dossier2#hist">Historique des anciens dossiers</a>
<a href="dossier2#cf">La composition du foyer</a>
<a href="dossier2#inf">Les informations du foyer</a><br/>
<a href="dossier2#filie">Lier des pièces jointes</a>
<a href="dossier#tri">Trier vos résultats</a>
<a href="icones#icones">Les icônes d'information</a>
<a href="icones#acces">Les Accès concurrents</a>
<a href="dosssep#separe">Cas des couples séparés</a>
</div>
<br/>	
<a name="rechalloc"></a>

<a name="icones"></a>
<ol start="5"><li>Les icônes d'information du dossier</li></ol>
<p>Cette icône (la clé)&nbsp; <img class="bimg" src="/didac/images/cle.jpg" alt="La clé" width="24px" height="25px" />     vous indique que vous avez accès au dossier et pouvez effectuer des modifications dessus. <br/>
Si vous êtes simplement en consultation sur un dossier, vous pouvez permettre l'accès à un autre utilisateur en double cliquant sur la clé &nbsp;: Vous libérez ainsi l'accès au dossier pour permettre à un autre utilisateur d'effectuer des modifications dessus.</p>
<p>Cette icône (le cadenas)&nbsp; <img class="bimg" src="/didac/images/cadenas.jpg" alt="Le cadenas" width="23px" height="24px" />   vous informe qu'un autre utilisateur est en train de travailler sur ce dossier et que par conséquent vous n'avez pas accès aux modifications. Vous pouvez juste le consulter.</p>
<p>Cette icône (un triangle)&nbsp; <img class="bimg" src="/didac/images/triangle.jpg" alt="Le triangle" width="21px" height="24px" />    vous indique que ce foyer comporte des personnes sans prestations. Il faudrait en informer le centre appel via votre référent informatique pour une mise à jour des prestations.</p>
<img src="/didac/images/infotri.jpg" alt="Les icônes d'informations" width="691px" height="210px" />
<img src="/didac/images/infocle.jpg" alt="Les icônes d'informations" width="578x" height="124px" />
<p>Pour déverrouiller un dossier, effectuez un double clic sur la clé  &nbsp;: ceci permet à un autre utilisateur d'accéder aux modifications (Rdv, CER etc..).</p>
<p>Les dossiers comportant des &nbsp; <img class="bimg" src="/didac/images/excl.jpg" alt="Le point d'exclamation" width="24px" height="23px" />   comportent des doublons. De même, il est conseillé de les signaler au centre appel via votre référent informatique.</p>
<img src="/didac/images/infocadenas.jpg" alt="Les icônes d'informations" width="555px" height="150px" />
<a name="acces"></a><ol start="6"><li>Les accès concurrents</li></ol>
<img src="/didac/images/acces_concurrent.jpg" alt="Les Accès concurrents" width="690px" height="372px" />
<p>Lorsque vous avez le cadenas sur un dossier, vous pouvez voir qui est connecté dessus en passant la souris en survol sur le cadenas : Le nom de la personne connecté apparaît dans une info bulle ainsi que l'heure à laquelle sera rendue possible la connexion.</p>

<br/>
<br/>
<a href="#haut">Haut de la page</a>
</div>
<div id="footer"><hr/><p>Crée par DEIAT/PSI</p></div>
</div>

	</body>
</html>