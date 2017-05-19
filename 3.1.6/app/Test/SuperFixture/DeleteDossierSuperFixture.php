<?php
	/**
	 * Code source de la classe DeleteDossierSuperFixture.
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */

	App::uses('SuperFixtureInterface', 'SuperFixture.Interface');

	/**
	 * DeleteDossierSuperFixture
	 *
	 * @package SuperFixture
	 * @subpackage Utility
	 */
	class DeleteDossierSuperFixture implements SuperFixtureInterface {
		/**
		 * Fixtures à charger en plus pour un bon fonctionnement
		 * 
		 * @var array
		 */
		public static $fixtures = array(
			'app.Dossier',
			'app.Foyer',
			'app.User',
			'app.Typeorient',
			'app.Structurereferente',
			'app.Referent',
			'app.Adresse',
			'app.Adressefoyer',
			'app.Situationdossierrsa',
			'app.Personne',
			'app.Prestation',
			'app.Detaildroitrsa',
			'app.Detailcalculdroitrsa',
			'app.Foyer',
			'app.Calculdroitrsa',
			'app.Orientstruct',
			'app.Cui',
			'app.AdresseCanton',
			'app.Canton',
			'app.Nonoriente66',
			'app.Avispcgdroitrsa',
			'app.Jeton',
			'app.Dernierdossierallocataire',
			'app.Infofinanciere',
			'app.Suiviinstruction',
			'app.VxTransfertpdv93',
			'app.Anomalie',
			'app.Controleadministratif',
			'app.Creance',
			'app.Evenement',
			'app.Modecontact',
			'app.Paiementfoyer',
			'app.Bilanparcours66',
			'app.Activite',
			'app.Allocationsoutienfamilial',
			'app.Avispcgpersonne',
			'app.Creancealimentaire',
			'app.Dossierep',
			'app.Dossiercov58',
			'app.Entretien',
			'app.Grossesse',
			'app.Informationeti',
			'app.Contratinsertion',
			'app.Autreavissuspension',
			'app.Autreavisradiation',
			'app.Nonrespectsanctionep93',
			'app.Signalementep93',
			'app.Contratcomplexeep93',
			'app.Sanctionep58',
			'app.Defautinsertionep66',
			'app.Cer93',
			'app.Compofoyercer93',
			'app.Diplomecer93',
			'app.Expprocer93',
			'app.Histochoixcer93',
			'app.Cer93Sujetcer93',
			'app.Dossierpcg66',
			'app.Propodecisioncer66',
			'app.ContratinsertionUser',
			'app.Apre',
			'app.Memo',
			'app.Orientation',
			'app.Parcours',
			'app.Infoagricole',
			'app.Detaildroitrsa',
		);
		
		/**
		 * Permet d'obtenir les informations nécéssaire pour charger la SuperFixture
		 * 
		 * @return array
		 */
		public static function getData() {
			return array();
		}
	}
