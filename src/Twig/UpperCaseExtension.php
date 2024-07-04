<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class UpperCaseExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('uppercase', [$this, 'uppercase'])
        ];
    }

    public function uppercase($value)
    {
        return strtoupper($value);
    }
}