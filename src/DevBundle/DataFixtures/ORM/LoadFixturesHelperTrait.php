<?php

namespace RP\DevBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

trait LoadFixturesHelperTrait
{
    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = new PropertyAccessor();
    }

    /**
     * @param object $entity
     * @param array  $data
     */
    protected function fillFromArray($entity, array $data)
    {
        foreach ($data as $key => $value) {
            try {
                $this->guessTypeAndConvert($key, $value);

                $this->propertyAccessor->setValue($entity, $key, $value);
            } catch (NoSuchPropertyException $e) {
                if ($key !== 'reference') {
                    printf('--- Unknown property %s. Omitted.'.PHP_EOL, $key);
                }
            }
        }
    }

    protected function guessTypeAndConvert($key, &$value)
    {
        if (in_array($key, ['created_at', 'updated_at']) && !$value instanceof \DateTime) {
            $value = new \DateTime($value);
        }
    }

    /**
     * @param ObjectManager|EntityManager $em
     * @param $className
     */
    protected function disablePKGenerator(ObjectManager $em, $className)
    {
        $metadata = $em->getClassMetaData($className);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
    }
}
