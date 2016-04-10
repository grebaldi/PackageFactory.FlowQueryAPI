<?php
namespace PackageFactory\FlowQueryAPI\Domain\Dto;

use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

interface NodeAddressInterface
{
    /**
     * @return NodeInterface
     */
    public function resolve();
}
