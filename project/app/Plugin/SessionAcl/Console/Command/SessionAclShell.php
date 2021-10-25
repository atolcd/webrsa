<?php
/**
 * Fichier source de la classe SessionAclShell.
 *
 * PHP 5.3
 *
 * @package SessionAcl.Console.Command
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

App::uses('AppShell', 'Console/Command');
App::uses('AppController', 'Controller');
App::uses('SessionAclUtility', 'SessionAcl.Utility');
App::uses('SessionAcl', 'SessionAcl.Model/Datasource');
App::uses('CakeEventManager', 'Event');
App::uses('CakeEvent', 'Event');

/**
 * La classe SessionAclShell permet l'initialisation de SessionAcl
 * 
 * @package SessionAcl.Console.Command
 */
class SessionAclShell extends AppShell
{
	/**
	 * @var SessionAclComponent 
	 */
	public $sessionAclComponent;
	
	/**
	 * @var string
	 */
	public $sessionAclUtility = 'SessionAclUtility';
	
	/**
	 * @var string
	 */
	public $sessionAclDatasource = 'SessionAcl';
	
	/**
	 * Méthode par défault
	 */
	public function main() {
		$this->err("<error>Méthodes disponnibles</error> : update Aro|Aco, forceHeritage, deleteOrphans Aro|Aco, fastRecover Aro|Aco");
		$this->_stop(self::ERROR);
	}
	
	/**
	 * Initialise le plugin
	 * 
	 * @throw Exception Si le plugin n'est pas correctement installé dans AppController
	 */
	public function startup() {
		$appController = new AppController();
		$appController->constructClasses();
		
		if (get_class($appController->Acl) === 'SessionAclComponent') {
			$SessionAclUtility = $this->sessionAclUtility;
			$SessionAclDatasource = $this->sessionAclDatasource;
			$SessionAclUtility::initialize();
			$this->sessionAclComponent = $SessionAclDatasource::get('acl');
		} else {
			throw new Exception("Le plugin SessionAcl ne semble pas être présent dans les components de AppController.");
		}
		
		parent::startup();
	}
	
	/**
	 * Ecoute certains évènements
	 * 
	 * @param CakeEvent $Event
	 */
	public function eventListener(CakeEvent $Event) {
		if ($Event->name() === 'SessionAcl.insert') {
			$this->out(sprintf("Ajout d'un %s : %s - %s::%s",
				str_pad($Event->subject()->useTable, 9),
				str_pad($Event->subject()->id, 7),
				$Event->data['parent'],
				$Event->data['alias']
			));
			
		} elseif ($Event->name() === 'SessionAcl.delete') {
			$deleteString = "Suppression d'un %s : %s - %s::%s";
			$items = array();
			foreach ($Event->data as $values) {
				if (!empty($values['Permission'])) {
					$this->out(sprintf($deleteString,
						str_pad($Event->subject()->Permission->useTable, 9),
						str_pad($values['Permission']['id'], 7),
						$values['parent']['alias'],
						$values[$Event->subject()->alias]['alias']
					));
				}
				
				// Les identiques s'écrasent
				$items[$values[$Event->subject()->alias]['id']] = array(
					'alias' => $values[$Event->subject()->alias]['alias'],
					'parent' => $values['parent']['alias'],
				);
			}
			
			foreach ($items as $id => $values) {
				$this->out(sprintf($deleteString,
					str_pad($Event->subject()->useTable, 9),
					str_pad($id, 7),
					$values['parent'],
					$values['alias']
				));
			}
		}
	}
	
	/**
	 * Met à jour les Aco en fonction des controlleurs de l'application
	 * Met à jour les Aro en fonction des requesters
	 * 
	 * @see SessionAclUtility::updateAcos()
	 */
	public function update() {
		if (!isset($this->args[0])) {
			$this->err("<error>Argument manquant</error> : veuillez indiquer un nom de Model (Aro | Aco)");
			$this->_stop(self::ERROR);
		}
		
		$SessionAclUtility = $this->sessionAclUtility;
		
		$SessionAclUtility::initialize();
		$Model = $this->sessionAclComponent->{$this->args[0]};
		
		CakeEventManager::instance()->attach(array($this, "eventListener"), 'SessionAcl.insert');
		CakeEventManager::instance()->attach(array($this, "eventListener"), 'SessionAcl.delete');
		
		if (method_exists($Model, 'getDataSource')) {
			$Model->begin();
		}
		
		$this->out("<warning>Suppression des orphelins...</warning>");
		$SessionAclUtility::deleteOrphans($Model, false);
		
		// Nécéssaire pour éviter des problèmes en cas de suppression
		$this->out("<warning>Reconstruction des left et right...</warning>");
		$SessionAclUtility::fastRecover($Model, false);
		
		$this->out("<warning>Récupération de la liste des {$Model->useTable}...</warning>");
		$SessionAclUtility::initUpdate($Model);
		
		if (preg_match('/aro/i', $Model->alias)) {
			$this->out("<warning>Recherche des aros manquant...</warning>");
			$success = $SessionAclUtility::addMissingsAros(false);
			
			$this->out("<warning>Suppression des aros en trop...</warning>");
			$success = $success && $SessionAclUtility::deleteNotExistingAros(false);
		} else {
			$this->out("<warning>Recherche des {$Model->useTable} manquant...</warning>");
			$success = $SessionAclUtility::addMissingsAcos(false);
			
			$this->out("<warning>Suppression des {$Model->useTable} en trop...</warning>");
			$success = $success && $SessionAclUtility::deleteNotExistingAcos(false) && $SessionAclUtility::deleteNotExistingAliasAcos();

		}
		
		$this->out("<warning>Suppression des orphelins...</warning>");
		$SessionAclUtility::deleteOrphans($Model, false);
		
		if ($success) {
			$this->out("<success>La mise à jour des {$Model->useTable} a été effectuée avec succès</success>");
		} else {
			$this->out("<error>Un problème est survenu lors de la mise à jour des {$Model->useTable}!</error>");
		}
		
		if (method_exists($Model, 'getDataSource')) {
			if ($success) {
				$this->out("<warning>Reconstruction des left et right...</warning>");
				$SessionAclUtility::fastRecover($Model, false);
				$Model->commit();
			} else {
				$Model->rollback();
			}
		}

		if ($success) {
			$this->out('update terminé avec succès!');
			$this->_stop(self::SUCCESS);
		} else {
			$this->err('update terminé avec erreur!');
			$this->_stop(self::ERROR);
		}
	}
	
	/**
	 * Rétabli l'héritage si l'enfant possède les mêmes droits que le parent
	 */
	public function forceHeritage() {
		$SessionAclUtility = $this->sessionAclUtility;
		$count = $SessionAclUtility::forceHeritage();

		if ($count !== false) {
			$this->out(sprintf("Suppression de %d permissions", $count));
			$this->_stop(self::SUCCESS);
		} else {
			$this->err('Impossible de supprimer les permissions');
			$this->_stop(self::ERROR);
		}
	}
	
	/**
	 * Supprime les orphelins (dont le parent_id n'existe plus)
	 */
	public function deleteOrphans() {
		$SessionAclUtility = $this->sessionAclUtility;
		if (!isset($this->args[0])) {
			$this->err("<error>Argument manquant</error> : veuillez indiquer un nom de Model (Aro | Aco)");
			$this->_stop(self::ERROR);
		}
		
		if ($SessionAclUtility::deleteOrphans(ClassRegistry::init($this->args[0]))) {
			$this->out("Suppression des orhelins effectué avec succès !");
			$this->_stop(self::SUCCESS);
		} else {
			$this->err('Impossible de supprimer les orphelins !');
			$this->_stop(self::ERROR);
		}
	}
	
	/**
	 * Rétabli les left et les right
	 */
	public function fastRecover() {
		$SessionAclUtility = $this->sessionAclUtility;
		if (!isset($this->args[0])) {
			$this->err("<error>Argument manquant</error> : veuillez indiquer un nom de Model (Aro | Aco)");
			$this->_stop(self::ERROR);
		}
		
		if ($SessionAclUtility::fastRecover(ClassRegistry::init($this->args[0]))) {
			$this->out("Calcul des left et right effectué");
			$this->_stop(self::SUCCESS);
		} else {
			$this->err('Impossible de redifinir les left et les right');
			$this->_stop(self::ERROR);
		}
	}
}