<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
    private $api_key;
    private $api_key_secret;
    private $email;

    public function __construct(EntityManagerInterface $entityManager, string $api_key, string $api_key_secret, string $email)
    {
        $this->entityManager = $entityManager;
        // On récupère les éléments sensibles
        $this->api_key = $api_key;
        $this->api_key_secret = $api_key_secret;
        $this->email = $email;
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

            // On prépare l'envoi du mail
            $content = "Bonjour " . $order->getUser()->getFirstName() . "<br>Merci pour votre commande.<br><br>Enim laboris quis cupidatat non consequat mollit nostrud enim magna. Tempor ullamco ad in officia laboris elit reprehenderit ut reprehenderit voluptate nulla. Et et consequat quis quis ex minim ipsum consectetur consectetur mollit ad sint magna sint.";
            $mail = new Mail($this->api_key, $this->api_key_secret, $this->email);
            // On envoie le mail
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Merci pour votre commande', $content);
        }

        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
