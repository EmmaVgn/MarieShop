<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SlugGenerator
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function generate(string $title): string
    {
        // Générer le slug de base
        $slug = $this->slugger->slug($title)->lower();
        // Ajouter le préfixe 'blog/'
        return 'blog/' . $slug;
    }
}
