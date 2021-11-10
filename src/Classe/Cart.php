<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }


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
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else { // Sinon, on met la quantité à 1
            $cart[$id] = 1;
        }

        // On actualise le panier avec les modifications
        $this->session->set('cart', $cart);
    }

    /**
     * Affiche le panier
     *
     */
    public function get()
    {
        return $this->session->get('cart');
    }

    /**
     * Supprime le panier
     *
     */
    public function remove()
    {
        return $this->session->remove('cart');
    }

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

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart);
    }

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
}
