<?php
/**
	 * Code source de la classe CreationHistoriquesdroitsShell.
	 *
	 * @package app.Console.Command
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	 App::uses( 'XShell', 'Console/Command' );
	 App::uses( 'ConnectionManager', 'Model' );
	 App::uses( 'View', 'View' );

	/**
	 * La classe CreationHistoriquesdroitsShell permet de créer l'historique des droits des personnes
     * à partir d'ancien Flux Bénificiaires mensuels.
     * La commande se fait par fichier
	 *
	 * sudo -u apache ./vendor/cakephp/cakephp/lib/Cake/Console/cake CreationHistoriquesdroits -app app FICHIER
	 *
	 * @package app.Console.Command
	 */
	class CreationHistoriquesdroitsShell extends XShell
	{

        public $uses = array(
            'Personne',
            'Historiquedroit'
        );

        function lit_xml($fichier,$item,$champs) {
            // on lit le fichier
            if($chaine = @implode("",@file($fichier))) {
               // on explode sur <item>
               // Dans l'exemple il s'agit de 'profil'
               $tmp = preg_split("/<\/?".$item.">/",$chaine);
               // pour chaque <item> donc tous les profils
               for($i=1;$i<sizeof($tmp)-1;$i+=2)
                   // on lit les champs demandés <champ> donc il s'agit de 'id' et 'prenom'
                  foreach($champs as $champ) {
                     $tmp2 = preg_split("/<\/?".$champ.">/",$tmp[$i]);
                     // on ajoute l'élément au tableau
                     $tmp3[$i-1][] = @$tmp2[1];
                  }
               // et on retourne le tableau dans la fonction
               return $tmp3;
            }
          }

        function main() {
            // Récupération de la date du flux
            $xmlDate = $this->lit_xml($this->args[0], 'IdentificationFlux', array('DTREF'));
            $dateToInsert = $xmlDate[0][0] . ' 00:00:00';
            $infos = array(
                'NOM',
                'PRENOM',
                'NIR',
                'ETATDOSRSA',
                'ROLEPERS',
                'TOPPERSDRODEVORSA',
                'MOTICLORSA'
            );

            $xmlInfos = $this->lit_xml($this->args[0], 'InfosFoyerRSA', $infos);

            $datas = array();
            $countPersonnesNonAjoutees = $countPersonnesMAJ = $countPersonnesMAJOld = $countPersonnesAjoutees = 0;
            // Préparation des données à sauvegarder
            foreach($xmlInfos as $info) {
                if($info[4] == 'DEM' || $info[4] == 'CJT') {
                    // Recherche de l'id de la personne récupérée
                    $idPersonne = $this->Personne->find('first', array(
                        'fields' => array('Personne.id'),
                        'recursive' => 0,
                        'conditions' => array(
                            'Personne.nom' => $info[0],
                            'Personne.prenom' => $info[1],
                            'Personne.nir LIKE \''. substr($info[2], 0, 13) . '%\'' // NIR sur 13 caractères
                        )
                    ));
                    if( !empty($idPersonne) ) {
						//Identifiant de la personne à mettre à jour
						$idPersonne = $idPersonne['Personne']['id'];
						//Recherche de l'historique précédent
						$histoPersonne = $this->Historiquedroit->find('first', array('conditions' => array('Historiquedroit.personne_id' => $idPersonne) ) );
						//Si on a un historique précédent
						if( !empty($histoPersonne) ){
							//Si l'historique est toujours le même
							if ( $histoPersonne['Historiquedroit']['etatdosrsa'] == $info[3] ) {
								//On déplace la date de fin
								$date = array('modified' => $dateToInsert);
								$idHisto = array( 'id' => $histoPersonne['Historiquedroit']['id']);
								$countPersonnesMAJ++;
							}else{ //Sinon
								//Il faut déplacer la date de fin de l'historique précédent
	                            $idOldHisto = array( 'id' => $histoPersonne['Historiquedroit']['id']);
	                            $dateOld = array('modified' =>  date('Y-m-d', strtotime($dateToInsert.' - 1 DAY')) . ' 00:00:00' );
		                        $datas[] = array_merge(
		                            array(
		                                'personne_id' => $idPersonne,
									),
									$dateOld,
									$idOldHisto
								);
								$countPersonnesMAJOld++;
								//Et crée une nouvelle ligne avec le nouvel historique
								$date = array('created' => $dateToInsert, 'modified' => $dateToInsert);
								$idHisto = array();
								$countPersonnesAjoutees++;
							}
						} else { //Sinon on crée une nouvelle ligne pour la personne
							$date = array('created' => $dateToInsert, 'modified' => $dateToInsert);
							$idHisto = array();
							$countPersonnesAjoutees++;
						}

						$datas[] = array_merge(
							array(
								'personne_id' => $idPersonne,
								'toppersdrodevorsa' => $info[5],
								'etatdosrsa' => $info[3],
								'moticlorsa' => $info[6]
							),
							$date,
							$idHisto
						);
						$idHisto = array();

                    } else {
                        $this->out( 'La personne ' . $info[0] . ' ' . $info[1] . ' ayant le NIR ' . $info[2] . ' n\'a pas été trouvée en base.' ).
                        $this->out( 'Les informations de cette personnes était : ');
                        $this->out( 'toppersdrodevorsa : ' . $info[5] . ', etatdosrsa : ' . $info[3] . ', moticlorsa : ' . $info[6] );
                        $this->out();
                        $this->out();
                        $countPersonnesNonAjoutees++;
                    }
                }
            }

            $this->out($countPersonnesAjoutees . ' personnes seront ajoutées à la table historiquesdroits.' );
            $this->out('- dont ' . $countPersonnesMAJOld . ' personnes ont changer d\'état.' );
            $this->out($countPersonnesMAJ . ' personnes seront mise à jour dans la table historiquesdroits.' );
            $this->out($countPersonnesNonAjoutees . ' personnes ne seront pas ajoutées à la table historiquesdroits dû à un problème de personne non trouvé en base.' );
            $success = $this->Historiquedroit->saveAll($datas);
            if($success) {
                $this->out( 'Insertion terminée' );
            } else {
                $this->out( 'Les insertions n\'ont pas été effectuées' );
            }
        }
    }