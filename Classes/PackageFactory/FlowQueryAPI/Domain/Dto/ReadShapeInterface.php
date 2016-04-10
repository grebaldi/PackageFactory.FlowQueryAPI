<?php
namespace PackageFactory\FlowQueryAPI\Domain\Dto;

use TYPO3\Flow\Mvc\Controller\ControllerContext;

interface ReadShapeInterface extends \JsonSerializable
{
    public function setControllerContext(ControllerContext $controllerContext);
}
