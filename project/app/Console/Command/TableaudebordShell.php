<?php
	/**
	 * Fichier source de la classe Tableaudebord.
	 *
	 * PHP 7.2
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
     *
     *
     * Arguments attendus : année, trimestre, tableau
     * Se lance avec : sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake Tableaudebord -app app
	 *
	 */
	App::uses( 'XShell', 'Console/Command' );
	App::import( 'Controller', 'Tableauxbords93' );

	/**
	 * La classe Tableaudebord ...
	 *
	 * @package app.Console.Command
	 */
	class TableaudebordShell extends XShell
	{
		public $uses = [
			'Tdb2HistoCorpus',
			'Structurereferente',
			'Personne'
		];

        public function main(){

			//On vérifie que les arguments sont corrects
			//trimestre, annee, tableau (si vide, par défaut on enregistre tous les tableaux)


            if (!isset($this->args[1])) {
                $this->out("<error>Paramètres manquants</error>");
                exit();
            } else if (!in_array($this->args[0], [1, 2, 3, 4])) {
				$this->out("<error>Trimestre incorrect</error>");
                exit();
			} else if (!is_int((int)$this->args[1]) || strlen((string)$this->args[1]) != 4){
				$this->out("<error>Année incorrecte</error>");
                exit();
			}

			$trimestre = $this->args[0];
			$annee = $this->args[1];

			//On vérifie que le couple trimestre /année n'a pas déjà été enregistré
			$deja_enregistre = $this->Tdb2HistoCorpus->find(
				'first',
				[
					'conditions' => [
						'trimestre' => $trimestre,
						'annee' => $annee
					]
				]
			);

			if (!empty($deja_enregistre)) {
                $this->out("<error>Ce trimestre a déjà été enregistré</error>");
                exit();
            }

			//On récupère le controller des tableaux de bord
			$tdb = new Tableauxbords93Controller();

			//On récupère la date du dernier jour du trimestre
			switch($trimestre){
				case 1:
					$date_du_jour = $annee.'-03-31';
					break;
				case 2:
					$date_du_jour = $annee.'-06-30';
					break;
				case 3:
					$date_du_jour = $annee.'-09-30';
					break;
				case 4:
					$date_du_jour = $annee.'-12-31';
					break;
			}


			//On récupère le liste des structures actives
			$structures = $this->Structurereferente->find(
				'list',
				[
					'conditions' => [
						'Structurereferente.actif' => 'O'
						]
				]
			);


			//Pour chaque structure, on récupère les infos du corpus et on enregistre dans la table tdb2_histo_corpus
			foreach($structures as $id_structure => $libelle){
				debug($id_structure);
				//On récupère les données du corpus
				$query_corpus = $tdb->sql_tab2_corpus(true, $date_du_jour, $annee, $id_structure, null, null, null);
				$donnees_corpus = $this->Personne->query($query_corpus);
				//On enregistre dans la table associée chaque personne
				// debug($donnees_corpus);
				foreach($donnees_corpus as $data){
					$data = $data[0];
					$data['annee'] = $annee;
					$data['trimestre'] = $trimestre;
					$data['structure_referente'] = $id_structure;
					$saved = $this->Tdb2HistoCorpus->save($data);
					debug($saved);
					$this->Tdb2HistoCorpus->clear();
				}
			}

        }

    }