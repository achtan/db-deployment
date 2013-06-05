# DB Deployment
## Instalacia
do `config.ini` treba nastavit pripojenie k DB 
```ini
db['user'] = root
db['password'] = root
db['database'] = db-deployment
```
potom nainstalujeme DB Deployment:
```
$ php cesta/k/installDeployment.php /cesta/k/config.ini
```

**Pozor!** prikaz spustajte z rootoveho adresare pre vas projekt

Instalacia vytvory adresar `/sql` a tabulku `__db-deployment`

## Deploy
spustime:
```
$ php cesta/k/deployment.php /cesta/k/config.ini
```
