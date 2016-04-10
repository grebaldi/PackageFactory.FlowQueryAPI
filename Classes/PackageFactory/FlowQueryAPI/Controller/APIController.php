<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\View\JsonView;
use TYPO3\Flow\Mvc\Controller\ActionController;
use PackageFactory\FlowQueryAPI\Domain\Service\ReadShapeResolver;

abstract class APIController extends ActionController
{
    /**
     * @Flow\Inject
     * @var ReadShapeResolver
     */
    protected $readShapeResolver;

    /**
     * @var array
     */
    protected $supportedMediaTypes = ['application/json'];

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = ['json' => JsonView::class];

    protected function prepareResponse($resource, $shape = [])
    {
        return $this->readShapeResolver->resolve($resource, $shape, $this->controllerContext);
    }
}
