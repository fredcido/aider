<?php

namespace Aider\Bundle\AiderBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class BaseController
 *
 * @package Aider\Bundle\AiderBundle\Controller
 */
class BaseController extends FOSRestController
{
    protected $serviceName = 'aider.query_creator';
    /**
     * List all Data (Collection) or Finds and displays Data by params
     *
     * @param $serviceName
     * @param $tableName
     * @param $params
     *
     * @return mixed
     */
    public function searchWithParam($tableName, $params)
    {
        $queryBuilder = $this->get($this->serviceName);

        $queryBuilder->buildAQuery(null, null, $tableName);

        foreach($params as $param => $value)
        {
            $queryBuilder->addCondition($param, $value);
        }

        return $queryBuilder->getResults();

    }

    /**
     * Finds and displays Data by id
     *
     * @param $entityName
     * @param $text
     * @param $id
     *
     * @return object
     */
    public function listDataId($entityName, $text, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository($entityName)->find($id);

        if (!$entity)
            throw $this->createNotFoundException($text." not found for id:".$id);

        return $entity;

    }
}