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

	public function __construct () {
		$this->user_generation_mdp['transport'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_TRANSPORT');
		$this->user_generation_mdp['from'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_FROM');
		$this->user_generation_mdp['replyTo'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_REPLYTO');
		$this->user_generation_mdp['to'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_TO');
		$this->user_generation_mdp['subject'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_SUBJECT');
		$this->user_generation_mdp['host'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_HOST');
		$this->user_generation_mdp['username'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_USERNAME');
		$this->user_generation_mdp['password'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_PASSWORD');
		$this->user_generation_mdp['client'] = env('EMAIL_CONFIG_USER_GENERATION_MDP_CLIENT');

		$this->apre66_piecesmanquantes['transport'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_TRANSPORT');
		$this->apre66_piecesmanquantes['from'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_FROM');
		$this->apre66_piecesmanquantes['replyTo'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_REPLYTO');
		$this->apre66_piecesmanquantes['to'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_TO');
		$this->apre66_piecesmanquantes['subject'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_SUBJECT');
		$this->apre66_piecesmanquantes['host'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_HOST');
		$this->apre66_piecesmanquantes['username'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_USERNAME');
		$this->apre66_piecesmanquantes['password'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_PASSWORD');
		$this->apre66_piecesmanquantes['client'] = env('EMAIL_CONFIG_APRE66_PIECES_MANQUANTES_CLIENT');

		$this->fiche_candidature['transport'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_TRANSPORT');
		$this->fiche_candidature['from'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_FROM');
		$this->fiche_candidature['replyTo'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_REPLYTO');
		$this->fiche_candidature['to'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_TO');
		$this->fiche_candidature['subject'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_SUBJECT');
		$this->fiche_candidature['host'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_HOST');
		$this->fiche_candidature['username'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_USERNAME');
		$this->fiche_candidature['password'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_PASSWORD');
		$this->fiche_candidature['client'] = env('EMAIL_CONFIG_FICHE_CANDIDATURE_CLIENT');

		$this->avis_technique_cui['transport'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_TRANSPORT');
		$this->avis_technique_cui['from'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_FROM');
		$this->avis_technique_cui['replyTo'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_REPLYTO');
		$this->avis_technique_cui['to'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_TO');
		$this->avis_technique_cui['subject'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_SUBJECT');
		$this->avis_technique_cui['host'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_HOST');
		$this->avis_technique_cui['username'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_USERNAME');
		$this->avis_technique_cui['password'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_PASSWORD');
		$this->avis_technique_cui['client'] = env('EMAIL_CONFIG_AVIS_TECHNIQUE_CUI_CLIENT');

		$this->mail_employeur_cui['transport'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_TRANSPORT');
		$this->mail_employeur_cui['from'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_FROM');
		$this->mail_employeur_cui['replyTo'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_REPLYTO');
		$this->mail_employeur_cui['to'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_TO');
		$this->mail_employeur_cui['subject'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_SUBJECT');
		$this->mail_employeur_cui['host'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_HOST');
		$this->mail_employeur_cui['username'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_USERNAME');
		$this->mail_employeur_cui['password'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_PASSWORD');
		$this->mail_employeur_cui['client'] = env('EMAIL_CONFIG_MAIL_EMPLOYEUR_CUI_CLIENT');

		$this->mail_decision_employeur_cui['transport'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_TRANSPORT');
		$this->mail_decision_employeur_cui['from'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_FROM');
		$this->mail_decision_employeur_cui['replyTo'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_REPLYTO');
		$this->mail_decision_employeur_cui['to'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_TO');
		$this->mail_decision_employeur_cui['subject'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_SUBJECT');
		$this->mail_decision_employeur_cui['host'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_HOST');
		$this->mail_decision_employeur_cui['username'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_USERNAME');
		$this->mail_decision_employeur_cui['password'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_PASSWORD');
		$this->mail_decision_employeur_cui['client'] = env('EMAIL_CONFIG_MAIL_DECISION_EMPLOYEUR_CUI_CLIENT');

		$this->fiche_de_liaison['transport'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_TRANSPORT');
		$this->fiche_de_liaison['from'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_FROM');
		$this->fiche_de_liaison['replyTo'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_REPLYTO');
		$this->fiche_de_liaison['to'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_TO');
		$this->fiche_de_liaison['subject'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_SUBJECT');
		$this->fiche_de_liaison['host'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_HOST');
		$this->fiche_de_liaison['username'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_USERNAME');
		$this->fiche_de_liaison['password'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_PASSWORD');
		$this->fiche_de_liaison['client'] = env('EMAIL_CONFIG_MAIL_FICHE_DE_LIAISON_CLIENT');

	}
}