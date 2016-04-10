<?php
namespace PackageFactory\FlowQueryAPI\Domain\Dto;

use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\Flow\Reflection\ObjectAccess;

class NodeShape implements \JsonSerializable
{
    /**
     * @var NodeInterface
     */
    protected $node;

    /**
     * @var array
     */
    protected $shapeDescription;

    public function __construct(NodeInterface $node, array $shapeDescription = [])
    {
        $this->node = $node;
        $this->shapeDescription = $shapeDescription;
    }

    public function mergeShapeDescription(array $shapeDescription)
    {
        $this->shapeDescription = array_merge_recursive($this->shapeDescription, $shapeDescription);
    }

    public function jsonSerialize()
    {
        $shape = [];

        $topLevelPropertyNames = ObjectAccess::getGettablePropertyNames($this->node);
        foreach ($topLevelPropertyNames as $propertyName) {
            if ($propertyName != 'properties' && !empty($this->shapeDescription) && isset($this->shapeDescription[$propertyName])) {
                $shape[$propertyName] = ObjectAccess::getProperty($node, $propertyName);
            }
        }

        if (!empty($this->shapeDescription) && isset($this->shapeDescription['properties'])) {
            $propertyNames = $this->node->getPropertyNames();
            $shape['properties'] = [];

            foreach ($propertyNames as $propertyName) {
                if (!empty($this->shapeDescription['properties'][$propertyName])) {
                    $property = $this->node->getProperty($propertyName);

                    if (is_array($property)) {
                        $shape['properties'][$propertyName] = $this->recursivelySerializeNodeProperties(
                            $property,
                            $this->shapeDescription['properties'][$propertyName]
                        );
                    }

                    $shape['properties'][$propertyName] = $property;
                }
            }
        }

        return $shape;
    }

    public function recursivelySerializeNodeProperties($shape['properties'][$propertyName], $shapeDescription)
    {
        $result = [];

        foreach ($properties as $key => $value) {
            if (isset($shapeDescription[$key])) {
                if (is_array($value)) {
                    $result[$key] = $this->recursivelySerializeNodeProperties(
                        $value,
                        $shapeDescription[$key]
                    );
                }
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
