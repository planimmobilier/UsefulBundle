<?php

namespace Resomedia\UsefulBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EntityToIdTransformer
 * @package Resomedia\UsefulBundle\Form\DataTransformer
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    protected $em;
    protected $class;
    protected $unitOfWork;

    /**
     * EntityToIdTransformer constructor.
     * @param ObjectManager $em
     * @param $class
     */
    public function __construct(ObjectManager $em, $class)
    {
        $this->em = $em;
        $this->unitOfWork = $this->em->getUnitOfWork();
        $this->class = $class;
    }

    /**
     * @param mixed $entity
     * @return string
     * @throws \Exception
     */
    public function transform($entity)
    {
        if (null === $entity || '' === $entity) {
            return 'null';
        }
        if (!is_object($entity)) {
            throw new UnexpectedTypeException($entity, 'object');
        }
        if (!$this->unitOfWork->isInIdentityMap($entity)) {
            throw new \Exception('Entities passed to the choice field must be managed');
        }

        return $entity->getId();
    }

    /**
     * @param mixed $id
     * @return null|object
     */
    public function reverseTransform($id)
    {
        if ('' === $id || null === $id) {
            return null;
        }

        if (!is_numeric($id)) {
            throw new UnexpectedTypeException($id, 'numeric' . $id);
        }

        $entity = $this->em->getRepository($this->class)->find($id);

        if ($entity === null) {
            throw new TransformationFailedException(sprintf('The entity with key "%s" could not be found', $id));
        }

        return $entity;
    }
}
