<?php
namespace PackageFactory\FlowQueryAPI\TypeConverter;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use PackageFactory\FlowQueryAPI\Domain\Dto\NodeAddressInterface;
use PackageFactory\FlowQueryAPI\Domain\Dto\ContextPathNodeAddress;

/**
 * An Object Converter for NodeAddresses.
 *
 * @Flow\Scope("singleton")
 */
class NodeAddressConverter extends AbstractTypeConverter
{
    /**
     * @var array
     */
    protected $sourceTypes = ['string'];

    /**
     * @var string
     */
    protected $targetType = NodeAddressInterface::class;

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * @inheritdoc
     */
    public function convertFrom(
        $source,
        $targetType,
        array $subProperties = array(),
        PropertyMappingConfigurationInterface $configuration = null
    )
    {
        return new ContextPathNodeAddress($source);
    }
}
