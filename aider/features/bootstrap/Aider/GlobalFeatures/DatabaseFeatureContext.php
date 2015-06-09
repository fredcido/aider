<?php

namespace Aider\GlobalFeatures;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Inflector\Inflector;

/**
 * Defines application features from the specific context.
 */
class DatabaseFeatureContext implements Context, SnippetAcceptingContext, KernelAwareContext
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    protected $kernel = null;

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return null
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given the following :arg1 exist(s)
     */
    public function theFollowingExist(TableNode $table, $arg1)
    {
        $arg1 = Inflector::classify(Inflector::singularize($arg1));

        $this->createGenericRecords($table, $arg1);
    }

    /**
     * Takes a relation name and an id and attempts to find a record.
     */
    protected function retrieveRelation($relationName, $id)
    {
        $em = $this->getEntityManager();

        $className = 'AiderBundle:'.Inflector::classify(Inflector::singularize($relationName));

        $repo = $em->getRepository($className);
        if($repo)
        {
            $entity = $em->getRepository($className)->find($id);
            if($entity)
            {
                return $entity;
            }            
        }
        throw new \Exception('Entity not found '.$relationName.' '.$id);
    }

    /**
     * @return null
     */
    public function buildSchema()
    {
        $metadata = $this->getMetadata();

        if (!empty($metadata)) {
            $tool = new SchemaTool($this->getEntityManager());
            $tool->dropSchema($metadata);
            $tool->createSchema($metadata);
        }
    }

    public function getDoctrineRepository($class)
    {
        return $this->kernel->getContainer()->get('doctrine')->getRepository($class);
    }

    /**
     * @return array
     */
    protected function getMetadata()
    {
        return $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * Create records - works for most entities.
     */
    protected function createGenericRecords(TableNode $table, $entityClass)
    {
        $em = $this->getEntityManager();

        $entityNamespace = 'Aider\Bundle\AiderBundle\Entity\\'.$entityClass;

        if(! class_exists($entityNamespace))
            throw new \Exception("Unrecognised entity {$entityClass}");

        foreach($table as $row){

            $entity = new $entityNamespace();

            foreach ($row as $key => $value){

                $setProperty = 'set'.Inflector::classify(Inflector::singularize($key));

                if ( method_exists($entity, $setProperty) ){

                    $relationalClass = 'Aider\Bundle\AiderBundle\Entity\\'.Inflector::classify(Inflector::singularize($key));

                    if(class_exists($relationalClass)){

                        if($value){
                            $relationRetrieved = $this->retrieveRelation($key,$value);
                            $entity->$setProperty($relationRetrieved);
                        }

                    }else
                        $entity->$setProperty($value);

                } else{
                    $addProperty = 'add'.Inflector::classify(Inflector::singularize($key));

                    if ($value){

                        // if there are multiple ids, repeat for each id.
                        $relations = explode(',', $value);

                        foreach ($relations as $relation){

                            $relationRetrieved = $this->retrieveRelation($key,$relation);

                            if(method_exists($entity, $addProperty))
                                $entity->$addProperty($relationRetrieved);

                            else
                                throw new \Exception('Can\'t create entity relationship '.$addProperty);
                        }
                    }
                }
            }
            $em->persist($entity);
        }
        $em->flush();
    }
}
