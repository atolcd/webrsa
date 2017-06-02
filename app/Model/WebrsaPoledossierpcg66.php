<?php
	/**
	 * Code source de la classe WebrsaPoledossierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('WebrsaAbstractLogic', 'Model');

	/**
	 * La classe WebrsaPoledossierpcg66 s'occupe de la logique métier des pôles
	 * chargés des dossiers PCG.
	 *
	 * @package app.Model
	 */
	class WebrsaPoledossierpcg66 extends WebrsaAbstractLogic
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaPoledossierpcg66';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Poledossierpcg66' );

		/**
		 * Retourne la liste des pôles chargés des dossiers PCG, à utiliser
		 * dans un select.
		 *
		 * Si on se sert de cette liste pour du traitement, on fera apparaitre
		 * uniquement les poles actifs.
		 *
		 * @param boolean $traitement Cette liste doit-elle servir pour du
		 *	traitement (true par défaut) ?
		 * @return array
		 */
		public function polesdossierspcgs66( $traitement = true ) {
			$query = array(
				'fields' => array(
					'Poledossierpcg66.id',
					'Poledossierpcg66.name'
				),
				'conditions' => array(),
				'order' => array( 'Poledossierpcg66.name ASC' )
			);

			if( true === $traitement ) {
				$query['conditions']['Poledossierpcg66.isactif'] = '1';
			}

			return $this->Poledossierpcg66->find( 'list', $query );
		}

		/**
		 * Complète les options des clés Dossierpcg66.poledossierpcg66_id et
		 * Dossierpcg66.user_id avec les valeurs se trouvant dans data si le
		 * pôle ou le gestionnaire n'y figurent pas afin de ne pas perdre
		 * d'information en cas de modification.
		 *
		 * @param array $options Les options
		 * @param array $data Les données permettant de rempir le formulaire
		 * @param array $params Les chemins vers les pôles et les gestionnaires
		 *	(clés poledossierpcg66_id et user_id) et le fait qu'il faille préfixer
		 *	l'id du gestionnaire par l'id du pôle.
		 * @return array
		 */
		public function completeOptions( array $options = array(), array $data = array(), array $params = array() ) {
			$params += array(
				'poledossierpcg66_id' => 'Dossierpcg66.poledossierpcg66_id',
				'user_id' => 'Dossierpcg66.user_id',
				'prefix' => false
			);

			// Ajout du pole à la liste ?
			$poledossierpcg66_id = Hash::get( $data, $params['poledossierpcg66_id'] );
			$polesdossierspcgs66 = Hash::get( $options, $params['poledossierpcg66_id'] );
			if( false === empty( $poledossierpcg66_id ) && false === in_array( $poledossierpcg66_id, $polesdossierspcgs66 ) ) {
				$query = array( 'conditions' => array( 'Poledossierpcg66.id' => $poledossierpcg66_id ) );
				$polesdossierspcgs66 = $polesdossierspcgs66 + $this->Poledossierpcg66->find( 'list', $query );
				asort( $polesdossierspcgs66 );
				$options = Hash::remove( $options, $params['poledossierpcg66_id'] );
				$options = Hash::insert( $options, $params['poledossierpcg66_id'], $polesdossierspcgs66 );
			}

			// Ajout du gestionnaire à la liste ?
			$user_id = Hash::get( $data, $params['user_id'] );
			$gestionnaires = Hash::get( $options, $params['user_id'] );
			if( false === empty( $user_id ) && false === in_array( $user_id, $gestionnaires ) ) {
				// Utilisateurs liés à des poles désactivés
				$query = array(
					'fields' => array(
						'User.id',
						'User.nom_complet',
						'Poledossierpcg66.id'
					),
					'contain' => false,
					'joins' => array(
						$this->Poledossierpcg66->User->join( 'Poledossierpcg66', array( 'type' => 'INNER' ) ),
					),
					'conditions' => array(
						'User.id' => $user_id
					)
				);
				$users = $this->Poledossierpcg66->User->find( 'all', $query );

				if( true === $params['prefix'] ) {
					$gestionnaires = $gestionnaires + Hash::combine( $users, array( '%d_%d', '{n}.Poledossierpcg66.id', '{n}.User.id' ), '{n}.User.nom_complet' );
				}
				else {
					$gestionnaires = $gestionnaires + Hash::combine( $users, '{n}.User.id', '{n}.User.nom_complet' );
				}

				// Utilisateurs anciennement liés à des poles
				$query = array(
					'fields' => array(
						'User.id',
						'User.nom_complet',
						'Poledossierpcg66User.poledossierpcg66_id'
					),
					'contain' => false,
					'joins' => array(
						$this->Poledossierpcg66->User->join( 'Poledossierpcg66User', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'User.id' => $user_id
					)
				);
				$users = $this->Poledossierpcg66->User->find( 'all', $query );

				if( true === $params['prefix'] ) {
					$gestionnaires = $gestionnaires + Hash::combine( $users, array( '%d_%d', '{n}.Poledossierpcg66User.poledossierpcg66_id', '{n}.User.id' ), '{n}.User.nom_complet' );
				}
				else {
					$gestionnaires = $gestionnaires + Hash::combine( $users, '{n}.User.id', '{n}.User.nom_complet' );
				}

				// Tri par om de gestoinnaire et remplacement des options
				asort( $gestionnaires );
				$options = Hash::remove( $options, $params['user_id'] );
				$options = Hash::insert( $options, $params['user_id'], $gestionnaires );
			}

			return $options;
		}
	}
?>