<?php
namespace PackageFactory\FlowQueryAPI\FlowQuery\Operations;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Eel\FlowQuery\Operations\AbstractOperation;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use PackageFactory\FlowQueryAPI\Domain\Dto\NodeShape;

class ShapeOperation extends AbstractOperation
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $shortName = 'shape';

    /**
     * {@inheritdoc}
     *
     * @param \TYPO3\Eel\FlowQuery\FlowQuery $flowQuery the FlowQuery object
     * @param array $arguments the context index to fetch from
     * @return mixed
     */
    public function evaluate(\TYPO3\Eel\FlowQuery\FlowQuery $flowQuery, array $arguments)
    {
        $context = $flowQuery->getContext();
        list ($shapeDescription) = $arguments;

        $newContext = [];
        foreach ($context as $value) {
            if ($value instanceof NodeInterface) {
                $newContext[] = new NodeShape($value, $shapeDescription);
                continue;
            }

            if ($value instanceof NodeShape) {
                $value->mergeShapeDescription($shapeDescription);
            }

            $newContext[] = $value;
        }

        $flowQuery->setContext($newContext);
    }
}
