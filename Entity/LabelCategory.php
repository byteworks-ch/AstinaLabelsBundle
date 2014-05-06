<?php

namespace Astina\Bundle\LabelsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Astina\Bundle\LabelsBundle\Entity\LabelCategoryRepository")
 */
class LabelCategory
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * For sorting in UI
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Label", mappedBy="category", cascade={"remove"})
     */
    private $labels;

    function __construct()
    {
        $this->labels = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setLabels(ArrayCollection $labels)
    {
        $this->labels = $labels;
    }

    public function getLabels()
    {
        return $this->labels;
    }
}