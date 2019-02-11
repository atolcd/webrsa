# AnalyseSQL

## Installation

### app/Config/bootstrap.php

```php
CakePlugin::loadAll(
	array(
		// ...
		'AnalyseSql' => array( 'bootstrap' => true ),
		// ...
	)
);
```

### app/View/Layouts/default.ctp

Dans le layout, en mode ```debug``` à 2, il faudra ajouter.

```php
echo $this->Html->css( 'AnalyseSql.analysesql' );
```

et plus bas

```php
echo $this->element( 'sql_dump' );
```