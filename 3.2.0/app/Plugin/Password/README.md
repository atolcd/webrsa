# Plugin Password pour CakePHP 2.x

## Configuration

Ce plugin permet la génération automatique de mots de passe et la vérification de leur force.

Il est recommandé par l'ANSSI d'utiliser des mots de passe générés automatiquement avec une entropie minimale de 100.
La configuration ci-dessous et l'utilisation de la classe PasswordAnssi sont donc recommandés pour générer des mots de passes automatiques.

```php
Configure::write(
    'Password',
    array(
		'length' => 16,
		'typesafe' => true,
		'class_extra2' => true,
		'class_extra1' => false,
		'class_alphabetical_lower' => true,
		'class_hexadeciaml_lower' => true,
		'class_alphabetical_upper' => true,
		'class_hexadecimal_upper' => true,
		'class_numerical' => true,
		'class_binary' => true
	)
);
```

## Configuration

### app/Config/bootstrap.php

```php
CakePlugin::loadAll(
	array(
		'Password' => array( 'bootstrap' => true )
	)
);
```

#### Classe PasswordPassword

```php
Configure::write(
    'Password',
    array(
		'length' => 8,
		'typesafe' => true,
		'class_number' => true,
		'class_lower' => true,
		'class_upper' => true,
		'class_symbol' => true
	)
);
```
#### Classe PasswordAnssi

```php
Configure::write(
    'Password',
    array(
		'length' => 20,
		'typesafe' => true,
		'class_extra2' => true,
		'class_extra1' => true,
		'class_alphabetical_lower' => true,
		'class_hexadeciaml_lower' => true,
		'class_alphabetical_upper' => true,
		'class_hexadecimal_upper' => true,
		'class_numerical' => true,
		'class_binary' => true
	)
);
```

## Utilisation

### Classe PasswordPassword

```php
App::uses('PasswordPassword', 'Password.Utility');

// Générer un mot de passe
PasswordPassword::generate(); // 57vt,KtV
```

### Classe PasswordAnssi

```php
App::uses('PasswordAnssi', 'Password.Utility');

// Générer un mot de passe
PasswordAnssi::generate(); // #d@8?uqE2g[BDRk5X$tK
// Calcul de l'entropie du mot de passe généré
PasswordAnssi::entropyBits('#d@8?uqE2g[BDRk5X$tK'); // 130
// Calcul de la force (de 1 à 5) du mot de passe généré
PasswordAnssi::strength('#d@8?uqE2g[BDRk5X$tK'); // 5

// Calcul de l'entropie du mot de passe généré plus haut par la classe PasswordPassword
PasswordAnssi::entropyBits('57vt,KtV'); // 51
// Calcul de la force (de 1 à 5) du mot de passe généré plus haut par la classe PasswordPassword
PasswordAnssi::strength('57vt,KtV'); // 1
```