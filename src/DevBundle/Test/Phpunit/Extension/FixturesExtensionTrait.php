<?php

namespace RP\DevBundle\Test\Phpunit\Extension;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\HttpKernel\KernelInterface;

trait FixturesExtensionTrait
{
    /**
     * @var ReferenceRepository
     */
    protected $referenceRepository;

    /**
     * @var array
     */
    protected static $referenceRepositoryData;

    protected function setUpFixtures()
    {
        $this->setReferenceRepositoryData();
        $manager = $this->getKernel()->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->referenceRepository = new ReferenceRepository($manager);
    }
    
    private function setReferenceRepositoryData()
    {
        if (static::$referenceRepositoryData) {
            return;
        }
        $referenceRepositoryPath = $this->getKernel()->getContainer()->getParameter('fixtures.reference_repository_path');
        if (false == file_exists($referenceRepositoryPath)) {
            throw new \LogicException(sprintf('Reference repository path %s is not valid', $referenceRepositoryPath));
        }
        static::$referenceRepositoryData = unserialize(file_get_contents($referenceRepositoryPath));
    }

    /**
     * @param string $name
     *
     * @return object
     */
    protected function getFixture($name)
    {
        $manager = $this->getKernel()->getContainer()->get('doctrine.orm.default_entity_manager');

        if (!$this->referenceRepository->hasReference($name)) {
            if (isset(static::$referenceRepositoryData[$name])) {
                $reference = $manager->getReference(
                    static::$referenceRepositoryData[$name]['class'],
                    static::$referenceRepositoryData[$name]['identifier']
                );

                $this->referenceRepository->setReference($name, $reference);
            }
        }

        return $this->referenceRepository->getReference($name);
    }

    protected function cleanFixtures()
    {
        $this->referenceRepository = null;
    }

    /**
     * @return KernelInterface
     */
    abstract protected function getKernel();
}
