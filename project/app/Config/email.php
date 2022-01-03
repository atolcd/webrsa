<?php
/**
 * This is email configuration file.
 *
 * Use it to configure email transports of Cake.
 *
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 2.0.0
 */

/**
 * Email configuration class.
 * You can specify multiple configurations for production, development and testing.
 *
 * transport => The name of a supported transport; valid options are as follows:
 *		Mail		- Send using PHP mail function
 *		Smtp		- Send using SMTP
 *		Debug		- Do not send the email, just return the result
 *
 * You can add custom transports (or override existing transports) by adding the
 * appropriate file to app/Network/Email. Transports should be named 'YourTransport.php',
 * where 'Your' is the name of the transport.
 *
 * from =>
 * The origin email. See CakeEmail::from() about the valid values
 */
class EmailConfig {

	/**
	 * Configuration de l'envoi de mails pour les mots de passe oubliés.
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $user_generation_mdp = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails pour les les pièces manquantes de
	 * l'APRE du CG 66.
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 * Les clés 'from' et 'replyTo' remplacent les valeurs de
	 * 'Apre66.EmailPiecesmanquantes.from' et 'Apre66.EmailPiecesmanquantes.replyto'
	 * qui se trouvaient dans le fichier webrsa.inc.
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 *
	 * @var array
	 */
	public $apre66_piecesmanquantes = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails pour les fiches de candidatures du
	 * CG 66.
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 * Les clés 'from' et 'replyTo' remplacent les valeurs de
	 * 'FicheCandidature.Email.from' et 'FicheCandidature.Email.replyto'
	 * qui se trouvaient dans le fichier webrsa.inc.
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $fiche_candidature = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails pour les avis techniques des CUIs
	 * CG 66.
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 * Les clés 'from' et 'replyTo' remplacent les valeurs de
	 * 'Cui.Email.from' et 'Cui.Email.replyto'
	 * qui se trouvaient dans le fichier webrsa.inc.
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $avis_technique_cui = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails pour les employeurs liés aux CUIs
	 * CG 66.
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 * Les clés 'from' et 'replyTo' remplacent les valeurs de
	 * 'Cui.Email.from' et 'Cui.Email.replyto'
	 * qui se trouvaient dans le fichier webrsa.inc.
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $mail_employeur_cui = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails aux employeurs pour les décisions prises
	 * sur les CUIs CG 66.
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 * Les clés 'from' et 'replyTo' remplacent les valeurs de
	 * 'Cui.Email.from' et 'Cui.Email.replyto'
	 * qui se trouvaient dans le fichier webrsa.inc.
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $mail_decision_employeur_cui = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Fiche de liaison
	 *
	 * @var array
	 */
	public $fiche_de_liaison = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails aux administrateur pour les imports FRSA en rejet
	 * CG 93.
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $import_frsa = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails aux personnes dédiées pour l'ajout de
	 * pièce jointe avec une certaine catégorie (case à cocher dans la catégorie de la pièce jointe)
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $piece_jointe = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails en cas d'ajout de certaines catégories
	 * de pièces jointes à un dossier
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $cat_piecejointe = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',

	);

	/**
	 * Configuration pour l'envoi de mails dans le module email	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $mail_recours_gracieux = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',
		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);
	public $mail_titresuivi = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',
		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',
	);

	/**
	 * Configuration de l'envoi de mails en cas de personne ayant essayé de se
	 * connecté à WebRSA sans que l'utilisateur ait été trouvé
	 *
	 * Les clés 'port', 'timeout', 'host', 'username', 'password', 'client'
	 * remplacent les valeurs qui étaient contenues dans 'Email.smtpOptions'
	 * du fichier webrsa.inc.
	 *
	 *
	 * Lorsque l'application est en debug > 0, alors le mail est envoyé à
	 * l'adresse spécifiée pour la clé 'to', ou à l'expéditeur (clé 'from').
	 *
	 * De même, si une clé 'subject' est spécifiée, elle sera utilisée comme
	 * sujet du mail.
	 *
	 * @var array
	 */
	public $ldap_utilisateur_non_trouve = array(
		'transport' => 'Smtp',
		'from' => '',
		'replyTo' => '',
		'to' => '',
		'subject' => '',
		'host' => '',
		'username' => '',
		'password' => '',
		'client' => '',

		'port' => 25,
		'timeout' => 30,
		'log' => false,
		'charset' => 'utf-8',
		'headerCharset' => 'utf-8',

	);

	public function __construct () {
		$this->user_generation_mdp['transport'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_TRANSPORT');
		$this->user_generation_mdp['from'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_FROM');
		$this->user_generation_mdp['replyTo'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_REPLYTO');
		$this->user_generation_mdp['to'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_TO');
		$this->user_generation_mdp['subject'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_SUBJECT');
		$this->user_generation_mdp['host'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_HOST');
		$this->user_generation_mdp['port'] = intval(env('EMAIL_CONFIG_USER_GENERATION_MDP_PORT'));
		$this->user_generation_mdp['username'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_USERNAME');
		$this->user_generation_mdp['password'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_PASSWORD');
		$this->user_generation_mdp['client'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_CLIENT');

		$this->apre66_piecesmanquantes['transport'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_TRANSPORT');
		$this->apre66_piecesmanquantes['from'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_FROM');
		$this->apre66_piecesmanquantes['replyTo'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_REPLYTO');
		$this->apre66_piecesmanquantes['to'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_TO');
		$this->apre66_piecesmanquantes['subject'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_SUBJECT');
		$this->apre66_piecesmanquantes['host'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_HOST');
		$this->apre66_piecesmanquantes['port'] = intval(env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_PORT'));
		$this->apre66_piecesmanquantes['username'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_USERNAME');
		$this->apre66_piecesmanquantes['password'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_PASSWORD');
		$this->apre66_piecesmanquantes['client'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_CLIENT');

		$this->fiche_candidature['transport'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_TRANSPORT');
		$this->fiche_candidature['from'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_FROM');
		$this->fiche_candidature['replyTo'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_REPLYTO');
		$this->fiche_candidature['to'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_TO');
		$this->fiche_candidature['subject'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_SUBJECT');
		$this->fiche_candidature['host'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_HOST');
		$this->fiche_candidature['port'] = intval(env('EMAIL_CONFIG_FICHE_CANDIDATURE_PORT'));
		$this->fiche_candidature['username'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_USERNAME');
		$this->fiche_candidature['password'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_PASSWORD');
		$this->fiche_candidature['client'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_CLIENT');

		$this->avis_technique_cui['transport'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_TRANSPORT');
		$this->avis_technique_cui['from'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_FROM');
		$this->avis_technique_cui['replyTo'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_REPLYTO');
		$this->avis_technique_cui['to'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_TO');
		$this->avis_technique_cui['subject'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_SUBJECT');
		$this->avis_technique_cui['host'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_HOST');
		$this->avis_technique_cui['port'] = intval(env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_PORT'));
		$this->avis_technique_cui['username'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_USERNAME');
		$this->avis_technique_cui['password'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_PASSWORD');
		$this->avis_technique_cui['client'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_CLIENT');

		$this->mail_employeur_cui['transport'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_TRANSPORT');
		$this->mail_employeur_cui['from'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_FROM');
		$this->mail_employeur_cui['replyTo'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_REPLYTO');
		$this->mail_employeur_cui['to'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_TO');
		$this->mail_employeur_cui['subject'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_SUBJECT');
		$this->mail_employeur_cui['host'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_HOST');
		$this->mail_employeur_cui['port'] = intval(env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_PORT'));
		$this->mail_employeur_cui['username'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_USERNAME');
		$this->mail_employeur_cui['password'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_PASSWORD');
		$this->mail_employeur_cui['client'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_CLIENT');

		$this->mail_decision_employeur_cui['transport'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_TRANSPORT');
		$this->mail_decision_employeur_cui['from'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_FROM');
		$this->mail_decision_employeur_cui['replyTo'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_REPLYTO');
		$this->mail_decision_employeur_cui['to'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_TO');
		$this->mail_decision_employeur_cui['subject'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_SUBJECT');
		$this->mail_decision_employeur_cui['host'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_HOST');
		$this->mail_decision_employeur_cui['port'] = intval(env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_PORT'));
		$this->mail_decision_employeur_cui['username'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_USERNAME');
		$this->mail_decision_employeur_cui['password'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_PASSWORD');
		$this->mail_decision_employeur_cui['client'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_CLIENT');

		$this->fiche_de_liaison['transport'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_TRANSPORT');
		$this->fiche_de_liaison['from'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_FROM');
		$this->fiche_de_liaison['replyTo'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_REPLYTO');
		$this->fiche_de_liaison['to'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_TO');
		$this->fiche_de_liaison['subject'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_SUBJECT');
		$this->fiche_de_liaison['host'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_HOST');
		$this->fiche_de_liaison['port'] = intval(env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_PORT'));
		$this->fiche_de_liaison['username'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_USERNAME');
		$this->fiche_de_liaison['password'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_PASSWORD');
		$this->fiche_de_liaison['client'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_CLIENT');

		$this->import_frsa['transport'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_TRANSPORT');
		$this->import_frsa['from'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_FROM');
		$this->import_frsa['replyTo'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_REPLYTO');
		$this->import_frsa['to'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_TO');
		$this->import_frsa['subject'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_SUBJECT');
		$this->import_frsa['host'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_HOST');
		$this->import_frsa['port'] = intval(env('EMAIL_CONFIG_MAIL_IMPORTFRSA_PORT'));
		$this->import_frsa['username'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_USERNAME');
		$this->import_frsa['password'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_PASSWORD');
		$this->import_frsa['client'] = env('EMAIL_CONFIG_MAIL_IMPORTFRSA_CLIENT');

		$this->piece_jointe['transport'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_TRANSPORT');
		$this->piece_jointe['from'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_FROM');
		$this->piece_jointe['replyTo'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_REPLYTO');
		$this->piece_jointe['to'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_TO');
		$this->piece_jointe['subject'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_SUBJECT');
		$this->piece_jointe['host'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_HOST');
		$this->piece_jointe['port'] = intval(env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_PORT'));
		$this->piece_jointe['username'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_USERNAME');
		$this->piece_jointe['password'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_PASSWORD');
		$this->piece_jointe['client'] = env('EMAIL_CONFIG_MAIL_PIECE_JOINTE_CLIENT');

		$this->mail_recours_gracieux['transport'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_TRANSPORT');
		$this->mail_recours_gracieux['from'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_FROM');
		$this->mail_recours_gracieux['replyTo'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_REPLYTO');
		$this->mail_recours_gracieux['to'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_TO');
		$this->mail_recours_gracieux['subject'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_SUBJECT');
		$this->mail_recours_gracieux['host'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_HOST');
		$this->mail_recours_gracieux['port'] = intval(env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_PORT'));
		$this->mail_recours_gracieux['username'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_USERNAME');
		$this->mail_recours_gracieux['password'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_PASSWORD');
		$this->mail_recours_gracieux['client'] = env('EMAIL_CONFIG_MAIL_RECOURS_GRACIEUX_CLIENT');

		$this->mail_titresuivi['transport'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_TRANSPORT');
		$this->mail_titresuivi['from'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_FROM');
		$this->mail_titresuivi['replyTo'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_REPLYTO');
		$this->mail_titresuivi['to'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_TO');
		$this->mail_titresuivi['subject'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_SUBJECT');
		$this->mail_titresuivi['host'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_HOST');
		$this->mail_titresuivi['port'] = intval(env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_PORT'));
		$this->mail_titresuivi['username'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_USERNAME');
		$this->mail_titresuivi['password'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_PASSWORD');
		$this->mail_titresuivi['client'] = env('EMAIL_CONFIG_MAIL_TITRE_SUIVI_CLIENT');

		$this->ldap_utilisateur_non_trouve['transport'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_TRANSPORT');
		$this->ldap_utilisateur_non_trouve['from'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_FROM');
		$this->ldap_utilisateur_non_trouve['replyTo'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_REPLYTO');
		$this->ldap_utilisateur_non_trouve['to'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_TO');
		$this->ldap_utilisateur_non_trouve['subject'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_SUBJECT');
		$this->ldap_utilisateur_non_trouve['host'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_HOST');
		$this->ldap_utilisateur_non_trouve['port'] = intval(env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_PORT'));
		$this->ldap_utilisateur_non_trouve['username'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_USERNAME');
		$this->ldap_utilisateur_non_trouve['password'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_PASSWORD');
		$this->ldap_utilisateur_non_trouve['client'] = env('EMAIL_CONFIG_MAIL_LDAP_UTILISATEUR_NON_TROUVE_CLIENT');
	}
}