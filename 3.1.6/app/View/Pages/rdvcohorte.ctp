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

<ol start="3">
<li>Mise à jour des statuts des rendez-vous en masse</li>
</ol>
<p>A partir de cette cohorte, vous pouvez mettre à jour les statuts de vos rendez-vous sans passer par le dossier. </p>
<p>Menu cohorte / Cohorte RDV</p>
<img src="/didac/images/rdvcoh.jpg" alt="Rendez-vous en cohorte" width="166px" height="117px"/>
<p>Cliquez sur le bouton "Formulaire"</p>
<img src="/didac/images/rdvcofor.jpg" alt="Rendez-vous en cohorte" width="369px" height="149px"/>
<p>Le formulaire de la cohorte contient les champs communs à tous les formulaires WebRSA:<br/>
N° CAF et dossier RSA, Etat du dossier, Nature de prestation.</p>
<img src="/didac/images/rdvcoform.jpg" alt="Rendez-vous en cohorte" width="604px" height="334px"/>
<p>Recherche par adresse, par allocataire (sur l'état civil).</p>
<img src="/didac/images/rdvadre.jpg" alt="Rendez-vous en cohorte" width="605px" height="144px"/>
<p>Le bloc de "Recherche par rendez-vous":<br/>
Les filtres par défaut sont modifiables pour correspondre à vos critères de recherche et mettre à jour de vos rendez-vous.</p>
<ul><li>La date des rendez-vous "prévu" correspond au premier jour du mois et à la date du jour : Cette valeur par défaut peut être modifiée si besoin.</li></ul>
<img src="/didac/images/rdvsta.jpg" alt="Rendez-vous en cohorte" width="643px" height="234px"/>
<p>Structure référente => Structure proposant le RDV<br/>
Nom du référent => Personne proposant le RDV.
<ul><li>Le filtre "Objet du rendez-vous"</li><ul></p>
<img src="/didac/images/rdvobj.jpg" alt="Rendez-vous en cohorte" width="557px" height="120px"/>
<p><u>Résultat</u>&nbsp; :</p>
<img src="/didac/images/rdvresco.jpg" alt="Rendez-vous en cohorte" width="606px" height="159px"/>
<p>Sélectionner le statut dans la liste déroulante de la colonne "Nouveau statut du RDV".<br/>

Une fois mis à jour à un autre état que "Prévu" le rendez-vous sort de la cohorte : La page est actualisée.
Dans le cas d'un" premier Rendez-vous" de l'année, si le questionnaire D1 n'est pas renseigné, un message vous alerte que le statut du rendez-vous ne peut pas être passé à "Honoré". 
</p>
<img src="/didac/images/rdvmesd1.jpg" alt="Rendez-vous en cohorte" width="644px" height="180px"/>
<p>La mise à jour d'un premier rendez-vous de l'année dont le dossier ne contient pas de D1 pour cette même année rend impossible le passage du rendez-vous à "Honoré".
Cliquer sur le lien "Voir" pour accéder au dossier et renseigner le questionnaire D1.
</p>
<p class="nb">NB : Si la secrétaire est en train de mettre à jour les statuts des rendez-vous en cohorte, les chargés d'insertion ne peuvent accéder au dossier figurant dans cette cohorte en écriture. Il faut alors que la secrétaire en libère l'accès en mettant à jour le statut du rendez-vous, puis le Chargé d'Insertion peut accéder au dossier après avoir fait Ctrl+F5 sur son poste de travail.</p>



<br/><br/>
<a href="#haut">Haut de la page</a>
</div>
<div id="footer"><hr/><p>Crée par DPAS/CESDI</p></div>
</div>
</body>
</html>