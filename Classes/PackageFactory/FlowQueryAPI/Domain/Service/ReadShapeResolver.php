<?php
namespace PackageFactory\FlowQueryAPI\Domain\Service;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\TypeHandling;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Mvc\Controller\ControllerContext;
use PackageFactory\FlowQueryAPI\Annotations\ReadShape;
use PackageFactory\FlowQueryAPI\Domain\Dto\ReadShapeInterface;

/**
 * @Flow\Scope("singleton")
 */
class ReadShapeResolver
{
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

    public function resolve($resource, $shape = [], ControllerContext $controllerContext = null)
    {
        //
        // If we're dealing with an array of resources, resolve each item individually
        //
        if (is_array($resource)) {
            $result = [];
            foreach ($resource as $node) {
                $result[] = $this->resolve($node, $shape, $controllerContext);
            }

            return $result;
        }

        //
        // Try to find a fitting ReadShape implementation for the given resource
        //
        if ($shapeClassName = $this->discoverShapeForResource($resource)) {
            $readShapeAnnotation = $this->reflectionService->getClassAnnotation($shapeClassName, ReadShape::class);

            $shape = $this->objectManager->get($shapeClassName, $resource, $shape);

            if ($controllerContext) {
                $shape->setControllerContext($controllerContext);
            }

            //
            // Build Envelope for client consumtion
            //
            return [
                $readShapeAnnotation->getAlias() => $shape
            ];
        }

        //
        // There's no API exposure without proper control
        //
        throw new \Exception(
            sprintf('Could not find read shape for %s', gettype($resource)),
            1460299754
        );
    }

    protected function discoverShapeForResource($resource)
    {
        $readShapeClasses = $this->reflectionService->getAllImplementationClassNamesForInterface(
            ReadShapeInterface::class
        );

        foreach ($readShapeClasses as $readShapeClass) {
            $readShapeAnnotation = $this->reflectionService->getClassAnnotation($readShapeClass, ReadShape::class);

            if (!$readShapeAnnotation) {
                throw new \Exception(
                    sprintf('Error in %s - ReadShapes need to have a ReadShape Annotation.', $readShapeClass),
                    1460302151
                );
            }

            if (
                $readShapeAnnotation->getType() === TypeHandling::getTypeForValue($resource) ||
                (
                    is_object($resource) &&
                    is_a($resource, $readShapeAnnotation->getType())
                )
            ) {
                return $readShapeClass;
            }
        }
    }
}
