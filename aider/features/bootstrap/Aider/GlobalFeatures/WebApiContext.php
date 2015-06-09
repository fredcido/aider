<?php

namespace Aider\GlobalFeatures;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\WebApiExtension\Context\WebApiContext as BaseApiContext;
use PHPUnit_Framework_Assert as Assertions;

class WebApiContext extends BaseApiContext implements Context
{
    /**
     * @Then I get :count result(s)
     */
    public function iGetResults($count)
    {
        $data = $this->response->json();
        Assertions::assertEquals($count, count($data));
    }

    /**
     * @Then result :index contains :key :value
     */
    public function resultContains($index, $key, $value)
    {
        $index = $index - 1;
        $data = $this->response->json();
        Assertions::assertArrayHasKey($index, $data);
        Assertions::assertArrayHasKey($key, $data[$index]);
        Assertions::assertEquals($data[$index][$key], $value);
    }
}
