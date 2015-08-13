<?php

namespace Astina\Bundle\LabelsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LabelRepository extends EntityRepository
{

    /**
     * @param $labels array
     * @param LabelCategory $category
     * @return Label[]
     */
    public function findByNamesOrIds(array $labels, LabelCategory $category = null)
    {
        if (count($labels) == 0) {
            return array();
        }

        $builder = $this->createQueryBuilder('l')
            ->where('l.name in (:labels)')
            ->orWhere('l.id in (:labels)')
            ->setParameter('labels', $labels)
        ;

        if ($category) {
            $builder
                ->andWhere('l.category = :category')
                ->setParameter('category', $category)
            ;
        }

        return $builder->add('orderBy', 'l.position ASC, l.id ASC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @param array $categories names or ids
     * @return Label[]
     */
    public function findByCategories(array $categories)
    {
        $builder = $this->createQueryBuilder('l')
            ->join('l.category', 'c')
            ->where('c.name in (:categories)')
            ->orWhere('c.id in (:categories)')
            ->setParameter('categories', $categories)
            ->add('orderBy', 'c.position ASC, l.position ASC, l.id ASC')
        ;

        return $builder->getQuery()->getResult();
    }


    /**
     * @param array $categories names or ids
     * @return array
     */
    public function findByCategoriesGrouped(array $categories)
    {
        $labels = $this->findByCategories($categories);

        $groupedLabels = array();

        foreach ($categories as $category) {
            if ($category instanceof LabelCategory) {
                $category = $category->getId();
            }

            $groupedLabels[$category] = array();

            foreach ($labels as $label) {
                if ($label->getCategory()->getId() === $category || $label->getCategory()->getName() === $category) {
                    $groupedLabels[$category][] = $label;
                }
            }
        }

        return $groupedLabels;
    }

}
