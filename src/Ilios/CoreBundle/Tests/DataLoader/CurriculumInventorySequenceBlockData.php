<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventorySequenceBlockData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        
        $arr[] = array(
            'id' => 1
        );


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
