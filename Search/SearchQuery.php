<?php

namespace Astina\Bundle\LabelsBundle\Search;

class SearchQuery
{
    private $labels;

    private $offset;

    private $max;

    private $orderBy;

    private $mode;

    private $locale;

    const MODE_AND = 'and';
    const MODE_OR = 'or';
    const MODE_CATEGORIES = 'categories';

    /**
     * @param array $labels array of ids, names or Label objects
     * @param int $offset
     * @param int $max
     * @param array $orderBy array(field => asc|desc)
     * @param null $mode
     * @param null $locale
     */
    function __construct(array $labels = array(), $offset = 0, $max = 50, array $orderBy = array(), $mode = null, $locale = null)
    {
        $this->labels = $labels;
        $this->max = $max;
        $this->offset = $offset;
        $this->orderBy = $orderBy;
        $this->mode = $mode ?: self::MODE_CATEGORIES;
        $this->locale = $locale;
    }

    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    public function getLabels()
    {
        return $this->labels;
    }

    public function setMax($max)
    {
        $this->max = $max;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
    }
} 