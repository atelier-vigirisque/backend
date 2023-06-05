# Backend

## Prérequis

- PHP >= 8.0
- Composer
- Base de données PostgreSQL

## Installation

Installation des dépendances :
```
php composer.phar install
```

Dupliquer le fichier `.env` en `.env.local` et ajuster la connexion SQL.

Importer la base de données :
```
php bin/console app:migrations:execute
```

Lancer le webserver en local :
```
symfony server:start
```

## Webservices disponibles

http://localhost:8000/
http://localhost:8000/stations.json

## Installation en production

[Documentation Debian 11](documentation/installation_debian11.md)
