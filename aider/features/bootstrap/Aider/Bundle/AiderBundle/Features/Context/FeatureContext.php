<?php

namespace Aider\Bundle\AiderBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;


/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext 
{
    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->dbContext = $environment->getContext('Aider\GlobalFeatures\DatabaseFeatureContext');
    }


    /**
     * @BeforeScenario
     */
    public function cleanDatabase()
    {
        $this->dbContext->buildSchema();
    }
}
