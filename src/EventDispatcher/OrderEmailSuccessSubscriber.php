<?php

namespace App\EventDispatcher;

use App\Entity\User;
use App\Event\OrderSuccessEvent;
use App\Service\SendMailService;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderEmailSuccessSubscriber implements EventSubscriberInterface
{
    protected $sendMail;
    protected $security;

    public function __construct(SendMailService $sendMail, Security $security)
    {
        $this->sendMail = $sendMail;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'order.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(OrderSuccessEvent $orderSuccessEvent)
    {
        // 1. Récupérer l'utilisateur actuellement en ligne (service: Security)
        /** @var User */
        $currentUser = $this->security->getUser();

        // 2. Récupérer la commande (je la trouve dans PurchaseSuccessEvent)
        $order = $orderSuccessEvent->getOrder();

        // 3. Envoyer le mail (service: SendMailService)
        $this->sendMail->sendEmail(
            "no-reply@monsite.net",
            "Votre commande",
            $currentUser->getEmail(),
            "Bravo votre commande n°{$order->getId()} a bien été confirmée",
            "order_success",
            [
                'order' => $order,
                'user' => $currentUser,
            ]
        );
    }
}