<?php

namespace Resomedia\UsefulBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
    protected $where;

    /**
     * EntityToPropertyTransformer constructor.
     * @param EntityManagerInterface $em
     * @param $class
     * @param $property
     * @param $where
     */
    public function __construct(EntityManagerInterface $em, $class, $property, $where)
    {
        $this->em = $em;
        $this->unitOfWork = $this->em->getUnitOfWork();
        $this->class = $class;
        $this->property = $property;
        $this->where = $where;
    }

    /**
     * @param mixed $entity
     * @return mixed|null
     * @throws Exception
     */
    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        if (!$this->unitOfWork->isInIdentityMap($entity)) {
            throw new Exception('Entities passed to the choice field must be managed');
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
        if (!$prop_value)
            return null;

        if (!is_numeric($prop_value))
            $prop_value = '\'' . $prop_value . '\'';

        $query = $this->em->getRepository($this->class)
            ->createQueryBuilder('e')
            ->where('e.' . $this->property . '= :prop_value')
            ->setParameter('prop_value', $prop_value);
        if ($this->where)
            $query->andWhere('e.' . $this->where);

        $query->setMaxResults(1);
        return $query->getQuery()->getOneOrNullResult();
    }
}
