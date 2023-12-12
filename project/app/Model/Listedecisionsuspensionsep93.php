<?php
	/**
	 * Code source de la classe Motifreorientep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Listedecisionsuspensionsep93 ...
	 *
	 * @package app.Model
	 */
	class Listedecisionsuspensionsep93 extends AppModel
	{
		public $name = 'Listedecisionsuspensionsep93';

		public $useTable = 'listedecisionssuspensionseps93';

        /*
        * Retourne les décisions paramétrées, rangées par niveau de décision pour les listes déroulantes
        */
        public function listeDecisions(){
            $results = $this->query(
                'select code[1] as code, premier_niveau, deuxieme_niveau, libelle
                from listedecisionssuspensionseps93
                where actif = true
                order by libelle'
            );

            $liste = [];
            foreach ($results as $result){
                $result = $result[0];
                if($result['premier_niveau']){
                    $liste['Décisions de 1er niveau'][$result['code']] = $result['libelle'];
                }
                if($result['deuxieme_niveau']){
                    $liste['Décisions de 2ème niveau'][$result['code']] = $result['libelle'];
                }
            }

            return $liste;
        }


	}
?>