<?php
namespace PackageFactory\FlowQueryAPI\Domain\Dto;

use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

class ContextPathNodeAddress implements NodeAddressInterface
{
    /**
     * @var string
     */
    protected $contextPath;

    /**
     * Set the context path
     *
     * @param string $contextPath
     */
    public function setContextPath($contextPath)
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

        return $this->nodeService->getNodeByContextPath($this->contextPath);
    }
}
