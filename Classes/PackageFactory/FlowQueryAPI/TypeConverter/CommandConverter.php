<?php
namespace PackageFactory\FlowQueryAPI\TypeConverter;

use TYPO3\Flow\Annotations as Flow;
use PackageFactory\FlowQueryAPI\Domain\Command\CommandInterface;

/**
 * An Object Converter for Commands.
 *
 * @Flow\Scope("singleton")
 */
class CommandConverter extends WriteShapeConverter
{
    /**
     * @var string
     */
    protected $targetType = CommandInterface::class;
}
