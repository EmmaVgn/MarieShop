<?php

namespace App\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


trait CreatedAtTrait
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private  $createdAt ;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}

