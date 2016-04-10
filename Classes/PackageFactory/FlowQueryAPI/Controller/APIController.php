<?php
namespace PackageFactory\FlowQueryAPI\Controller;

use TYPO3\Flow\Mvc\View\JsonView:

abstract class APIController
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
