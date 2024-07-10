<?php

namespace App\EventListener;

use App\Entity\Advise;
use App\Service\SlugGenerator;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AdviseEntityListener 
{
    private SlugGenerator $slugGenerator;

    public function __construct(SlugGenerator $slugGenerator)
    {
        $this->slugGenerator = $slugGenerator;
    }

    public function prePersist(Advise $advise, LifecycleEventArgs $args): void
    {
        $this->updateSlug($advise);
    }

    public function preUpdate(Advise $advise, LifecycleEventArgs $args): void
    {
        $this->updateSlug($advise);
    }

    private function updateSlug(Advise $advise): void
    {
        if (null === $advise->getSlug() && null !== $advise->getTitle()) {
            $slug = $this->slugGenerator->generate($advise->getTitle());
            $advise->setSlug($slug);
        }
    }
}