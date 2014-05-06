<?php

namespace Astina\Bundle\LabelsBundle\Search;

interface LabelSearchInterface
{
    /**
     * @param SearchQuery $searchQuery
     * @return array
     */
    public function search(SearchQuery $searchQuery);

    /**
     * @param SearchQuery $searchQuery
     * @return int
     */
    public function count(SearchQuery $searchQuery);
}