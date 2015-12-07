<?php

namespace RP\DevBundle\Doctrine;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManager;

class FixtureReferenceRepositorySerializer
{
    /**
     * @param ReferenceRepository $referenceRepository
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function serialize(ReferenceRepository $referenceRepository)
    {
        $references = array();
        $isORM = $referenceRepository->getManager() instanceof EntityManager;
        foreach ($referenceRepository->getReferences() as $name => $reference) {
            $reference = $referenceRepository->getReference($name);

            $references[$name]['identifier'] = $referenceRepository->getManager()->getUnitOfWork()->getEntityIdentifier($reference);

            if ($reference instanceof Proxy) {
                $ro = new \ReflectionObject($reference);
                $references[$name]['class'] = $ro->getParentClass()->getName();
            } else {
                $references[$name]['class'] = get_class($reference);
            }
        }

        return serialize($references);
    }
}
