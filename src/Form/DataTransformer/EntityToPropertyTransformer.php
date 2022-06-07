<?php

namespace Resomedia\UsefulBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class EntityToPropertyTransformer
 * @package Resomedia\UsefulBundle\Form\DataTransformer
 */
class EntityToPropertyTransformer implements DataTransformerInterface
{
    protected $em;
    protected $class;
    protected $property;
    protected $unitOfWork;

    /**
     * EntityToPropertyTransformer constructor.
     * @param EntityManagerInterface $em
     * @param $class
     * @param $property
     */
    public function __construct(EntityManagerInterface $em, $class, $property)
    {
        $this->em = $em;
        $this->unitOfWork = $this->em->getUnitOfWork();
        $this->class = $class;
        $this->property = $property;

    }

    /**
     * @param mixed $entity
     * @return mixed|null
     * @throws \Exception
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        if (!$this->unitOfWork->isInIdentityMap($entity)) {
            throw new \Exception('Entities passed to the choice field must be managed');
        }

        if ($this->property) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            
            return $propertyAccessor->getValue($entity, $this->property);
        }

        return current($this->unitOfWork->getEntityIdentifier($entity));
    }

    /**
     * @param mixed $prop_value
     * @return null|object
     */
    public function reverseTransform($prop_value)
    {
        if (!$prop_value) {
            return null;
        }

        $entity = $this->em->getRepository($this->class)->findOneBy(array($this->property => $prop_value));

        return $entity;
    }
}
