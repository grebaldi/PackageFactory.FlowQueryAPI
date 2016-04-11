<?php
namespace PackageFactory\FlowQueryAPI\TypeConverter;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\TypeConverter\AbstractTypeConverter;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use PackageFactory\FlowQueryAPI\TYPO3CR\Service\NodeService;

/**
 * An Object Converter for FlowQuery.
 *
 * @Flow\Scope("singleton")
 */
class FlowQueryConverter extends AbstractTypeConverter
{
    /**
     * @var array
     */
    protected $sourceTypes = ['array'];

    /**
     * @var string
     */
    protected $targetType = FlowQuery::class;

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * @Flow\inject
     * @var NodeService
     */
    protected $nodeService;

    /**
     * @inheritdoc
     */
    public function canConvertFrom($source, $targetType)
    {
        if (!is_array($source)) {
            return false;
        }

        if (!isset($source['context'])) {
            return false;
        }

        return true;
    }

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
        $context = $this->determineContext($source['context']);
        $q = new FlowQuery(is_array($context) ? $context : [$context]);

        return $this->buildQuery(isset($source['chain']) ? $source['chain'] : [], $q);
    }

    /**
     * @param string|array $contextDescription
     * @return NodeInterface|array<NodeInterface>
     */
    public function determineContext($contextDescription)
    {
        if (is_array($contextDescription)) {
            $result = [];
            foreach ($contextDescription as $singleContextDescription) {
                $result[] = $this->determineContext($singleContextDescription);
            }

            return $result;
        }

        if ($contextDescription === 'site') {
            return $this->nodeService->getFirstSiteNode();
        }

        return $this->nodeService->getNodeFromContextPath($contextDescription);
    }

    /**
     * @param array $chainDescription [description]
     * @param FlowQuery $q [description]
     * @return FlowQuery
     */
    public function buildQuery(array $chainDescription, FlowQuery $q)
    {
        if (!$chainDescription || empty($chainDescription)) {
            return $q;
        }

        $operationDescription = array_shift($chainDescription);

        switch($operationDescription['type']) {
            case 'children':
                $filter = isset($operationDescription['filter']) ? $operationDescription['filter'] : null;
                $q = $filter ? $q->children($filter) : $q->children();
                break;

            case 'parent':
                $q = $q->parent();
                break;

            case 'parents':
                $filter = isset($operationDescription['filter']) ? $operationDescription['filter'] : null;
                $q = $filter ? $q->parents($filter) : $q->parents();
                break;

            case 'filter':
                if ($filter = $operationDescription['filter']) {
                    $q = $q->filter($filter);
                    break;
                }

                throw new \Exception('No filter argument for FlowQuery filter operation provided.', 1460285412);

            case 'find':
                $filter = isset($operationDescription['filter']) ? $operationDescription['filter'] : null;
                $q = $filter ? $q->find($filter) : $q->find();
                break;

            case 'closest':
                $filter = isset($operationDescription['filter']) ? $operationDescription['filter'] : null;
                $q = $filter ? $q->find($filter) : $q->find();
                break;

            default:
                throw new \Exception(
                    sprintf('FlowQuery operation %s is not allowed here', $operationDescription['type']), 1460285096);
        }

        return $this->buildQuery($chainDescription, $q);
    }
}
