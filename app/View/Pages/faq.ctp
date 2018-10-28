<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<meta name="googlebot" content="noindex, nofollow" />
	<title>Didacticiel WebRSA</title>
	<link rel="stylesheet" type="text/css" href="/didac/layout.css" />
	<link rel="stylesheet" href="jq.css" />
	<link href="favicon.png" type="image/png" rel="icon" />
	<link href="favicon.ico" type="image/x-icon" rel="shortcut icon" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script  type="text/javascript">
	$(function() {
		$( "#accordion" ).accordion();
		//$('#accordion').attr('tabindex','');
	});	
// Execution de cette fonction lorsque le DOM sera entièrement chargé
$(document).ready(function() {  
$("#accordion dl").css({ 'height': 'auto' });//Hauteur de la div par rapport au contenu
	// Masquage des questions et des réponses
	$("dl").hide();
	$("dd").hide();	
	
// Clic sur le thème
	$("h5").click(function(){
		// Actions uniquement si la réponse n'est pas déjà visible RAJOUTER UNE BOUCLE POUR REINITIALISER LE TABINDEX!!!
		if($(this).next().is(":visible") == false) {
			// Masquage des réponses
			$("dl").slideUp();
			// Affichage de la réponse placée juste après dans le code HTML
			$(this).next().slideDown();
			}
		//Masquer les réponses lorsqu'on re-clique sur la question
		
		else{ $(this).next().slideDown(); $("dl").hide();}//$(this).next().slideUp();$(this).next().stop(true,true).slideUp('normal');$(this).removeattr('tabindex',-1);  $(this).next().slideUp();.attr('data-active') === "1" ? 1 : 0;  .attr('tabindex',0)
		});	
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
		
		else { $(this).next().slideUp(); }
	});	
});
</script>

<style type="text/css">
<!--
#accordion h5{display:block; /*Pas obligatoire*/ 
text-decoration:underline;
font-weight:strong;
font-size:1em;
padding:5px;
margin-top:7px;
margin-bottom:7px;
color:#6495ED;
width:640px;
height:25px;
background-color:#ADD8E6;
border:solid 1px #6495ED;
border-radius:3px;
}
dt{color:#000080;margin-top:7px;}
dd{font-style:italic;color:#808080;margin-bottom:5px;margin-top:5px;}
-->
</style>
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
		<li ><a title="Affecter un référent à un allocataire" href="affectref">Affecter un référent</a>
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
		<li  id="activation"><a title="Foire aux questions" href="faq">FAQ</a></li>
		</ul>
		</div>
	<div id="main">
	<a style="text-align:left;" href="javascript:history.back()">&lsaquo;&lsaquo;&nbsp;Page précédente</a>&nbsp;
	<a style="text-align:right;padding-right:15px;float:right;" href="javascript:history.forward(+1)">Page suivante&nbsp;&rsaquo;&rsaquo;</a>
	<h2>Foire aux questions</h2>
	<p>Vous trouverez dans cette FAQ les réponses à vos questions les plus fréquentes. Cette FAQ s'inspire des questions que vous envoyez à notre centre de services (CDS à la DINSI).<br/> S'il y a d'autres questions que vous vous posez et dont vous souhaiteriez voir figurer les réponses dans cette FAQ, n'hésitez pas à nous solliciter via le centre de service.</p>
<div class="nav">

<div id="accordion" >

	<h5 title="Cliquez sur les questions ci-dessous pour afficher les réponses" >Les CER</h5>
<dl>
<span style="font-weight:bold;font-style:italic;color:#000080;">NB: Merci de veiller à ce qu'un référent actif soit affecté sur vos dossiers, afin que vos CER puissent être validés par le CG.</span>
	<dt>Je ne peux pas faire de CER : Le bouton n'est pas actif</dt>
		<dd>Vérifier le statut du dernier CER du dossier : est-il à l'étape signature? Envoi responsable ? Envoi CG? Ou en attente de validation CG?<br/>Si le CER nécessite une validation rapide par le BOP (situation personnelle, demande d’APRE, passage en équipe pluridisciplinaire, CER de 3 mois …), merci de le signaler à l’adresse fonctionnelle bada-secretariat@cg93.fr.<br/>Nous vous rappellons qu'un <u>référent actif</u>&nbsp;est obligatoire pour que le CER puisse être validé par le CG.</dd>
	<dt>Comment visualiser mes CER en cours de validité?</dt>
		<dd>Menu Recherches / Par contrats / par CER. <br/>Cocher la case "par période de validité, puis indiquer les dates de début et de fin de la période concernée;<br/>Vous avez tout le détail <a href="cpcerfa">ici</a></dd>
	<dt>Le niveau d'étude n'est pas le même sur le CER que ce que j'ai renseigné dans la DSP&nbsp;:</dt>
	<dd>Cela se produit parfois, lorsqu'il existe un ancien CER, ce sont les informations du précédent CER qui sont automatiquement rapatriées sur le nouveau CER; si cela ne correspond plus à la réalité, vous pouvez modifier le niveau d'étude depuis la liste déroulante.</dd>

	<dt>Pourquoi les niveaux I et II renseignés dans la DSP ne sont pas récupérés sur le CER&nbsp;?</dt>
	
		<dd>Sur la DSP, les niveaux I et II sont regroupés.&nbsp; <img src="/didac/images/niv12dsp.jpg" alt="Niveau 1 et 2 DSP" width="397px" height="22px" /><br/>
		Sur le CER, ils sont séparés.&nbsp; <img src="/didac/images/niv12cer.jpg" alt="Niveau 1 et 2 CER" width="459px" height="67px" /><br/>
		Vous avez la possibilité de le renseigner sur le CER à partir des listes déroulantes proposées pour le niveau d'étude.</dd>
	</dl>	

	<h5 title="Cliquez sur les questions ci-dessous pour afficher les réponses" >Les D1</h5>
<dl >	
	<dt>Pré-requis au renseignement du questionnaire D1</dt>
	<dd><ul>
	<li>Renseigner le niveau d'étude dans la DSP</li>
	<li>Renseigner le 1er RDV de l'année en cours avec un statut "Prévu"</li>
	</ul>
	<p>N'oubliez pas de modifier le statut du rdv à "Honoré" après avoir reçu le bénéficiaire et renseigner la D1.</p></dd>
	<dt>Comment voir mes D1?</dt>
		<dd>Depuis le menu "Tableaux de bords", Tableaux D1.</dd>
	<dt>Pourquoi la D1 me montre des messages en rouges ?</dt>
	<dd>
	<p><b>Réponse</b> Il s'agit de messages d'information qui vous indique&nbsp; :</p>
	<ul>
	<li>Vous n'avez pas renseigné le niveau d'étude dans la DSP</li>
	<li>Vous n'avez pas de 1er RDV enregistré</li>
	<li>Vous avez déjà un formulaire D1 renseigné pour l'année en cours.</li>
	</ul>
	</dd>	
	<dt>Pourquoi, dans mon tableau D1 2014 « soumis à droit et devoirs », ligne 8&nbsp; :</dt>
	<dd>Exemple&nbsp;<br>Rsa socle= 268
Rsa majoré= 47<br>
Rsa socle+activité= 19<br>
Le total fait 334 et pas 336 (le nombre de suivis) ?<br><br>
<u>Réponse</u><br><br>"Le Total ne correspond pas au détail car toutes les prestations comptabilisées dans le Total ne sont pas affichées en détail (RSA activité, activité majoré, autre). Certains allocataires au <u>moment de la saisie de la  D1</u> n'étaient pas forcément enregistrés  avec le statut  demandé."
</dd>
</dl>
	<h5 title="Cliquez sur les questions ci-dessous pour afficher les réponses" >Les D2</h5>
	<dl>
	<dt>Comment puis-je renseigner les D2 des allocataires transférés dans une autre commune?</dt>
		<dd>Après transfert par le BOP, si le PIE source a enregistré une D1, une D2 est enregistrée automatiquement après le transfert. Si vous constatez une incohérence, merci de la remonter via le centre de service.</dd>
	<dt>Quelles sont les informations rapportées dans la colonne du D2 "Dont couvert par un CER = Objectif "SORTIE"</dt>
	<dd>La colonne  < Dont couvert par un CER = Objectif "SORTIE" > correspond au nombre de personnes avec :
<ul>
    <li>Un questionnaire D1</li>
    <li>Un D2 validé sur l'année de référence</li>
    <li>D&D au moment du D1 ou à un moment donné l'année de référence</li>
    <li>Ayant un CER en cours de validité au moment de la validation du questionnaire D2 (c'est à dire avec un CER validé dont la date de début est antérieur à la date de validation de D2 et la date de fin est postérieur à la date de validation de D2).</li>
</ul>
ATTENTION, cette règle n'est pas valable pour la première ligne du tableau de bord, c'est à dire la ligne "Total des participants".
En effet, cette ligne comptabilisant tous les D1, la colonne < Dont couvert par un CER = Objectif "SORTIE" > prend en compte les personnes avec
<ul>
    <li>Un questionnaire D1 validé sur l'année de référence</li>
    <li>D&D au moment du D1 ou à un moment donné l'année de référence</li>
    <li>Avec un CER validé dont la date de début est antérieur à la date de validation de D1 et la date de fin est postérieur à la date du jour de la requête (la date de recherche de tableau dans webRSA).</li></ul></dd>
	</dl>	
	<h5 title="Cliquez sur les questions ci-dessous pour afficher les réponses">Le référent</h5>
<dl>	
	<dt>Comment affecter les dossiers d'un référent à son remplaçant&nbsp;?</dt>
	
		<dd>Depuis la cohorte d'affectation du référent : Menu CER / Affectation d'un référent.</dd>
		<dd>Cette liste vous permet d'affecter un référent en masse, mais ne conserve pas l'historique des référents du parcours.</dd>
		
			
	<dt>Comment ajouter un nouveau référent en conservant l'historique&nbsp; ?</dt>
		<dd>Un lien permettant de clôturer un référent est disponible sur les profils de secrétaires et de responsable structure.<br/>
		A partir du dossier allocataire, cliquer sur le lien "Référent du parcours", puis sur le lien clôturer.<br/>
		Renseigner la date de fin d'habilitation.<br/>
		Le bouton "Ajouter" est de nouveau disponible.<br/>
		L'historique est conservé.</dd>

</dl>


	
<h5 title="Cliquez sur les questions ci-dessous pour afficher les réponses">Le dossier allocataire</h5>
<dl>
	<dt>Je ne trouve pas le dossier d'un allocataire</dt>
	<dd>Retourner sur le formulaire de recherche par allocataire et décocher la case "Uniquement la dernière demande pour un même allocataire"; néanmoins veillez bien à <u>ne pas réaliser de CER sur des dossiers en droits clos ou non défini.</u>.<br/>Si le dossier n'est toujours pas visible, il se peut que l'allocataire ait changé de commune : Le BOP procède mensuellement aux transferts des allocataires ayant changé de commune à l’intérieur du département. Si vous ne retrouvez toujours pas l’allocataire dans WebRSA, merci de contacter le BOP par mail à l’adresse fonctionnelle bada-secretariat@cg93.fr.</dd>
	<dt>Les données de l'allocataire ne correspondent pas à son état civil</dt>
	<dd>C'est à  l'allocataire d'aller demander la correction de son état civil auprès de la CAF.</dd>	
	<dt>Je suis cet allocataire depuis longtemps et son orientation ne correspond pas</dt>
	<dd>Ceci est lié a l'évolution d'un dossier sur le parcours d'un allocataire : Il se peut pour les allocataires rentrants et sortants du dispositif ("les yoyos") que l'orientation ne correspondent pas à votre suivi: L'allocataire peut avoir alors une nouvelle orientation; ceci ne vous empêche pas de continuer son suivi dans votre projet insertion emploi; si vous le souhaitez, vous pouvez demander sa ré-orentation ou son orientation vers votre structure.</dd>
</dl>

<h5 title="Cliquez sur les questions ci-dessous pour afficher les réponses">Les Exports</h5>
<dl>
	<dt>Les lignes du fichier Excel ressortent en décalé</dt>
	<dd>Webrsa est un logiciel libre. A ce titre les développements de l'outil sont fait pour Open Office (ou Libre Office); la solution consiste donc à réaliser l'export avec Open Office, ceci afin d'avoir l'affichage des lignes correct, puis à enregistrer le fichier au format Excel. </dd>
</dl>



<h5 title="Cliquez sur les questions ci-dessous pour afficher les réponses">La fiche de prescription</h5>
<dl>	
	<dt>Comment renseigner le suivi lorsqu'un Brsa quitte la formation qu'il a commencée&nbsp;?</dt>
	
		<dd><p>Dans le champ du suivi de l'action,<br/>A la question l'allocataire a intégré l'action, répondez "<b><i>Non</i></b>" pour avoir accès aux items de la liste déroulante.
Sur la ligne du motif, sélectionnez "<b><i>Abandon</i></b>". Les réponses se trouvent <a href="cpfpabd">ici</a></p></dd>
		
</dl>
<h5 title="Cliquez sur les questions ci-dessous pour afficher les réponses">Les codes ROME</h5>
<dl>	
	<dt>Astuce : Faciliter la recherche des métiers par code ROME&nbsp;?</dt>
	
		<dd><p><img class="bimg" src="/didac/images/astuce.jpg" alt="Utiliser les codes ROME" width="45px" height="49px" /> Astuce : Pour faciliter la recherche des métiers par code ROME, saisissez directement un mot clé de l'intitulé du métier à rechercher<br/>Exemple : Agent de sécurité<br/>Initier la recherche par "Agent" ->  Le résultat affiche tous les métiers commençant par agent.
<img style="position:relative;width:650px;left:-15px;float:left;" src="/didac/images/astucerome.jpg" alt="Recherche des métiers" width="650px" height="179px" /></p><p>Naviguer avec l'ascenseur droit jusqu'à "Agent de surveillance et de sécurité".</p><br/>
<img src="/didac/images/astuceromeli.jpg" alt="Recherche des métiers" width="524px" height="88px" /><p>Les informations  "Code famille, code domaine, code métier et Appellation métier" sont automatiquement renseignés.</p><br/><img src="/didac/images/astuceromeac.jpg" alt="Recherche des métiers" width="566px" height="148px" />
<p><u>Ou</u><br/><br/>Initier la recherche de domaine du métier ici "sécurité" -> présentation de tous les métiers liés à la "sécurité".</p>
<img src="/didac/images/astuceromerr.jpg" alt="Recherche des métiers" width="606px" height="182px" />
<p>Une liste de liens apparaît vous permettant de choisir le métier. En cliquant sur le lien, les champs "Code famille, code domaine, code métier et Appellation métier" sont automatiquement renseignés.<br/>
<img src="/didac/images/astuceromeac.jpg" alt="Recherche des métiers" width="566px" height="148px" /></p>
</dd>	
</dl>


	</div>
</div>
</div>
	<div id="footer"><hr/><p>Crée par DEIAT/PSI</p></div>
</div>
</body>
</html>
