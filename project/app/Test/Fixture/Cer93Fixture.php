<?php
	/**
	 * Code source de la classe Cer93Fixture.
	 *
	 * PHP 5.3
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * Classe Cer93Fixture.
	 *
	 * @package app.Test.Fixture
	 */
	class Cer93Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Cer93',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'contratinsertion_id' => 1,
				'user_id' => 1,
				'matricule' => '987654321000000',
				'dtdemrsa' => '2010-07-12',
				'qual' => 'MME',
				'nom' => 'DURAND',
				'prenom' => 'JEANNE',
				'nomnai' => 'DUPUIS',
				'dtnai' => '1956-12-05',
				'adresse' => '676 Rue Jacques Duclos',
				'codepos' => '93600',
				'nomcom' => 'AULNAY SOUS BOIS',
				'sitfam' => 'PAC',
				'natlog' => '0907',
				'incoherencesetatcivil' => 'Aucune incohérence',
				'inscritpe' => '1',
				'cmu' => 'non',
				'cmuc' => 'encours',
				'nivetu' => '1203',
				'numdemrsa' => '77777777793',
				'rolepers' => 'CJT',
				'identifiantpe' => '0605304120V',
				'positioncer' => '99valide',
				'formeci' => 'S',
				'datesignature' => '2010-10-25',
				'autresexps' => 'Autre expériences professionnelles',
				'isemploitrouv' => 'O',
				'metierexerce_id' => 1,
				'secteuracti_id' => 2,
				'naturecontrat_id' => 3,
				'dureehebdo' => 35,
				'dureecdd' => 1,
				'bilancerpcd' => NULL,
				'duree' => '3',
				'pointparcours' => 'aladate',
				'datepointparcours' => '2010-12-31',
				'pourlecomptede' => 'JACQUES ANTOINE',
				'observpro' => 'Observations du professionnel',
				'observbenef' => 'Obsrevations du bénéficiaire',
				'created' => '2010-10-25 11:00:00',
				'modified' => '2010-10-25 11:45:00',
			),
			array(
				'contratinsertion_id' => 2,
				// FIXME: les données de la personne 1, adresse, foyer, dossier, ...
				'user_id' => 1,
				'matricule' => '987654321000000',
				'dtdemrsa' => '2010-07-12',
				'qual' => 'MME',
				'nom' => 'DURAND',
				'prenom' => 'JEANNE',
				'nomnai' => 'DUPUIS',
				'dtnai' => '1956-12-05',
				'adresse' => '676 Rue Jacques Duclos',
				'codepos' => '93600',
				'nomcom' => 'AULNAY SOUS BOIS',
				'sitfam' => 'PAC',
				'natlog' => '0907',
				'incoherencesetatcivil' => 'Aucune incohérence',
				'inscritpe' => '1',
				'cmu' => 'non',
				'cmuc' => 'encours',
				'nivetu' => '1203',
				'numdemrsa' => '77777777793',
				'rolepers' => 'CJT',
				'identifiantpe' => '0605304120V',
				'positioncer' => '02attdecisioncpdv',
				'formeci' => null,
				'datesignature' => null,
				'autresexps' => 'Autre expériences professionnelles',
				'isemploitrouv' => 'O',
				'metierexerce_id' => 1,
				'secteuracti_id' => 2,
				'naturecontrat_id' => 3,
				'dureehebdo' => 35,
				'dureecdd' => 1,
				'bilancerpcd' => NULL,
				'duree' => '3',
				'pointparcours' => 'aladate',
				'datepointparcours' => '2010-12-31',
				'pourlecomptede' => 'JACQUES ANTOINE',
				'observpro' => 'Observations du professionnel',
				'observbenef' => 'Obsrevations du bénéficiaire',
				'created' => '2010-10-25 11:00:00',
				'modified' => '2010-10-25 11:45:00',
			),
			array(
				'contratinsertion_id' => 3,
				// FIXME: les données de la personne 3, adresse, foyer, dossier, ...
				'user_id' => 1,
				'matricule' => '987654321000000',
				'dtdemrsa' => '2010-07-12',
				'qual' => 'MME',
				'nom' => 'DURAND',
				'prenom' => 'JEANNE',
				'nomnai' => 'DUPUIS',
				'dtnai' => '1956-12-05',
				'adresse' => '676 Rue Jacques Duclos',
				'codepos' => '93600',
				'nomcom' => 'AULNAY SOUS BOIS',
				'sitfam' => 'PAC',
				'natlog' => '0907',
				'incoherencesetatcivil' => 'Aucune incohérence',
				'inscritpe' => '1',
				'cmu' => 'non',
				'cmuc' => 'encours',
				'nivetu' => '1203',
				'numdemrsa' => '77777777793',
				'rolepers' => 'CJT',
				'identifiantpe' => '0605304120V',
				'positioncer' => '00enregistre',
				'formeci' => null,
				'datesignature' => null,
				'autresexps' => 'Autre expériences professionnelles',
				'isemploitrouv' => 'O',
				'metierexerce_id' => 1,
				'secteuracti_id' => 2,
				'naturecontrat_id' => 3,
				'dureehebdo' => 35,
				'dureecdd' => 1,
				'bilancerpcd' => NULL,
				'duree' => '3',
				'pointparcours' => 'aladate',
				'datepointparcours' => '2010-12-31',
				'pourlecomptede' => 'JACQUES ANTOINE',
				'observpro' => 'Observations du professionnel',
				'observbenef' => 'Obsrevations du bénéficiaire',
				'created' => '2010-10-25 11:00:00',
				'modified' => '2010-10-25 11:45:00',
			),
		);
	}
?>