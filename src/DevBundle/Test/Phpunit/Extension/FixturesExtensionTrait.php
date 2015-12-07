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
    protected $referenceRepositoryData;

    protected function setUpFixtures()
    {
        $referenceRepositoryPath = $this->getKernel()->getContainer()->getParameter('fixtures.reference_repository_path');
        if (false == file_exists($referenceRepositoryPath)) {
            throw new \LogicException(sprintf('Reference repository path %s is not valid', $referenceRepositoryPath));
        }

        $manager = $this->getKernel()->getContainer()->get('doctrine.orm.default_entity_manager');

        $this->referenceRepositoryData = unserialize(file_get_contents($referenceRepositoryPath));
        $this->referenceRepository = new ReferenceRepository($manager);
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
            if (isset($this->referenceRepositoryData[$name])) {
                $reference = $manager->getReference(
                        $this->referenceRepositoryData[$name]['class'],
                        $this->referenceRepositoryData[$name]['identifier']
                    );

                $this->referenceRepository->setReference($name, $reference);
            }
        }

        return $this->referenceRepository->getReference($name);
    }

    /**
     * @return KernelInterface
     */
    abstract protected function getKernel();
}
