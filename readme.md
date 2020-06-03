# Espace Musival v2
Ce projet est une API, une version améliorée du premier projet Espace Musical réalisé en équipe commme projet de fin de formation O'Clock.
Ce projet a été utilisé pour le passage du titre professionnel RNCP Développeur Web & Web Mobile de niveau III (Bac +2)

## Installation
  ### Requis
  - Composer
  - PHP 5.4
  - Symfony 4.3
  ### Procédure
  - Cloner le repo
  - Executer un `composer install`
  - remplacer les identifiant de connexion à la base de donnée dans le fichier .env
    `DATABASE_URL=mysql://<identifiant>:<mot de passe>@<host>/<nom de la base de donnée à créé>`
  - Executer un `php bin/console doctrine:database:create` afin de créer la base de donnée
  - Executer un `php bin/console doctrine:migrations:migrate` afin d'executer les dernières migrations
  Vous devriez pouvoir tester l'API directement en executant des reauêtes sur les différentes routes en utilisant la méthode appropriée pour chacule d'entre elles