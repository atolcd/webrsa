<?php
App::uses('AppModel', 'Model');
/**
 * Requete Model
 *
 */
class Requete extends AppModel {

    /**
     * Use database config
     *
     * @var string
     */
    public $useDbConfig = 'ref';
    public function executeSql($requete) {
        $this->useDbConfig = 'default';
        //   $db = $this->getDataSource();
        // return  $db->fetchAll($requete['Requete']['sql_select']) ;
        $resultat = $this->query($requete['Requete']['sql_select']) ;
        $resultat = hash::extract($resultat,'{n}.{n}');
        return $resultat ;
    }
    public function getListeChamps($vue = null) {
        switch($vue) {
            case 'Requete':
            default:
                return array (
                'Requete.id'                => 'CACHE',
				'Requete.nom'               => '',
                'Requete.typereq'           => 'LIEN,SEARCH',
                'Requete.description'          => ''
                ) ;
                break ;
        }
    }
    // retourne les conditions de sélection pour la vue index et csv
    public function getConditions($vue = NULL) {
        switch($vue) {
            case 'Requete':
            default:
                return array (
                'Requete.isactif'                => TRUE
                ) ;
                break ;
        }
    }
    // extrait les noms des colonnes de la requêtes
    public function getExtraitChamps($requete) {
        $requete = strtoupper($requete);
        $requete = str_replace("SELECT","",$requete);
        $req_select= substr($requete,0,strrpos($requete,"FROM"));
        $reqlistes = explode(',',$req_select) ;
        $liste = array();
        foreach ($reqlistes as $reqliste) {
            $element = explode(' AS ',$reqliste);
            $liste[trim($element[0])] = trim(strtr($element[1],'"',' ')) ;
        }
        return $liste;
    }

    // extrait les noms des colonnes de la requêtes
    public function getExtraitConditions($requete,$user_groupement=NULL) {
        if (!empty($user_groupement)) {
            $requete = "(".$requete.") AND ( groupement = ".$user_groupement." ) ";
        }
      return $requete ;
    }
    // extrait les options de tri (ORDER), de groupement( GROUP BY) de la requêtes
    public function getExtraitOptions($requete) {
        return $requete ;
    }
}
