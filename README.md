# Backend

## Installation

Installation des dépendances :
```
composer install
```

Dupliquer le fichier `.env` en `.env.local` et ajuster la connexion SQL.

Importer la base de données :
```
php bin/console app:migrations:execute
```
