<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\MeshPreviousIndexingManager;

class MeshPreviousIndexing extends ReadWriteController
{
    public function __construct(MeshPreviousIndexingManager $manager)
    {
        parent::__construct($manager, 'meshpreviousindexings');
    }
}
