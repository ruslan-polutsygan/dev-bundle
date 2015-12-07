<?php

namespace RP\DevBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * @method addReference($name, $object)
 */
trait SimpleLoadFixtureTrait
{
    use LoadFixturesHelperTrait;

    /**
     * @param ObjectManager $manager
     * @param string        $className
     * @param array         $data
     * @param string        $referencePrefix
     * @param string        $referenceKey
     * @param callback|null $processRawItemArrayCallback
     */
    protected function doSimpleLoad(ObjectManager $manager, $className, $data, $referencePrefix, $referenceKey, $processRawItemArrayCallback = null)
    {
        $this->disablePKGenerator($manager, $className);

        $i = 0;
        foreach ($data as $item) {
            $entity = new $className();

            if ($processRawItemArrayCallback) {
                $processRawItemArrayCallback($item);
            }

            $this->fillFromArray($entity, $item);

            $manager->persist($entity);
            $reference = $referenceKey === '__index__' ? ++$i : $item[$referenceKey];
            $this->addReference($referencePrefix.'-'.$reference, $entity);
        }

        $manager->flush();
    }
}
