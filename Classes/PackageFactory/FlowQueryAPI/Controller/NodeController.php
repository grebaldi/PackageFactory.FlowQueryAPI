<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use TYPO3\Flow\Annotations as Flow;
use PackageFactory\FlowQueryAPI\Domain\Dto\NodeAddressInterface;
use PackageFactory\FlowQueryAPI\Domain\Dto\NodeShape;
use PackageFactory\FlowQueryAPI\TYPO3CR\Service\NodeService;

class NodeController extends APIController
{
    use \TYPO3\Neos\Controller\CreateContentContextTrait;

    /**
     * @Flow\Inject
     * @var NodeService
     */
    protected $nodeService;

    /**
     * @param string $workspaceName
     * @param array $dimensionvalues
     * @param array $shape
     * @return void
     */
    public function rootAction($workspaceName = 'live', $dimensionvalues = [], $shape = [])
    {
        $contentContext = $this->createContentContext($workspaceName, $dimensionvalues);
        $rootNode = $contentContext->getNode('/');

        $this->view->assign('value', $this->prepareResponse($rootNode, $shape));
    }

    /**
     * @param NodeAddressInterface $node
     * @param array $shape
     * @return void
     */
    public function showAction(NodeAddressInterface $node, $shape = [])
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', $this->prepareResponse($resolvedNode, $shape));
    }

    /**
     * @param NodeAddressInterface $node
     * @param array $shape
     * @return void
     */
    public function childrenAction(NodeAddressInterface $node, $shape = [])
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', $this->prepareResponse($resolvedNode->getChildNodes(), $shape));
    }

    /**
     * @param NodeAddressInterface $node
     * @param array $shape
     * @return void
     */
    public function parentAction(NodeAddressInterface $node, $shape = [])
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', $this->prepareResponse($resolvedNode->getParent(), $shape));
    }

    /**
     * @param NodeAddressInterface $node
     * @param array $shape
     * @return void
     */
    public function parentsAction(NodeAddressInterface $node, $shape = [])
    {
        $resolvedNode = $node->resolve();
        $parents = [];

        while ($parent = $resolvedNode->getParent()) {
            $parents[] = $parent;
            $resolvedNode = $parent;
        }

        $this->view->assign('value', $this->prepareResponse($parents, $shape));
    }

    /**
     * @param NodeAddressInterface $node
     * @param array $shape
     * @return void
     */
    public function documentAction(NodeAddressInterface $node, $shape = [])
    {
        $resolvedNode = $node->resolve();
        $closestDocument = $this->nodeService->getClosestDocumentNode($resolvedNode);

        $this->view->assign('value', $this->prepareResponse($closestDocument, $shape));
    }
}
