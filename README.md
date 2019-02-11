# Installation

## Prérequis

Composer + Vagrant

## Récupérer le projet infra

Clone du dépot atolcd/infrastructure

Lancer le script `make dependency` dans le dossier ou a été cloné le dépot.

## Récupérer cakephp et faire que tout s'install

`composer install`

## Démarrer la VM virtualbox avec Vagrant et la provisionner

S'assurer que la variable d'environnement `PUPPET_PATH` pointe vers le dossier où a été cloner le dépot atolcd/infrastructure

`vagrant up`

eventuellement relancer `vagrant provision`

## Adapter le fichier /etc/hosts

Ajouter les noms de domaines complet vers 127.0.0.1