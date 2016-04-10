<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use TYPO3\Flow\Mvc\View\JsonView;
use TYPO3\Flow\Mvc\Controller\ActionController;

abstract class APIController extends ActionController
{
    /**
     * @var array
     */
    protected $supportedMediaTypes = array(
        'application/json'
    );

    /**
     * @var array
     */
    protected $viewFormatToObjectNameMap = array(
        'json' => JsonView::class
    );
}
