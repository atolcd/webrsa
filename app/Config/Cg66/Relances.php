<?php
	/**
	 * Relance : variables possibles
	 */
	Configure::write(
		'relances.variables',
		array (
			'#DateRV#' => 'Date du rendez-vous (JJ/MM/AA)',
			'#HRV#' => 'Heure du rendez-vous (HH:MM)',
			'#LieuRV#' => 'Lieu du rendez-vous',
		)
	);

	/**
	 * Adresse électronique du destinataire du mail contenant les SMS à envoyer.
	 */
	Configure::write('relances.prestataire.mail.destinataire', 'pla@atolcd.com');

	/**
	 * Objet du message du mail contenant les les SMS à envoyer.
	 */
	Configure::write('relances.prestataire.mail.sujet.sujet', 'Relance par SMS');

	/**
	 * Contenu du message du mail contenant les les SMS à envoyer.
	 */
	Configure::write('relances.prestataire.mail.message', 'Liste des allocataires à relancer.');

	/**
	 * Domaine du message du mail contenant les les SMS à envoyer.
	 */
	Configure::write('relances.prestataire.mail.domaine', 'CD 66 - Webrsa');

	/**
	 * Expéditeur du message du mail contenant les les SMS à envoyer.
	 */
	Configure::write('relances.prestataire.mail.expéditeur', 'p.lavigne@atolcd.com');

	/**
	 * Exécute ou non l'envoi du mail contenant les les SMS à envoyer.
	 */
	Configure::write('relances.prestataire.mail.envoi.prestataire', true);

	/**
	 * Exécute ou non l'envoi des mails de relance aux allocataires.
	 */
	Configure::write('relances.prestataire.mail.envoi.allocataire', true);

	/**
	 * Objet du message des mails de relance aux allocataires.
	 */
	Configure::write('relances.prestataire.mail.sujet.allocataire', 'Rappel - Convocation');

	/**
	 * Définit ou non un contexte de débogage où les adresses mail des allocataires
	 * sont remplacées par celle du destinataire du mail contenant les SMS à envoyer.
	 */
	Configure::write('relances.prestataire.mail.phase.test', true);
?>