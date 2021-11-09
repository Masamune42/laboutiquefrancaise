<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
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
        if(!empty($cart[$id])) {
            $cart[$id]++;
        }
        else { // Sinon, on met la quantité à 1
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

}
