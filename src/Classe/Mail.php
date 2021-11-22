<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key;
    private $api_key_secret;
    private $email;

    public function __construct(string $api_key, string $api_key_secret, string $email)
    {
        // On récupère la clé de l'API de Stripe
        $this->api_key = $api_key;
        $this->api_key_secret = $api_key_secret;
        $this->email = $email;
    }

    /**
     * On envoie un mail
     *
     * @param string $to_email Email destinataire
     * @param string $to_name Nom destinataire
     * @param string $subject Sujet du mail
     * @param string $content Contenu du mail
     * @return void
     */
    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->email,
                        'Name' => "La boutique française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 3357612,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    "Variables" => [
                        "content" => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}
