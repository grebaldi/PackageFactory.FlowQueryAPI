<?php
namespace PackageFactory\FlowQueryAPI\Domain\Dto;

use TYPO3\Flow\Annotations as Flow;
use PackageFactory\FlowQueryAPI\Annotations as FQAPI;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Mvc\Controller\ControllerContext;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Neos\Service\LinkingService;

/**
 *
 */
class NodeShape implements \JsonSerializable
{
    const INCLUDE_BRANCH = 1461168043;

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
     * @var array
     */
    protected $shapeDescription = self::INCLUDE_BRANCH;

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
        'dimensions',
        'autocreated',
        'properties'
    ];

    public function __construct(NodeInterface $node, array $shapeDescription = [])
    {
        $this->node = $node;
        $this->mergeShapeDescription($shapeDescription);
    }

    public function mergeShapeDescription(array $shapeDescription)
    {
        if (!is_array($this->shapeDescription)) {
            $this->shapeDescription = $shapeDescription;
            return;
        }
        $this->shapeDescription = array_merge_recursive($this->shapeDescription, $shapeDescription);
    }

    protected function buildNodeArray()
    {
        $shape = [];

        //
        // Serialize direct properties
        //
        foreach (self::$allowedDirectNodeProperties as $propertyName) {

            //
            // For the node type, just return the name
            //
            if ($propertyName === 'nodeType') {
                $shape[$propertyName] = $this->node->getNodeType()->getName();
                continue;
            }

            //
            // For the workspace, just return the name
            //
            if ($propertyName === 'workspace') {
                $shape[$propertyName] = $this->node->getWorkspace()->getName();
                continue;
            }

            //
            // For everything else, use the direct property value
            //
            $shape[$propertyName] = ObjectAccess::getProperty($this->node, $propertyName);
        }

        return $shape;
    }

    protected function reduceNodeArray($nodeArray, $shapeDescription)
    {
        $shape = [];

        foreach ($nodeArray as $key => $value) {
            if (
                $shapeDescription === self::INCLUDE_BRANCH ||
                array_key_exists($key, $shapeDescription)
            ) {
                //
                // If the value is an array, build the next shape description and resolve it
                // recursively
                //
                if (is_array($value)) {

                    if ($shapeDescription === self::INCLUDE_BRANCH) {
                        //
                        // If we're in a branch that is included entirely, just keep it that way
                        //
                        $nextShapeDescription = $shapeDescription;
                    } else {
                        //
                        // If there's an include rule for the current property, we need to figure out,
                        // whether the shape has more depth at this point, or we need to include the entire
                        // branch
                        //
                        $nextShapeDescription = $shapeDescription[$key] === $key ?
                            self::INCLUDE_BRANCH : $shapeDescription[$key];
                    }

                    $shape[$key] = $this->reduceNodeArray($value, $nextShapeDescription);
                    continue;
                }

                $shape[$key] = $value;
            }
        }

        return $shape;
    }

    public function jsonSerialize()
    {
        $shape = $this->buildNodeArray();
        return $this->reduceNodeArray($shape, $this->shapeDescription);
    }
}
