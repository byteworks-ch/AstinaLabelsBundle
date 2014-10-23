<?php

namespace Astina\Bundle\LabelsBundle\Search;

use Astina\Bundle\LabelsBundle\Entity\Label;
use Astina\Bundle\LabelsBundle\Entity\LabelRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class LabelSearch implements LabelSearchInterface
{
    /**
     * @var LabelRepository
     */
    protected $labelRepository;

    /**
     * @var EntityRepository
     */
    protected $sourceRepository;

    /**
     * @var string
     */
    protected $labelsProperty;

    function __construct(LabelRepository $labelRepository,
                         EntityRepository $sourceRepository,
                         $labelsProperty = 'labels')
    {
        $this->labelRepository = $labelRepository;
        $this->sourceRepository = $sourceRepository;
        $this->labelsProperty = $labelsProperty;
    }

    public function search(SearchQuery $searchQuery)
    {
        return $this->createQuery($searchQuery)->getResult();
    }

    public function count(SearchQuery $searchQuery)
    {
        $builder = $this->createQueryBuilder($searchQuery);

        $builder->select('count(e)');

        return (int) $builder->getQuery()->getSingleScalarResult();
    }

    protected function createQuery(SearchQuery $searchQuery)
    {
        $builder = $this->createQueryBuilder($searchQuery)
            ->setFirstResult($searchQuery->getOffset())
            ->setMaxResults($searchQuery->getMax())
        ;

        foreach ($searchQuery->getOrderBy() as $order => $sort) {
            $builder->addOrderBy('e.' . $order, $sort);
        }

        return $builder->getQuery();
    }

    protected function createQueryBuilder(SearchQuery $searchQuery)
    {
        $builder = $this->sourceRepository->createQueryBuilder('e')
            ->select('distinct e')
        ;

        if ($searchQuery->getLabels()) {
            switch ($searchQuery->getMode()) {
                case SearchQuery::MODE_OR:
                    $this->addLabelsOr($builder, $searchQuery);
                    break;
                case SearchQuery::MODE_AND:
                    $this->addLabelsAnd($builder, $searchQuery);
                    break;
                case SearchQuery::MODE_CATEGORIES:
                    $this->addLabelsCategories($builder, $searchQuery);
                    break;
                default:
                    throw new \Exception('Invalid search query mode: ' . $searchQuery->getMode());
            }
        }

        return $builder;
    }

    protected function addLabelsOr(QueryBuilder $builder, SearchQuery $searchQuery)
    {
        $labelIds = $this->getLabelIds($searchQuery->getLabels());

        $builder
            ->join('e.' . $this->labelsProperty, 'l')
            ->where('l.id in (:label_ids)')
            ->setParameter('label_ids', $labelIds)
        ;
    }

    protected function addLabelsAnd(QueryBuilder $builder, SearchQuery $searchQuery)
    {
        $labelIds = $this->getLabelIds($searchQuery->getLabels());

        $first = true;
        foreach ($labelIds as $labelId) {
            $alias = 'l' . $labelId;
            $param = $alias . '_id';
            $whereMethod = $first ? 'where' : 'andWhere';
            $builder
                ->join('e.' . $this->labelsProperty, $alias)
                ->$whereMethod($alias . '.id = :' . $param)
                ->setParameter($param, $labelId)
            ;
            $first = false;
        }
    }

    protected function addLabelsCategories(QueryBuilder $builder, SearchQuery $searchQuery)
    {
        $groupedLabelIds = $this->getGroupedLabelIds($searchQuery->getLabels());

        $first = true;
        foreach ($groupedLabelIds as $categoryId => $categoryLabelIds) {
            $alias = 'l' . $categoryId;
            $param = $alias . '_ids';
            $whereMethod = $first ? 'where' : 'andWhere';
            $builder
                ->join('e.' . $this->labelsProperty, $alias)
                ->$whereMethod($alias . '.id in (:' . $param . ')')
                ->setParameter($param, $categoryLabelIds)
            ;
            $first = false;
        }
    }

    private function getLabelIds($labels)
    {
        $ids = array();

        $names = array();
        foreach ($labels as $label) {
            if ($label instanceof Label) {
                $ids[] = $label->getId();
            } elseif (is_numeric($label)) {
                $ids[] = $label;
            } elseif (is_string($label)) {
                $names[] = $label;
            }
        }

        if (!empty($names)) {
            $labels = $this->labelRepository->findByNames($names);
            foreach ($labels as $label) {
                $ids[] = $label->getId();
            }
        }

        $ids = array_unique($ids);

        return $ids;
    }

    /**
     * @param $queryLabels
     * @return Label[]
     */
    private function getGroupedLabelIds($queryLabels)
    {
        $labels = array();
        $nameOrIds = array();

        foreach ($queryLabels as $label) {
            if ($label instanceof Label) {
                $labels[] = $label;
            } elseif (is_scalar($label)) {
                $nameOrIds[] = $label;
            }
        }

        if (!empty($names)) {
            $queryLabels = $this->labelRepository->findByNamesOrIds($nameOrIds);
            foreach ($queryLabels as $label) {
                $labels[$label->getId()] = $label;
            }
        }

        $groupedIds = array();
        /** @var Label $label */
        foreach ($labels as $label) {
            if (null == $label->getCategory()) {
                continue;
            }
            $groupedIds[$label->getCategory()->getId()][] = $label->getId();
        }

        return $groupedIds;
    }
}