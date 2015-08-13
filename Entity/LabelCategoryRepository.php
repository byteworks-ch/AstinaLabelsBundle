<?php

namespace Astina\Bundle\LabelsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LabelCategoryRepository extends EntityRepository
{

    public function findAll()
    {
        return parent::findBy(array(), array('position' => 'asc'));
    }


    /**
     * @param $names
     * @return LabelCategory[]
     */
    public function findByNames($names)
    {
        return $this->createQueryBuilder('c')
            ->where('c.name in (:names)')
            ->setParameter('names', $names)
            ->orderBy('c.position')
            ->getQuery()
            ->getResult()
        ;
    }

}