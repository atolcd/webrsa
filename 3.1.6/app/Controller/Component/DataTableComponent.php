<?php

/**
 * This component provides compatibility between the dataTables jQuery plugin and CakePHP 2
 * @author chris
 * @package DataTableComponent
 * @link http://www.datatables.net/release-datatables/examples/server_side/server_side.html parts of code borrowed from dataTables example
 * @since version 1.1.1
 Copyright (c) 2013 Chris Nizzardini

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.
 */
class DataTableComponent extends Component{

    private $model;
    private $controller;
    private $times = array();
    public $conditionsByValidate = 0;
    public $emptyElements   = 0;
    public $fields          = array();
    public $entetes         = array();
    public $conditions      = array();
    public $filename        = 'data';
    // nombre de lignes maximum extraites pour l'export csv
    public $maxligne        = 50000 ;
    //
    //private $req_select      = array();
    private $req_from        = array();
    private $req_conditions  = array();
    public $mDataProp       = false;
    public $requete         = array();
    private $dt_namecolumns = array();
    private $dt_colSearch   = array();
    private $tableChamps    = array();
    private $listeVide         = "-----" ;
    public function __construct(){

    }

    public function initialize(Controller $controller){
        $this->controller = $controller;
        $modelName = $this->controller->modelClass;
        $this->model = $this->controller->{$modelName};
    }

    /**
     * returns dataTables compatible array - just json_encode the resulting aray
     * @param object $controller optional
     * @param object $model optional
     * @return array
     */

    public function getDataTable($model=null){

        if($model != null ){
            if(is_string($model)){
                $this->model = $this->controller->{$model};
            }
            else{
                $this->model = $model;
                unset($model);
            }
        }

        if(isset($this->controller->request->query)){
            $httpGet = $this->controller->request->query;
        }
        // initialisation du tableau de correspondance champs => colonne
        foreach ($this->fields as $cle=>$options) {
            $dt_namecolumns[]    = $cle ;
            $tableChamps[] = $cle ;
            $valeurs = explode(',',$options);
            if (!empty($valeurs)) {
                if (in_array('SEARCH',$valeurs)) {
                    $dt_colSearch[] = TRUE ;
                } else {
                    $dt_colSearch[] = FALSE ;
                }
            }
        }
        $dt_columns        = array() ;
        // réagencement du tableau par no de colonne
        if (isset($httpGet['columns'])) {
            foreach($httpGet['columns'] as $columns) {
                $dt_columns[$columns['data']] = $columns ;
                $dt_columns[$columns['data']]['name'] = $dt_namecolumns [$columns['data']] ;
                $dt_columns[$columns['data']]['searchable'] = $dt_colSearch [$columns['data']] ;
            }
        }
        // draw datatable
        $dt_draw = intval(isset($httpGet['draw'])?$httpGet['draw']:0);
        // info pagination
        $paginationDebut   = isset($httpGet['start'])?$httpGet['start']:0;
        $paginationNbLig   = isset($httpGet['length'])?$httpGet['length']:20;
        $paginationNbLig   = ((-1 == $paginationNbLig))?20:$paginationNbLig ;
        $requete['offset'] = $paginationDebut ;
        $requete['limit']  = $paginationNbLig ;

        // info colonnes
        $colonneNb         = isset($httpGet['columns'])?count($httpGet['columns']):count($dt_columns);
        $requete['fields'] = implode(',',$dt_namecolumns);
        // tri par colonne
        $dt_order       = array();
        if ( isset( $httpGet['order']  ) )
        {
            for ( $i=0 ; $i< count( $httpGet['order']); $i++ )
            {
                $dt_orderCol    = isset( $httpGet['order'][$i]['column']  )?intval($httpGet['order'][$i]['column']):NULL;
                $dt_orderdir    = isset( $httpGet['order'][$i]['dir']  )?$httpGet['order'][$i]['dir']:'ASC';
                $dt_colSortable = isset( $dt_columns[$dt_orderCol]['orderable'] )?$dt_columns[$dt_orderCol]['orderable']:FALSE;
                if ( $dt_colSortable == "true" )
                {
                    $dt_order[$dt_columns[$dt_orderCol]['name']] = $dt_orderdir;
                }
            }
            if ( !empty($dt_order) )
            {
                $requete['order'] = $dt_order;
            }
        }
        // récupération des conditions obligatoires
        $requete['conditions'] = $this->conditions;
        // Filtre globale
        $conditionsGlobales = array();
        $dt_search            = isset( $httpGet['search']['value']  )?$httpGet['search']['value']:NULL;
        if(!empty($dt_search))
        {
            for ($i = 0 ; $i < count($dt_columns) ; $i++)
            {
                if ( TRUE == $dt_columns[$i]['searchable']) {
                    $conditionsGlobales['OR'][$dt_columns[$i]['name'].' LIKE '] = '%'.$dt_search.'%' ;
                }
            }
        }
        // Filtre par colonne

        $conditionsColonnes = array();
        for ( $i = 0 ; $i< count($dt_columns); $i++ )
        {
            $dt_searchable = isset( $dt_columns[$i]['searchable'] )?("true" == $dt_columns[$i]['searchable']):FALSE;
            $dt_regex      = isset( $dt_columns[$i]['search']['regex'] )?("true" == $dt_columns[$i]['search']['regex'] ):FALSE;
            $dt_search       = isset( $dt_columns[$i]['search']['value'] )?$dt_columns[$i]['search']['value']:'';
            if( $dt_searchable == TRUE && $dt_search != '' && $dt_regex == TRUE )
            {
                $conditionsColonnes[$dt_columns[$i]['name'].' LIKE '] = '%'.(($dt_search == $this->listeVide)?'':$dt_search).'%' ;
            }
            if( $dt_searchable == TRUE && $dt_search != '' && $dt_regex == FALSE)
            {
                $conditionsColonnes[$dt_columns[$i]['name']] = ($dt_search == $this->listeVide)?'':$dt_search ;
            }
        }
        // calcul du nb de ligne sans filtre
        //$this->model->recursive = $recursive;
        //
        $total = $this->model->find('count',array('conditions'=>$requete['conditions']));
        // calcul du nb de ligne avec filtre
        $requete['conditions'] = array_merge($requete['conditions'],$conditionsGlobales,$conditionsColonnes);
        $filtreTotal           = $this->model->find('count',array('conditions'=>$requete['conditions']));
        $resultatReqs          = $this->model->find('all',$requete);
        // suppression les labels de champs pour tous les modeles présents
        $resultat    = array();
        //$aColumnsInv = array_flip($aColumns);
        foreach($resultatReqs as $resultatReq) {
            $resultatReq =  hash::flatten($resultatReq,'.');
            $resultat[] = array_values($resultatReq) ;
        }
        return array(
          'draw' => $dt_draw,
          'recordsTotal' => $total,
          'recordsFiltered' => $filtreTotal,
          'data' => $resultat
        );

    }
    //
    public function getDataTableSql($option=NULL){

        //$this->model->useDbConfig = 'default';
        // $this->getExtraitChamps($req_sql) ;

        if(isset($this->controller->request->query)){
            $httpGet = $this->controller->request->query;
        }
        // initialisation du tableau de correspondance champs => colonne
        // fields contient les libellés
        // entetes contient les règles CACHE SEARCH posées sur les colonnes
        //prise en compte uniquement des entetes pour datatable
        $entetesDatatable = array();
        $col = 0 ;
        foreach ($this->entetes as $entete) {
            $valeurs = (empty($entete))?array():explode('-',$entete);
            if ( 'ALL' != $option && array_intersect($valeurs,array('SEARCH','SHOW','CACHE'))) {
                $entetesDatatable[] = array('numcol' => $col,'valeurs'=>$valeurs) ;
            }
            if ( 'ALL' == $option ) {
                $entetesDatatable[] = array('numcol' => $col,'valeurs'=>$valeurs);
            }
            $col++ ;
        }
        $col = 0 ;
        $cles = array_keys($this->fields);
        //foreach ($this->fields as $cle=>$libelle) {
        foreach ($entetesDatatable as $enteteDatatable) {
            $dt_namecolumns[]    = $cles[$enteteDatatable['numcol']] ;
            //$tableChamps[] = $cles[$enteteDatatable['numcol']] ;
            $valeurs = $enteteDatatable['valeurs'];
            if (!empty($valeurs)) {
                if (in_array('SEARCH',$valeurs)) {
                    $dt_colSearch[] = TRUE ;
                } else {
                    $dt_colSearch[] = FALSE ;
                }
            }
            $col++ ;
        }
        $dt_columns        = array() ;
        // réagencement du tableau par no de colonne
        if (isset($httpGet['columns'])) {
            foreach($httpGet['columns'] as $columns) {
                $dt_columns[$columns['data']] = $columns ;
                $dt_columns[$columns['data']]['name'] = $dt_namecolumns [$columns['data']] ;
                $dt_columns[$columns['data']]['searchable'] = $dt_colSearch [$columns['data']] ;
            }
        }
        // draw datatable
        $dt_draw = intval(isset($httpGet['draw'])?$httpGet['draw']:0);
        // info pagination
        $paginationDebut   = isset($httpGet['start'])?$httpGet['start']:0;
        $paginationNbLig   = isset($httpGet['length'])?$httpGet['length']:20;
        $paginationNbLig   = ((-1 == $paginationNbLig))?20:$paginationNbLig ;
        $requete['offset'] = $paginationDebut ;
        $requete['limit']  = $paginationNbLig ;

        // info colonnes
        $colonneNb         = isset($httpGet['columns'])?count($httpGet['columns']):count($dt_columns);
        $requete['fields'] = implode(',',$dt_namecolumns);
        // tri par colonne
        $dt_order       = "" ;
        $requete['order'] = '';
        if ( isset( $httpGet['order']  ) )
        {
            for ( $i=0 ; $i< count( $httpGet['order']); $i++ )
            {
                $dt_orderCol    = isset( $httpGet['order'][$i]['column']  )?intval($httpGet['order'][$i]['column']):NULL;
                $dt_orderdir    = isset( $httpGet['order'][$i]['dir']  )?$httpGet['order'][$i]['dir']:'ASC';
                $dt_colSortable = isset( $dt_columns[$dt_orderCol]['orderable'] )?$dt_columns[$dt_orderCol]['orderable']:FALSE;
                if ( $dt_colSortable == "true" )
                {
                    if (!empty($dt_order)) {
                        $dt_order.=', ' ;
                    }
                    $dt_order .= $dt_columns[$dt_orderCol]['name']." ".$dt_orderdir;
                }
            }
            if ( !empty($dt_order) )
            {
                $requete['order'] = 'ORDER BY '.$dt_order;
            }
        }
        // initialisation du tableau contenant toutes les conditions
        $req_conditions = array();
        // Filtre globale
        $conditionsGlobales = "";
        $motCleOR           = "" ;
        $dt_search            = isset( $httpGet['search']['value']  )?$httpGet['search']['value']:NULL;
        if(!empty($dt_search))
        {
            for ($i = 0 ; $i < count($dt_columns) ; $i++)
            {
                if ( TRUE == $dt_columns[$i]['searchable']) {
                    $conditionsGlobales .= $motCleOR.$dt_columns[$i]['name'].' ILIKE \'%'.$dt_search.'%\'' ;
                    $motCleOR = " OR ";
                }

            }
            if ( !empty($conditionsGlobales)) {
                $req_conditions[] = '( '.$conditionsGlobales.' ) ';
            }
        }
        // Filtre par colonne
        $conditionsColonnes = "";
        $motCleAND          = "" ;
        for ( $i = 0 ; $i< count($dt_columns); $i++ )
        {
            $dt_searchable = isset( $dt_columns[$i]['searchable'] )?("true" == $dt_columns[$i]['searchable']):FALSE;
            $dt_regex      = isset( $dt_columns[$i]['search']['regex'] )?("true" == $dt_columns[$i]['search']['regex'] ):FALSE;
            $dt_search       = isset( $dt_columns[$i]['search']['value'] )?$dt_columns[$i]['search']['value']:'';
            if( $dt_searchable == TRUE && $dt_search != '' && $dt_regex == TRUE )
            {
                $conditionsColonnes.= $motCleAND.$dt_columns[$i]['name'].' ILIKE \'%'.(($dt_search == $this->listeVide)?'':$dt_search).'%\'' ;
                $motCleAND = " AND " ;
            }
            if( $dt_searchable == TRUE && $dt_search != '' && $dt_regex == FALSE)
            {
                $conditionsColonnes.=  $motCleAND.$dt_columns[$i]['name']. ' = \''.($dt_search == $this->listeVide)?'':$dt_search.'\'' ;
                $motCleAND = " AND " ;
            }
        }
        if ( !empty($conditionsColonnes)) {
            $req_conditions[] = '( '.$conditionsColonnes.' ) ';
        }
        // calcul du nb de ligne sans filtre
        //$this->model->recursive = $recursive;
        //
        // récupération des conditions obligatoires
        $motCleWHERE = "" ;
        if (!empty($this->conditions)) {
            $req_conditions[] =    $this->conditions;
            $motCleWHERE = " WHERE " ;
        }
        $sql_count = 'SELECT count(*) '.$this->req_from.$motCleWHERE.$this->conditions;
        $total = end(array_pop(array_pop($this->model->query($sql_count,FALSE))));
        // concaténation de toutes les conditions
        if (count($req_conditions) > 0) {
            $motCleWHERE = " WHERE " ;
        }
        $sql_conditions = implode(' AND ',$req_conditions) ;
        $sql_count = 'SELECT count(*) '.$this->req_from.$motCleWHERE.$sql_conditions;

        $filtreTotal = $total ; // par défaut si pas de conditions ajoutées
        //$filtreTotal           = $this->model->find('count',array('conditions'=>$requete['conditions']));
        if (!empty($conditionsGlobales) || !empty($conditionsColonnes)) {
            // $filtreTotal = hash::flatten($this->model->query($sql_count),'.');
            $filtreTotal = end(array_pop(array_pop($this->model->query($sql_count,FALSE))));
        }
        //$resultatReqs          = $this->model->find('all',$requete);
        // extraction limitée pour vue dataTable
        if ( 'ALL' != $option ) {
            $sql_exe = 'SELECT '.$requete['fields'].' '.$this->req_from.$motCleWHERE.$sql_conditions.$requete['order'];
            $sql_exe .= ' LIMIT '.$requete['limit'].' OFFSET '.$requete['offset'];
            $resultatReqs= $this->model->query($sql_exe,FALSE);
            // suppression les labels de champs pour tous les modeles présents
            $resultat    = array();
            //$aColumnsInv = array_flip($aColumns);
            foreach($resultatReqs as $resultatReq) {
                $resultat[] = array_values(array_pop($resultatReq));
                //$resultatReq =  hash::flatten($resultatReq,'.');
            }
            return array(
          'draw' => $dt_draw,
          'recordsTotal' => $total,
          'recordsFiltered' => $filtreTotal,
          'data' => $resultat
            );
        }
        // extraction avec export csv pour ne pas consommer de la mémoire
        if ( 'ALL' == $option ) {
            $sql_exe = 'SELECT '.$requete['fields'].' '.$this->req_from.$motCleWHERE.$sql_conditions.$requete['order'];
            $offset  = 0 ;
            $limit   = 1000 ;
            $resultat    = array();
            header("Content-disposition:attachment;filename=".$this->filename.'-'.date( 'Ymd-Hhm' ).'.csv');
   		 	header("Content-type:application/vnd.ms-excel");
   			echo implode(";",$this->fields)."\n";
            while ($offset < $filtreTotal) {
                $sqlexp_exe   = $sql_exe.' LIMIT '.$limit.' OFFSET '.$offset;
                $resultatReqs = $this->model->query($sqlexp_exe,FALSE);
                foreach($resultatReqs as $resultatReq) {
                    echo mb_convert_encoding(implode(";",array_values(array_pop($resultatReq))),'ISO-8859-1','UTF-8' )."\n";
                }
                $offset = $offset + count($resultatReqs);
            }
            return ;
        }


    }

    // extrait les noms des colonnes de la requêtes select colonne as champs from tables
    //
    public function getExtraitChamps($requete) {
        $motsqlminus  = array("select "," from"," as ");
        $motsqlmajus  = array("SELECT "," FROM"," AS ");
        $requete = str_replace($motsqlminus,$motsqlmajus,$requete);
        $req_select = substr($requete,0,strpos($requete,"FROM"));
        $req_select = str_replace("SELECT","",$req_select);
        $this->req_from = substr($requete,strpos($requete,"FROM"));
        $reqlistes = explode(',',$req_select) ;
        $liste = array();
        foreach ($reqlistes as $reqliste) {
            $element = explode(' AS ',$reqliste);
            $liste[trim($element[0])] = str_replace("#v",",",trim(strtr($element[1],'"',' '))) ;
        }
        $this->fields     = $liste;
        return ;
    }


}