<?php
namespace PackageFactory\FlowQueryAPI\TypeConverter;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\TypeHandling;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use PackageFactory\FlowQueryAPI\Annotations\Shape;
use PackageFactory\FlowQueryAPI\Domain\Dto\WriteShapeInterface;

/**
 * An Object Converter for Write Shapes.
 *
 * @Flow\Scope("singleton")
 */
class WriteShapeConverter extends AbstractTypeConverter
{
    /**
     * @var array
     */
    protected $sourceTypes = ['array'];

    /**
     * @var string
     */
    protected $targetType = WriteShapeInterface::class;

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @var array
     */
    protected $aliasMap = [];

    /**
     * @inheritdoc
     */
    public function canConvertFrom($source, $targetType)
    {
        if (is_array($source)) {
            $keys = array_keys($source);

            if (count($keys) !== 1) {
                return false;
            }

            $writeShapeAlias = $keys[0];
            $writeShapeClass = $this->discoverShapeForAlias($writeShapeAlias);

            if (!$writeShapeClass) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function convertFrom(
        $source,
        $targetType,
        array $subProperties = [],
        PropertyMappingConfigurationInterface $configuration = null
    )
    {
        $keys = array_keys($source);
        $writeShapeAlias = $keys[0];

        if ($shapeClassName = $this->discoverShapeForAlias($writeShapeAlias)) {
            $shape = $this->objectManager->get($shapeClassName);
            foreach ($source[$writeShapeAlias] as $key => $value) {
                if (!ObjectAccess::isPropertySettable($shape, $key)) {
                    throw new \Exception(
                        sprintf('Property %s cannot be set in %s', $key, $shapeClassName),
                        1460307960
                    );
                }

                if (is_array($value) && $this->canConvertFrom($value, $targetType)) {
                    ObjectAccess::setProperty($shape, $key, $this->convertFrom(
                        $value,
                        $targetType,
                        $subProperties,
                        $configuration
                    ));

                    continue;
                }

                ObjectAccess::setProperty($shape, $key, $value);
            }

            if (!$shape) {
                throw new \Exception(
                    sprintf('Could not convert %s to %s', $writeShapeAlias, $shapeClassName),
                    1460304889
                );
            }

            return $shape;
        }

        //
        // There's no API exposure without proper control
        //
        throw new \Exception(
            sprintf('Could not find write shape for %s', $writeShapeAlias),
            1460304889
        );
    }

    protected function discoverShapeForAlias($alias) {
        if (isset($aliasMap[$alias])) {
            return $aliasMap[$alias];
        }

        $writeShapeClasses = $this->reflectionService->getAllImplementationClassNamesForInterface(
            WriteShapeInterface::class
        );

        foreach ($writeShapeClasses as $writeShapeClass) {
            $shapeAnnotation = $this->reflectionService->getClassAnnotation($writeShapeClass, Shape::class);

            if (!$shapeAnnotation) {
                throw new \Exception(
                    sprintf('Error in %s - WriteShapes need to have a Shape Annotation.', $writeShapeClass),
                    1460304711
                );
            }

            if ($shapeAnnotation->getAlias() === $alias) {
                return $aliasMap[$alias] = $writeShapeClass;
            }
        }
    }

}
