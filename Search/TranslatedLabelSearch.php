<?php

namespace Astina\Bundle\LabelsBundle\Search;

use Doctrine\ORM\Query;

/**
 * Class TranslatedLabelSearch
 *
 * @package   Astina\Bundle\LabelsBundle\Search
 * @author    Drazen Peric <dperic@astina.ch>
 * @copyright 2014 Astina AG (http://astina.ch)
 */
class TranslatedLabelSearch extends LabelSearch
{
    public function search(SearchQuery $searchQuery)
    {
        $query = $this->createQuery($searchQuery);

        if ($locale = $searchQuery->getLocale()) {
            $this->setTranslatableQueryHints($query, $locale);
        }

        return $query->getResult();
    }

    /**
     * @param Query $query
     * @param string $locale
     * @throws \Exception
     */
    protected function setTranslatableQueryHints(Query $query, $locale)
    {
        if (!class_exists('Gedmo\Translatable\TranslatableListener')) {
            throw new \Exception('"Translatable" extension missing. "stof/doctrine-extensions-bundle" needs to be installed');
        }

        $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1);
    }
}