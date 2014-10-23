Astina Labels Bundle
====================

Add categorized translatable labels to Doctrine entities and use them for filtered search.

### Configuration

Add the following to your AppKernel.php file

```php
class AppKernel extends Kernel {
    public function registerBundles() {
        $bundles = array(
            ...
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Astina\Bundle\LabelsBundle\AstinaLabelsBundle(),
            ...
        );
    }
}
```

Add the following to your config.yml

```yaml
stof_doctrine_extensions:
    default_locale: de
    translation_fallback: true
    orm:
        default:
            translatable: true

doctrine:
    orm:
        mappings:
            gedmo_translatable:
                type: annotation
                prefix: Gedmo\Translatable\Entity
                dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Translatable/Entity"
                is_bundle: false
```

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

Use it in a formType like this:

```php
    $builder
        ->add('labels', 'astina_labels', array(
            'categories' => array('category1', 'category2'...),
            'label' => 'Labels',
            'required' => false,
        ))
    ;
```

Each label has to belong to a category if you want to see them in a formType so configure them with fixtures.

```php
    public function load(ObjectManager $manager)
    {
        $labelCategory = new LabelCategory();
        $labelCategory->setName('category1');
        $manager->persist($labelCategory);

        foreach ($labelsArray as $labelName) {
            $label = new Label();
            $label->setName($labelName);
            $label->setCategory($labelCategory);
            $manager->persist($label);
        }
        $manager->flush();
    }
```

### Search

Configure a filter search service:

```yaml
# .../config/services.yml
services:
    app.filter_search.foo:
        class: Astina\Bundle\LabelsBundle\Search\LabelSearch
        #class: Astina\Bundle\LabelsBundle\Search\TranslatedLabelSearch
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