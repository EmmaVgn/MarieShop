<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $apiKey = "pk_test_51PYmV7Ek3IUhoeZsuFiSE6qmyRQJP9DaqekFjEqVUKsN5Up7FvtT4VgNjystKR38y9UkiQ6OtVFLMZmFaXqa3Xb000AF9owrMt";
    private $apiKeySecret = "sk_test_51PYmV7Ek3IUhoeZsQjEW2etfg1ttQ2lAviitRad21SaSYgs3OXvRoNVeSzXAg9Vh7cbaEpnCE54xu7JRPI4UKfFP00TQMb2avc";

    public function send(string $emailTo, string $name, string $subject, string $content)
    {
        $mj = new Client($this->apiKey, $this->apiKeySecret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "bonnal.tristan@hotmail.fr",
                        'Name' => "Tristan",
                    ],
                    'To' => [
                        [
                            'Email' => $emailTo,
                            'Name' => $name
                        ]
                    ],
                    'TemplateID' => 3732103,
                    'TemplateLanguage' => true,
                    'CustomID' => "AppGettingStartedTest",
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            // Log or debug the response data
            dump($response->getData());
        } else {
            // Log or debug the error
            dump($response->getReasonPhrase());
        }
    }
}
