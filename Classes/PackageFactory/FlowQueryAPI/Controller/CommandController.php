<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use PackageFactory\FlowQueryAPI\Domain\Command\CommandInterface;

class CommandController extends APIController
{

    public function initializeDispatchAction()
    {
        $this->arguments['commands']->getPropertyMappingConfiguration()->allowAllProperties();
    }

    /**
     * @param array<CommandInterface> $commands
     * @return void
     */
    public function dispatchAction(array $commands)
    {

    }
}
