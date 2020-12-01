# Environnement de développement

## Pré-requis

* Docker 19.03+
* Docker compose 1.22+

## Configurer l'instance de développement

<<<<<<< HEAD
Créer un fichier `.env.local` à partir de `.env.local.sample` et remplir les champs
* "CD_DEPARTEMENT"

* "CMIS_URL"
* "CMIS_USERNAME"
* "CMIS_PASSWORD"

* "GEDOOO_WSDL"
* "GEDOOO_CLOUDOOO_HOST"

Créer un fichier `docker-compose.yml`  à partir de `docker-compose.yml.sample` et ajouter les configurations BDD manquantes (voir fichier pour plus d'explications, section ## Utiliser plusieurs bases de données)
=======
`.env.local`
>>>>>>> Environnement de dev Docker

## Packager l'application

`docker-compose run php composer install`

* installation des dépendances composer
* lien pour fichiers nécessaires à l'exécution (dossier `app`)

## Démarrer les services

* Postgresql : `docker-compose up [-d] postgresql`
* Php : `docker-compose up [-d] php`
* Apache : `docker-compose up --build [-d] apache`
* Mailhog : `docker-compose up [-d] mailhog`

Il est possible de lancer tout d'un coup avec `docker-compose up --build [-d]`

L'application est accessible à l'adresse http://webrsa.docker.localhost:8020/

## Initialisation des données

L'instance docker `postgresql` doit être démarée.

```
docker-compose exec postgresql_XXXXXX psql -h localhost -U webrsa -d webrsa -f /data/webrsa.SCHEMA.public.sql
docker-compose exec postgresql_XXXXXX psql -h localhost -U webrsa -d webrsa -f /data/webrsa.SCHEMA.administration.sql
docker-compose exec postgresql_XXXXXX psql -h localhost -U webrsa -d webrsa -f /data/webrsa.DATA.public.sql
docker-compose exec postgresql_XXXXXX psql -h localhost -U webrsa -d webrsa -f /data/patches/4.x/patch-4.8.0.sql
docker-compose exec postgresql_XXXXXX psql -h localhost -U webrsa -d webrsa -f /data/patches/4.x/patch-4.8.0-administration.sql
```

## Initialisation de l'application

Le script `webrsa.sh` n'est pas utilisable (`chown`, `sudo`, …), on lance directement les commandes bas niveau.

```
docker-compose exec --user=$(id -u) php vendor/cakephp/cakephp/lib/Cake/Console/cake WebrsaSessionAcl update Aco -app app
docker-compose exec --user=$(id -u) php vendor/cakephp/cakephp/lib/Cake/Console/cake WebrsaSessionAcl update Aro -app app
docker-compose exec --user=$(id -u) php vendor/cakephp/cakephp/lib/Cake/Console/cake Prechargements -app app
docker-compose exec --user=$(id -u) php vendor/cakephp/cakephp/lib/Cake/Console/cake Checks -app app
```

## Utiliser plusieurs bases de données

Il est possible de définir d'autres service postgresql dans le fichier `docker-composer.yml`.

Il suffit de repartir de l'exemple en commentaire en modifiant :
* le nom du service,
* la variable d'environnement `PGDATA` pour que les données soient un répertoire spécifique (pas celui du service `postgresql` de base),
* l'association du port : le port accessible despuis la machine hôte (votre PC).

```yml
postgresql_XXXXXX:
  <<: *default-postgresql
  environment:
    <<: *default-postgresql-env
    PGDATA: /var/lib/postgresql/data/pgdata_XXXXXX # custom PGDATA
  ports:
    - "5433:5432" # port mapping
```

Ensuite pour définir quelle base utiliser au niveau de l'application, il faut modifier le fichier `.env.local` (clé `DATABASE_CONFIG_DEFAULT_HOST` et `DATABASE_CONFIG_LOG_HOST`)

Les commandes de "Initialisation des données" sont utilisables en modifiant `postgresql` (après `docker-composer exec`) par le nom du service souhaité.


# TODO, remarques et améliorations

* cloudoo
* doc multi base
* `webrsa.sh` : utilisateur système en dur
* revoir la gestion `app` avec symlink ?
* erreurs avec webrsa.SCHEMA.public.sql
  * ERROR:  relation "public.tmp_table2" does not exist
  * ERROR:  relation "public.tmp_tPostgresqlOutputBulkExec_1_tPBE_XXXXXXX" does not exist
