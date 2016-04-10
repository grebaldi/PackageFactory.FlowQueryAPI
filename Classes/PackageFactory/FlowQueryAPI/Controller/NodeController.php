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

    protected function prepareResponse($resource)
    {
        if (is_array($resource)) {
            $result = [];
            foreach ($resource as $node) {
                $result[] = $this->prepareResponse($node);
            }

            return $result;
        }

        $nodeShape = new NodeShape($resource);
        $nodeShape->setControllerContext($this->controllerContext);

        return $nodeShape;
    }

    /**
     * @param string $workspaceName
     * @param array $dimensionvalues
     * @return void
     */
    public function rootAction($workspaceName = 'live', $dimensionvalues = [])
    {
        $contentContext = $this->createContentContext($workspaceName, $dimensionvalues);
        $rootNode = $contentContext->getNode('/');

        $this->view->assign('value', $this->prepareResponse($rootNode));
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function showAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', $this->prepareResponse($resolvedNode));
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function childrenAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', $this->prepareResponse($resolvedNode->getChildNodes()));
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function parentAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();

        $this->view->assign('value', $this->prepareResponse($resolvedNode->getParent()));
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

        $this->view->assign('value', $this->prepareResponse($parents));
    }

    /**
     * @param NodeAddressInterface $node
     * @return void
     */
    public function documentAction(NodeAddressInterface $node)
    {
        $resolvedNode = $node->resolve();
        $closestDocument = $this->nodeService->getClosestDocumentNode($resolvedNode);

        $this->view->assign('value', $this->prepareResponse($closestDocument));
    }
}
