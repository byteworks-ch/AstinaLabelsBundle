Astina Labels Bundle
====================

Add categorized labels to Doctrine entities and use them for filtered search.

### Usage

Add `labels` property to your entity:

```php
class Foo
{
    // ...

    /**
     * @ORM\ManyToMany(targetEntity="Astina\Bundle\LabelsBundle\Entity\Label")
     */
    private $labels;
}
```

Configure a filter search service:

```yaml
# .../config/services.yml
services:
    app.filter_search.foo:
        class: Astina\Bundle\LabelsBundle\Search\LabelSearch
        arguments:
            - @astina_labels.repository.label
            - @app.repository.foo

    app.repository.foo:
        class: Doctrine\ORM\EntityRepository
        factory_service: doctrine
        factory_method: getRepository
        arguments:
            - AppMyBundle:Foo
```

Use the service to find entities for given labels:

```php
$search = $container->get('app.filter_search.foo');
$results = $search->search(new SearchQuery($labels));
```