<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        /**
         * @var Order
         */
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        // Si la commande n'existe pas ou que l'utilisateur connecté n'est pas celui de la commande, on redirige à home
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // Si la commande n'est pas payée, on indique qu'elle l'est
        if (!$order->getIsPaid()) {
            // On vide le panier
            $cart->remove();

            $order->setIsPaid(1);
            $this->entityManager->flush();
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
