# Plugin Gedooo pour CakePHP 2.x

## Introduction

Le plugin Gedooo fournit un point d'entrée unique (une couche d'abstraction) pour la génération de document PDF à partir d'un modèle ODT et de données qui seront envoyées en tant que variables utilisateur lors de la fusion documentaire.

La couche d'abstraction se fait tant au niveau de la version des librairies __phpgedooo__ utilisées, que du mode de conversion du document fusionné en PDF.

La fusion génère automatiquement le nom des variables utilisateur, détermine leur type et permet leur export au format CSV lors de la fusion en mode ```debug > 0```.

## Pré-requis

- CakePHP 2.x
- Plugin Appchecks (dans le code source de web-rsa) pour les fonctions de vérification (gedTests, TestGedoooShell).
- le serveur cloudooo (https://github.com/Nexedi/cloudooo) si la transformation du document fusionné en PDF se fai par ce moyen
- le binaire unoconv (https://github.com/dagwieers/unoconv) si la transformation du document fusionné en PDF se fai par ce moyen
- le binaire pdftk (https://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/) si l'on souhaite concaténer des fichiers PDF avant de retourner un seul fichier à l'utilisateur

## Configuration

Au moyen de de la classe ```Configure``` de CakePHP, et avec le préfixe Gedooo, les variables de configuration ci-dessous sont disponibles.

- **method:** la méthode permettant de générer le document de sortie; valeurs possibles: classic, unoconv, cloudooo (ainsi que cloudooo_ancien et unoconv_ancien)
- **wsdl:** l'URL du web-service, utilisé quelle que soit la méthode de génération; exemple: http://192.168.2.35:8880/ODFgedooo/OfficeService?wsdl
- **unoconv_bin:** chemin vers le binaire unoconv, lorsque la méthode unoconv ou unoconv_ancien est paramétrée; exemple: /usr/bin/unoconv
- **cloudooo_host:** le serveur cloudooo, lorsque la méthode cloudooo ou cloudooo_ancien est utilisée; exemple: 192.168.2.35
- **cloudooo_port:** le port du serveur cloudooo, lorsque la méthode cloudooo ou cloudooo_ancien est utilisée; exemple: 8012
- **debug_export_data:** permet d'exporter les données envoyées en plus des noms des variables dans un fichier du répertoire app/tmp/logs lorsque l'on n'est pas en mode production; false (par défaut) ou true
- **dont_force_newlines:** pour que les retours à la ligne ne soient pas forcés (ancien comportement de unoconv et cloudooo); false (par défaut) ou true
- **filter_vars:** permet de filtrer les variables envoyées à l'impression en analysant les variables utilisateur définies dans le modèle ; false (par défaut) ou true

### Exemple

```php
Configure::write(
	'Gedooo',
	array(
		'method' => 'cloudooo',
		'wsdl' => 'http://192.168.2.35:8880/ODFgedooo-0.8.05/OfficeService?wsdl',
		'cloudooo_host' => 'cloudooo.test.adullact.org',
		'cloudooo_port' => '8011'
	)
);
```

## Fusion et génération de fichier PDF

### Dans une classe de modèle

Il faut utiliser le __behavior__ ```Gedooo``` du plugin ```Gedooo```.

```php
class Orientstruct extends AppModel
{
	// ...

	/**
	 * Les behaviors utilisés par ce modèle.
	 *
	 * @var array
	 */
	public $actsAs = array( 'Gedooo.Gedooo' );

	// ...
}
```

### Exemples d'appel de la méthode ged

La méthode ```ged(array $datas, string $document, boolean $section = false, array $options = array())__ permet de générer un PDF à partir d'un modèle de document et
de données envoyées à l'impression.

Les variables utilisateur envoyées à la fusion seront d'abord "traduites" par les options, puis leur type (text, string ou date) sera déterminé à la volée.

Leur nom sera le nom de leur chemin complet, mis en underscore, donc par exemple si on envoie les données ci-dessous, on aura accès aux variables utilisateur __foo_bar_baz__, __foo_bar_buz_boz__ et __foo_bar_boz_buz___.

```php
array(
	'FooBar' => array(
		'baz' => 1,
		'buzBoz' => 2,
		'boz' => array(
			'buz' => 3
		)
	)
)
```

Lorsqu'une variable de type date et heure (__TIMESTAMP__, __DATETIME__) est envoyée à la méthode ```ged```, deux variables utilisateur seront envoyées à la vue, la première est la variable normale, de type date, la seconde porte le nom de la variable normale avec le suffixe "___time__" et est de type texte.

Une variable utilisateur spéciale, __modeleodt_path__, contenant le chemin vers le fichier de modèle ODT utilisé, sera envoyée également.

### Avec uniquement une partie principale (sans itération)

```php
$data = array(
	'User' => array(
		'qual' => 'M',
		'nom' => 'Dupont',
		'prenom' => 'Michel',
		'dtnai' => '1978-06-07'
	)
);

$options = array(
	'User' => array(
		'qual' => array(
			'M' => 'Monsieur',
			'Mme' => 'Madame'
		)
	)
);

$this->Orientstruct->ged(
	$data,
	'/var/www/webrsa/Vendor/modelesodt/test.odt',
	false,
	$options
);
```

Dans le modèle de document ODT, les variables utilisateur ci-dessous seront disponibles.

- ```user_qual```
- ```user_nom```
- ```user_prenom```

### Avec une partie principale et une itération

```php
$data = array(
	'User' => array(
		'qual' => 'M',
		'nom' => 'Dupont',
		'prenom' => 'Michel',
		'dtnai' => '1978-06-07'
	)
);

$options = array(
	// Partie principale
	'0' => array(
		'User' => array(
			'qual' => array(
				'M' => 'Monsieur',
				'Mme' => 'Madame'
			)
		),
		'Personne' => array(
			'qual' => array(
				'M' => 'Monsieur',
				'Mme' => 'Madame'
			)
		)
	),
	// Ajout d'une itération "orientations" contenant 2 éléments
	'orientations' => array(
		0 => array(
			'Personne' => array(
				'qual' => 'M',
				'nom' => 'Marley',
				'prenom' => 'Robert'
			),
			'Orientation' => array(
				'date_valid' => array(
					'2009-06-30 11:06:12'
				)
			),
			'Typeorient' => array(
				'lib_type_orient' => 'Sociale'
			)
		),
		1 => array(
			'Personne' => array(
				'qual' => 'Mme',
				'nom' => 'Sapienza',
				'prenom' => 'Goliarda'
			),
			'Orientation' => array(
				'date_valid' => array(
					'2015-11-25 17:54:05'
				)
			),
			'Typeorient' => array(
				'lib_type_orient' => 'Sociale'
			)
		)
	)
);

$this->Orientstruct->ged(
	$data,
	'/var/www/webrsa/Vendor/modelesodt/test_iteration.odt',
	true,
	$options
);
```

Dans le modèle de document ODT, les variables utilisateur ci-dessous seront disponibles.

- ```user_qual```
- ```user_nom```
- ```user_prenom```

Dans l'itération "orientations" du modèle de document ODT, les variables utilisateur ci-dessous seront disponibles.

- ```personne_qual```
- ```personne_nom```
- ```personne_prenom```
- ```orientation_date_valid```
- ```orientation_date_valid_time```
- ```typeorient_lib_type_orient```

## Autres

### Shells de vérification

#### TestModelesOdt

Ce shell teste, de manière récursive, l'impression de tous les modèles ODT se trouvant dans un répertoire et ses sous-répertoires.

Le test consiste à utiliser le modèle de document pour générer un PDF, sans envoyer de variable utilisateur autre que __modeleodt_path__.
On vérifie alors que l'on récupère bien un fichier PDF, sinon l'erreur renvoyée sera affichée.

##### Exemple de sortie du shell

```
Test du fichier questionnaireorientation66.odt
	succès lors du test du fichier questionnaireorientation66.odt
Test du fichier proposition_orientation_vers_pole_emploi.odt
	2017-04-07 13:52:17 Error: Erreur lors de la génération du document (Error num. 005: Error : Error when parsing conditionnal text condition : personne_qual="MR").
	erreur lors du test du fichier proposition_orientation_vers_pole_emploi.odt (voir dans le fichier error.log vers 2017-04-07 13:52:17)
```

```bash
sudo -u www-data lib/Cake/Console/cake Gedooo.TestModelesOdt app/Vendor/modelesodt
```

#### TestGedooo

Ce shell vérifie la configuration, tente de se connecteur au(x) serveur(s) déclarés dans la configuration et réalise un test d'impression au moyen du fichier Vendor/modelesodt/test_gedooo.odt.

```bash
sudo -u www-data lib/Cake/Console/cake Gedooo.TestGedooo
```

### Au niveau des contrôleurs

La classe GedoooComponent permet de concaténer des PDF et d'envoyer un PDF à l'utilisateur, ce qui est pratique si l'on n'utilise pas le meme modèle de document pour chaque enregistrement, ce qui entraine que l'on ne peut pas passer par des itérations.

La variable de configuration ```Cohorte.dossierTmpPdfs``` est utilisée pour la création d'un répertoire temporaire où seront stockés les fichiers PDF avant concaténation.

#### Dans le fichier app/Config/bootstrap.php

```php
Configure::write( 'Cohorte.dossierTmpPdfs', APP.'tmp/files/pdf' );
```

#### Dans le fichier app/Controller/OrientsstructsController.php

```php
class OrientsstructsController extends AppController
{
	// ...

	/**
	 * Components utilisés par ce contrôleur.
	 */
	public $components = array('Gedooo.Gedooo');

	// ...

	/**
	 * Génère les PDF d'orientation, les concatène et renvoie le fichier PDF
	 * généré au navigateur sous le nom "Liste des contrats d'insertion.pdf".
	 */
	public function impressions() {
		// ...

		$pdfs = array();
		foreach($results as $result) {
			$pdfs = $this->Orientstruct->ged($result, $result['modeleodt'], false, $options);
		}

		$pdf = $this->Gedooo->concatPdfs($pdfs, 'Orientations');

		$this->Gedooo->sendPdfContentToClient($pdf, 'Liste des contrats d\'insertion');
	}
}
```


## Critiques et limitations

- au niveau de l'architecture, la fusion documentaire étant une façon de présenter les données, il faudrait avoir du code lié à la vue plutôt qu'au modèle
- la méthode ```GedoooClassicBehavior::ged``` tente, pour chaque variable, de deviner son type, sans que l'on puisse le spécifier ou le mettre en cache.
- la méthode ```GedoooClassicBehavior::ged``` devrait être décomposée proprement