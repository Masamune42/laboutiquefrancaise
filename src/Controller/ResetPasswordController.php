<?php

namespace App\Controller;

use DateTime;
use App\Classe\Mail;
use App\Entity\User;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
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
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {
        if($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if($request->get('email')) {
            /**
             * @var User
             */
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
            if($user) {
                $reset_password = new ResetPassword();
                $reset_password->setUser($user)
                    ->setToken(uniqid())
                    ->setCreatedAt(new DateTime());

                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour " . $user->getFirstname() . "<br>Vous avez demandé à réinitialiser votre mot de passe sur le site La Boutique Française.<br><br>";
                $content  .= 'Merci de bien vouloir cliquer sur le lien suivant pour <a href="'.$url.'">mettre à jour votre mot de passe</a>';

                $mail = new Mail($this->api_key, $this->api_key_secret, $this->email);
                $mail->send($user->getEmail(), $user->getFirstName(). ' '  . $user->getlastName(),  'Réinitialiser votre mot de passe sur La Boutique Française', $content);
                $this->addFlash('notice', 'Vous allez recevoir prochainement un mail.');
            } else {
                $this->addFlash('notice', 'Cette adresse mail est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordEncoderInterface $encoder): Response
    {
        /**
         * @var ResetPassword
         */
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        // Vérier si on fait la demande il y a moins de 3h
        $now = new DateTime();
        if($now > $reset_password->getCreatedAt()->modify('+3 hour')) {
            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveler.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $new_pwd = $form->get('new_password')->getData();
            $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);

            $reset_password->getUser()->setPassword($password);
            $this->entityManager->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');
        }




        return $this->render('reset_password/update.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
