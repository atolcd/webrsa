<?php	
	/**
	 * Code source de la classe Offreinsertion.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Offreinsertion ...
	 *
	 * @package app.Model
	 */
	class Offreinsertion extends AppModel
	{
		public $name = 'Offreinsertion';

		public $useTable = false;
		
		public $actsAs = array( 'Formattable' );

		/**
		*
		*/

		protected function _search( $params ) {
			/// Conditions de base
			$conditions = array();

			/// Critères
			$actionname = Set::extract( $params, 'Search.Actioncandidat.name' );
			$partenaire_id = Set::extract( $params, 'Search.Partenaire.id' );
			$contact_id = Set::extract( $params, 'Search.Contactpartenaire.id' );
			$codepartenaire = Set::extract( $params, 'Search.Partenaire.codepartenaire' );
			$themecode = Set::extract( $params, 'Search.Actioncandidat.themecode' );
			$codefamille = Set::extract( $params, 'Search.Actioncandidat.codefamille' );
			$numcodefamille = Set::extract( $params, 'Search.Actioncandidat.numcodefamille' );
			$correspondant = Set::extract( $params, 'Search.Actioncandidat.referent_id' );
			$hasfichecandidature = Hash::get( $params, 'Search.Actioncandidat.hasfichecandidature' );
			$isactive = Set::extract( $params, 'Search.Actioncandidat.actif' );

			if( !empty( $actionname ) ){
				$conditions[] = 'Actioncandidat.id = \''.Sanitize::clean( $actionname, array( 'encode' => false ) ).'\'';
			}
			
// 			if( is_numeric( $partenaire_id ) && !empty( $partenaire_id )  ){
// 				$conditions[] = 'Partenaire.id = \''.Sanitize::clean( $partenaire_id, array( 'encode' => false ) ).'\'';
// 			}

			if( is_numeric( $contact_id ) && !empty( $contact_id ) ){
				$conditions[] = 'Contactpartenaire.id = \''.Sanitize::clean( $contact_id, array( 'encode' => false ) ).'\'';
			}
			
			if( !empty( $codepartenaire ) ){
				$conditions[] = 'Partenaire.codepartenaire = \''.$codepartenaire.'\'';
			}

			if( is_numeric( $themecode ) && !empty( $themecode ) ){
				$conditions[] = 'Actioncandidat.themecode = \''.Sanitize::clean( $themecode, array( 'encode' => false ) ).'\'';
			}
			
			if( !empty( $codefamille ) ){
				$conditions[] = 'Actioncandidat.codefamille ILIKE \''.$codefamille.'\'';
			}
			
			if( !empty( $numcodefamille ) ){
				$conditions[] = 'Actioncandidat.numcodefamille = \''.Sanitize::clean( $numcodefamille, array( 'encode' => false ) ).'\'';
			}
			
			if( !empty( $correspondant ) ){
				$conditions[] = 'Actioncandidat.referent_id = \''.Sanitize::clean( $correspondant, array( 'encode' => false ) ).'\'';
			}
						
			if( isset( $hasfichecandidature ) && ( $hasfichecandidature != '' ) ){
				$conditions[] = 'Actioncandidat.hasfichecandidature = \''.$hasfichecandidature.'\'';
			}
						
			if( isset( $isactive ) && !empty( $isactive ) ){
				$conditions[] = 'Actioncandidat.actif = \''.Sanitize::clean( $isactive, array( 'encode' => false )  ).'\'';
			}

			/*FIXME*/
			if( !empty( $partenaire_id ) ) {
				$conditions[] = 'Partenaire.id IN ( '.
					ClassRegistry::init( 'Contactpartenaire' )->sq(
						array(
							'alias' => 'contactspartenaires',
							'fields' => array( 'contactspartenaires.partenaire_id' ),
							'contain' => false,
							'conditions' => array(
								'contactspartenaires.partenaire_id' => $partenaire_id
							),
							'joins' => array(
								array(
									'table'      => 'partenaires',
									'alias'      => 'partenaires',
									'type'       => 'INNER',
									'foreignKey' => false,
									'conditions' => array( 'contactspartenaires.partenaire_id = partenaires.id' ),
								),
								array(
									'table'      => 'actionscandidats',
									'alias'      => 'actionscandidats',
									'type'       => 'INNER',
									'foreignKey' => false,
									'conditions' => array( 'actionscandidats.contactpartenaire_id = contactspartenaires.id' ),
								)
							)
						)
					)
				.' )';	
			}
			/*Test*/
			
			$Actioncandidat = ClassRegistry::init( 'Actioncandidat' );
			$query = array(
				'fields' => array_merge(
					$Actioncandidat->fields(),
					$Actioncandidat->Contactpartenaire->fields(),
					$Actioncandidat->Contactpartenaire->Partenaire->fields(),
					$Actioncandidat->Chargeinsertion->fields(),
					$Actioncandidat->Secretaire->fields(),
					array(
						$Actioncandidat->Contactpartenaire->sqVirtualField( 'nom_candidat' ),
						$Actioncandidat->Partenaire->sqVirtualField( 'adresse' ),
						$Actioncandidat->Secretaire->sqVirtualField( 'nom_complet' ),
						$Actioncandidat->Chargeinsertion->sqVirtualField( 'nom_complet' ),
						$Actioncandidat->Fichiermodule->sqNbFichiersLies( $Actioncandidat, 'nb_fichiers_lies' )
					)
				),
				'joins' => array(
					$Actioncandidat->join( 'Contactpartenaire', array( 'LEFT OUTER' ) ),
					$Actioncandidat->Contactpartenaire->join( 'Partenaire', array( 'LEFT OUTER' ) ),
					$Actioncandidat->join( 'Chargeinsertion', array( 'LEFT OUTER' ) ),
					$Actioncandidat->join( 'Secretaire', array( 'LEFT OUTER' ) ),
					$Actioncandidat->join( 'Referent', array( 'LEFT OUTER' ) )
				),
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => array( 'Actioncandidat.name ASC' ),
                'limit' => 200
			);
            
			return $query;
		}

        /*
            'global' => $this->Offreinsertion->searchGlobal( $this->request->data ),
            'actions' => $this->Offreinsertion->searchActions( $this->request->data ),
            'contactpartenaires' => $this->Offreinsertion->searchContactpartenaires( $this->request->data ),
            'partenaires' => $this->Offreinsertion->searchPartenaires( $this->request->data ),
            'actions_par_partenaires' => $this->Offreinsertion->searchActionsParPartenaires( $this->request->data ),
         */

        public function searchGlobal( $params ) {
            return $this->_search( $params );
        }
        
        /*
         *  Recherche avec pour critères les informations des partenaires
         */
        public function searchPartenaires( $params ) {
            $querydata = $this->_search( $params );
            
            $querydata['conditions'][] = 'Partenaire.id IS NOT NULL';
            
            $Partenaire = ClassRegistry::init( 'Partenaire' );
            
            $fields = $Partenaire->fields();
            
            $querydata['fields'] = array_merge(
                $fields,
                array(
                    $Partenaire->sqVirtualField( 'adresse' )
                )
            );

            $querydata['group'] = array_merge(
                $fields
            );
                    
            $querydata['order'] = array( 'Partenaire.libstruc ASC' );

            return $querydata;
        }
        
        /*
         *  Recherche avec pour critères les informations des partenaires
         */
        public function searchActions( $params ) {
            $querydata = $this->_search( $params );
            
            $Actioncandidat = ClassRegistry::init( 'Actioncandidat' );
            
            $fields = array_merge(
                $Actioncandidat->fields(),
                $Actioncandidat->Chargeinsertion->fields(),
                $Actioncandidat->Secretaire->fields()
            );
                    
            $querydata['fields'] = array_merge(
                $fields,
                array(
                    'Actioncandidat.id',
                    $Actioncandidat->Secretaire->sqVirtualField( 'nom_complet' ),
                    $Actioncandidat->Chargeinsertion->sqVirtualField( 'nom_complet' ),
                    $Actioncandidat->Fichiermodule->sqNbFichiersLies( $Actioncandidat, 'nb_fichiers_lies' )
                )
            );
            $querydata['group'] =  array_merge(
                $fields,
                array(
                    'Actioncandidat.id',
                    $Actioncandidat->Secretaire->sqVirtualField( 'nom_complet', false ),
                    $Actioncandidat->Chargeinsertion->sqVirtualField( 'nom_complet', false ),
                    '( '.$Actioncandidat->Fichiermodule->sqNbFichiersLies( $Actioncandidat, null ).')'
                )
            );
            $querydata['order'] = array( 'Actioncandidat.name ASC' );

            return $querydata;
        }
        
        /*
         *  Recherche avec pour critères les informations des partenaires
         */
        public function searchContactpartenaires( $params ) {
            $querydata = $this->_search( $params );
            
            $Contactpartenaire = ClassRegistry::init( 'Contactpartenaire' );
            
            $querydata['conditions'][] = 'Contactpartenaire.id IS NOT NULL';
            
            $fields = $Contactpartenaire->fields();
                    
            $querydata['fields'] = array_merge(
                $fields,
                array(
                    'Partenaire.libstruc',
                    $Contactpartenaire->sqVirtualField( 'nom_candidat' )
                )
            );

            $querydata['group'] = array_merge(
                $fields,
                array(
                    'Partenaire.libstruc',
                    $Contactpartenaire->sqVirtualField( 'nom_candidat', false )
                )
            );
            $querydata['order'] = array( 'Contactpartenaire.nom ASC' );
//            $querydata['order'] = null;

            return $querydata;
        }
        
        
        /*
         *  Recherche avec pour critères les informations des partenaires
         */
        public function searchActionsParPartenaires( $params ) {
            $querydata = $this->_search( $params );
            
            $Actioncandidat = ClassRegistry::init( 'Actioncandidat' );
            
            $fields = array_merge(
                $Actioncandidat->fields(),
                $Actioncandidat->Contactpartenaire->Partenaire->fields(),
                array(
                    'Actioncandidat.id'
                )
            );
                    
            $querydata['fields'] = $fields;
            $querydata['group'] = $fields;
            $querydata['order'] = array( 'Actioncandidat.name DESC' );

            return $querydata;
        }
        
	}
?>