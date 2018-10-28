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
		<h2>Clôturer en masse le suivi d'un référent </h2>
<div class="nav">
<a href="affectref#masse">Affectation en masse d'un référent</a> <br/>
<a href="affectaffin#affiner">Affiner les recherches d'affectation</a> <br/>
<!-- <a href="#rem">Remplacer en masse un référent</a> <br/> -->
<a href="affectdos#affdoss">Affectation depuis le dossier</a> <br/>
<a href="affectdos#cloref">Clôturer un référent</a> <br/>
<a href="cohcloref#clorefma">Clôturer un référent en masse</a> <br/>
<a href="cohcloref#reafma">Réaffecter les dossiers du référents clôturé</a> <br/>
</div>		

<ol start="5"><li><a name="clorefma">Clôture des dossiers d’un référent et réaffectation de la file active en masse</a></li></ol>
<ol style="font-weight:normal;list-style-type:lower-alpha;"><li>Clôture des dossiers d’un référent</li>

<p>Cette cohorte de clôture est disponible uniquement sur les profils : responsable du projet insertion emploi et secrétaire. Ce formulaire vous permettra de mettre une date de fin de désignation à l’ensemble des dossiers d’un référent qui a quitté la structure (Personne chargée du suivi du dossier).
</br>A partir du menu "Cohortes" / "Clôture référents"</p>
<img src="/didac/images/cohclo.jpg" alt="Affectation en masse" width="202px" height="119px" />

<p>Ecrire le nom du référent</p>

<img src="/didac/images/nref.jpg" alt="Affectation en masse" width="690px" height="218px" />

<p>Seule votre structure est disponible dans la liste de "Structure référente liée".
Il permet également de retrouver les référents clôturés en fonction de la date de clôture.</p>

<img src="/didac/images/dateclo.jpg" alt="Affectation en masse" width="690px" height="154px" />

<p>Le lien "Clôturer" me conduit sur une nouvelle page.</p>
<img src="/didac/images/pagecloref.jpg" alt="Affectation en masse" width="690px" height="103px" />
<p>Veuillez indiquer la date de clôture. Cette date sera indiquée comme date de fin de désignation sur l’ensemble des dossiers suivi par le référent.</br>
Le bouton "Annuler" vous ramène à la page précédente, c'est-à-dire le formulaire de recherche.</p>
<img src="/didac/images/avtclo.jpg" alt="Affectation en masse" width="690px" height="227px" />


<a name="reafma"></a><li>Réaffecter les dossiers d'un référent</li></ol>

<p>Vous pouvez affecter de nouveau un référent à l’ensemble des dossiers clôturés à partir du menu CER->Affectation d’un référent.</br>

A partir du menu CER / Affectation d'un référent.</br>
Dans le bloc "Réaffectation du référent" saisir le nom du référent précédent.</br></p>

<img src="/didac/images/reafref.jpg" alt="Affectation en masse" width="690px" height="115px" />

<p>A partir de la liste de résultats, vous pouvez affecter un référent de votre choix.</p>
<img src="/didac/images/lireaf.jpg" alt="Affectation en masse" width="690px" height="277px" />

<a href="#haut">Haut de la page</a>

</div>
<div id="footer"><hr/><p>Crée par DEIAT/PSI</p></div>
</div>
</body>
</html>