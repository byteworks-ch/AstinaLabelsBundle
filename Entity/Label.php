<?php

namespace Astina\Bundle\LabelsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Astina\Bundle\LabelsBundle\Entity\LabelRepository")
 */
class Label
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * For sorting in UI
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @var LabelCategory
     * @ORM\ManyToOne(targetEntity="LabelCategory", inversedBy="labels")
     */
    private $category;

    /**
     * @Gedmo\Locale
     */
    private $locale;


    function __toString()
    {
        return $this->name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setCategory(LabelCategory $category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

}
