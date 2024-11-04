# GMS Exchange

## Pour Lancer le projet

- Installer le projet avec 
  - **__composer install__**
  - Créer un fichier __.env__
  - Ouvrir le fichier .env qui accompagne le projet
  - Copier le contenu et modifier le .env du projet. 
  - Ajouter les valeurs des variable de bases de données 
  - importer si possible la base de données de sauvegarde.

- Générer les configs et clés
  - __php artisan key:generate__
  - __php artisan passport:install__
  - __php artisan passport:keys__
  - __php artisan passport:client__

> Ainsi on configure Laravel Passport utilisé pour gérer le projet.
> 
> si des difficultés consulter: [https://laravel.com/docs/10.x/passport#introduction](https://laravel.com/docs/10.x/passport#introduction )

- Générer les migrations:
  __php artisan migrate__

## Démarrer avec:
  
    - __php artisan serve__

## A decommenter
//event(new PaymentReceivedFromUser(null, null, $tarif->id)); dans TarifController

QR code
https://www.akilischool.com/cours/laravel-generer-un-qr-code-avec-simple-qrcode

ssh -p 65002 u257389445@191.101.79.5

docker-compose up --build -d

docker tag mysql:5.7 aikpe/aikpe-mysql:5.7

docker push aikpe/aikpe-mysql:5.7

# To check database container ip
docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' 1telavis-mysql

# Manually Run Migrations:
## After the containers are up and running, you can manually enter the Laravel app container

docker exec -it 1telavis-api /bin/bash
php artisan migrate
docker logs 1telavis-api

ssh root@195.35.25.205 server koner
cd /home/fintechgodwin-optimo/htdocs/optimo