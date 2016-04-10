<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use PackageFactory\FlowQueryAPI\Domain\Command\CommandInterface;

class CommandController extends APIController
{
    public function dispatchAction(CommandInterface $command)
    {

    }
}
