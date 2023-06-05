## Installation en production (Debian 11)

Installer PHP 8.2 et ses extensions : :
```
apt install -y php8.2 php8.2-pgsql php8.2-xml php8.2-mbstring php8.2-curl php8.2-intl
```

Installer Apache :
```
apt install -y apache2
a2enmod rewrite
```

Créer la configuration du site :
```
nano /etc/apache2/sites-available/backend.conf
```

```
<VirtualHost *:80>
    # Saisir le nom de domaine voulu
    ServerName backend.local
    DocumentRoot /var/www/backend/public
    <Directory /var/www/backend/public>
        AllowOverride All
        Order Allow,Deny
        Allow from All
    </Directory>
</VirtualHost>
```

Activer le site :
```
a2ensite backend
```

Redémarrer Apache :
```
systemctl restart apache2
```

Installer postgresql :
```
apt install -y postgresql
```

Créer la base de données et choisir un mot de passe:
```
su postgres
psql
CREATE DATABASE vigirisque;
\password
```

`ctrl+D` x2 pour quitter

Cloner le projet :
```
cd /var/www
git clone https://github.com/atelier-vigirisque/backend
```

Installer les dépendances :
```
cd /var/www/backend
php composer.phar install
```

Dupliquer le fichier `.env` en `.env.local` et ajuster la connexion SQL.

Importer la base de données :
```
php bin/console app:migrations:execute
```

Vérifier le bon fonctionnement du site :
- http://backned.local/
- http://backend.local/stations.json




