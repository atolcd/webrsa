<?php
App::uses('AppController', 'Controller');
/**
 * Requetes Controller
 *
 * @property Requete $Requete
 */
class RequetesController extends AppController {

    public $components = array('RequestHandler','DataTable','Workflowscers93');
    //public $helpers = array( 'Xform','Csv' );
    public $helpers = array( 'Xform' );

    /**
     * index method
     *
     * @return void
     */
        public function index() {
        $this->DataTable->fields     = $this->Requete->getListeChamps('Requete');
        $this->DataTable->conditions = $this->Requete->getConditions('Requete');
        $user_filtre_zone_geo = $this->Session->read("Auth.User.filtre_zone_geo");
        // vÃ©rification qu'un compte avec un filtre sur zone gÃ©ograhique a une structure rÃ©fÃ©rente
        $user_structurereferente_id = end($this->Workflowscers93->getUserStructurereferenteId( $user_filtre_zone_geo ));

        if (!empty($user_structurereferente_id)) {
            $user_groupement = end(end(end($this->Requete->query('SELECT groupement as "groupement" FROM referentiel.fse_pdv WHERE structurereferente_id = '.$user_structurereferente_id.' LIMIT 1' , FALSE)))) ;
			$this->Session->write("Rsaquery.User.groupement",$user_groupement);
        }
        if ($this->RequestHandler->isAjax() ) {
            $this->autoRender = FALSE;
            Configure::write('debug',0);
			echo(json_encode($this->DataTable->getDataTable()));
        }
        $this->layout = 'cg93';
    } 
    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = NULL) {
        if (!$id) {
            $this->redirect(array('action' => 'index'));
        }
        $requete=$this->Requete->read(NULL, $id) ;

        // contrôle de la validité de la requête
        if (empty($requete) || $requete['Requete']['isactif'] = FALSE || $requete['Requete']['typereq'] !== 'sql') {
            $this->redirect(array('action' => 'index'));
        }
        $user_groupement = $this->Session->read("Rsaquery.User.groupement") ;
        $this->DataTable->entetes    = explode(",",$requete['Requete']['sql_entete']);
        $this->DataTable->getExtraitChamps( $requete['Requete']['sql_select']) ;
        $this->DataTable->conditions = $this->Requete->getExtraitConditions($requete['Requete']['sql_condition'],$user_groupement);
        $this->DataTable->options    = $this->Requete->getExtraitOptions($requete['Requete']['sql_option']);
        if ($this->RequestHandler->isAjax() ) {
            $this->autoRender = FALSE;
            Configure::write('debug',0);
            echo(json_encode($this->DataTable->getDataTableSql()));
            return ;
        }
        $this->set('listefields',$this->DataTable->fields);
        $this->set('requete',$requete);
        $this->set('entetes',$this->DataTable->entetes);

        if ( (!empty($this->data['Requete']['export']) && !empty($this->data['Requete']['id']) && 'csv' == $this->data['Requete']['export'] ) || (!empty($this->request->params['named']['export']))) {
            return $this->exportCSV($id,$requete) ;
        }
        $this->layout = 'cg93';
    }
    private function exportCSV($id,$requete) {
        @ini_set( 'memory_limit', '100M' );
        $this->DataTable->filename = $requete['Requete']['nom'];
        $this->set('extractions', $this->DataTable->getDataTableSql('ALL'));
        $this->autoRender = FALSE;
        //        $this->layout = '';
        //        $this->render('exportcsv') ;
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Requete->create();
            if ($this->Requete->save($this->request->data)) {
                $this->flash(__('Requete saved.'), array('action' => 'index'));
            } else {
            }
        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->Requete->id = $id;
        if (!$this->Requete->exists()) {
            throw new NotFoundException(__('Invalid requete'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Requete->save($this->request->data)) {
                $this->flash(__('The requete has been saved.'), array('action' => 'index'));
            } else {
            }
        } else {
            $this->request->data = $this->Requete->read(null, $id);
        }
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Requete->id = $id;
        if (!$this->Requete->exists()) {
            throw new NotFoundException(__('Invalid requete'));
        }
        if ($this->Requete->delete()) {
            $this->flash(__('Requete deleted'), array('action' => 'index'));
        }
        $this->flash(__('Requete was not deleted'), array('action' => 'index'));
        $this->redirect(array('action' => 'index'));
    }
}
