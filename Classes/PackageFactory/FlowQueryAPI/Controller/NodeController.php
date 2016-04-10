<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use PackageFactory\FlowQueryAPI\Domain\Dto\NodeAddressInterface;
use PackageFactory\FlowQueryAPI\Domain\Dto\NodeShape;
use PackageFactory\FlowQueryAPI\TYPO3CR\Service\NodeService;

class NodeController extends APIController
{
    use \TYPO3\Neos\Controller\CreateContentContextTrait;

    /**
     * @Flow\inject
     * @var NodeService
     */
    protected $nodeService;

    /**
     * @param string $workspaceName
     * @param array $dimensionvalues
     * @return void
     */
    public function rootAction($workspaceName = 'live', $dimensionvalues = [])
    {
        $contentContext = $this->createContentContext($workspaceName, $dimensionvalues);
        $rootNode = $contentContext->getNode('/');

        $this->view->assign('value', new NodeShape($rootNode));
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function showAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', new NodeShape($resolvedNode));
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function childrenAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', array_map(function($node) {
            return new NodeShape($node);
        }, $resolvedNode->children()));
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function parentAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', new NodeShape($resolvedNode->getParent()));
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function parentsAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();
        $parents = [];

        while ($parent = $resolvedNode->getParent()) {
            $parents[] = $parent;
            $resolvedNode = $parent;
        }

        $this->view->assign('value', array_map(function($node) {
            return new NodeShape($node);
        }, $parents);
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function documentAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();
        $closestDocument = $this->nodeService->getClosestDocumentNode($resolvedNode);

        $this->view->assign('value', new NodeShape($closestDocument));
    }
}
