<?php
	/**
	 * Code source de la classe Indicateurmensuel.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Indicateurmensuel ...
	 *
	 * @package app.Model
	 */
	class Indicateurmensuel extends AppModel
	{
		public $name = 'Indicateurmensuel';

		public $useTable = false;

		public $actsAs = array(
			'Conditionnable'
		);

		public $validate = array(
			'lib_struc' => array(
				'inList' => array(
					'rule' => array('inList', array('','FLU', 'MAN')
					)
				)
			),
		);

		public	$vagues	=	array();
		private	$communes	=	'0';

		/**
		 *
		 * @param string $sql
		 * @return array
		 */
		protected function _query( $sql ) {
			$results = $this->query( $sql );
			return Set::combine( $results, '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbrDossiersInstruits( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM dossiers.dtdemrsa) AS mois, EXTRACT(YEAR FROM dossiers.dtdemrsa) AS annee, COUNT(dossiers.id) AS indicateur
						FROM dossiers
						WHERE dossiers.dtdemrsa BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbrDossiersRejetesCaf( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM dossiers.dtdemrsa) AS mois, EXTRACT(YEAR FROM dossiers.dtdemrsa) AS annee, COUNT(dossiers.id) AS indicateur
						FROM dossiers
							INNER JOIN situationsdossiersrsa ON situationsdossiersrsa.dossier_id = dossiers.id
						WHERE situationsdossiersrsa.etatdosrsa = \'1\'
							AND dossiers.dtdemrsa BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbrOuverturesDroits( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM dossiers.dtdemrsa) AS mois, EXTRACT(YEAR FROM dossiers.dtdemrsa) AS annee, COUNT(dossiers.id) AS indicateur
						FROM dossiers
							INNER JOIN situationsdossiersrsa ON situationsdossiersrsa.dossier_id = dossiers.id
						WHERE situationsdossiersrsa.etatdosrsa IN ( \'2\', \'3\', \'4\' )
							AND dossiers.dtdemrsa BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbrAllocatairesDroitsEtDevoirs( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM dossiers.dtdemrsa) AS mois, EXTRACT(YEAR FROM dossiers.dtdemrsa) AS annee, COUNT(prestations.id) AS indicateur
						FROM prestations
							INNER JOIN personnes
								ON  prestations.personne_id = personnes.id
							INNER JOIN calculsdroitsrsa
								ON  calculsdroitsrsa.personne_id = personnes.id
							INNER JOIN foyers
								ON  personnes.foyer_id = foyers.id
							INNER JOIN dossiers
								ON  foyers.dossier_id = dossiers.id
						WHERE calculsdroitsrsa.toppersdrodevorsa = \'1\'
							AND prestations.natprest = \'RSA\'
							AND prestations.rolepers IN ( \'DEM\', \'CJT\' )
							AND dossiers.dtdemrsa BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @param string $type
		 * @return array
		 */
		protected function _nbrPreorientations( $annee, $type ) {
			$sql = 'SELECT EXTRACT(MONTH FROM orientsstructs.date_propo) AS mois, EXTRACT(YEAR FROM orientsstructs.date_propo) AS annee, COUNT(orientsstructs.id) AS indicateur
						FROM orientsstructs
						WHERE orientsstructs.statut_orient = \'Orienté\'
							AND orientsstructs.propo_algo IN
								( SELECT typesorients.id
									FROM typesorients
										WHERE typesorients.lib_type_orient = \''.$type.'\'
								)
							AND orientsstructs.date_propo BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _delaiOuvertureNotification( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM dossiers.dtdemrsa) AS mois, EXTRACT(YEAR FROM dossiers.dtdemrsa) AS annee, AVG( ABS(orientsstructs.date_impression - dossiers.dtdemrsa ) ) AS indicateur
						FROM orientsstructs
							INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
							INNER JOIN foyers ON personnes.foyer_id = foyers.id
							INNER JOIN dossiers ON foyers.dossier_id = dossiers.id
						WHERE orientsstructs.statut_orient = \'Orienté\'
							AND orientsstructs.date_impression IS NOT NULL
							AND dossiers.dtdemrsa IS NOT NULL
							AND dossiers.dtdemrsa BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _delaiNotificationSignature( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM orientsstructs.date_impression) AS mois, EXTRACT(YEAR FROM orientsstructs.date_impression) AS annee, AVG( ABS( contratsinsertion.date_saisi_ci - orientsstructs.date_impression ) ) AS indicateur
						FROM orientsstructs
							INNER JOIN contratsinsertion ON contratsinsertion.personne_id = orientsstructs.personne_id
						WHERE orientsstructs.date_impression BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
							AND orientsstructs.date_impression IS NOT NULL
							AND contratsinsertion.date_saisi_ci IS NOT NULL
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _montantsIndusConstates( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM infosfinancieres.moismoucompta) AS mois, EXTRACT(YEAR FROM infosfinancieres.moismoucompta) AS annee, SUM(infosfinancieres.mtmoucompta) AS indicateur
						FROM infosfinancieres
						WHERE infosfinancieres.type_allocation = \'IndusConstates\'
							AND infosfinancieres.moismoucompta BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
							AND infosfinancieres.moismoucompta IS NOT NULL
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _montantsIndusTransferes( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM infosfinancieres.moismoucompta) AS mois, EXTRACT(YEAR FROM infosfinancieres.moismoucompta) AS annee, SUM(infosfinancieres.mtmoucompta) AS indicateur
						FROM infosfinancieres
						WHERE infosfinancieres.type_allocation = \'IndusTransferesCG\'
							AND infosfinancieres.moismoucompta BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
							AND infosfinancieres.moismoucompta IS NOT NULL
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbrCiNouveauxEntrantsCg( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM contratsinsertion.date_saisi_ci) AS mois, EXTRACT(YEAR FROM contratsinsertion.date_saisi_ci) AS annee, COUNT(contratsinsertion.id) AS indicateur
						FROM contratsinsertion
							INNER JOIN personnes ON personnes.id = contratsinsertion.personne_id
							INNER JOIN foyers ON foyers.id = personnes.foyer_id
							INNER JOIN dossiers ON dossiers.id = foyers.dossier_id
						WHERE ( AGE( contratsinsertion.date_saisi_ci, dossiers.dtdemrsa ) <= INTERVAL \'2 months\' )
							AND contratsinsertion.num_contrat = \'PRE\'
							AND contratsinsertion.rg_ci = 1
							AND contratsinsertion.date_saisi_ci IS NOT NULL
							AND contratsinsertion.date_saisi_ci BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbrSuspensionsDroits( $annee ) {
			$sql = 'SELECT EXTRACT(MONTH FROM suspensionsdroits.ddsusdrorsa) AS mois, EXTRACT(YEAR FROM suspensionsdroits.ddsusdrorsa) AS annee, COUNT(suspensionsdroits.id) AS indicateur
						FROM suspensionsdroits
						WHERE suspensionsdroits.ddsusdrorsa BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
							AND suspensionsdroits.ddsusdrorsa IS NOT NULL
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er RDV individuel prévu par le PIE
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvIndivPrevuPIE( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  $this->_addSearchCommune($commune);
			$sql =	  'SELECT
							EXTRACT(MONTH FROM orientsstructs.date_valid) AS mois,
							EXTRACT(YEAR FROM orientsstructs.date_valid) AS annee,
							AVG( ABS(rdv.daterdv - orientsstructs.date_valid ) ) AS indicateur
						FROM orientsstructs
							INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
							INNER JOIN foyers ON personnes.foyer_id = foyers.id
							INNER JOIN rendezvous rdv ON rdv.personne_id=personnes.id
							LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
							LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
						WHERE
							orientsstructs.typeorient_id=1
							'.$where.'
							AND orientsstructs.date_valid BETWEEN \''.$annee.'-01-01\' AND \''.($annee+1).'-12-31\'
							AND rdv.daterdv>=orientsstructs.date_propo
							AND rdv.statutrdv_id=2
							AND rdv.typerdv_id=15
							AND rdv.id IN (
								SELECT id FROM rendezvous
								WHERE personne_id=personnes.id AND daterdv>=orientsstructs.date_valid
								ORDER BY id
								LIMIT 1
							)
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er RDV individuel honoré par le PIE
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvIndivHonorePIE( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  $this->_addSearchCommune($commune);
			$sql =	  'SELECT
							EXTRACT(MONTH FROM orientsstructs.date_valid) AS mois,
							EXTRACT(YEAR FROM orientsstructs.date_valid) AS annee,
							AVG( ABS(rdv.daterdv - orientsstructs.date_valid ) ) AS indicateur
						FROM orientsstructs
							INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
							INNER JOIN foyers ON personnes.foyer_id = foyers.id
							INNER JOIN rendezvous rdv ON rdv.personne_id=personnes.id
							LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
							LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
						WHERE
							orientsstructs.typeorient_id=1
							'.$where.'
							AND orientsstructs.date_valid BETWEEN \''.$annee.'-01-01\' AND \''.($annee+1).'-12-31\'
							AND rdv.daterdv>=orientsstructs.date_propo
							AND rdv.statutrdv_id=1
							AND rdv.typerdv_id=15
							AND rdv.id IN (
								SELECT id FROM rendezvous
								WHERE personne_id=personnes.id AND daterdv>=orientsstructs.date_valid
								ORDER BY id
								LIMIT 1
							)
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er RDV collectif prévu par le PIE
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvCollPrevuPIE( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  $this->_addSearchCommune($commune);
			$sql =	  'SELECT
							EXTRACT(MONTH FROM orientsstructs.date_valid) AS mois,
							EXTRACT(YEAR FROM orientsstructs.date_valid) AS annee,
							AVG( ABS(rdv.daterdv - orientsstructs.date_valid ) ) AS indicateur
						FROM orientsstructs
							INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
							INNER JOIN foyers ON personnes.foyer_id = foyers.id
							INNER JOIN rendezvous rdv ON rdv.personne_id=personnes.id
							LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
							LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
						WHERE
							orientsstructs.typeorient_id=1
							'.$where.'
							AND orientsstructs.date_valid BETWEEN \''.$annee.'-01-01\' AND \''.($annee+1).'-12-31\'
							AND rdv.daterdv>=orientsstructs.date_propo
							AND rdv.statutrdv_id=2
							AND rdv.typerdv_id=14
							AND rdv.id IN (
								SELECT id FROM rendezvous
								WHERE personne_id=personnes.id AND daterdv>=orientsstructs.date_valid
								ORDER BY id
								LIMIT 1
							)
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er RDV collectif honoré par le PIE
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvCollHonorePIE( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  $this->_addSearchCommune($commune);
			$sql =	  'SELECT
							EXTRACT(MONTH FROM orientsstructs.date_valid) AS mois,
							EXTRACT(YEAR FROM orientsstructs.date_valid) AS annee,
							AVG( ABS(rdv.daterdv - orientsstructs.date_valid ) ) AS indicateur
						FROM orientsstructs
							INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
							INNER JOIN foyers ON personnes.foyer_id = foyers.id
							INNER JOIN rendezvous rdv ON rdv.personne_id=personnes.id
							LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
							LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
						WHERE
							orientsstructs.typeorient_id=1
							'.$where.'
							AND orientsstructs.date_valid BETWEEN \''.$annee.'-01-01\' AND \''.($annee+1).'-12-31\'
							AND rdv.daterdv>=orientsstructs.date_propo
							AND rdv.statutrdv_id=1
							AND rdv.typerdv_id=14
							AND rdv.id IN (
								SELECT id FROM rendezvous
								WHERE personne_id=personnes.id AND daterdv>=orientsstructs.date_valid
								ORDER BY id
								LIMIT 1
							)
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er CER établi par le PIE
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsCerEtabliPIE( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  $this->_addSearchCommune($commune);
			$sql =	  'SELECT
							EXTRACT(MONTH FROM orientsstructs.date_valid) AS mois,
							EXTRACT(YEAR FROM orientsstructs.date_valid) AS annee,
							AVG( ABS(cer.dd_ci - orientsstructs.date_valid ) ) AS indicateur
						FROM orientsstructs
							INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
							INNER JOIN foyers ON personnes.foyer_id = foyers.id
							INNER JOIN contratsinsertion cer ON cer.personne_id=personnes.id
							LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
							LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
						WHERE
							orientsstructs.typeorient_id=1
							'.$where.'
							AND orientsstructs.date_valid BETWEEN \''.$annee.'-01-01\' AND \''.($annee+1).'-12-31\'
							AND cer.dd_ci>=orientsstructs.date_propo
							AND cer.id IN (
								SELECT id FROM contratsinsertion
								WHERE personne_id=personnes.id AND dd_ci>=orientsstructs.date_valid
								ORDER BY id
								LIMIT 1
							)
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre le 1er RDV individuel honoré PIE et le 1er CER établi par le PIE
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvIndivCerEtabliePIE( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  $this->_addSearchCommune($commune);

			$sql =	  'SELECT
							EXTRACT(MONTH FROM cer.dd_ci) AS mois,
							EXTRACT(YEAR FROM cer.dd_ci) AS annee,
							AVG( ABS(cer.dd_ci - rdv.daterdv ) ) AS indicateur
						FROM rendezvous rdv
							INNER JOIN personnes ON rdv.personne_id = personnes.id
							INNER JOIN foyers ON personnes.foyer_id = foyers.id
							INNER JOIN contratsinsertion cer ON cer.personne_id=personnes.id
							INNER JOIN orientsstructs ON orientsstructs.personne_id = personnes.id
							LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
							LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
						WHERE
							rdv.daterdv BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
							'.$where.'
							AND cer.dd_ci>=rdv.daterdv
							AND rdv.id IN (
								SELECT id FROM rendezvous
								WHERE personne_id=personnes.id AND rdv.daterdv BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
								ORDER BY id
								LIMIT 1
							)
							AND cer.id IN (
								SELECT id FROM contratsinsertion
								WHERE personne_id=personnes.id AND cer.dd_ci BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
								ORDER BY id
								LIMIT 1
							)
							AND rdv.daterdv BETWEEN \''.$annee.'-01-01\' AND \''.($annee+1).'-12-31\'
						GROUP BY annee, mois
						ORDER BY annee, mois;';
			return $this->_query( $sql );
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		public function liste( $annee ) {
			$annee  =   Sanitize::clean( $annee, array( 'encode' => false ) );
			$results['nbrDossiersInstruits'] = $this->_nbrDossiersInstruits( $annee );
			$results['nbrDossiersRejetesCaf'] = $this->_nbrDossiersRejetesCaf( $annee );
			$results['nbrOuverturesDroits'] = $this->_nbrOuverturesDroits( $annee );
			$results['nbrAllocatairesDroitsEtDevoirs'] = $this->_nbrAllocatairesDroitsEtDevoirs( $annee );
			$results['nbrPreorientationsEmploi'] = $this->_nbrPreorientations( $annee, 'Emploi' );
			$results['nbrPreorientationsSocial'] = $this->_nbrPreorientations( $annee, 'Social' );
			$results['nbrPreorientationsSocioprofessionnelle'] = $this->_nbrPreorientations( $annee, 'Socioprofessionnelle' );
			$results['delaiOuvertureNotification'] = $this->_delaiOuvertureNotification( $annee );
			$results['delaiNotificationSignature'] = $this->_delaiNotificationSignature( $annee );
			$results['montantsIndusConstates'] = $this->_montantsIndusConstates( $annee );
			$results['montantsIndusTransferes'] = $this->_montantsIndusTransferes( $annee );
			$results['nbrCiNouveauxEntrantsCg'] = $this->_nbrCiNouveauxEntrantsCg( $annee );
			$results['nbrSuspensionsDroits'] = $this->_nbrSuspensionsDroits( $annee );

			return $results;
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		public function listeRdvCer( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$annee  =   Sanitize::clean( $annee, array( 'encode' => false ) );
			$results['nbMoyJrsRdvIndivPrevuPIE'] = $this->_nbMoyJrsRdvIndivPrevuPIE( $annee, $structureReferente, $communautesSrs, $commune );
			$results['nbMoyJrsRdvIndivHonorePIE'] = $this->_nbMoyJrsRdvIndivHonorePIE($annee, $structureReferente, $communautesSrs, $commune);
			$results['nbMoyJrsRdvCollPrevuPIE'] = $this->_nbMoyJrsRdvCollPrevuPIE($annee, $structureReferente, $communautesSrs, $commune);
			$results['nbMoyJrsRdvCollHonorePIE'] = $this->_nbMoyJrsRdvCollHonorePIE($annee, $structureReferente, $communautesSrs, $commune);
			$results['nbMoyJrsCerEtabliPIE'] = $this->_nbMoyJrsCerEtabliPIE($annee, $structureReferente, $communautesSrs, $commune);
			$results['nbMoyJrsRdvIndivCerEtabliePIE'] = $this->_nbMoyJrsRdvIndivCerEtabliePIE($annee, $structureReferente, $communautesSrs, $commune);

			return $results;
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		public function listeRdvCerVagues( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$annee  =   Sanitize::clean( $annee, array( 'encode' => false ) );
			if($commune!='')
				$this->setCommunes($commune);
			$results['nbMoyJrsRdvIndivNonHonorePIEVagues'] = $this->_nbMoyJrsRdvIndivNonHonorePIEVagues( $annee, $structureReferente, $communautesSrs, $commune );
			$results['nbMoyJrsRdvIndivHonorePIEVagues'] = $this->_nbMoyJrsRdvIndivHonorePIEVagues( $annee, $structureReferente, $communautesSrs, $commune );
			$results['nbMoyJrsRdvCollectifNonHonorePIEVagues'] = $this->_nbMoyJrsRdvCollectifNonHonorePIEVagues( $annee, $structureReferente, $communautesSrs, $commune );
			$results['nbMoyJrsRdvCollectifHonorePIEVagues'] = $this->_nbMoyJrsRdvCollectifHonorePIEVagues( $annee, $structureReferente, $communautesSrs, $commune );
			$results['nbMoyJrsCerEtabliPIE'] = $this->_nbMoyJrsCerEtabliPIEVagues($annee, $structureReferente, $communautesSrs, $commune);
			$results['nbMoyJrsRdvIndivCerEtabliePIE'] = $this->_nbMoyJrsRdvIndivCerEtabliePIEVagues($annee, $structureReferente, $communautesSrs, $commune);

			return $results;
		}

		public function setCommunes($communes) {
			$sql	=	'SELECT foyers2.id
						FROM foyers as foyers2
						INNER JOIN adressesfoyers as adressesfoyers2 ON adressesfoyers2.foyer_id=foyers2.id
						INNER JOIN adresses as adresses2 ON adresses2.id=adressesfoyers2.adresse_id
						WHERE adresses2.numcom=\''.$communes.'\'
						GROUP BY foyers2.id';
			$result	=	$this->query($sql);
			foreach($result as $index=>$value) {
				$this->communes	.=	', '.$value[0]["id"];
			}
		}

		public function getCommunes() {
			return $this->communes;
		}

		/**
		 *
		 * @param integer $annee
		 * @return array
		 */
		public function listeVagues( $annee ) {
			$sql 	=	'SELECT dateDebut, dateFin
						FROM vaguesdorientations
						WHERE dateDebut BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
						ORDER BY dateDebut ASC';
			$result	=	$this->query( $sql );

			foreach($result as $index=>$value)
				$this->vagues[($index+1)]	=	array('dateDebut'=>$value[0]["datedebut"], 'dateFin'=>$value[0]["datefin"]);

			return $this->vagues;
		}

		/**
		 *
		 * @param integer $commune
		 * @return string
		 */
		public function _addSearchCommune($commune) {
			return  ' AND foyers.id IN (
						SELECT foyers2.id
						FROM foyers as foyers2
						INNER JOIN adressesfoyers as adressesfoyers2 ON adressesfoyers2.foyer_id=foyers2.id
						INNER JOIN adresses as adresses2 ON adresses2.id=adressesfoyers2.adresse_id
						WHERE adresses2.numcom=\''.$commune.'\'
						GROUP BY foyers2.id
					)';
		}

		// ---------------------------------------------------------------------

		/**
		 * Filtre par service instructeur.
		 *
		 * @param array $search
		 * @return string
		 */
		protected function _conditionServiceInstructeur( $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$serviceinstructeur_id = trim( Hash::get( $search, 'Indicateurmensuel.serviceinstructeur' ) );

			if( !empty( $serviceinstructeur_id ) ) {
				$sq = $Dossier->Suiviinstruction->sq(
					array(
						'alias' => 'suivisinstruction',
						'fields' => array( 'suivisinstruction.dossier_id' ),
						'contain' => false,
						'joins' => array(
							array_words_replace(
								$Dossier->Suiviinstruction->join( 'Serviceinstructeur', array( 'type' => 'INNER' ) ),
								array( 'Suiviinstruction' => 'suivisinstruction', 'Serviceinstructeur' => 'servicesinstructeurs' )
							)
						),
						'conditions' => array(
							'servicesinstructeurs.id' => $serviceinstructeur_id
						)
					)
				);

				return "Dossier.id IN ( {$sq} )";
			}

			return null;
		}

		/**
		 *
		 * @param boolean $primo
		 * @return string
		 */
		protected function _conditionPrimoArrivant( $primo ) {
			if( is_null( $primo ) ) {
				return null;
			}

			$Dossier = ClassRegistry::init( 'Dossier' );

			$sq = $Dossier->sq(
				array(
					'alias' => 'dossierspcds',
					'fields' => array( 'dossierspcds.id' ),
					'contain' => false,
					'joins' => array(
						array_words_replace(
							$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
							array( 'Foyer' => 'foyerspcds', 'Dossier' => 'dossierspcds' )
						),
						array_words_replace(
							$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
							array( 'Foyer' => 'foyerspcds', 'Personne' => 'personnespcds' )
						),
						array_words_replace(
							$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER', 'conditions' => array( 'Prestation.rolepers' => array( 'DEM', 'CJT' ) ) ) ),
							array( 'Personne' => 'personnespcds', 'Prestation' => 'prestationspcds' )
						),
					),
					'conditions' => array(
						'Personne.foyer_id <> personnespcds.foyer_id',
						'Dossier.dtdemrsa > dossierspcds.dtdemrsa',
						'OR' => array(
							array(
								'nir_correct13( Personne.nir )',
								'nir_correct13( personnespcds.nir )',
								'SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM personnespcds.nir ) FROM 1 FOR 13 )',
								'Personne.dtnai = personnespcds.dtnai'
							),
							array(
								'UPPER( Personne.nom ) = UPPER( personnespcds.nom )',
								'UPPER( Personne.prenom ) = UPPER( personnespcds.prenom )',
								'Personne.dtnai = personnespcds.dtnai'
							),
						)
					)
				)
			);

			if( $primo ) {
				return "NOT EXISTS( {$sq} )";
			}
			else {
				return "EXISTS( {$sq} )";
			}
		}

		/**
		 *
		 * @param array $search
		 * @param boolean $primo
		 * @return string
		 */
		protected function _nombreAllocatairesNouveauxEntrants( $search, $primo ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			// Filtre sur l'année
			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );

			// ... à remplacer
			$conditions = array(
				$this->_conditionPrimoArrivant( $primo ),
				"Dossier.dtdemrsa BETWEEN '{$annee}-01-01' AND '{$annee}-12-31'",
				array(
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.rgadr' => '01',
					)
				),
				'Prestation.rolepers' => array( 'DEM', 'CJT' )
			);

			// Condition sur le service instructeur
			$conditions[] = $this->_conditionServiceInstructeur( $search );

			// Conditions sur l'adresse de l'allocataire
			$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

			$querydata = array(
				'fields' => array(
					'EXTRACT(YEAR FROM "Dossier"."dtdemrsa") AS "annee"',
					'EXTRACT(MONTH FROM "Dossier"."dtdemrsa") AS "mois"',
					'COUNT(DISTINCT("Personne"."id")) AS "indicateur"'
				),
				'joins' => array(
					$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
				),
				'contain' => false,
				'conditions' => $conditions,
				'group' => array(
					'annee',
					'mois'
				),
				'order' => array(
					'annee',
					'mois'
				),
			);

			return Set::combine( $Dossier->find( 'all', $querydata ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 * TODO: ne compter que le nombre de dossiers ?
		 * TODO: conditions search (cf. ci-dessus)
		 *
		 * @param type $annee
		 * @param array $etatdosrsa
		 * @return string
		 */
		protected function _nombreDemandeursDroits( $search, array $etatdosrsa ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM' ),
					'Situationdossierrsa.etatdosrsa' => $etatdosrsa,
					'Dossier.dtdemrsa <' => $moissuivant,
					'NOW() >=' => date( 'Y-m-d', strtotime( "-1 month", strtotime( $moissuivant ) ) ),
				);

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT(DISTINCT("Personne"."id")) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' GROUP BY "mois" ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param array $search
		 * @param array $naturesInclues1
		 * @param array $naturesInclues2
		 * @param array $naturesExclues
		 * @return array
		 */
		protected function _nombreDemandeursNatpf( $search, array $naturesInclues1, array $naturesInclues2 = array(), array $naturesExclues = array() ) {
			// DetailcalculdroitrsaNatpf
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM' ),
					'Situationdossierrsa.etatdosrsa' => 2,
					'Dossier.dtdemrsa <' => $moissuivant,
					'NOW() >=' => date( 'Y-m-d', strtotime( "-1 month", strtotime( $moissuivant ) ) ),
				);

				// Filtre par nature de prestation
				// 1°) Natures inclues 1
				if( !empty( $naturesInclues1 ) ) {
					$sq = $Dossier->Detaildroitrsa->Detailcalculdroitrsa->sq(
						array(
							'alias' => 'detailscalculsdroitsrsa',
							'fields' => array( 'detailscalculsdroitsrsa.detaildroitrsa_id' ),
							'contain' => false,
							'conditions' => array(
								'detailscalculsdroitsrsa.detaildroitrsa_id = Detaildroitrsa.id',
								'detailscalculsdroitsrsa.natpf' => $naturesInclues1
							)
						)
					);
					$conditions[] = "Detaildroitrsa.id IN ( {$sq} )";
				}
				// 2°) Natures inclues 2
				if( !empty( $naturesInclues2 ) ) {
					$sq = $Dossier->Detaildroitrsa->Detailcalculdroitrsa->sq(
						array(
							'alias' => 'detailscalculsdroitsrsa',
							'fields' => array( 'detailscalculsdroitsrsa.detaildroitrsa_id' ),
							'contain' => false,
							'conditions' => array(
								'detailscalculsdroitsrsa.detaildroitrsa_id = Detaildroitrsa.id',
								'detailscalculsdroitsrsa.natpf' => $naturesInclues2
							)
						)
					);
					$conditions[] = "Detaildroitrsa.id IN ( {$sq} )";
				}
				// 3°) Natures exclues
				if( !empty( $naturesExclues ) ) {
					$sq = $Dossier->Detaildroitrsa->Detailcalculdroitrsa->sq(
						array(
							'alias' => 'detailscalculsdroitsrsa',
							'fields' => array( 'detailscalculsdroitsrsa.detaildroitrsa_id' ),
							'contain' => false,
							'conditions' => array(
								'detailscalculsdroitsrsa.detaildroitrsa_id = Detaildroitrsa.id',
								'detailscalculsdroitsrsa.natpf' => $naturesExclues
							)
						)
					);
					$conditions[] = "Detaildroitrsa.id NOT IN ( {$sq} )";
				}

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT(DISTINCT("Personne"."id")) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' GROUP BY "mois" ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 * Premièr tableau des indicateurs mensuels: nombre d'allocataires.
		 *
		 * @param array $search
		 * @return array
		 */
		public function nombreAllocataires( $search ) {
			$natpf = array(
				'activite' => array( 'RCD', 'RCI', 'RCU', 'RCB', 'RCJ' ),
				'socle' => array( 'RSD', 'RSI', 'RSU', 'RSB', 'RSJ' ),
				'non_majore' => array( 'RSD', 'RSU', 'RSB', 'RSJ', 'RCD', 'RCU', 'RCB', 'RCJ' ),
				'majore' => array( 'RSI', 'RCI' ),
			);
			return array(
				// Nouveaux allocataires
				'Indicateurmensuel.nouveaux_entrants_primo' => $this->_nombreAllocatairesNouveauxEntrants( $search, true ),
				'Indicateurmensuel.nouveaux_entrants_nonprimo' => $this->_nombreAllocatairesNouveauxEntrants( $search, false ),
				// État du droit
				'Indicateurmensuel.demandeurs_droits_payes' => $this->_nombreDemandeursDroits( $search, array( 2 ) ),
				'Indicateurmensuel.demandeurs_droits_suspendus' => $this->_nombreDemandeursDroits( $search, array( 3, 4 ) ),
				'Indicateurmensuel.demandeurs_droits_radies' => $this->_nombreDemandeursDroits( $search, array( 5, 6 ) ),
				// Nature de la prestation
				'Indicateurmensuel.demandeurs_droits_rsa_activite' => $this->_nombreDemandeursNatpf( $search, $natpf['activite'], array(), $natpf['socle'] ),
				'Indicateurmensuel.demandeurs_droits_rsa_socle_activite' => $this->_nombreDemandeursNatpf( $search, $natpf['activite'], $natpf['socle'] ),
				'Indicateurmensuel.demandeurs_droits_rsa_socle' => $this->_nombreDemandeursNatpf( $search, $natpf['socle'], array(), $natpf['activite'] ),
				'Indicateurmensuel.demandeurs_droits_rsa_non_majore' => $this->_nombreDemandeursNatpf( $search, $natpf['non_majore'] ),
				'Indicateurmensuel.demandeurs_droits_rsa_majore' => $this->_nombreDemandeursNatpf( $search, $natpf['majore'] ),
			);
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _sdd( $search, $notFuture = true ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Situationdossierrsa.etatdosrsa' => 2,
					'Dossier.dtdemrsa <' => $moissuivant,
					'Calculdroitrsa.toppersdrodevorsa' => '1',
					'NOW() >=' => date( 'Y-m-d', strtotime( "-1 month", strtotime( $moissuivant ) ) )
				);

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT(DISTINCT("Personne"."id")) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' GROUP BY "mois" ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param array $search
		 * @param mixed $typeorient_id
		 * @param boolean $notFuture Doit-on comptabiliser uniquement dans le passé ?
		 * @return array
		 */
		protected function _sddOrientees( $search, $typeorient_id = null, $structurereferente_id = null, $notFuture = true ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$mois = ( $mois < 10 ? "0{$mois}" : $mois );
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Situationdossierrsa.etatdosrsa' => 2,
					'Dossier.dtdemrsa <' => $moissuivant,
					'Calculdroitrsa.toppersdrodevorsa' => '1',
					'Orientstruct.statut_orient' => 'Orienté',
				);

				if( $notFuture ) {
					$conditions['NOW() >='] = date( 'Y-m-d', strtotime( "-1 month", strtotime( $moissuivant ) ) );
				}


				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				// Possédant au moins une orientation
				$sq = $Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere();
				$conditions[] = "Orientstruct.id IN ( {$sq} )";

				if( !empty( $typeorient_id ) ) {
					$conditions[] = array(
						'OR' => array(
							'Typeorient.id' => $typeorient_id,
							'Typeorient.parentid' => $typeorient_id
						)
					);
				}

				if( !empty( $structurereferente_id ) ) {
					$conditions['Structurereferente.id'] = $structurereferente_id;
				}

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT(DISTINCT("Personne"."id")) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' GROUP BY "mois" ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param array $search
		 * @param boolean $orientee_pcd
		 * @return array
		 */
		protected function _sddNonOrientees( $search, $orientee_pcd ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$mois = ( $mois < 10 ? "0{$mois}" : $mois );
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Situationdossierrsa.etatdosrsa' => 2,
					'Dossier.dtdemrsa <' => $moissuivant,
					'NOW() >=' => date( 'Y-m-d', strtotime( "-1 month", strtotime( $moissuivant ) ) ),
					'Calculdroitrsa.toppersdrodevorsa' => '1',
				);

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				// Non orientées dans ce dossier-ci
				$conditions[] = 'NOT EXISTS ( SELECT * FROM orientsstructs WHERE orientsstructs.personne_id = Personne.id AND orientsstructs.statut_orient = \'Orienté\' )';

				// Ne possédant pas de dossier précédent ou non orientée dans ce cas-là
				$sq = $Dossier->sq(
					array(
						'alias' => 'dossierspcds',
						'fields' => array( 'dossierspcds.id' ),
						'contain' => false,
						'joins' => array(
							array_words_replace(
								$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
								array( 'Foyer' => 'foyerspcds', 'Dossier' => 'dossierspcds' )
							),
							array_words_replace(
								$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
								array( 'Foyer' => 'foyerspcds', 'Personne' => 'personnespcds' )
							),
							array_words_replace(
								$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER', 'conditions' => array( 'Prestation.rolepers' => array( 'DEM', 'CJT' ) ) ) ),
								array( 'Personne' => 'personnespcds', 'Prestation' => 'prestationspcds' )
							),
							array_words_replace(
								$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER', 'conditions' => array( 'Orientstruct.statut_orient' => 'Orienté' ) ) ),
								array( 'Personne' => 'personnespcds', 'Orientstruct' => 'orientsstructspcds' )
							),
						),
						'conditions' => array(
							'Personne.foyer_id <> personnespcds.foyer_id',
							'Dossier.dtdemrsa > dossierspcds.dtdemrsa',
							'OR' => array(
								array(
									'nir_correct13( Personne.nir )',
									'nir_correct13( personnespcds.nir )',
									'SUBSTRING( TRIM( BOTH \' \' FROM Personne.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM personnespcds.nir ) FROM 1 FOR 13 )',
									'Personne.dtnai = personnespcds.dtnai'
								),
								array(
									'UPPER( Personne.nom ) = UPPER( personnespcds.nom )',
									'UPPER( Personne.prenom ) = UPPER( personnespcds.prenom )',
									'Personne.dtnai = personnespcds.dtnai'
								),
							)
						)
					)
				);

				if( $orientee_pcd ) {
					$conditions[] = "EXISTS ( {$sq} )";
				}
				else {
					$conditions[] = "NOT EXISTS ( {$sq} )";
				}

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT(DISTINCT("Personne"."id")) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' GROUP BY "mois" ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _nombreOrientations( $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$mois = ( $mois < 10 ? "0{$mois}" : $mois );
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Dossier.dtdemrsa <' => $moissuivant,
					'NOW() >=' => date( 'Y-m-d', strtotime( "-1 month", strtotime( $moissuivant ) ) ),
					'Orientstruct.date_valid <' => $moissuivant,
					'Orientstruct.date_valid >=' => "{$annee}-{$mois}-01",
				);

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT(DISTINCT("Personne"."id")) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' GROUP BY "mois" ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _delaiMoyenOrientation( $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$mois = ( $mois < 10 ? "0{$mois}" : $mois );
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Dossier.dtdemrsa <' => $moissuivant,
					'NOW() >=' => date( 'Y-m-d', strtotime( "-1 month", strtotime( $moissuivant ) ) ),
					'Orientstruct.date_valid <' => $moissuivant,
					'Orientstruct.date_valid >=' => "{$annee}-{$mois}-01",
				);

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				// Possédant au moins une orientation
				$sq = $Dossier->Foyer->Personne->Orientstruct->sq(
					array(
						'fields' => array(
							'orientsstructs.id'
						),
						'alias' => 'orientsstructs',
						'conditions' => array(
							'orientsstructs.personne_id = Personne.id',
							'orientsstructs.statut_orient' => 'Orienté',
							'orientsstructs.date_valid IS NOT NULL'
						),
						'order' => array( 'orientsstructs.date_valid ASC' ),
						'limit' => 1
					)
				);
				$conditions[] = "Orientstruct.id IN ( {$sq} )";

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'AVG( DATE_PART( \'day\', AGE( "Orientstruct"."date_valid", "Dossier"."dtdemrsa" ) ) ) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' GROUP BY "mois" ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _nombreReferentsNommes( $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$mois = ( $mois < 10 ? "0{$mois}" : $mois );
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Situationdossierrsa.etatdosrsa' => 2,
					'Calculdroitrsa.toppersdrodevorsa' => '1',
				);

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				// Possédant au moins une orientation - FIXME: CG / OA
				$sq = $Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere();
				$conditions[] = "Orientstruct.id IN ( {$sq} )";

//				$conditions[] = "PersonneReferent.dddesignation BETWEEN '{$annee}-{$mois}-01' AND '{$moissuivant}'";

				$conditions['PersonneReferent.dddesignation <='] = "{$annee}-{$mois}-01";
				$conditions[] = array(
					'OR' => array(
						'PersonneReferent.dfdesignation IS NULL',
						'PersonneReferent.dfdesignation >=' => "{$annee}-{$mois}-01"
					)
				);

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT( DISTINCT( PersonneReferent.id ) ) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'PersonneReferent', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' GROUP BY "mois" ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 * Second tableau des indicateurs mensuels: les orientations.
		 *
		 * @param array $search
		 * @return array
		 */
		public function orientations( $search ) {
			$Structurereferente = ClassRegistry::init( 'Structurereferente' );
			$structuresreferentesOa = $Structurereferente->find(
				'list',
				array(
					'contain' => false,
					'conditions' => array(
						'Structurereferente.typestructure' => 'oa'
					)
				)
			);
			$structuresreferentesCg = $Structurereferente->find(
				'list',
				array(
					'contain' => false,
					'conditions' => array(
						'NOT' => array(
							'Structurereferente.id' => array_keys( $structuresreferentesOa )
						)
					)
				)
			);

			$_sdd = $this->_sdd( $search );
			$_orientees = $this->_sddOrientees( $search );

			// Taux d'orientation
			$_ratio = array();
			foreach( range( 1, 12 ) as $mois ) {
				$tmp_sdd = Hash::get( $_sdd, $mois );
				$tmp_orientees = Hash::get( $_orientees, $mois );

				$tmp_sdd = ( empty( $tmp_sdd ) ? 1 : $tmp_sdd );

				$_ratio[$mois] = ( $tmp_orientees / $tmp_sdd * 100 );
			}

			return array(
				'Indicateurmensuel.sdd' => $_sdd,
				'Indicateurmensuel.sdd_jamais_orientees' => $this->_sddNonOrientees( $search, false ),
				'Indicateurmensuel.sdd_non_orientees' => $this->_sddNonOrientees( $search, true ),
				'Indicateurmensuel.orientees' => $_orientees,
				'Indicateurmensuel.taux_orientation' => $_ratio,
				'Indicateurmensuel.orientees_emploi' => $this->_sddOrientees( $search, Configure::read( 'Orientstruct.typeorientprincipale.Emploi' ) ),
				'Indicateurmensuel.orientees_prepro' => $this->_sddOrientees( $search, 4 ), // FIXME
				'Indicateurmensuel.orientees_social' => $this->_sddOrientees( $search, 6 ), // FIXME
				'Indicateurmensuel.orientees_pe' => $this->_sddOrientees( $search, 2 ), // FIXME
				'Indicateurmensuel.orientees_cg' => $this->_sddOrientees( $search, array( 5, 7 ), array_keys( $structuresreferentesCg ) ), // FIXME
				'Indicateurmensuel.orientees_oa' => $this->_sddOrientees( $search, null, array_keys( $structuresreferentesOa ) ),
				'Indicateurmensuel.orientations' => $this->_nombreOrientations( $search ),
				'Indicateurmensuel.delai_moyen_orientation' => $this->_delaiMoyenOrientation( $search ), // FIXME: WTF
				'Indicateurmensuel.nombre_referents_nommes' => $this->_nombreReferentsNommes( $search ),
			);
		}

		/**
		 *
		 * @param array $search
		 * @param string $decision_ci
		 * @param string $positioncer
		 * @param integer $typeorient_id
		 * @param integer $structurereferente_id
		 * @return array
		 */
		protected function _nombreContratsinertion( $search, $decision_ci = null, $positioncer = null, $typeorient_id = null, $structurereferente_id = null ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$mois = ( $mois < 10 ? "0{$mois}" : $mois );
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Situationdossierrsa.etatdosrsa' => 2,
					'Calculdroitrsa.toppersdrodevorsa' => '1',
					'Contratinsertion.dd_ci <' => $moissuivant,
					'Contratinsertion.df_ci >=' => "{$annee}-{$mois}-01",
				);

				if( !is_null( $decision_ci ) ) {
					$conditions['Contratinsertion.decision_ci'] = $decision_ci;
				}

				if( !is_null( $positioncer ) ) {
					$conditions['Contratinsertion.positioncer'] = $positioncer;
				}

				if( !empty( $typeorient_id ) ) {
					$conditions[] = array(
						'OR' => array(
							'Typeorient.id' => $typeorient_id,
							'Typeorient.parentid' => $typeorient_id
						)
					);
				}

				if( !empty( $structurereferente_id ) ) {
					$conditions['Structurereferente.id'] = $structurereferente_id;
				}

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				// Possédant au moins une orientation
				$sq = $Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere();
				$conditions[] = array(
					"Orientstruct.id IN ( {$sq} )"
				);

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT(DISTINCT "Personne"."id") AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Contratinsertion', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _delaiMoyenContractualisation( $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$mois = ( $mois < 10 ? "0{$mois}" : $mois );
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Contratinsertion.date_saisi_ci >= Orientstruct.date_valid',
					'Contratinsertion.date_saisi_ci <' => $moissuivant,
					'Contratinsertion.date_saisi_ci >=' => "{$annee}-{$mois}-01",
				);

				// Possédant une première orientation
				$sq = $Dossier->Foyer->Personne->Orientstruct->sq(
					array(
						'fields' => array(
							'orientsstructs.id'
						),
						'alias' => 'orientsstructs',
						'conditions' => array(
							'orientsstructs.personne_id = Personne.id',
							'orientsstructs.statut_orient' => 'Orienté',
							'orientsstructs.date_valid IS NOT NULL'
						),
						'order' => array( 'orientsstructs.date_valid ASC' ),
						'limit' => 1
					)
				);
				$conditions[] = "Orientstruct.id IN ( {$sq} )";

				// On ne s'occupe que du premier CER
				$sq = $Dossier->Foyer->Personne->Contratinsertion->sq(
					array(
						'fields' => array(
							'contratsinsertion.id'
						),
						'alias' => 'contratsinsertion',
						'conditions' => array(
							'contratsinsertion.personne_id = Personne.id',
							'contratsinsertion.date_saisi_ci IS NOT NULL'
						),
						'order' => array( 'contratsinsertion.date_saisi_ci ASC' ),
						'limit' => 1
					)
				);
				$conditions[] = "Contratinsertion.id IN ( {$sq} )";

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'AVG( DATE_PART( \'day\', AGE( "Contratinsertion"."date_saisi_ci", "Orientstruct"."date_valid" ) ) ) AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Contratinsertion', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _nombreCersRealises( $search ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$annee = trim( Hash::get( $search, 'Indicateurmensuel.annee' ) );
			$return = array();

			foreach( range( 1, 12 ) as $mois ) {
				$mois = ( $mois < 10 ? "0{$mois}" : $mois );
				$moissuivant = date( 'Y-m-d', strtotime( "+ {$mois} month", strtotime( "{$annee}-01-01" ) ) );

				$conditions = array(
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.rgadr' => '01',
						)
					),
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					'Contratinsertion.date_saisi_ci <' => $moissuivant,
					'Contratinsertion.date_saisi_ci >=' => "{$annee}-{$mois}-01",
				);

				// Condition sur le service instructeur
				$conditions[] = $this->_conditionServiceInstructeur( $search );

				// Conditions sur l'adresse de l'allocataire
				$conditions = $this->conditionsAdresse( $conditions, $search, false, array() );

				$querydata = array(
					'fields' => array(
						$mois.' AS "mois"',
						'COUNT(DISTINCT "Contratinsertion"."id") AS "indicateur"'
					),
					'joins' => array(
						$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Contratinsertion', array( 'type' => 'INNER' ) ),
						$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					),
					'contain' => false,
					'conditions' => $conditions,
				);

				$return[] = $Dossier->sq( $querydata );
			}

			$return = implode( ' UNION ', $return ).' ORDER BY "mois"';
			return Set::combine( $Dossier->query( $return ), '{n}.0.mois', '{n}.0.indicateur' );
		}

		/**
		 * Troisième tableau des indicateurs mensuels: les CER.
		 *
		 * @param array $search
		 * @return array
		 */
		public function contratsinsertion( $search ) {
			$Structurereferente = ClassRegistry::init( 'Structurereferente' );
			$typeorient_emploi_id = 1;// FIXME

			$structuresreferentesOaCgIds = $Structurereferente->find(
				'list',
				array(
					'contain' => false,
					'joins' => array(
						$Structurereferente->join( 'Typeorient' )
					),
					'conditions' => array(
//						'Structurereferente.typestructure' => 'oa',
						array(
							'Typeorient.id <>' => $typeorient_emploi_id,
							'Typeorient.parentid <>' => $typeorient_emploi_id
						)
					)
				)
			);
			$structuresreferentesOaCgIds = array_keys( $structuresreferentesOaCgIds );

			$_sdd = $this->_sddOrientees( $search, null, $structuresreferentesOaCgIds, false );
			$_cers = $this->_nombreContratsinertion( $search, 'V', null, null, $structuresreferentesOaCgIds );

			// Taux de CER
			$_ratio = array();
			foreach( range( 1, 12 ) as $mois ) {
				$tmp_sdd = Hash::get( $_sdd, $mois );
				$tmp_orientees = Hash::get( $_cers, $mois );

				$tmp_sdd = ( empty( $tmp_sdd ) ? 1 : $tmp_sdd );

				$_ratio[$mois] = ( $tmp_orientees / $tmp_sdd * 100 );
			}

			return array(
				'Indicateurmensuel.nombre_cers' => $_cers,
				'Indicateurmensuel.taux_cer' => $_ratio,
				'Indicateurmensuel.nombre_cers_prepro' => $this->_nombreContratsinertion( $search, 'V', null, 4, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_social' => $this->_nombreContratsinertion( $search, 'V', null, 6, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_encours' => $this->_nombreContratsinertion( $search, null, 'encours', null, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_attvalid' => $this->_nombreContratsinertion( $search, null, 'attvalid', null, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_encoursbilan' => $this->_nombreContratsinertion( $search, null, 'encoursbilan', null, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_perime' => $this->_nombreContratsinertion( $search, null, 'perime', null, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_attrenouv' => $this->_nombreContratsinertion( $search, null, 'attrenouv', null, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_nonvalid' => $this->_nombreContratsinertion( $search, null, 'nonvalid', null, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_annule' => $this->_nombreContratsinertion( $search, null, 'annule', null, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_fincontrat' => $this->_nombreContratsinertion( $search, null, 'fincontrat', null, $structuresreferentesOaCgIds ),
				'Indicateurmensuel.nombre_cers_realises' => $this->_nombreCersRealises( $search ),
				'Indicateurmensuel.delai_moyen_signature_cer' => $this->_delaiMoyenContractualisation( $search ),
			);
		}


		/**
		 * Ajoute des conditions au querydata pour les filtres Search.sitecov58_id
		 * du tableau de suivi du CG 58.
		 *
		 * @param array $querydata
		 * @param array $search
		 * @return array
		 */
		protected function _qdAddConditions58( array $querydata, array $search ) {
			$sitecov58_id = Hash::get( $search, 'Search.sitecov58_id' );

			$Sitecov58 = ClassRegistry::init( 'Sitecov58' );

			$zonesgeographiques = $Sitecov58->find(
				'all',
				array(
					'fields' => array( 'Zonegeographique.codeinsee' ),
					'conditions' => array( 'Sitecov58.id' => $sitecov58_id ),
					'joins' => array(
						$Sitecov58->join( 'Sitecov58Zonegeographique', array( 'type' => 'INNER' ) ),
						$Sitecov58->Sitecov58Zonegeographique->join( 'Zonegeographique', array( 'type' => 'INNER' ) )
					),
					'contain' => false
				)
			);
			$zonesgeographiques = Hash::extract( $zonesgeographiques, '{n}.Zonegeographique.codeinsee' );

			if( !empty( $zonesgeographiques ) ) {
				$querydata['conditions'][] = array( 'Adresse.numcom' => $zonesgeographiques );
			}

			return $querydata;
		}

		/**
		 * Retourne les résultats du tableau de suivi du CG 58, partie "CAF",
		 * suivant les filtres envoyés.
		 *
		 * @param array $search
		 * @return array
		 */
		public function personnescaf58( array $search ) {
			$year = Hash::get( $search, 'Search.year' );

			// -----------------------------------------------------------------
			$labels = array( 'total', '0', '1' );
			$months = array_fill( 1, 12, 0 );
			$personnescaf58 = array_combine( $labels, array_fill( 0, count( $labels ), $months ) );
			// -----------------------------------------------------------------
			$labels = array( 'versable', 'suspendu' );
			$etatdosrsa = array_combine( $labels, array_fill( 0, count( $labels ), $months ) );
			// -----------------------------------------------------------------
			$labels = array( 'socle', 'socle_activite' );
			$natspfs = array_combine( $labels, array_fill( 0, count( $labels ), $months ) );
			// -----------------------------------------------------------------

			$Personne = ClassRegistry::init( 'Personne' );

			$natpf = $Personne->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->vfsSummary();
			$natpf = array_words_replace( $natpf, array( 'Detailcalculdroitrsa' => 'detailscalculsdroitsrsa' ) );

			$querydata = array(
				'fields' => array(
					'COUNT( DISTINCT( "Personne"."id" ) ) AS "Indicateurcaf__nombre"',
					'"Situationdossierrsa"."etatdosrsa" AS "Indicateurcaf__etatdosrsa"',
					'( CASE WHEN "Calculdroitrsa"."toppersdrodevorsa" = \'1\' THEN \'1\' ELSE \'0\' END ) AS "Indicateurcaf__toppersdrodevorsa"',
					'DATE_PART( \'month\', "Dossier"."dtdemrsa" ) AS "Indicateurcaf__month"',
					$natpf['socle'],
					$natpf['activite'],
				),
				'joins' => array(
					$Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) )

				),
				'conditions' => array(
					'Prestation.rolepers' => array( 'DEM', 'CJT' ),
					"Dossier.dtdemrsa BETWEEN '{$year}-01-01' AND '{$year}-12-31'",
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ( '.$Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
					)
				),
				'contain' => false,
				'group' => array(
					'Calculdroitrsa.toppersdrodevorsa',
					'Situationdossierrsa.etatdosrsa',
					'DATE_PART( \'month\', "Dossier"."dtdemrsa" )',
					'Detaildroitrsa.id',
					preg_replace( '/^(.*) AS (.*)$/', '\1', $natpf['socle'] ),
					preg_replace( '/^(.*) AS (.*)$/', '\1', $natpf['activite'] ),
				)
			);

			$querydata = $this->_qdAddConditions58( $querydata, $search );

			$results = $Personne->find( 'all', $querydata );
			foreach( $results as $result ) {
				$toppersdrodevorsa = $result['Indicateurcaf']['toppersdrodevorsa'];

				$personnescaf58[$toppersdrodevorsa][$result['Indicateurcaf']['month']] += $result['Indicateurcaf']['nombre'];
				$personnescaf58['total'][$result['Indicateurcaf']['month']] += $result['Indicateurcaf']['nombre'];

				if( $toppersdrodevorsa ) {
					// Etat du dossier
					if( in_array( $result['Indicateurcaf']['etatdosrsa'], array( 2, 3, 4 ) ) ) {
						if( $result['Indicateurcaf']['etatdosrsa'] == 2 ) {
							$etatdosrsa['versable'][$result['Indicateurcaf']['month']] += $result['Indicateurcaf']['nombre'];
						}
						else {
							$etatdosrsa['suspendu'][$result['Indicateurcaf']['month']] += $result['Indicateurcaf']['nombre'];
						}
					}

					// Nature de la prestation
					if( $result['Detailcalculdroitrsa']['natpf_socle'] && !$result['Detailcalculdroitrsa']['natpf_activite'] ) {
						$natspfs['socle'][$result['Indicateurcaf']['month']] += $result['Indicateurcaf']['nombre'];
					}
					else if( $result['Detailcalculdroitrsa']['natpf_socle'] && $result['Detailcalculdroitrsa']['natpf_activite'] ) {
						$natspfs['socle_activite'][$result['Indicateurcaf']['month']] += $result['Indicateurcaf']['nombre'];
					}
				}
			}

			// Ajout du total par ligne
			foreach( $personnescaf58 as $key => $data ) {
				$personnescaf58[$key]['total'] = array_sum( $data );
			}

			foreach( $etatdosrsa as $key => $data ) {
				$etatdosrsa[$key]['total'] = array_sum( $data );
			}

			foreach( $natspfs as $key => $data ) {
				$natspfs[$key]['total'] = array_sum( $data );
			}

			// Suppression des éléments non désirés
			unset( $personnescaf58[0] ); // Détails des non SDD

			$personnescaf58 = $personnescaf58 + array( 'total_sdd' => $personnescaf58[1] ) + $natspfs + $etatdosrsa;

			return $personnescaf58;
		}

		/**
		 * Retourne les résultats du tableau de suivi du CG 58, partie "COV",
		 * suivant les filtres envoyés.
		 *
		 * @param array $search
		 * @return array
		 */
		public function dossierscovs58( array $search ) {
			$year = Hash::get( $search, 'Search.year' );

			// -----------------------------------------------------------------

			$Dossiercov58 = ClassRegistry::init( 'Dossiercov58' );

			$querydata = array(
				'fields' => array(
					'COUNT( DISTINCT( "Dossiercov58"."id" ) ) AS "Indicateurcov__nombre"',
					'"Dossiercov58"."themecov58" AS "Indicateurcov__theme"',
					'DATE_PART( \'month\', "Cov58"."datecommission" ) AS "Indicateurcov__month"',
				),
				'joins' => array(
					$Dossiercov58->join( 'Passagecov58', array( 'type' => 'INNER' ) ),
					$Dossiercov58->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossiercov58->Passagecov58->join( 'Cov58', array( 'type' => 'INNER' ) ),
					$Dossiercov58->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossiercov58->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Dossiercov58->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) )

				),
				'conditions' => array(
					"Cov58.datecommission BETWEEN '{$year}-01-01' AND '{$year}-12-31'",
					'Cov58.etatcov' => 'finalise',
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ( '.$Dossiercov58->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
					)
				),
				'contain' => false,
				'group' => array(
					'Dossiercov58.themecov58',
					'DATE_PART( \'month\', "Cov58"."datecommission" )'
				)
			);

			$querydata = $this->_qdAddConditions58( $querydata, $search );

			// Résultats vides, par thématique et par mois
			$enums = $Dossiercov58->enums();
			$labels = array_keys( $enums['Dossiercov58']['themecov58'] );
			array_unshift( $labels, 'total' );

			$months = array_fill( 1, 12, 0 );

			$dossierscovs58 = array_combine( $labels, array_fill( 0, count( $labels ), $months ) );

			$results = $Dossiercov58->find( 'all', $querydata );
			foreach( $results as $result ) {
				$theme = $result['Indicateurcov']['theme'];

				$dossierscovs58[$theme][$result['Indicateurcov']['month']] += $result['Indicateurcov']['nombre'];
				$dossierscovs58['total'][$result['Indicateurcov']['month']] += $result['Indicateurcov']['nombre'];
			}

			// Ajout du total par ligne
			foreach( $dossierscovs58 as $theme => $datatheme ) {
				$dossierscovs58[$theme]['total'] = array_sum( $datatheme );
			}

			return $dossierscovs58;
		}

		/**
		 * Retourne les résultats du tableau de suivi du CG 58, partie "EP",
		 * suivant les filtres envoyés.
		 *
		 * @param array $search
		 * @return array
		 */
		public function dossierseps58( array $search ) {
			$year = Hash::get( $search, 'Search.year' );

			// -----------------------------------------------------------------

			$Dossierep = ClassRegistry::init( 'Dossierep' );

			$querydata = array(
				'fields' => array(
					'COUNT( DISTINCT( "Dossierep"."id" ) ) AS "Indicateurep__nombre"',
					'"Dossierep"."themeep" AS "Indicateurep__themeep"',
					'DATE_PART( \'month\', "Commissionep"."dateseance" ) AS "Indicateurep__month"',
				),
				'joins' => array(
					$Dossierep->join( 'Passagecommissionep', array( 'type' => 'INNER' ) ),
					$Dossierep->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'INNER' ) ),
					$Dossierep->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossierep->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$Dossierep->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) )
				),
				'conditions' => array(
					"Commissionep.dateseance BETWEEN '{$year}-01-01' AND '{$year}-12-31'",
					'Commissionep.etatcommissionep' => 'traite',
					'OR' => array(
						'Adressefoyer.id IS NULL',
						'Adressefoyer.id IN ( '.$Dossierep->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
					)
				),
				'contain' => false,
				'group' => array(
					'Dossierep.themeep',
					'DATE_PART( \'month\', "Commissionep"."dateseance" )'
				)
			);

			$querydata = $this->_qdAddConditions58( $querydata, $search );

			// Résultats vides, par thématique et par mois
			$labels = array_keys( $Dossierep->themesCg() );
			array_unshift( $labels, 'total' );

			foreach( $labels as $i => $label ) {
				if( strpos( $label, 'sanctions' ) === 0 ) {
					unset( $labels[$i] );
				}
			}

			$labels = array_values( $labels );
			$labels[] = 'sanctionseps58';

			$months = array_fill( 1, 12, 0 );

			$dossierseps = array_combine( $labels, array_fill( 0, count( $labels ), $months ) );

			$results = $Dossierep->find( 'all', $querydata );
			foreach( $results as $result ) {
				$themeep = preg_replace( '/^sanctions(.*)$/', 'sanctionseps58', $result['Indicateurep']['themeep'] );

				$dossierseps[$themeep][$result['Indicateurep']['month']] += $result['Indicateurep']['nombre'];
				$dossierseps['total'][$result['Indicateurep']['month']] += $result['Indicateurep']['nombre'];
			}

			// Ajout du total par ligne
			foreach( $dossierseps as $themeep => $datatheme ) {
				$dossierseps[$themeep]['total'] = array_sum( $datatheme );
			}

			return $dossierseps;
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er RDV individuel prévu/excusé/non honoré par le PIE
		 * en fonction d'une plage de date (vague)
		 * Calcul propre au CD93
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvIndivNonHonorePIEVagues( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  ' AND foyers.id IN ('.$this->getCommunes().')';

			$tabInfos	=	array();
			$dateDebut	=	'';
			$dateFin	=	'';
			$reqDateDebutFin	=	'';
			$dernierIndex	=	0;
			$moyenne	=	0;
			$nbMoisAvecResult	=	0;
			$nbPersonnes	=	0;

			foreach($this->vagues as $index=>$value)
			{
				if($dateDebut=='')
					$dateDebut	=	$value['dateDebut'];
				$dateFin	=	$value['dateFin'];
				$dernierIndex	=	$index;

				$reqDateDebutFin	.=	(($reqDateDebutFin!='')? 'OR' : '').' orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\' ';

				$sql =	  'SELECT
								AVG( ABS(rdv.daterdv - orientsstructs.date_valid ) ) AS indicateur,
								COUNT(DISTINCT(personnes.id)) as nbPersonnes
							FROM orientsstructs
								INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
								INNER JOIN foyers ON personnes.foyer_id = foyers.id
								INNER JOIN rendezvous rdv ON rdv.personne_id=personnes.id
								LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
								LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
							WHERE
								orientsstructs.typeorient_id=1
								'.$where.'
								AND orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\'
								AND rdv.daterdv>=orientsstructs.date_propo
								AND rdv.statutrdv_id IN (2, 3, 4)
								AND rdv.typerdv_id=15
								AND rdv.id IN (
									SELECT id FROM rendezvous
									WHERE personne_id=personnes.id AND daterdv>=orientsstructs.date_valid
									ORDER BY id
									LIMIT 1
								)';
				$result	=	$this->query($sql);
				$tabInfos[$index]	=	$result;
				$moyenne	+=	$result[0][0]["indicateur"];
				$nbPersonnes	+=	$result[0][0]["nbpersonnes"];
				if($result[0][0]["indicateur"]>0)
					$nbMoisAvecResult++;
			}

			if($dernierIndex>0){
				$moyenne	=	$moyenne / $nbMoisAvecResult;
				$tabInfos[($dernierIndex+1)]	=	array(0=>array(0=>array('indicateur'=>$moyenne)));
				$tabInfos[($dernierIndex+2)]	=	array(0=>array(0=>array('indicateur'=>$nbPersonnes)));
			}

			return $tabInfos;
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er RDV individuel honoré par le PIE
		 * en fonction d'une plage de date (vague)
		 * Calcul propre au CD93
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvIndivHonorePIEVagues( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  ' AND foyers.id IN ('.$this->getCommunes().')';

			$tabInfos	=	array();
			$dateDebut	=	'';
			$dateFin	=	'';
			$reqDateDebutFin	=	'';
			$dernierIndex	=	0;
			$moyenne	=	0;
			$nbMoisAvecResult	=	0;
			$nbPersonnes	=	0;

			foreach($this->vagues as $index=>$value)
			{
				if($dateDebut=='')
					$dateDebut	=	$value['dateDebut'];
				$dateFin	=	$value['dateFin'];
				$dernierIndex	=	$index;

				$reqDateDebutFin	.=	(($reqDateDebutFin!='')? 'OR' : '').' orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\' ';

				$sql =	  'SELECT
								AVG( ABS(rdv.daterdv - orientsstructs.date_valid ) ) AS indicateur,
								COUNT(DISTINCT(personnes.id)) as nbPersonnes
							FROM orientsstructs
								INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
								INNER JOIN foyers ON personnes.foyer_id = foyers.id
								INNER JOIN rendezvous rdv ON rdv.personne_id=personnes.id
								LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
								LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
							WHERE
								orientsstructs.typeorient_id=1
								'.$where.'
								AND orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\'
								AND rdv.daterdv>=orientsstructs.date_propo
								AND rdv.statutrdv_id=1
								AND rdv.typerdv_id=15
								AND rdv.id IN (
									SELECT id FROM rendezvous
									WHERE personne_id=personnes.id AND daterdv>=orientsstructs.date_valid
									ORDER BY id
									LIMIT 1
								)';
				$result	=	$this->query($sql);
				$tabInfos[$index]	=	$result;
				$moyenne	+=	$result[0][0]["indicateur"];
				$nbPersonnes	+=	$result[0][0]["nbpersonnes"];
				if($result[0][0]["indicateur"]>0)
					$nbMoisAvecResult++;
			}

			if($dernierIndex>0){
				if($nbMoisAvecResult>0)
					$moyenne = $moyenne / $nbMoisAvecResult;
				else
					$moyenne = 0;
				$tabInfos[($dernierIndex+1)]	=	array(0=>array(0=>array('indicateur'=>$moyenne)));
				$tabInfos[($dernierIndex+2)]	=	array(0=>array(0=>array('indicateur'=>$nbPersonnes)));
			}

			return $tabInfos;
		}

/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er RDV collectif prévu/excusé/non honoré par le PIE
		 * en fonction d'une plage de date (vague)
		 * Calcul propre au CD93
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvCollectifNonHonorePIEVagues( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  ' AND foyers.id IN ('.$this->getCommunes().')';

			$tabInfos	=	array();
			$dateDebut	=	'';
			$dateFin	=	'';
			$reqDateDebutFin	=	'';
			$dernierIndex	=	0;
			$moyenne	=	0;
			$nbMoisAvecResult	=	0;
			$nbPersonnes	=	0;

			foreach($this->vagues as $index=>$value)
			{
				if($dateDebut=='')
					$dateDebut	=	$value['dateDebut'];
				$dateFin	=	$value['dateFin'];
				$dernierIndex	=	$index;

				$reqDateDebutFin	.=	(($reqDateDebutFin!='')? 'OR' : '').' orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\' ';

				$sql =	  'SELECT
								AVG( ABS(rdv.daterdv - orientsstructs.date_valid ) ) AS indicateur,
								COUNT(DISTINCT(personnes.id)) as nbPersonnes
							FROM orientsstructs
								INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
								INNER JOIN foyers ON personnes.foyer_id = foyers.id
								INNER JOIN rendezvous rdv ON rdv.personne_id=personnes.id
								LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
								LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
							WHERE
								orientsstructs.typeorient_id=1
								'.$where.'
								AND orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\'
								AND rdv.daterdv>=orientsstructs.date_propo
								AND rdv.statutrdv_id IN (2, 3, 4)
								AND rdv.typerdv_id=14
								AND rdv.id IN (
									SELECT id FROM rendezvous
									WHERE personne_id=personnes.id AND daterdv>=orientsstructs.date_valid
									ORDER BY id
									LIMIT 1
								)';
				$result	=	$this->query($sql);
				$tabInfos[$index]	=	$result;
				$moyenne	+=	$result[0][0]["indicateur"];
				$nbPersonnes	+=	$result[0][0]["nbpersonnes"];
				if($result[0][0]["indicateur"]>0)
					$nbMoisAvecResult++;
			}

			if($dernierIndex>0){
				if($nbMoisAvecResult>0)
					$moyenne = $moyenne / $nbMoisAvecResult;
				else
					$moyenne = 0;
				$tabInfos[($dernierIndex+1)] = array(0=>array(0=>array('indicateur'=>$moyenne)));
				$tabInfos[($dernierIndex+2)] = array(0=>array(0=>array('indicateur'=>$nbPersonnes)));
			}

			return $tabInfos;
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er RDV individuel prévu/excusé/non honoré par le PIE
		 * en fonction d'une plage de date (vague)
		 * Calcul propre au CD93
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvCollectifHonorePIEVagues( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  ' AND foyers.id IN ('.$this->getCommunes().')';

			$tabInfos	=	array();
			$dateDebut	=	'';
			$dateFin	=	'';
			$reqDateDebutFin	=	'';
			$dernierIndex	=	0;
			$moyenne	=	0;
			$nbMoisAvecResult	=	0;
			$nbPersonnes	=	0;

			foreach($this->vagues as $index=>$value)
			{
				if($dateDebut=='')
					$dateDebut	=	$value['dateDebut'];
				$dateFin	=	$value['dateFin'];
				$dernierIndex	=	$index;

				$reqDateDebutFin	.=	(($reqDateDebutFin!='')? 'OR' : '').' orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\' ';

				$sql =	  'SELECT
								AVG( ABS(rdv.daterdv - orientsstructs.date_valid ) ) AS indicateur,
								COUNT(DISTINCT(personnes.id)) as nbPersonnes
							FROM orientsstructs
								INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
								INNER JOIN foyers ON personnes.foyer_id = foyers.id
								INNER JOIN rendezvous rdv ON rdv.personne_id=personnes.id
								LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
								LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
							WHERE
								orientsstructs.typeorient_id=1
								'.$where.'
								AND orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\'
								AND rdv.daterdv>=orientsstructs.date_propo
								AND rdv.statutrdv_id=1
								AND rdv.typerdv_id=14
								AND rdv.id IN (
									SELECT id FROM rendezvous
									WHERE personne_id=personnes.id AND daterdv>=orientsstructs.date_valid
									ORDER BY id
									LIMIT 1
								)';
				$result	=	$this->query($sql);
				$tabInfos[$index]	=	$result;
				$moyenne	+=	$result[0][0]["indicateur"];
				$nbPersonnes	+=	$result[0][0]["nbpersonnes"];
				if($result[0][0]["indicateur"]>0)
					$nbMoisAvecResult++;
			}

			if($dernierIndex>0){
				if($nbMoisAvecResult>0)
					$moyenne = $moyenne / $nbMoisAvecResult;
				else
					$moyenne = 0;
				$tabInfos[($dernierIndex+1)]	=	array(0=>array(0=>array('indicateur'=>$moyenne)));
				$tabInfos[($dernierIndex+2)]	=	array(0=>array(0=>array('indicateur'=>$nbPersonnes)));
			}

			return $tabInfos;
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre l'orientation socio-professionnelle et le 1er CER établi par le PIE
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsCerEtabliPIEVagues( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  ' AND foyers.id IN ('.$this->getCommunes().')';

			$tabInfos	=	array();
			$dateDebut	=	'';
			$dateFin	=	'';
			$reqDateDebutFin	=	'';
			$dernierIndex	=	0;
			$moyenne	=	0;
			$nbMoisAvecResult	=	0;
			$nbPersonnes	=	0;

			foreach($this->vagues as $index=>$value)
			{
				if($dateDebut=='')
					$dateDebut	=	$value['dateDebut'];
				$dateFin	=	$value['dateFin'];
				$dernierIndex	=	$index;

				$reqDateDebutFin	.=	(($reqDateDebutFin!='')? 'OR' : '').' orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\' ';
				$sql =	  'SELECT
								AVG( ABS(cer.dd_ci - orientsstructs.date_valid ) ) AS indicateur,
								COUNT(DISTINCT(personnes.id)) as nbPersonnes
							FROM orientsstructs
								INNER JOIN personnes ON orientsstructs.personne_id = personnes.id
								INNER JOIN foyers ON personnes.foyer_id = foyers.id
								INNER JOIN contratsinsertion cer ON cer.personne_id=personnes.id
								LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
								LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
							WHERE
								orientsstructs.typeorient_id=1
								'.$where.'
								AND orientsstructs.date_valid BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\'
								AND cer.dd_ci>=orientsstructs.date_propo
                                AND cer.dd_ci BETWEEN orientsstructs.date_propo AND \''.$annee.'-12-31\'
								AND cer.id IN (
									SELECT id FROM contratsinsertion
									WHERE personne_id=personnes.id AND dd_ci>=orientsstructs.date_valid
									ORDER BY id
									LIMIT 1
								)';
				$result	=	$this->query($sql);
				$tabInfos[$index]	=	$result;
				$moyenne	+=	$result[0][0]["indicateur"];
				$nbPersonnes	+=	$result[0][0]["nbpersonnes"];
				if($result[0][0]["indicateur"]>0)
					$nbMoisAvecResult++;
			}

			if($dernierIndex>0){
				if($nbMoisAvecResult>0)
					$moyenne = $moyenne / $nbMoisAvecResult;
				else
					$moyenne = 0;
				$tabInfos[($dernierIndex+1)]	=	array(0=>array(0=>array('indicateur'=>$moyenne)));
				$tabInfos[($dernierIndex+2)]	=	array(0=>array(0=>array('indicateur'=>$nbPersonnes)));
			}

			return $tabInfos;
		}

		/**
		 * Retourne le délai moyen ( en jour ) entre le 1er RDV individuel honoré PIE et le 1er CER établi par le PIE
		 *
		 * @param integer $annee
		 * @return array
		 */
		protected function _nbMoyJrsRdvIndivCerEtabliePIEVagues( $annee, $structureReferente='', $communautesSrs='', $commune='' ) {
			$where  =   "";
			if($structureReferente!='' && $structureReferente>0)
				$where  .=   " AND orientsstructs.structurereferente_id='".$structureReferente."'";
			if($communautesSrs!='' && $communautesSrs>0)
				$where  .=   " AND communautessrs_structuresreferentes.communautesr_id='".$communautesSrs."'";
			if($commune!='' && $commune>0)
				$where  .=  ' AND foyers.id IN ('.$this->getCommunes().')';

			$tabInfos	=	array();
			$dateDebut	=	'';
			$dateFin	=	'';
			$reqDateDebutFin	=	'';
			$dernierIndex	=	0;
			$moyenne	=	0;
			$nbMoisAvecResult	=	0;
			$nbPersonnes	=	0;

			foreach($this->vagues as $index=>$value)
			{
				if($dateDebut=='')
					$dateDebut	=	$value['dateDebut'];
				$dateFin	=	$value['dateFin'];
				$dernierIndex	=	$index;

				$reqDateDebutFin	.=	(($reqDateDebutFin!='')? 'OR' : '').' rdv.daterdv BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\' ';

				$sql =	  'SELECT
								AVG( ABS(cer.dd_ci - rdv.daterdv ) ) AS indicateur,
								COUNT(DISTINCT(personnes.id)) as nbPersonnes
							FROM rendezvous rdv
								INNER JOIN personnes ON rdv.personne_id = personnes.id
								INNER JOIN foyers ON personnes.foyer_id = foyers.id
								INNER JOIN contratsinsertion cer ON cer.personne_id=personnes.id
								INNER JOIN orientsstructs ON orientsstructs.personne_id = personnes.id
								LEFT OUTER JOIN structuresreferentes ON structuresreferentes.id=orientsstructs.structurereferente_id
								LEFT OUTER JOIN communautessrs_structuresreferentes ON communautessrs_structuresreferentes.structurereferente_id=structuresreferentes.id
							WHERE
								rdv.daterdv BETWEEN \''.$value['dateDebut'].'\' AND \''.$value['dateFin'].'\'
								'.$where.'
								AND cer.dd_ci>=rdv.daterdv
								AND rdv.id IN (
									SELECT id FROM rendezvous
									WHERE personne_id=personnes.id AND rdv.daterdv BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
									ORDER BY id
									LIMIT 1
								)
								AND cer.id IN (
									SELECT id FROM contratsinsertion
									WHERE personne_id=personnes.id AND cer.dd_ci BETWEEN \''.$annee.'-01-01\' AND \''.$annee.'-12-31\'
									ORDER BY id
									LIMIT 1
								)';
				$result	=	$this->query($sql);
				$tabInfos[$index]	=	$result;
				$moyenne	+=	$result[0][0]["indicateur"];
				$nbPersonnes	+=	$result[0][0]["nbpersonnes"];
				if($result[0][0]["indicateur"]>0)
					$nbMoisAvecResult++;
			}

			if($dernierIndex>0){
				if($nbMoisAvecResult>0)
					$moyenne = $moyenne / $nbMoisAvecResult;
				else
					$moyenne = 0;
				$tabInfos[($dernierIndex+1)]	=	array(0=>array(0=>array('indicateur'=>$moyenne)));
				$tabInfos[($dernierIndex+2)]	=	array(0=>array(0=>array('indicateur'=>$nbPersonnes)));
			}

			return $tabInfos;
		}
	}
?>