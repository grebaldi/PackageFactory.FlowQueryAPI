<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\View\JsonView;
use TYPO3\Flow\Mvc\Controller\ActionController;

abstract class APIController extends ActionController
{
    /**
     * @var array
     */
    protected $supportedMediaTypes = ['application/json'];

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = ['json' => JsonView::class];
}
