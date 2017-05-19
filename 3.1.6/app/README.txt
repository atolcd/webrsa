CETTE VERSION 2.4 EST UNE VERSION BETA ET NE DOIT PAS ETRE PASSÉE EN PRODUCTION

La version 2.4 beta est la première version de web-rsa utilisant le framework CakePHP en version 2.x.
La version est livrée avec le framework CakePHP en version 2.2.3.1.
A noter que la version minimale du framework doit être la version 2.2.3.1, version améliorée par nos soins, corrigeant une anomalie que nous avons détecté dans la version 2.2.3.
Une prochaine version 2.2.4 est déjà prévue par l'équipe CakePHP (incluant notre correctif) mais disponible uniquement à partir du 11/11/2012.

Cette version comprend :
	- l'amélioration des performances avec une meilleure utilisation du cache
	- l'amélioration des performances avec l'optimisation des requêtes trop longues
	- mise en place d'une table derniersdossiersallocataires (et d'un script lié) afin de ne pas avoir à faire, à chaque recherche, la requête permettant de ne retourner que le dernier dossier RSA d'un allocataire
	- mise en place de contraintes en base de données afin de respecter les règles de validation des modèles cakePHP
	- une gestion des jetons moins bloquantes (SELECT FOR UPDATE à la place des LOCK TABLE)
	- le nouveau formulaire du CER pour le Cg93
	- le workflow complet du CER pour le Cg93
	- une vérification de l'application plus poussée
	- des tests unitaires plus complets avec l'utilisation de PHPUnit 3.6.12
	- un patch SQL pour le nouveau schéma de base de données (patch-2.4.sql)
	- un patch SQL (UNIQUEMENT POUR LE CG93) pour la remontée de données des anciens contrats dans les nouvelles tables mises en place pour le nouveau formulaire (patch-2.4-datas-cg93.sql)
	
ATTENTION:
	* Dans le fichier app/Config/core.php, deux nouvelles variables sont à paramétrer.
	Par défaut, elles sont comme ceci :
		Configure::write( 'production', false ); -> 	* En production: le debug est à 0 et le cache activé.
														* Sinon: le debug est à 2 et le cache désactivé.
														
		Configure::write( 'Session', array(
			'defaults' => 'php',
			'timeout' => 240, // Test 4 * 60 minutes
			'ini' => array(
				'session.gc_maxlifetime' => 240 * 60
			)
		) );
		ini_set( 'session.gc_maxlifetime', 240 * 60 );
		

	* Ne pas oublier de récupérer les nouveaux paramétrages présents dans le fichier webrsa.inc propres à chaque CG.