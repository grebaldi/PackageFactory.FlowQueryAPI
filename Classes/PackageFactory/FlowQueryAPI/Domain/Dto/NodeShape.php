<?php
namespace PackageFactory\FlowQueryAPI\Domain\Dto;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Mvc\Controller\ControllerContext;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Neos\Service\LinkingService;

class NodeShape implements \JsonSerializable
{
    /**
     * @var NodeInterface
     */
    protected $node;

    /**
     * @Flow\Inject
     * @var LinkingService
     */
    protected $linkingService;

    /**
     * @var ControllerContext
     */
    protected $controllerContext;

    /**
     * @var array
     */
    protected $shapeDescription = [
        '$include' => null,
        '$exclude' => null
    ];

    private static $allowedDirectNodeProperties = [
        'name',
        'label',
        'fullLabel',
        'nodeType',
        'hidden',
        'hiddenBeforeDateTime',
        'hiddenAfterDateTime',
        'hiddenInIndex',
        'accessRoles',
        'path',
        'contextPath',
        'depth',
        'workspace',
        'identifier',
        'removed',
        'visible',
        'accessible',
        // 'context',
        'dimensions',
        'autocreated'
    ];

    public function __construct(NodeInterface $node, array $shapeDescription = [])
    {
        $this->node = $node;
        $this->mergeShapeDescription($shapeDescription);
    }

    public function mergeShapeDescription(array $shapeDescription)
    {
        $this->shapeDescription = array_merge_recursive($this->shapeDescription, $shapeDescription);
    }

    public function setControllerContext(ControllerContext $controllerContext)
    {
        $this->controllerContext = $controllerContext;
    }

    public function jsonSerialize()
    {
        $shape = [];

        foreach (self::$allowedDirectNodeProperties as $propertyName) {
            if (
                $this->isWhiteListed($propertyName, $this->shapeDescription['$include']) &&
                !$this->isBlackListed($propertyName, $this->shapeDescription['$exclude'])
            ) {
                if ($propertyName === 'nodeType') {
                    $shape[$propertyName] = $this->node->getNodeType()->getName();
                    continue;
                }

                if ($propertyName === 'workspace') {
                    $shape[$propertyName] = $this->node->getWorkspace()->getName();
                    continue;
                }

                $shape[$propertyName] = ObjectAccess::getProperty($this->node, $propertyName);
            }
        }

        if (
            $this->isWhiteListed('properties', $this->shapeDescription['$include']) &&
            !$this->isBlackListed('properties', $this->shapeDescription['$exclude'])
        ) {
            $propertyNames = $this->node->getPropertyNames();
            $shape['properties'] = [];

            foreach ($propertyNames as $propertyName) {
                if (
                    $this->isWhiteListed($propertyName, $this->shapeDescription['$include']) &&
                    !$this->isBlackListed($propertyName, $this->shapeDescription['$exclude'])
                ) {
                    $property = $this->node->getProperty($propertyName);

                    if (is_array($property)) {
                        $shape['properties'][$propertyName] = $this->recursivelySerializeNodeProperties(
                            $property,

                            //
                            // Pass the whitelist, if existent - an empty array otherwise
                            //
                            isset($this->shapeDescription['$include']['properties']) &&
                            isset($this->shapeDescription['$include']['properties'][$propertyName]) ?
                            $this->shapeDescription['$include']['properties'][$propertyName] : [],

                            //
                            // Pass the blacklist, if existent - an empty array otherwise
                            //
                            isset($this->shapeDescription['$exclude']['properties']) &&
                            isset($this->shapeDescription['$exclude']['properties'][$propertyName]) ?
                            $this->shapeDescription['$exclude']['properties'][$propertyName] : []
                        );
                    }

                    $shape['properties'][$propertyName] = $property;
                }
            }
        }

        return [
            static::class => $shape + $this->buildRelatedSection()
        ];
    }

    protected function recursivelySerializeNodeProperties($propertyName, $whiteList, $blackList)
    {
        $result = [];

        foreach ($properties as $key => $value) {
            if (
                $this->isWhiteListed($key, $whiteList) &&
                !$this->isBlackListed($key, $blackList)
            ) {
                if (is_array($value)) {
                    $result[$key] = $this->recursivelySerializeNodeProperties(
                        $value,
                        isset($whiteList[$key]) ? $whiteList[$key] : [],
                        isset($blackList[$key]) ? $blackList[$key] : []
                    );
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }

    protected function isWhiteListed($propertyName, $shapeDescription)
    {
        if (!isset($this->shapeDescription['$include'])) {
            return true;
        }

        return $shapeDescription === '$include' || isset($shapeDescription[$propertyName]);
    }

    protected function isBlackListed($propertyName, $shapeDescription)
    {
        if (!isset($this->shapeDescription['$exclude'])) {
            return false;
        }

        return $shapeDescription === '$exclude' || isset($shapeDescription[$propertyName]);
    }

    protected function buildRelatedSection()
    {
        if (
            $this->controllerContext &&
            $this->isWhiteListed('_links', $this->shapeDescription['$include']) &&
            !$this->isBlackListed('_links', $this->shapeDescription['$exclude'])
        ) {
          return [
              '_related' => [
                  'self' => $this->buildRelation($this->node, $this->controllerContext),
                  'parent' => $this->buildRelation($this->node->getParent()?:$this->node, $this->controllerContext),
                  'children' => $this->buildRelations($this->node->getChildNodes(), $this->controllerContext)
              ]
          ];
        }

        return [];
    }

    protected function buildRelations($nodes, $controllerContext)
    {
        $result = [];

        foreach ($nodes as $node) {
            $result[] = $this->buildRelation($node, $controllerContext);
        }

        return $result;
    }

    protected function buildRelation(NodeInterface $node, $controllerContext)
    {
      $result = [
          'contextPath' => $node->getContextPath(),
          'nodeType' => $node->getNodeType()->getName(),
          '_links' => []
      ];

      $request = $controllerContext->getRequest()->getMainRequest();

      $uriBuilder = clone $controllerContext->getUriBuilder();
      $uriBuilder->setRequest($request);
      $result['_links']['service'] = $uriBuilder
          ->reset()
          ->setFormat($request->getFormat())
          ->uriFor('show', array('node' => $node), 'Node', 'PackageFactory.FlowQueryAPI');

      if (
          $node->getNodeType()->isOfType('TYPO3.Neos:Document')
      ) {
          $result['_links']['frontend'] = $this->linkingService->createNodeUri($controllerContext, $node, null, 'html');
      }

      return $result;
    }
}
