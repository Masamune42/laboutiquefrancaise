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

### Tunnel d'achat : Choix de l'adresse de livraison
1. On crée OrderController + le page twig

2. On crée OrderType sans lier d'objet et on le customize + on ajoute une fonction __toString() dans Address
```php
 public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $user = $options['user'];
    $builder
        ->add('addresses', EntityType::class, [
            'label' => 'Choissisez votre adresse de livraison',
            'required' => true,
            // On peut choisir une adresse si on a renseigné le __toString() dans la classe
            'class' => Address::class,
            // Les choix possibles sont les adresses du user connecté
            'choices' => $user->getAddresses(),
            'multiple' => false,
            'expanded' => true
        ])
    ;
}

public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'user' => array()
    ]);
}
```

```php
public function __toString()
{
    return $this->getName().'[br]'.$this->getAddress().'[br]'.$this->getCity().' - '.$this->getCountry();
} 
```

3. On modifie le security.yaml pour que l'utilisateur qui accède à sa commande
```yaml
access_control:
    # - { path: ^/admin, roles: ROLE_ADMIN }
    - { path: ^/compte, roles: ROLE_USER }
    - { path: ^/commande, roles: ROLE_USER }
```

### Tunnel d'achat : choix du transporteur
On ajoute un champ dans OrderType lié à Carrier
```php
$builder
    ->add('addresses', EntityType::class, [
        'label' => 'Choissisez votre adresse de livraison',
        'required' => true,
        // On peut choisir une adresse si on a renseigné le __toString() dans la classe
        'class' => Address::class,
        // Les choix possibles sont les adresses du user connecté
        'choices' => $user->getAddresses(),
        'multiple' => false,
        'expanded' => true
    ])->add('carriers', EntityType::class, [
        'label' => 'Choissisez votre transporteur',
        'required' => true,
        // On peut choisir un transporteur si on a renseigné le __toString() dans la classe
        'class' => Carrier::class,
        'multiple' => false,
        'expanded' => true
    ])
;
```

### Tunnel d'achat : Stocker les informations de la commande en base
1. On modifie l'entité Order et on lui ajoute un champ isPaid

2. On modifie la page twig de la commande pour rediriger vers un autre chemin après soumission du formulaire + on choisi le label à afficher => add.html.twig
```twig
{% set formHtml %}
    {{ form_start(form, {action:path('order_recap')}) }}
    {# On choisi le label à afficher pour le champ addresses #}
    {{ form_label(form.addresses, 'Choissisez votre adresse de livraison') }}
    <a href="{{ path('account_address_add') }}">Ajouter une nouvelle adresse</a>
    {{ form_end(form) }}
{% endset %}
```

3. On modifie le OrderController pour ajouter une fonction add()
```php
/**
 * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
 */
public function add(Cart $cart, Request $request): Response
{
    // 2e param null car non lié à un objet
    // 3e param : on récupère le user pour l'envoyer au form
    $form = $this->createForm(OrderType::class, null, [
        'user' => $this->getUser()
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $date = new DateTime();
        // On récupère le transporteur et l'adresse
        $carriers = $form->get('carriers')->getData();
        $delivery = $form->get('addresses')->getData();

        // On rempli les informations de livraison
        $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname();
        $delivery_content .= '<br>' . $delivery->getPhone();
        if ($delivery->getCompany())
            $delivery_content .= '<br>' . $delivery->getCompany();
        $delivery_content .= '<br>' . $delivery->getAddress();
        $delivery_content .= '<br>' . $delivery->getPostal() . ' ' . $delivery->getCity();
        $delivery_content .= '<br>' . $delivery->getCountry();

        // Enregistrer ma commande Order()
        // On crée un nouvel objet Order
        $order = new Order();
        // On assigne l'utilisateur actuel à la commande
        $order->setUser($this->getUser());
        $order->setCreatedAt($date);
        // On assigne le nom et le prix du transporteur
        $order->setCarrierName($carriers->getName());
        $order->setCarrierPrice($carriers->getPrice());
        $order->setDelivery($delivery_content);
        $order->setIsPaid(0);
        // On persiste la commande
        $this->entityManager->persist($order);

        // Pour chaque élément du panier, on persiste
        foreach ($cart->getFull() as $product) {
            $orderDetails = new OrderDetails();
            $orderDetails->setMyOrder($order);
            $orderDetails->setProduct($product['product']->getName());
            $orderDetails->setQuantity($product['quantity']);
            $orderDetails->setPrice($product['product']->getPrice());
            $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
            $this->entityManager->persist($orderDetails);
        }

        // On envoie en BDD
        // $this->entityManager->flush();

        return $this->render('order/add.html.twig', [
            'cart' => $cart->getFull(),
            'carrier' => $carriers,
            'delivery' => $delivery_content,
        ]);

    }
    
    // Si on ne vient pas d'un formulaire soumis
    return $this->redirectToRoute('cart');
}
```

### Mapping de l'entité Order avec EasyAdmin
1. On modifie le OrderCrudController.php
```php
// Permet d'ajouter une action pour consulter chaque commande
public function configureActions(Actions $actions): Actions
{
    return $actions
        ->add('index', 'detail');
}


public function configureFields(string $pageName): iterable
{
    return [
        IdField::new('id'),
        DateTimeField::new('createdAt', 'Passée le'),
        TextField::new('user.getFullName', 'Utilisateur'),
        MoneyField::new('total')->setCurrency('EUR'),
        BooleanField::new('isPaid', 'Payée')
    ];
}
```

2. Pour utiliser le total dans le code précédent, il faut créer une fonction dans Order.php
```php
public function getTotal(): ?float
{
    $total = null;
    foreach ($this->getOrderDetails()->getValues() as $product) {
        $total = $total + ($product->getPrice() *  $product->getQuantity());
    }
    return $total;
}
```

3. Idem pour utiliser getFullName dans User.php
```php
public function getFullName(): ?string
{
    return $this->firstname . ' ' . $this->lastname;
}
```

4. On redirige la page principale vers Order dans DashboardController.php

### Installation de Stripe
1. Aller sur Stripe et se créer un compte (ou se connecter)

2. Aller sur https://stripe.com/docs/checkout/quickstart dans Paiements et prendre la commande
```
composer require stripe/stripe-php
```

3. On crée notre session Stripe dans OrderController.php

4. On crée de quoi récupérer la clé de l'API de Stripe pour ne pas qu'elle soit public
- Création de config/packages/parameters.yaml
```yaml
parameters:
  # Clé de l'API Stripe
  api_key_stripe : '%env(string:API_KEY_STRIPE)%'
```

- Modification de config/services.yaml, ajout des lignes :
```yaml
App\Controller\:
    resource: '../src/Controller/'
    tags: ['controller.service_arguments']
# On envoie la clé de l'API de Stripe vers StripeController
App\Controller\StripeController:
    tags: [controller.service_arguments]
    bind:
        # for any $logger argument, pass this specific service
        # for any $projectDir argument, pass this parameter value
        $api_key: '%api_key_stripe%'
```

- On crée un fichier .env.local avec la référence de parameters.yaml
```env
# Clé pour l'API Stripe
API_KEY_STRIPE=...
```

5. On crée le StripeController et on y ajoute les informations de paiement
```php
/**
 * @Route("/commande/create-session", name="stripe_create_session")
 */
public function index(Cart $cart): Response
{
    $product_for_stripe = [];
    $YOUR_DOMAIN = 'http://localhost:8000';

    foreach ($cart->getFull() as $product) {
        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $product['product']->getPrice(),
                'product_data' => [
                    'name' => $product['product']->getName(),
                    'images' => [$YOUR_DOMAIN . "/uploads/" . $product['product']->getIllustration()],
                ],
            ],
            'quantity' => $product['quantity'],
        ];
    }

    Stripe::setApiKey($this->api_key);
    $checkout_session = Session::create([
        'line_items' => [
            $product_for_stripe
        ],
        'payment_method_types' => [
            'card',
        ],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.html',
        'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
    ]);

    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
    exit;
}
```

### Ajout de la livraison dans les informations envoyé à Stripe
1. On modifie Order.php, on ajoute une propriété reference

2. On ajoute une référence dans le OrderController que l'on envoie à la vue

3. On utilise la référence dans la vue, dans le lien de validation
```twig
<a href="{{ path('stripe_create_session', {'reference' : reference}) }}" class="btn btn-success btn-block mt-3">Payer | {{ (total / 100 + carrier.price)|number_format(2, ',', '.') }} €</a>
```

4. On adapte le StripeController
```php
/**
 * @Route("/commande/create-session/{reference}", name="stripe_create_session")
 */
public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
{
    $product_for_stripe = [];
    $YOUR_DOMAIN = 'http://localhost:8000';

    // On récupère la commande par sa référence
    $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

    // Si la référence n'existe pas, on retourne à la page de la commande
    if(!$order) {
        return $this->redirectToRoute('order');
    }

    // On parcourt les détailes de la commandes afin de contruire le tableau à envoyer à Stripe
    foreach ($order->getOrderDetails()->getValues() as $product) {
        // On récupère les informations du produit (sert pour récupérer l'image)
        $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $product->getPrice(),
                'product_data' => [
                    'name' => $product->getProduct(),
                    'images' => [$YOUR_DOMAIN . "/uploads/" . $product_object->getIllustration()],
                ],
            ],
            'quantity' => $product->getQuantity(),
        ];
    }
    // On ajoute au tableau le cout de la livraison
    $product_for_stripe[] = [
        'price_data' => [
            'currency' => 'eur',
            'unit_amount' => $order->getCarrierPrice() * 100,
            'product_data' => [
                'name' => $order->getCarrierName(),
                'images' => [$YOUR_DOMAIN],
            ],
        ],
        'quantity' => 1,
    ];

    // On utilise clé de l'API
    Stripe::setApiKey($this->api_key);

    // On crée une session avec les informations
    $checkout_session = Session::create([
        'line_items' => [
            $product_for_stripe
        ],
        'payment_method_types' => [
            'card',
        ],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
        'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        // Auto remplissage de l'adresse mail pour la commande
        'customer_email' => $this->getUser()->getEmail()
    ]);

    header("HTTP/1.1 303 See Other");
    header("Location: " . $checkout_session->url);
    exit;
}
```

5. On modifie le lien vers la validation d'achat dans add.html.twig (aussi possible avec un button dans un form en méthode POST)
```twig
<a href="{{ path('stripe_create_session') }}" class="btn btn-success btn-block mt-3">Payer | {{ (total / 100 + carrier.price)|number_format(2, ',', '.') }} €</a>
```

### Création des vues "Merci pour votre commande" / "Echec de paiement"
1. On modifie Order en lui ajoutant un paramètre stripeSessionId

2. On récupère l'ID de session dans StripeController.php et on l'envoie en BDD
```php
$order->setStripeSessionId($checkout_session->id);
$entityManager->flush();
```

3. On crée un OrderSuccessController dans lequel on indique la route de réussite de paiement + page twig

4. On crée un OrderCancelController dans lequel on indique la route d'échec de paiement + page twig

5. On ajuste la valeur du prix du transporteur dans tout le code (à la base laissé par défaut dans le CarrierCrudController mais doit être ajusté)
```php
public function configureFields(string $pageName): iterable
{
    return [
        TextField::new('name'),
        TextareaField::new('description'),
        MoneyField::new('price')->setCurrency('EUR'),
    ];
}
```

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

### Récupérer des données à partir des fonctions créées pour les entités
```php
//  On récupère les adresses liées au user sous forme de collection / relation
$this->getUser()->getAddresses()
// On récupère les datas de la relation
$this->getUser()->getAddresses()->getValues()
```