# Bienvenue sur mon projet Optinote.

## Explications du projet

Ce projet permet une gestion de note (mémo) :
 - ajouter une note,
 - supprimer une note,
 - lister toutes les notes.

## Installation

### Pré-requis

- PHP 7 ou +,
- MySql 5 ou +,
- Symfony 4.

### Installation

Exécuter les commandes `git clone https://github.com/AlexPlt/optinote.git && cd optinote`.

Créer un utilisateur pour vous connectez à la base de données avec les informations :
- utilisateur : `db_user`,
- mot de passe : `db_password`.

Exécuter les commandes `php bin/console doctrine:database:create && php bin/console make:migration && php bin/console doctrine:migrations:migrate`.

Lancer le serveur avec la commande `php bin/console server:run`, puis rendez-vous sur un naviguateur web avec l'URL [127.0.0.1:8000](http://127.0.0.1:8000/).
