<?php
namespace PackageFactory\FlowQueryAPI\Domain\Command;

use PackageFactory\FlowQueryAPI\Annotations as FQAPI;

/**
 * @FQAPI\Shape(
 * 		alias="PackageFactory.FlowQueryAPI:Commands:Test"
 * )
 */
class TestCommand implements CommandInterface
{
    protected $test = 'nope, nothing set...';

    public function setTest($test)
    {
        $this->test = $test;
    }

    public function getTest()
    {
        return $this->test;
    }
}
