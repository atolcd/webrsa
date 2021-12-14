<?php
	/**
	 * Fichier source de la classe GestionSectorisationComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );

	/**
	 * @package app.Controller.Component
	 */
	class GestionSectorisationComponent extends Component
	{
		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
            'Session'
        );

        public function addConditionReferents($query){
            $referents_user = $this->Session->read( 'Auth.ReferentsSectorisation' );
            if($referents_user){
                $query['conditions'][] =
                    "(
                        select r.id
                        from personnes p
                            join personnes_referents pr on p.id = pr.personne_id
                            join referents r on r.id = pr.referent_id
                            join structuresreferentes s on s.id = r.structurereferente_id
                        where s.actif_sectorisation = true
                            and p.id = Personne.id
                        order by pr.dddesignation desc
                        limit 1
                    )
                    IN ($referents_user)";
            }

            return $query;
        }
    }

