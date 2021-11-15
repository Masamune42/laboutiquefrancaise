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

2. Je modifie LoginFormAuthenticator.php pour rediriger vers la page après la connexion

### Création de l'espace membre de l'utilisateur
1. On crée les fichiers
```
symfony console make:controller
```
__Fichiers créés__ :
- AccountController.php
- account/index.html.twig

2. Je modifie dans security.yaml la page d'accès pour les comptes :
```yaml
access_control:
        # - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/compte, roles: ROLE_USER }
```

3. Je modifie ma page account/index.html.twig

### Création de la page de modification du mdp
1. Création des fichiers
```
symfony console make:controller
```
__Fichiers créés__ :
- AccountPasswordController.php
- account_password/index.html.twig (à supprimer)=>(à créer) account/password.html.twig

2. On édite les 2 fichiers

### Utilisation de EasyAdmin
1. On ajoute le bundle au projet
```
composer require easycorp/easyadmin-bundle
```

2. On crée le Dashboard
```
symfony console make:admin:dashboard
```
__Fichiers créés__ :
- DashboardController.php : Permet de gérer des entités que l'on va définir

3. On crée un menu pour les users dans DashboardController.php
```php
yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
```

4. On crée une entité à manager pour le user
```
symfony console make:admin:crud
```
__Fichiers créés__ :
- UserCrudController.php

### Création de l'entité Category et liaison avec EasyAdmin
1. Création des fichiers
```
symfony console make:entity
```
__Fichiers créés__ :
- Category.php
- CategoryRepository.php

2. On effectue la migration
```
symfony console make:migration
symfony console doctrine:migration:migrate
```

3. On crée une entité à manager pour la catégorie
```
symfony console make:admin:crud
```
__Fichiers créés__ :
- CategoryCrudController.php

4. On modifie le fichier DashboardController.php pour ajouter un menu de catégories

### Création de l'entité Product et liaison avec EasyAdmin
1. Création des fichiers
```
symfony console make:entity
```
__Fichiers créés__ :
- Product.php
- ProductRepository.php

2. Migration
```
symfony console make:migration
symfony console doctrine:migrations:migrate
```

3. On crée une entité à manager pour le produit
```
symfony console make:admin:crud
```
__Fichiers créés__ :
- ProductCrudController.php

4. On modifie le fichier ProductCrudController.php pour changer les inputs du dashboard
```php
public function configureFields(string $pageName): iterable
{
    return [
        TextField::new('name'),
        // On crée le slug à partir du nom de l'article
        SlugField::new('slug')->setTargetFieldName('name'),
        // On crée le champ pour l'image et on organise son upload
        ImageField::new('illustration')
            ->setBasePath('uploads/')
            ->setUploadDir('public/uploads')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false),
        TextField::new('subtitle'),
        TextareaField::new('description'),
        MoneyField::new('price')->setCurrency('EUR'),
        AssociationField::new('category'),
    ];
}
```

5. On crée une fonction pour récupérer les informations de catégorie pour les produits
```php
public function __toString()
{
    return $this->getName();
}
```

### Création de la vue pour afficher les produits
1. Création des fichiers
```
symfony console make:controller
```
__Fichiers créés__ :
- ProductController.php
- product/index.html.twig

2. Modification de ProductController.php
- On crée 2 routes : une pour voir tous les articles et une autre pour voir chaque article

__Fichiers créés__ :
- product\show.html.twig

### Création de la barre de filtre
1. On crée le fichier Classe/Search.php

2. On crée Form/SearchType.php (sans commande) et on copie la classe configureOptions du RegisterType

3. On modifie ProductController.php pour qu'il puisse recevoir la requête du formulaire

4. On crée une fonction findWithSearch() dans ProductRepository.php pour récupérer les informations du formulaire en BDD dans ProductController.php
```php
public function findWithSearch(Search $search)
{
    $query = $this
        // p => product
        // c => category
        ->createQueryBuilder('p')
        ->select('c', 'p')
        ->join('p.category', 'c');

    if (!empty($search->categories)) {
        $query = $query
            // Dans categories on envoie une liste d'id
            ->andWhere('c.id IN (:categories)')
            ->setParameter('categories', $search->categories);
    }

    if(!empty($search->string)) {
        $query = $query
            ->andWhere('p.name LIKE :string')
            ->setParameter('string', "%$search->string%");
    }
```

### Création du panier
1. On crée le CartController
```
symfony console make:controller
```
__Fichiers créés__ :
- CartController.php
- cart/index.html.twig

2. Dans le CartController on dupplique la route par défaut pour créer add()
```php
/**
 * @Route("/cart/add/{id}", name="add_to_cart")
 */
public function add($id): Response
{
    // Code à venir ici...
    return $this->render('cart/index.html.twig');
}
```

3. On crée une classe Classe/Cart.php avec une fonction add() pour ajouter un produit au panier
```php
/**
 * Ajout d'un produit au panier par l'id
 *
 * @param int $id
 * @return void
 */
public function add($id)
{
    $this->session->set('cart', [
        [
            'id' => $id,
            'quantity' => 1,
        ]
    ]);
}
```

4. On utilise Cart en paramètre (injection) dans la fonction add() dans CartController
```php
/**
 * @Route("/cart/add/{id}", name="add_to_cart")
 */
public function add(Cart $cart, $id): Response
{
    $cart->add($id);

    // Ajout d'un produit puis redirection vers le panier
    return $this->redirectToRoute('cart');
}
```

5. On crée une fonction remove() pour supprimer le panier

6. On adapte la fonction add() dans Cart pour ajouter le même article si besoin
```php
/**
 * Ajout d'un produit au panier par l'id
 *
 * @param int $id
 * @return void
 */
public function add($id)
{
    // On récupère le panier, si vide => []
    $cart = $this->session->get('cart', []);

    // Si on a déjà le même produit (id) dans le panier, on ajoute une quantité
    if(!empty($cart[$id])) {
        $cart[$id]++;
    }
    else { // Sinon, on met la quantité à 1
        $cart[$id] = 1;
    }

    // On actualise le panier avec les modifications
    $this->session->set('cart', $cart);
}
```

### Page du panier
On modifie la fonction index() + ajout de l'Entity Manager dans CartController() pour récupérer le panier et les informations des produits liés
```php
/**
 * @Route("/mon-panier", name="cart")
 */
public function index(Cart $cart): Response
{
    // On crée un tableau vide pour y stocker les infos des produits
    $cartComplete = [];

    // Pour chaque emplacement dans le panier, on rempli $cartComplete avec le produit concerné et sa quantité
    foreach ($cart->get() as $id => $quantity) {
        $cartComplete[] = [
            'product' => $this->entityManager->getRepository(Product::class)->findOneById($id),
            'quantity' => $quantity
        ];
    }
    
    return $this->render('cart/index.html.twig', [
        'cart' => $cartComplete
    ]);
}
```

### Ajout, diminution et suppression de produit du panier
1. Création des fonctions dans Cart.php pour supprimer un élément du panier et supprimer une unité d'un élément
```php
/**
 * Supprime un élément du panier
 *
 */
public function delete($id)
{
    // On récupère le panier, si vide => []
    $cart = $this->session->get('cart', []);

    unset($cart[$id]);

    return $this->session->set('cart', $cart);
}

/**
 * Supprime un élément d'une unité du panier
 *
 */
public function decrease($id)
{
    // On récupère le panier, si vide => []
    $cart = $this->session->get('cart', []);

    if($cart[$id] > 1) {
        $cart[$id]--;
    } else {
        unset($cart[$id]);
    }

    return $this->session->set('cart', $cart);
}
```

2. Création des fonctions dans le CartController
```php
/**
 * @Route("/cart/delete/{id}", name="delete_to_cart")
 */
public function delete(Cart $cart, $id): Response
{
    $cart->delete($id);
    // Ajout d'un produit puis redirection vers le panier
    return $this->redirectToRoute('cart');
}

/**
 * @Route("/cart/decrease/{id}", name="decrease_to_cart")
 */
public function decrease(Cart $cart, $id): Response
{
    $cart->decrease($id);
    // Ajout d'un produit puis redirection vers le panier
    return $this->redirectToRoute('cart');
}
```

3. On lit les fonctions aux boutons + et - par un lien

4. On refactorise la fonction index() du CartController pour centraliser les actions
```php
// CartController.php
/**
 * @Route("/mon-panier", name="cart")
 */
public function index(Cart $cart): Response
{
    return $this->render('cart/index.html.twig', [
        'cart' => $cart->getFull()
    ]);
}

// Cart.php
 /**
 * Récupère tous les éléments associés du panier
 *
 */
public function getFull()
{
    // On crée un tableau vide pour y stocker les infos des produits
    $cartComplete = [];

    // Si j'ai un panier
    if ($this->get()) {
        // Pour chaque emplacement dans le panier, on rempli $cartComplete avec le produit concerné et sa quantité
        foreach ($this->get() as $id => $quantity) {
            $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
            // Si le produit ajouté au panier n'existe pas, on le supprime et on continue la boucle
            if (!$product_object) {
                $this->delete($id);
                continue;
            }
            $cartComplete[] = [
                'product' => $product_object,
                'quantity' => $quantity
            ];
        }
    }

    return $cartComplete;
}
```

### Ajouter, modifier, supprimer une adresse
1. On Ajoute AccountAddressConroller.php
```
symfony console make:controller
```

2. On supprime le fichier twig créé et on crée manuellement : address.html.twig et address_form.html.twig dans account

3. On crée un formulaire lié à l'entité Address et on l'utilise sur la page
```
symfony console make:form
```

4. On ajoute des fonctions dans AccountAddressController.php
```php
/**
 * @Route("/account/modifier-une-adresse/{id}", name="account_address_edit")
 */
public function edit(Request $request, $id): Response
{
    $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

    if (!$address || $address->getUser() != $this->getUser()) {
        return $this->redirectToRoute('account_address');
    }

    $form = $this->createForm(AddressType::class, $address);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $this->entityManager->flush();
        return $this->redirectToRoute('account_address');
    }

    return $this->render('account/address_form.html.twig', [
        'form' => $form->createView()
    ]);
}

/**
 * @Route("/account/supprimer-une-adresse/{id}", name="account_address_delete")
 */
public function delete($id): Response
{
    $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

    if ($address && $address->getUser() == $this->getUser()) {
        $this->entityManager->remove($address);
        $this->entityManager->flush();
    }

    return $this->redirectToRoute('account_address');
}
```

### Création de l'entité Carrier
1. On crée l'entité

2. On lie l'entité avec EasyAdmin
```
symfony console make:admin:crud
```

### Création de l'entité Order et OrderDetails
1. On crée Order
On ne fait pas de liaison entre Order et Carrier car si on modifie un élément du Carrier lié comme le prix de livraison, cela faussera les informations renseignées. Idem avec avec l'adresse de livraison.

2. On crée OrderDetails
Pas de liaison avec Product pour les mêmes raisons qu'avant (modif / supression du produit).

## Tips
### Vérifier les routes existantes
```
symfony console debug:router
```

### Affiche tous les services que l'on peut injecter et utiliser (ex : SessionInterface...)
```
symfony console debug:autowiring
```
On peut affiner la recherche
```
symfony console debug:autowiring session
```