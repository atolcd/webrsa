<?php
/**
 * Code source de la classe TagShell.
 *
 * @package app.Console.Command
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'XShell', 'Console/Command' );
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeTime', 'Utility');

class RelancesShell extends Shell {

	/**
	 * Variables
	 */
	 public $variables = array ();
	 private $relance = array();

	/**
	 * Main entry point to this shell
	 *
	 * sudo -u apache ./lib/Cake/Console/cake relance
	 *
	 * @return void
	 */
	public function main() {
		require_once ('app/Config/webrsa.inc');
		$departement = Configure::read('Cg.departement');
		require_once ('app/Config/Cg'.$departement.'/Relances.php');
		$variables = Configure::read('relances.variables');
		foreach ($variables as $key => $value) {
			$this->variables[] = $key;
		}

		$Relance = ClassRegistry::init( 'Relance' );
		$relances = $Relance->find ('all', array ('conditions' => array ('Relance.actif' => 1)));
		$informations = array ();

		foreach ($relances as $relance) {
			// Récupération des informations des rendez-vous (rendez-vous, commission ep, ...)
			$informations = $this->{'_'.strtolower($relance['Relance']['relancetype'])} ($relance);
			$relance['infos'] = $informations;

			// Génération de l'action finale (fichier csv, envoi de mail, ...).
			$this->_final ($relance);
		}
	}

////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Relance d'un rendez-vous
	 *
	 * Récupération des personnes ayant un rendez-vous et donc à relancer.
	 *
	 * @param $relance array
	 * @return array
	 */
	private function _rdv ($relance) {
		$Model = ClassRegistry::init( 'Rendezvous' );
		$date = new DateTime ();
		$date->add(new DateInterval('P'.$relance['Relance']['nombredejour'].'D'));

		$rendezvouss = $Model->find (
			'all',
			array (
				'recursive' => 1,
				'conditions' => array (
					'Rendezvous.daterdv' => $date->format('Y-m-d'),// Testable avec '2018-12-15'
					'Rendezvous.statutrdv_id' => 17// Prévu
				),
			)
		);

		$retour = array ();
		foreach ($rendezvouss as $rendezvous) {
			$temporaire = array ();

			// Récupération des informations de la personne
			$temporaire['id_personne'] = $rendezvous['Personne']['id'];
			$temporaire['nom_complet'] = $rendezvous['Personne']['nom_complet'];
			$temporaire['numport'] = $rendezvous['Personne']['numport'];
			$temporaire['email'] = $rendezvous['Personne']['email'];
			$temporaire['daterdv'] = $rendezvous['Rendezvous']['daterdv'];
			$temporaire['heurerdv'] = $rendezvous['Rendezvous']['heurerdv'];
			$temporaire['lieurdv'] = ($rendezvous['Structurereferente']['lib_struc_mini'] == '' ? $rendezvous['Structurereferente']['ville'] : $rendezvous['Structurereferente']['lib_struc_mini']);

			// Vérifie s'il existe des infos dans les modes de contact
			$contacts = $this->_modesDeContact ($Model, $relance, $rendezvous['Personne']['foyer_id'], $temporaire);

			// Fait les contrôles en fonction du support
			$this->{'_controle'.$relance['Relance']['relancesupport']} ($relance, $contacts);

			foreach ($contacts as $contact) {
				$retour[$this->_defineSupport ($contact, $relance)][] = $contact;
			}
		}

		return $retour;
	}

	/**
	 * Relance d'une commission EP
	 *
	 * Récupération des personnes ayant une commission audition et donc à relancer.
	 *
	 * @param $relance array
	 * @return array
	 */
	private function _ep ($relance) {
		$Model = ClassRegistry::init( 'Commissionep' );
		$Dossierep = ClassRegistry::init( 'Dossierep' );
		$date = new DateTime ();
		$date->add(new DateInterval('P'.$relance['Relance']['nombredejour'].'D'));
		$formatDate = $date->format('Y-m-d');// Testable avec '2018-06-13'

		$commissions = $Model->find (
			'all',
			array (
				'recursive' => 1,
				'conditions' => array (
					'Commissionep.dateseance >= \''.$formatDate.' 00:00:00\'',
					'Commissionep.dateseance <= \''.$formatDate.' 23:59:59\'',
				),
				'joins' => array (
					array (
						'table' => 'passagescommissionseps',
						'alias' => 'Passagecommissionep',
						'type' => 'INNER',
						'conditions' => array (
							'Commissionep.id = Passagecommissionep.commissionep_id',
							'Passagecommissionep.heureseance >= \'00:00:00\'',// Ayant une heure de passage
							'Passagecommissionep.heureseance <= \'23:59:59\'',// Ayant une heure de passage
							'Passagecommissionep.impressionconvocation IS NOT NULL'// Ayant déjà eu l'impression de la convocation
						)
					)
				)
			)
		);

		$retour = array ();
		foreach ($commissions as $commission) {
			foreach ($commission['Passagecommissionep'] as $passage) {
				$temporaire = array ();

				// Informations commission EP
				$dateseance = new DateTime ($commission['Commissionep']['dateseance']);
				$daterdv = $dateseance->format('Y-m-d');
				$heurerdv = $passage['heureseance'];
				if ($heurerdv == '') {
					$heurerdv = $dateseance->format('H:i:s');
				}

				// Informations personne
				$dossierep = $Dossierep->find (
					'first',
					array (
						'recursive' => 1,
						'conditions' => array (
							'Dossierep.id = '.$passage['dossierep_id'],
						),
					)
				);

				$temporaire['id_personne'] = $dossierep['Personne']['id'];
				$temporaire['nom_complet'] = $dossierep['Personne']['nom_complet'];
				$temporaire['numport'] = $dossierep['Personne']['numport'];
				$temporaire['email'] = $dossierep['Personne']['email'];
				$temporaire['daterdv'] = $daterdv;
				$temporaire['heurerdv'] = $heurerdv;
				$temporaire['lieurdv'] = $commission['Commissionep']['villeseance'];

				// Vérifie s'il existe des infos dans les modes de contact
				$contacts = $this->_modesDeContact ($Model, $relance, $dossierep['Personne']['foyer_id'], $temporaire);

				// Fait les contrôles en fonction du support
				$this->{'_controle'.$relance['Relance']['relancesupport']} ($relance, $contacts);

				foreach ($contacts as $contact) {
					$retour[$this->_defineSupport ($contact, $relance)][] = $contact;
				}
			}
		}

		return $retour;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Fonction _modesDeContact
	 *
	 * Récupération des autres modes de contact d'une personne
	 */
	private function _modesDeContact ($Model, $relance, $foyer_id, $temporaire) {
		$contacts = array ();
		$contacts[] = $temporaire;
		$modeContact = $Model->query("SELECT * FROM modescontact WHERE foyer_id='".$foyer_id."'");
		$autresNumeros = array ();
		$autresNumeros[] = $temporaire['numport'];
		$autresMails = array ();
		$autresMails[] = $temporaire['email'];

		foreach ($modeContact as $value) {
			// Un autre numéro de portable
			if (isset ($value[0]['numtel']) 
				&& $value[0]['numtel'] != '' 
				&& !in_array ($value[0]['numtel'], $autresNumeros) 
				&& $value[0]['autorutitel'] == 'A'
			) {
				$ajout = $temporaire;
				$ajout['numport'] = $value[0]['numtel'];
				$autresNumeros[] = $value[0]['numtel'];
				$contacts[] = $ajout;
			}

			// Une autre adresse électronique
			if (isset ($value[0]['adrelec']) 
				&& $value[0]['adrelec'] != '' 
				&& !in_array ($value[0]['adrelec'], $autresMails) 
				&& $value[0]['autorutiadrelec'] == 'A'
			) {
				$ajout = $temporaire;
				$ajout['email'] = $value[0]['adrelec'];
				$autresMails[] = $value[0]['adrelec'];
				$contacts[] = $ajout;
			}
		}

		return $contacts;
	}

	/**
	 * Fonction _controleSMS
	 *
	 * Vérification et nettoyage des données de contact d'une personne pour un envoi de SMS
	 */
	private function _controleSMS ($relance, &$contacts) {
		if ($this->_existeDonnee($contacts, 'numport')) {

			$this->_suppDonneeNull($contacts, 'numport');
			$this->_suppDonneeDoublon($contacts, 'numport');

		}
		else if ($this->_existeDonnee($contacts, 'email')) {

			$this->_suppDonneeNull($contacts, 'email');
			$this->_suppDonneeDoublon($contacts, 'email');

		}
		else {
			$temporaire = $contacts[0];
			$contacts = array ();
			$contacts[0] = $temporaire;
		}
	}

	/**
	 * Fonction _controleEMAIL
	 *
	 * Vérification et nettoyage des données de contact d'une personne pour un envoi de MAIL
	 */
	private function _controleEMAIL ($relance, &$contacts) {
		if ($this->_existeDonnee($contacts, 'email')) {

			$this->_suppDonneeNull($contacts, 'email');
			$this->_suppDonneeDoublon($contacts, 'email');

		}
		else {
			$temporaire = $contacts[0];
			$contacts = array ();
			$contacts[0] = $temporaire;
		}
	}

	/**
	 * Fonction _existeDonnee
	 *
	 * Vérification de l'existence d'un numéro de téléphone ou mail pour un contact
	 */
	private function _existeDonnee ($contacts, $donnee = 'numport') {
		foreach ($contacts as $contact) {
			if (!is_null($contact[$donnee])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Fonction _suppDonneeNull
	 *
	 * Suppression des contacts dont le numéro de téléphone ou le mail est null pour une personne
	 */
	private function _suppDonneeNull (&$contacts, $donnee = 'numport') {
		$nombreSuppression = 0;

		foreach ($contacts as $key => $value) {
			if (is_null($value[$donnee])) {
				unset ($contacts[$key]);
				$nombreSuppression++;
			}
		}

		return $nombreSuppression;
	}

	/**
	 * Fonction _suppDonneeDoublon
	 *
	 * Suppression des contacts en doublon pour le numéro de téléphone ou le mail pour une personne
	 */
	private function _suppDonneeDoublon (&$contacts, $donnee = 'numport') {
		$nombreSuppression = 0;
		$doublons = array ();

		foreach ($contacts as $key => $value) {
			if (in_array ($value[$donnee], $doublons)) {
				unset ($contacts[$key]);
			}
			else {
				$doublons[] = $value[$donnee];
			}
		}

		return $nombreSuppression;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Format _formatLieu
	 *
	 * Formatage du nom d'un lieu
	 */
	private function _formatLieu ($texte, $date, $heure, $lieu) {
		$replace = array ($date, $heure, '');
		$texte = str_replace($this->variables, $replace, $texte);

		$restant = 160 - strlen($texte);

		return trim (substr ($lieu, 0, $restant));
	}

	/**
	 * Format _formatDate
	 *
	 * Formatage d'une date
	 */
	private function _formatDate ($numero) {
		$date = new DateTime ($numero);

		return $date->format('d/m/y');
	}

	/**
	 * Format _formatHeure
	 *
	 * Formatage de l'heure
	 */
	private function _formatHeure ($numero) {
		$heure = explode(':', $numero);

		return $heure[0].':'.$heure[1];
	}

	/**
	 * Format _formatTel
	 *
	 * Formatage du numéro de téléphone
	 */
	private function _formatTel ($numero) {
		$numero = preg_replace ('/[^0-9]/', '', $numero);

		if (substr ($numero, 0, 1) == '0') {
			$numero = substr ($numero, 1);
		}

		if (substr ($numero, 0, 3) != '+33') {
			$numero = '+33'.$numero;
		}

		return $numero;
	}

////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * _defineSupport
	 *
	 * Définition du support d'envoi utilisé en fonction des informations disponibles
	 * pour les contacts
	 */
	private function _defineSupport ($temporaire, $relance) {
		$support = 'ERROR';

		if($relance["Relance"]["relancesupport"] == 'SMS') {
			if ($temporaire['numport'] != '') {
				$support = 'SMS';
			} else if ($temporaire['email'] != '') {
				$support = 'MAIL';
			}
		}
		elseif($relance["Relance"]["relancesupport"] == 'EMAIL') {
			if ($temporaire['email'] != '') {
				$support = 'MAIL';
			}
		}

		return $support;
	}

	/**
	 * _final
	 *
	 * À partir du tableau final formaté, création du fichier des SMS et envoi des mails
	 */
	private function _final ($relance) {
		switch ($relance['Relance']['relancemode']) {
			case 'ORANGE_CONTACT_EVERYONE':
				if (isset ($relance['infos']['SMS'])) {
					$this->_sms ($relance['Relance'], $relance['infos']['SMS']);
				}

				if (isset ($relance['infos']['MAIL'])) {
					$this->_mail ($relance['Relance'], $relance['infos']['MAIL']);
				}

				if (isset ($relance['infos']['ERROR'])) {
					$this->_error ($relance['Relance'], $relance['infos']['ERROR']);
				}
			break;
			case 'EMAIL':
				if (isset ($relance['infos']['MAIL'])) {
					$this->_mail ($relance['Relance'], $relance['infos']['MAIL']);
				}

				if (isset ($relance['infos']['ERROR'])) {
					$this->_error ($relance['Relance'], $relance['infos']['ERROR']);
				}
			break;
		}
	}

	/**
	 * Réponse pour SMS
	 *
	 * @param $relance array
	 * @param $informations array
	 * @return void
	 */
	private function _sms ($relance, $informations) {
		// Initialisation du CSV
		$chemin = 'app/Vendor/relances/'.date ('Y-m-d_H:i:s').'_SMS_'.$relance['relancetype'].'.csv';
		$fichier_csv = fopen($chemin, 'w+');
		fprintf($fichier_csv, chr(0xEF).chr(0xBB).chr(0xBF));
		$separateur = ';';

		// Entête du CSV
		$entete = array ();
		$entete[] = $relance['contenu'];
		foreach ($this->variables as $value) {
			$entete[] = substr($value, 1, -1);
		}
		fputcsv($fichier_csv, $entete, $separateur);

		// Contenu du CSV
		foreach ($informations as $key => $value) {
			$portable = $this->_formatTel($value['numport']);
			$daterdv = $this->_formatDate($value['daterdv']);
			$heurerdv = $this->_formatHeure($value['heurerdv']);
			$lieurdv = $this->_formatLieu($relance['contenu'], $daterdv, $heurerdv, $value['lieurdv']);

			$temp = array ();
			$temp[] = $portable;
			$temp[] = $daterdv;
			$temp[] = $heurerdv;
			$temp[] = $lieurdv;

			fputcsv($fichier_csv, $temp, $separateur);
			$this->_log ($relance, $value, 'SMS');
		}

		// Fermeture du fichier CSV
		fclose($fichier_csv);

		// Envoi du fichier au prestataire
		$this->_envoiFichierAuPrestataire($chemin, $relance['relancetype']);
	}

	/**
	 * Réponse pour MAIL
	 *
	 * @param $relance array
	 * @param $informations array
	 * @return void
	 */
	private function _mail ($relance, $informations) {
		// TODO : Faire envoi de mail.

		$tabInfos	=	array();
		//dédoublonnage des emails
		foreach($informations as $index=>$value) {
			if(!isset($tabInfos[$value["email"]]) && filter_var($value["email"], FILTER_VALIDATE_EMAIL)) {
				$dateRDV = CakeTime::format($value["daterdv"], '%d/%m/%Y');
				$heureRDV = CakeTime::format($value["heurerdv"], '%Hh%M');

				$message = $relance["contenu"];
				$message = str_replace("#DateRV#", $dateRDV, $message);
				$message = str_replace("#HRV#", $heureRDV, $message);
				$message = str_replace("#LieuRV#", $value["lieurdv"], $message);

				$this->out ('Email pour '.$value["email"].' - '.$message);

				if(Configure::read('relances.prestataire.mail.envoi.allocataire')) {
					$from = array(Configure::read('relances.prestataire.mail.expéditeur'));
					$to = $value["email"];
					if (Configure::read('relances.prestataire.mail.phase.test')) {
						$to = Configure::read('relances.prestataire.mail.destinataire');
					}

					$this->out ('Email RÉEL pour '.$value["email"].' envoyé à '.$to.' - '.$message);

					$Email = new CakeEmail();
					$Email->from($from);
					$Email->emailFormat('html');
					$Email->to($to);
					$Email->subject(Configure::read('relances.prestataire.mail.sujet.allocataire'));
					$Email->send($message);
				}

				$tabInfos[$value["email"]]	=	$value;
				$this->_log ($relance, $value, 'MAIL');
			}
			else {
				$this->_log ($relance, $value, 'ERROR');
			}
		}
	}

	/**
	 * Réponse pour ERROR
	 *
	 * @param $relance array
	 * @param $informations array
	 * @return void
	 */
	private function _error ($relance, $informations) {
		foreach ($informations as $information) {
			$this->_log ($relance, $information, 'ERROR');
		}
	}

	/**
	 * Réponse pour ERROR
	 *
	 * @param $relance array
	 * @param $information array
	 * @param $statut string
	 * @return void
	 */
	private function _log ($relance, $information, $statut) {
		$tabInsert	=	array(
			'personne_id'=>$information['id_personne'],
			'nom_complet'=>$information['nom_complet'],
			'numport'=>$information['numport'],
			'email'=>$information['email'],
			'daterdv'=>$information['daterdv'],
			'heurerdv'=>$information['heurerdv'],
			'lieurdv'=>$information['lieurdv'],
			'relancetype'=>$relance['relancetype'],
			'nombredejour'=>$relance['nombredejour'],
			'contenu'=>$relance['contenu'],
			'statut'=>$statut,
			'support'=>$relance["relancesupport"],
			'mode'=>$relance["relancemode"]
		);

		$Relanceslogs = ClassRegistry::init( 'Relanceslogs' );
		$Relanceslogs->create();
		$Relanceslogs->save($tabInsert);
	}

	/**
	 * Envoi du fichier au prestataire
	 *
	 * @param $chemin string
	 * @param $relanceType string
	 */
	private function _envoiFichierAuPrestataire ($chemin, $relanceType) {

		$this->out ('Envoi du fichier SMS par email');

		if(Configure::read('relances.prestataire.mail.envoi.prestataire')) {
			$this->out ('Envoi RÉEL du fichier SMS par email');

			$this->_envoimail (
				Configure::read('relances.prestataire.mail.destinataire'),
				Configure::read('relances.prestataire.mail.sujet.sujet').' '.$relanceType,
				Configure::read('relances.prestataire.mail.message'),
				Configure::read('relances.prestataire.mail.domaine'),
				Configure::read('relances.prestataire.mail.expéditeur'),
				"html",
				"utf-8",
				"",
				$chemin);
		}
	}

	/**
	 * @return Retourne true,false
	 * @param string $to(destinataire)
	 * @param string $sujet
	 * @param string $message
	 * @param string $domaine
	 * @param string $sender(mail qui envoi doit etre valide sur le site)
	 * @param string(plain/html(default)) $type
	 * @param string(utf-8/iso-8859-1(default)) $iso
	 * @param string $fichier(chemin relatif jusqu'au fichier à joindre) pour un seul fichier mettre direct le fichier, pour plusieurs faire un array
	 * @param string(optionnel) $head
	 * @desc fonction incluant les entetes pour un envoi via la fonction mail de php sur amen
	*/
	private function _envoimail($to, $sujet, $message, $domaine, $sender, $type="html", $iso="iso-8859-1", $head="", $fichier="")
	{
		if(!empty($fichier)){
			if(is_array($fichier)){
				$boundary = "_".md5 (uniqid (rand()));
				$attached = "\n\n";

				foreach($fichier as $key => $fic){
					$partOfFile = explode("/",$fic);
					$nomPJ = $partOfFile[count($partOfFile)-1];
					$typemime=mime_content_type($fic);
					$attached_file = file_get_contents($fic);
					$attached_file = chunk_split(base64_encode($attached_file));
					$attached .= "--" .$boundary."\nContent-Type: $typemime; name=\"$nomPJ\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"$nomPJ\"\r\n\n".$attached_file;
				}

				$attached .= "--" . $boundary . "--";
			}else{
				$partOfFile = explode("/",$fichier);
				$nomPJ = $partOfFile[count($partOfFile)-1];
				$typemime=mime_content_type($fichier);
				$boundary = "_".md5 (uniqid (rand()));
				$attached_file = file_get_contents($fichier);
				$attached_file = chunk_split(base64_encode($attached_file));
				$attached = "\n\n". "--" .$boundary . "\nContent-Type: $typemime; name=\"$nomPJ\"\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"$nomPJ\"\r\n\n".$attached_file . "--" . $boundary . "--";
			}
		}

		$mail_mime = "Organization: $domaine\r\n";
		$mail_mime .= "From: $domaine <$sender>\r\n";
		$mail_mime .= "Reply-To: $sender\r\n";
		$mail_mime .= "Return-Path: <$sender>\r\n"; // En cas d' erreurs
		$mail_mime .= "X-Sender: <$sender>\r\n";

		if(!empty($fichier)){
			$mail_mime .= "MIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
			$body = "--". $boundary ."\nContent-Type: text/$type; charset=$iso\r\n\n".$message . $attached;
		}else{
			$mail_mime .= "Content-Type: text/$type; charset=\"$iso\"\n ";
			$mail_mime .= "Content-Transfer-Encoding: 8bit\n";
			$body = $message;
		}

		$mail_mime .= $head;
		$res = mail($to,$sujet,$body,$mail_mime);

		return $res;
	}

}