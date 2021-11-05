# Cours Symfony 5 - Udemy

## Extensions utiles


## Soucis rencontrés + corrections


## Procédure de création du projet
### Configuration de la BDD
Dans le fichier .env on configure notre BDD

### Création de l'entité User
```
symfony console make:user
```
__Propriétés__ :
- email
- password

__Fichiers créés__ :
- src/Entity/User.php : Classe qui fait le lien entre la BDD et les données à envoyer / récupérer
- src/Repository/UserRepository.php : Classe qui va récupérer toutes les données de la BDD

### Création de la BDD
1. Création de la BDD
```
symfony console doctrine:database:create
```

2. Création de la migration dans un fichier PHP
```
symfony console make:migration
```

3. Application de la migration en BDD : ajout de la table pour l'entité User
```
symfony console doctrine:migration:migrate
```

### Création du formulaire d'inscription
1. On crée le formulaire en lien avec User
```
symfony console make:form
```
__Fichiers créés__ :
- RegisterType.php : Contient les champs utilisés par le formulaire d'inscription
- RegisterController.php : Contient les actions à effectuer pour sauvegarder les informations du formulaire d'inscription

2. On applique le thème de Bootstrap dans les formulaires dans twig.yaml
```yaml
twig:
    default_path: '%kernel.project_dir%/templates'
    form_themes: ['bootstrap_4_layout.html.twig']
```

3. On ajoute d'autres champs à User
```
symfony console make:entity
```

4. On crée une nouvelle migration et on l'envoie en BDD
```
symfony console make:migration
symfony console doctrine:migration:migrate
```

5. On édite le formulaire d'inscription dans RegisterType.php
- Dans translation.yaml : je change la langue utilisée
- Dans RegisterController.php : Je reçois les informations du formulaire pour les envoyer en BDD. On encrypte aussi le mdp.

### Création de la page d'authentification
1. On crée les fichiers
```
symfony console make:auth
```
__Fichiers créés__ :
- SecurityController.php : Routes de login et logout en fonction
- LoginFormAuthenticator.php : Méthodes qui permettra à Symfony de déterminer si le User existe bien