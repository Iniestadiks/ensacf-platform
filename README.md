ENSACF Event Management Platform
Introduction
Bienvenue sur la plateforme de gestion des événements de l'École Nationale Supérieure d'Architecture de Clermont-Ferrand (ENSACF). Cette application permet aux enseignants de proposer des événements, aux administrateurs de les approuver et de les gérer, et à tous les utilisateurs de consulter les événements approuvés.

Fonctionnalités
Gestion des événements
Création, modification et suppression d'événements.
Système d'approbation des événements par les administrateurs.
Calendrier interactif pour visualiser les événements.
Tableaux de bord
Tableau de bord personnalisé pour les enseignants et les administrateurs.
Visualisation des demandes en attente et des événements approuvés.
Système d'authentification
Inscription et connexion des enseignants.
Gestion des administrateurs par d'autres administrateurs.
Responsivité
Interface adaptative pour les appareils mobiles avec menu hamburger.
Prérequis
PHP 7.4 ou supérieur
Composer
Symfony CLI
MySQL ou autre base de données compatible
Installation
Clonez le dépôt :

bash
Copier le code
git clone https://github.com/Iniestadiks/ensacf-platform.git
cd ensacf-platform
Installez les dépendances :

bash
Copier le code
composer install
Configurez la base de données dans le fichier .env :

env
Copier le code
DATABASE_URL="mysql://user:password@127.0.0.1:3306/database_name"
Créez la base de données et les schémas :

bash
Copier le code
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
Chargez les fixtures (optionnel) :

bash
Copier le code
php bin/console doctrine:fixtures:load
Utilisation
Lancement du serveur
Lancez le serveur de développement Symfony :

bash
Copier le code
symfony server:start
Accédez à l'application via http://localhost:8000.

Authentification
Enseignants : Inscrivez-vous via la page d'inscription des enseignants. Les comptes doivent être approuvés par un administrateur.
Administrateurs : Les comptes administrateurs doivent être créés par un administrateur existant.
Structure des répertoires
src/Controller : Contient les contrôleurs de l'application.
src/Entity : Contient les entités Doctrine.
src/Repository : Contient les dépôts Doctrine.
templates/ : Contient les templates Twig.
public/ : Contient les fichiers publics (CSS, JS, images).
Contribution
Les contributions sont les bienvenues ! Veuillez ouvrir une issue ou soumettre une pull request pour proposer des améliorations ou signaler des bugs.

Licence
Ce projet est sous licence MIT. Consultez le fichier LICENSE pour plus d'informations.
