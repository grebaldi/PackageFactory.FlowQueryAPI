<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Eel\FlowQuery\FlowQuery;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use PackageFactory\FlowQueryAPI\Domain\Dto\NodeShape;

class FlowQueryController extends APIController
{
    /**
     * @Flow\IgnoreValidation("$q")
     * @param FlowQuery $q
     * @param string $finisher
     * @param array $finisherArguments
     * @return void
     */
    public function queryAction(FlowQuery $q, $finisher, array $finisherArguments = [])
    {
        $result = null;

        switch($finisher) {
            case 'get':
            case 'count':
            case 'property':
            case 'is':
                $result = call_user_func_array([$q, $finisher], $finisherArguments);
                break;

            default:
                throw new \Exception(sprintf('%s is not allowed as a finisher.', $finisher), 1460298033);
        }

        $this->view->assign('value', array_map(function($value) {
            if ($value instanceof NodeInterface) {
                return new NodeShape($value);
            }

            return $value;
        }, is_array($result) ? $result : [$result]));
    }
}
