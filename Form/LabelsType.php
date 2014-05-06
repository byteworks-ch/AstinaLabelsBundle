<?php

namespace Astina\Bundle\LabelsBundle\Form;

use Astina\Bundle\LabelsBundle\Entity\Label;
use Astina\Bundle\LabelsBundle\Entity\LabelCategory;
use Astina\Bundle\LabelsBundle\Entity\LabelCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LabelsType extends AbstractType
{
    /**
     * @var LabelCategoryRepository
     */
    private $categoryRepository;

    function __construct(LabelCategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = $this->loadCategories($options);

        foreach ($categories as $category) {
            $this->addCategoryField($builder, $category);
        }

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use ($categories) {

            // distribute labels to category fields

            $form = $event->getForm();

            $labels = new ArrayCollection();
            foreach ($event->getData() as $label) {
                if ($label instanceof Label) {
                    $labels->add($label);
                }
            }

            foreach ($categories as $category) {
                $field = $form->get($category->getName());

                $fieldData = new ArrayCollection();
                /** @var Label $label */
                foreach ($labels as $label) {
                    if ($label->getCategory()->getId() === $category->getId()) {
                        $fieldData->add($label);
                    }
                }

                $field->setData($fieldData);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {

            // collect labels from category fields

            /** @var PersistentCollection $data */
            $data = $event->getData();

            $tmpData = clone $data;
            $data->clear();
            foreach ($tmpData as $fieldData) {
                if ($fieldData instanceof Label) { // skip existing labels
                    continue;
                }
                // add labels from category field
                foreach ($fieldData as $label) {
                    $data->add($label);
                }
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'categories' => null,
        ));
    }

    /**
     * @param array $options
     * @return LabelCategory[]
     */
    protected function loadCategories(array $options)
    {
        if ($names = $options['categories']) {
            return $this->categoryRepository->findByNames($names);
        }

        return $this->categoryRepository->findAll();
    }

    protected function addCategoryField(FormBuilderInterface $builder, LabelCategory $category)
    {
        $queryBuilder = $this->createQueryBuilder($category);

        $builder->add($category->getName(), 'entity', array(
            'class' => 'Astina\Bundle\LabelsBundle\Entity\Label',
            'multiple' => true,
            'translation_domain' => 'labels',
            'query_builder' => $queryBuilder,
        ));
    }

    protected function createQueryBuilder(LabelCategory $category)
    {
        return function(EntityRepository $repo) use ($category) {
            return $repo->createQueryBuilder('l')
                ->where('l.category = :category')
                ->setParameter('category', $category->getId())
                ;
        };
    }

    public function getName()
    {
        return 'astina_labels';
    }
}