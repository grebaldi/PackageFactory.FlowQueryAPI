<?php
namespace PackageFactory\FlowQueryAPI\Domain\Dto;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use PackageFactory\FlowQueryAPI\TYPO3CR\Service\NodeService;

class ContextPathNodeAddress implements NodeAddressInterface
{
    /**
     * @var string
     */
    protected $contextPath;

    /**
     * @Flow\inject
     * @var NodeService
     */
    protected $nodeService;

    /**
     * @param string $contextPath
     */
    public function __construct($contextPath)
    {
        $this->contextPath = $contextPath;
    }

    /**
     * @return NodeInterface
     */
    public function resolve()
    {
        if (!$this->contextPath) {
            throw new \Exception('No context path set for ContextPathNodeAddress', 1460288474);
        }

        return $this->nodeService->getNodeFromContextPath($this->contextPath);
    }
}
