<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta name="googlebot" content="noindex, nofollow" />
	<title>Didacticiel WebRSA</title>
	<link rel="stylesheet" type="text/css" href="/didac/layout.css" />
	<link href="/didac/images/favicon.png" type="image/png" rel="icon" />
	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
<!-- <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css"> -->
<link rel="stylesheet" href="jq.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script>
	$(function() {
		$( "#accordion" ).accordion();
	});
	</script>
	 
<script type="text/javascript">
// Execution de cette fonction lorsque le DOM sera entièrement chargé
$(document).ready(function() {$("dt").css("color", "#000080");$("span").css("color", "#6495ED");});
// Execution de cette fonction lorsque le DOM sera entièrement chargé
$(document).ready(function() {
	// Masquage des réponses
	$("dd").hide();
	// CSS : curseur pointeur
	$("dt").css("cursor", "pointer");
	// Clic sur la question
	$("dt").click(function() {
		// Actions uniquement si la réponse n'est pas déjà visible
		if($(this).next().is(":visible") == false) {
			// Masquage des réponses
			$("dd").slideUp();
			// Affichage de la réponse placée juste après dans le code HTML
			$(this).next().slideDown();
		}
		//Masquer les réponses lorsqu'on re-clique sur la question
		else { $(this).next().slideUp(); };
		
	});
});

</script>

<style type="text/css">
span{display:block; 
text-decoration:underline;
font-weight:strong;
font-size:1em;
padding:5px;}
color:#6495ED;
width:605px;
height:35px;
background-color:#ADD8E6;
border:solid 1px #808080;
}

</style>
</head>

<body>
	<div id="container">
		<div id="header"></div>
		<a name="haut"></a>
		<div><h1>Guide utilisateur</h1><span style="margin-left:450px;font-size:12pt;">Version 2.6.8&nbsp;</span><a href="http://www.seine-saint-denis.fr/" target="_blank"><img style="position:absolute;top:109px;right:10px;"src="/didac/images/logo_cg.jpg" width="178px" height="50px" alt="logo cg93"/></a></div>
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
		<li ><a title="Affecter un référent à un allocataire" href="affectref">Affecter un référent&nbsp;+</a>
		<ul><li id="activ"><a title="Remplacer un référent par un autre" href="cpaffectref">Cas pratique &nbsp;: Remplacer en masse un référent</a></li></ul>
		</li>
	</ul>
		<ul>
		<li><a href="rdv">Les rendez-vous</a></li>
		<li><a href="cer">Saisie du CER</a></li>
		<li><a href="tabsui">Le tableau de suivi</a></li>
		<li><a href="qd1">Le questionnaire D1</a></li>
		<li><a href="qd2">Le questionnaire D2</a></li>
		<li><a href="dsp">La DSP</a></li>
		<li><a href="fp">La Fiche de prescription</a></li>
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
		
		<li><a href="/didac/pdf/Guide_utilisateur_final.pdf" target="_blank">Guide utilisateur (pdf)</a></li>
		<li><a href="/didac/pdf/Bureautique.pdf" target="_blank">Guide Bureautique (pdf)</a></li>
		
		<li><a title="Didacticiel Office 2003" href="off2003">Office 2003</a></li>
		<li><a title="Didacticiel Office 2007/2010" href="off2007">Office 2007 / 2010</a></li>
		<li><a title="Didacticiel OpenOffice" href="openoff">OpenOffice</a></li>
		<li  id="activation"><a title="Foire aux questions" href="faq">FAQ</a></li>
		</ul>
		</div>
	<div id="main">
	<h2>Foire aux questions</h2>
	<p>Vous trouverez dans cette FAQ les réponses à vos questions les plus fréquentes. Cette FAQ s'inspire des questions que vous envoyez à notre centre de service (DSI).<br/> S'il y a d'autres questions que vous vous posez et dont vous souhaiteriez voir figurer les réponses dans cette FAQ, n'hésitez pas à nous solliciter via le centre de service.</p>
	<div class="nav">

<div id="accordion">
<dl>
<span style="width:580px;
height:35px;
background-color:#ADD8E6;border:solid 1px #808080;" title="Cliquez sur les questions ci-dessous pour afficher les réponses">Les CER</span>
	<dt>Je ne peux pas faire de CER : Le bouton n'est pas actif</dt>
	
		<dd>Vérifier le statut du dernier CER du dossier : Est-il à l'étape signature? Envoi responsable ? Envoi CG? ou en attente de validation CG?</dd>
		

	<dt>Comment visualiser mes CER en cours de validité?</dt>
	
		<dd>
		Menu Recherches / Par contrats / par CER. <br/>Cocher la case "par période de validité, puis indiquer les dates de début et de fin de la période concernée;<br/>Vous avez tout le détail <a href="cpcerfa">ici</a>
		</dd>
	
	<span>Les statistiques</span>
	
	<dt>Comment voir mes D1?</dt>
	
		<dd>Depuis le menu "Tableaux de bords", Tableaux D1.</dd>
		
	<span>Le référent</span>
	<dt>Comment affecter les dossiers d'un référent à son remplaçant</dt>
	
		<dd>Depuis la cohorte d'affectation du référent : Menu CER / Affectation d'un référent.</dd>
		
		
		<dd>Cette liste vous permet d'affecter un référent en masse.</dd>
	
	<span>Le dossier allocataire</span>
	<dt>Je ne trouve pas le dossier d'un allocataire</dt>
	<dd>
		Retourner sur le formulaire de recherche par allocataire et décocher la case "Uniquement la dernière demande pour un même allocataire"; néanmoins veillez bien à <u>ne pas réaliser de CER sur des dossiers en droits clos</u>.
		</dd>
		
	
	<dt>Les données de l'allocataire ne correspondent pas à son état civil</dt>
	<dd>
		C'est à  l'allocataire d'aller demander la correction de son état civil auprès de la CAF.
		</dd>
		
	
</dl>	
	</div>
</div>

<p>Noter les pré-requis pour leurs stats.</p>
	
	
	<p>Plusieurs possibilités : - Questions en dur puis / réponses ...</p>
	<p> - Thématique des questions, avec questions en dur puis / réponses ...</p>
	<p>- Affichage des réponses : Page web, fenêtre JS, div dépliante, Fenêtre Jquery, Div dépliante (acordéon) Jquery</p>
	<p>Les informations sur l'allocataire ne sont pas correctes : Vérifier sur CAFPRO (si vous avez un accès), puis demander à l'allocataire de faire les démarches</p>
	<p>Expliquer ici les infos mises à jour quotidiennement et celles qui le sont mensuellement.</p>
</div>
	
	
	

	<div id="footer"><hr/><p>Crée par DPAS/CESDI</p></div>
</div>
</body>
</html>
