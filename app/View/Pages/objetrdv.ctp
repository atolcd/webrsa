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
		<li><a title="Affecter un référent à un allocataire" href="affectref">Affecter un référent</a>
		<!-- <ul><li id="activ"><a title="Remplacer un référent par un autre" href="cpaffectref">Cas pratique &nbsp;: Remplacer en masse un référent</a></li></ul> -->
		</li>
	</ul>
		<ul>
		<li  id="activation"><a href="rdv">Les rendez-vous</a></li>
		<li><a href="cer">Saisie du CER</a></li>
		<li><a href="tabsui">Le tableau de suivi</a></li>
		<li><a href="qd1">Le questionnaire D1</a></li>
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
		<h2>Les rendez-vous</h2>
<div class="nav">
<a href="rdv#dosben">Depuis le dossier du bénéficiaire</a> <br/>
<a href="objetrdv#coll">Rendez-vous collectif</a> <br/>
<a href="objetrdv#ind">Rendez-vous individuel</a> <br/>
<a href="objetrdv#tel">Téléphonique</a> <br/>
<a href="vmsrdv">Voir, Modifier, Supprimer un Rendez-vous</a> <br/>
<a href="rdvcer#mcer">Depuis le menu CER</a> <br/>
<a href="rdvcohorte">Mise à jour des statuts des rendez-vous en masse</a> <br/>
</div>

<ul style="list-style-type:none;">
<li><a name="coll" style="text-decoration: none;">a.&nbsp;Collectif</a><br/>
<img src="/didac/images/rdvcoll.jpg" alt="rdv collectif" width="663px" height="179px" /></li>
<li><a name="ind" style="text-decoration: none;">b.&nbsp;Individuel</a><br/>
<img src="/didac/images/rdvind.jpg" alt="rdv individuel" width="663px" height="127px" /></li>
<li><a name="tel" style="text-decoration: none;">c.&nbsp;Téléphonique</a><br/>
<ul type="square">
<li>Avec l'Allocataire</li>
<li>Avec des partenaires</li>
</ul>
<img src="/didac/images/rdvtel.jpg" alt="rdv téléphonique" width="643px" height="81px" /></li>
</ul>
<p><u>Exemple</u>&nbsp; : </p>
<ul><li>Un Brsa vous téléphone pour une demande spécifique, et la/le CI passe un certain temps avec lui au téléphone. Vous avez la possibilité de le noter.</li>
<li>J'ai une offre à faire à un brsa : Je l'appelle…et je passe un certain temps avec lui</li></ul>
<p>Le RDV téléphonique peut être noté dans WebRSA à partir du moment ou vous passez un certain temps avec les Brsas au téléphone.</p>
<img src="/didac/images/rdvtelpri.jpg" alt="rdv téléphonique" width="686px" height="266px" /></li>
<p>Renseigner le statut du rendez-vous&nbsp;:</p>
<img src="/didac/images/statutrdv.jpg" alt="Statut du rendez-vous" width="605px" height="185px" />
<p>Renseigner la date et l'heure du rendez-vous&nbsp;:</p>
<img src="/didac/images/datehrdv.jpg" alt="Date et heure du rendez-vous" width="605px" height="62px" />
<p>L'objectif du rendez-vous</p>
<img src="/didac/images/obrdv.jpg" alt="Objectifs du rendez-vous" width="605px" height="150px" />
<p>Valider le rendez-vous en cliquant sur "Enregistrer"&nbsp;:</p>
<img src="/didac/images/enrdv.jpg" alt="Enregistrer le rendez-vous" width="319px" height="54px" />

<p>Une fois validé, le rendez-vous apparaît dans la liste des rendez-vous.<br/>
Le rendez-vous est alors effectif.<br/>
Il est accessible à tout moment depuis le lien "Gestion RDV" sur le dossier.</p>
<img src="/didac/images/affrdv.jpg" alt="rdv collectif" width="605px" height="240px" />
<p>Cette liste permet d'avoir un historique des rendez-vous pris avec le bénéficiaire et renseigne également sur le statut du rendez-vous &nbsp;: prévu, honoré, non honoré, excusé etc….
<br/>
En passant la souris sur le rendez-vous, un popup renvoie les Objectifs et les commentaires saisis par le/la référent(e) du parcours.
</p>
<br/><br/>
<a href="#haut">Haut de la page</a>
</div>
<div id="footer"><hr/><p>Crée par DPAS/CESDI</p></div>
</div>
</body>
</html>