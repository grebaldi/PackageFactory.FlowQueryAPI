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
    protected $shapeDescription = [
        '$included' => [],
        '$excluded' => []
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

    public function jsonSerialize()
    {
        $shape = [];

        $topLevelPropertyNames = ObjectAccess::getGettablePropertyNames($this->node);
        foreach ($topLevelPropertyNames as $propertyName) {
            if (
                $propertyName != 'properties' &&
                $this->isWhiteListed($propertyName, $this->shapeDescription['$include']) &&
                !$this->isBlackListed($propertyName, $this->shapeDescription['$exclude'])
            ) {
                $shape[$propertyName] = ObjectAccess::getProperty($node, $propertyName);
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

        return $shape;
    }

    protected function recursivelySerializeNodeProperties($shape['properties'][$propertyName], $whiteList, $blackList)
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

        return isset($shapeDescription[$propertyName]);
    }

    protected function isBlackListed($propertyName, $shapeDescription)
    {
        if (!isset($this->shapeDescription['$exclude'])) {
            return false;
        }

        return isset($shapeDescription[$propertyName]);
    }
}
