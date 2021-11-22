<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    private $entityManager;
    private $api_key;
    private $api_key_secret;
    private $email;

    public function __construct(EntityManagerInterface $entityManager, string $api_key, string $api_key_secret, string $email)
    {
        $this->entityManager = $entityManager;
        // On récupère la clé de l'API de Stripe
        $this->api_key = $api_key;
        $this->api_key_secret = $api_key_secret;
        $this->email = $email;
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        // Notification si user enregistré ou existe déjà
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $password = $encoder->encodePassword($user, $user->getPassword());

                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Mail de bienvenue
                $content = "Bonjour " . $user->getFirstName() . "<br>Bienvenue sur la première boutique dédiée au made in France.<br><br>Enim laboris quis cupidatat non consequat mollit nostrud enim magna. Tempor ullamco ad in officia laboris elit reprehenderit ut reprehenderit voluptate nulla. Et et consequat quis quis ex minim ipsum consectetur consectetur mollit ad sint magna sint.";
                $mail = new Mail($this->api_key, $this->api_key_secret, $this->email);
                $mail->send($user->getEmail(), $user->getFirstName(), 'Bienvenue sur la boutique française', $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            } else {
                $notification = "L'email que avous avez renseignée existe déjà.";
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}
