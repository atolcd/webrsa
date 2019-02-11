<?php

/**
 * Code source de la classe DevelopmentCheckControllersShell.
 *
 * @package app.Console.Command
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */
App::uses( 'AppShell', 'Console/Command' );

/**
 * La classe DevelopmentCheckControllersShell vérifie la bonne utilisation des
 * attributs aucunDroit, commeDroit et crudMap des contrôleurs.
 *
 * @package app.Console.Command
 */
class DevelopmentCheckControllersShell extends AppShell
{
	/**
	 * La constante à utiliser dans la méthode _message() en cas d'erreur.
	 */
	const ERROR_MESSAGE = 0;

	/**
	 * La constante à utiliser dans la méthode _message() en cas d'avertissement.
	 */
	const WARNING_MESSAGE = 1;

	/**
	 * Nombre de messages d'erreur.
	 *
	 * @var integer
	 */
	public $errors = 0;

	/**
	 * Nombre de messages d'avertissement.
	 *
	 * @var integer
	 */
	public $warnings = 0;

	/**
	 * Méthode principale.
	 */
	public function main() {
		$controllers = App::objects('controllers');

		foreach ($controllers as $controller) {
			App::uses($controller, 'Controller');
			$reflect = new ReflectionClass($controller);

			if ($reflect->isAbstract()) {
				continue;
			}
			$Controller = new $controller;

			$actions = array();
			$reflectMethods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);
			foreach ($reflectMethods as $reflectMethod) {
				$actions[] = $reflectMethod->name;
			}

			// Vérifi que les attributs existent et qu'ils sont public
			$missings = array();
			foreach (array('aucunDroit', 'commeDroit', 'crudMap') as $property) {
				if ($reflect->hasProperty($property)) {
					if (!$reflect->getProperty($property)->isPublic()) {
						$this->_message('propertyIsNotPublic', $controller, $property);
					}

				} else {
					$this->_message('missingProperty', $controller, $property);
					$missings[] = $property;
				}
			}

			// Aucun droit
			if (!in_array('aucunDroit', $missings)) {
				foreach ($Controller->aucunDroit as $action) {
					if (!in_array($action, $actions)) {
						$this->_message('missingAction_aucunDroit', $controller, $action);
					}
				}
			}

			// Comme droit
			if (!in_array('commeDroit', $missings)) {
				foreach ($Controller->commeDroit as $action => $redirect) {
					if (!in_array($action, $actions)) {
						$this->_message('missingAction_commeDroit_key', $controller, $action);
					}

					if (!preg_match('/^([A-Z][\w]+):([\w]+)$/', $redirect, $matches)) {
						$this->_message('bad_syntax', $controller, $redirect);
						continue;
					}

					if (!in_array($matches[1].'Controller', $controllers)) {
						$this->_message('controller_not_found', $controller, $redirect);
						continue;
					}

					App::uses($matches[1].'Controller', 'Controller');
					$subReflect = new ReflectionClass($matches[1].'Controller');
					$subActions = array();
					foreach ($a = $subReflect->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectMethod) {
						$subActions[] = $reflectMethod->name;
					}

					if (!in_array($matches[2], $subActions)) {
						$this->_message('missingAction_commeDroit_value', $controller, $redirect);
					}

					// Fait-on référence à un autre controleur (peut-etre de manière erronée) ?
					$otherControllerName = preg_replace( '/^([^:]+):.*$/', '\1', $redirect );
					if( $controller !== "{$otherControllerName}Controller" ) {
						$this->_message('otherController_commeDroit_value', $controller, $redirect, self::WARNING_MESSAGE);
					}
				}
			}

			// Crud map
			if (!in_array('crudMap', $missings)) {
				foreach (array_keys($Controller->crudMap) as $action) {
					if (!in_array($action, $actions)) {
						$this->_message('missingAction_crudMap', $controller, $action);
					}
				}
			}
		}

		$this->out('', 2);
		$message = sprintf('Total de <info>%d</info> erreur(s) et <info>%d</info> avertissement(s) détectée(s)', $this->errors, $this->warnings);
		if(0 === $this->errors) {
			$this->out($message);
			$this->_stop(self::SUCCESS);
		}
		else {
			$this->err($message);
			$this->_stop(self::ERROR);
		}
	}

	protected function _message($message, $controllerName, $propertyName, $type = self::ERROR_MESSAGE) {
		switch ($message) {
			case 'missingProperty':
				$out = 'Propriété manquante dans le controlleur "<info>%s</info>" (<info>%s</info>)';
				break;
			case 'propertyIsNotPublic':
				$out = 'Propriété manquante dans le controlleur "<info>%s</info>" (<info>%s</info>)';
				break;
			case 'missingAction_commeDroit_key':
				$out = 'Une action n\'existe pas dans le controlleur "<info>%s</info>" (<info>%s</info>) sur la propriété <info>commeDroit</info> (clef)';
				break;
			case 'missingAction_commeDroit_value':
				$out = 'Une action n\'existe pas dans le controlleur "<info>%s</info>" (<info>%s</info>) sur la propriété <info>commeDroit</info> (valeur)';
				break;
			case 'otherController_commeDroit_value':
				$out = 'Une valeur de la map <info>commeDroit</info> du controlleur "<info>%s</info>" (<info>%s</info>) fait référence à un autre controller';
				break;
			case 'bad_syntax' :
				$out = 'Mauvaise syntaxe sur la propriété commeDroit du controlleur "<info>%s</info>" (<info>%s</info>)';
				break;
			case 'controller_not_found' :
				$out = 'Erreur sur la propriété <info>commeDroit</info> du controlleur "<info>%s</info>", '
				. 'le controlleur n\'a pas été trouvé avec la valeur : <info>%s</info>';
				break;
			case 'missingAction_aucunDroit':
				$out = 'Une action n\'existe pas dans le controlleur "<info>%s</info>" (%s) sur la propriété <info>aucunDroit</info>';
				break;
			case 'missingAction_crudMap':
				$out = 'Une action n\'existe pas dans le controlleur "<info>%s</info>" (<info>%s</info>) sur la propriété <info>crudMap</info>';
				break;
			default:
				$out = '';
		}

		$message = sprintf($out, $controllerName, $propertyName);

		if(self::WARNING_MESSAGE === $type) {
			$this->out("<warning>Avertissement</warning> {$message}");
			$this->warnings++;
		}
		else {
			$this->err("<error>Erreur</error> {$message}");
			$this->errors++;
		}
	}
}