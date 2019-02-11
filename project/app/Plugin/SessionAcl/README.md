# Plugin SessionAcl pour CakePHP 2.x.x

SessionAcl permet un gain de performances sur le contrôle des droits d'accès en mémorisant les droits en session. Il permet également de consulter ces droits à n'importe quel endroit de l'application.

## Guide d'installation

- Placez le répertoire « SessionAcl » dans un répertoire Plugin de l'application.
- Ajoutez 'SessionAcl' dans le `CakePlugin::loadAll` du `app/Config/bootstrap.php`
- Ajoutez / modifiez le component Acl dans le fichier `app/Controller/AppController.php`
	Mettre le paramètre `"className" => "SessionAcl.SessionAcl"`

```php
public $components = array(
    'Session',
    'Auth',
    'Acl' => array(
        'className' => 'SessionAcl.SessionAcl'
    )
);
```

Le droits seront d'ores et déjà conservé en Session.


## Optimisation

Vous pouvez, si vous disposez d'une base __Postgresql__, effectuer un chargement rapide des droits lors de la connexion.
Voici la marche à suivre :

Placez le résultat de la requête `SessionAclUtility::fastPostgresGetAll` dans la Session sous la clef `Auth.Permissions` lors de la connexion de l'utilisateur.

```php
public function login() {
	// L'utilisateur vient de se connecter
	if ($this->Auth->login()) { 
		// ...code...
		App::uses('SessionAclUtility', 'SessionAcl.Utility');
		$permissions = SessionAclUtility::fastPostgresGetAll(
			$this->User,
			$this->Session->read('Auth.User.id')
		);
		$this->Session->write('Auth.Permissions', $permissions);
	}
}
```








## Fonctionnalités intéressantes

Vérifiez les accès n'importe où dans l'application :

```php
SessionAcl::check('controllers/Monmodule/monaction');
```






## Console

```bash
cake SessionAcl.SessionAcl update Aco
```

Permet de nettoyer la table des acos :
-Supprime les acos enfant de « controllers » qui ne correspondent pas à un Controller / Action de l'application.
-Supprime les acos orphelins (dont le parent n'existe plus)
-Ajoute les acos manquant (les Controller / Action de l'application)
-Recalcule les left et right (algorithme de calcul revu pour de meilleurs performances)

```bash
cake SessionAcl.SessionAcl update Aro
```

Permet de nettoyer la table des aros :
-Supprime les aros qui ne correspondent pas à un Requester de l'application.
-Supprime les aros orphelins (dont le parent n'existe plus)
-Ajoute les aros manquant (les Requesters de l'application)
-Recalcule les left et right

A lancer lors d'ajout de nouveaux controllers

```bash
cake SessionAcl.SessionAcl forceHeritage
```

Permet de retirer des entrées de la table aros_acos qui font doublons. Si l'enfant possède les mêmes droits d'accès que le parent sur un Controlleur / Action, la ligne correspondante est supprimé de la table des aros_acos. Les droits restent identiques mais l'héritage est rétabli.

A lancer avant la modification d'un Groupe si vous souhaitez que la modification se répercute sur les enfants.

```bash
cake SessionAcl.SessionAcl deleteOrphans
```

Supprime les acos orphelins (dont le parent n'existe plus)

A lancer lors de la suppression d'un Controller

```bash
cake SessionAcl.SessionAcl fastRecover
```

Recalcule les left et right (algorithme de calcul revu pour de meilleurs performances)

A lancer en cas de modification « à la main » d'une table sous TreeBehavior (Aro ou Aco)
