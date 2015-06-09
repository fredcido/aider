<?php

namespace Aider\Bundle\AiderBundle\Entity;

use Doctrine\ORM\QueryBuilder;

class QueryCreator
{
    protected $qb;				// query builder

    protected $tableShort;          //table abreviation e.g. Test = T

    const DEFAULT_LIMIT = 20;	// default number of results to return
    const MAX_LIMIT = 60;		// max number of results to return

    public function __construct(QueryBuilder $qb)
    {
        $this->qb = $qb;
    }

    /**
     * @param int $offSet
     * @param int $limit
     * @param $table
     * @return $this
     */
    public function buildAQuery($offSet=0, $limit=self::DEFAULT_LIMIT, $table)
    {
        $classNamespace = 'Aider\Bundle\AiderBundle\Entity\\'.$table;
        $this->tableShort = $table[0];

        $this->qb->resetDQLParts();

        $this->qb->select($this->tableShort)
            ->from($classNamespace, $this->tableShort);

        $this->setOffset($offSet);
        $this->setLimit($limit);

        return $this;

    }

    public function addCondition($key, $value)
    {
        if($value !== NULL)
        {
            switch($key)
            {
                case 'id':
                    $this->addConditionOnIds('id',$value);
                    break;

                case 'profile':
                    $this->addConditionOnIds('profile',$value);
                    break;

                case 'offset':
                    $this->setOffset($value);
                    break;

                case 'limit':
                    $this->setLimit($value);
                    break;

                default:
                    return $this->addConditionOnText($key, $value);
            }
        }
        return $this;
    }

    protected function addConditionOnIds($field, $ids)
    {
        $this->qb->andWhere("$this->tableShort.{$field} IN ({$ids})");
        return $this;
    }

    protected function addConditionOnText($field, $text)
    {
        $this->qb->andWhere("$this->tableShort.{$field} LIKE '%{$text}%'");
        return $this;
    }

    /**
     * @param $limit
     * @return $this
     * @throws \Exception
     */
    protected function setLimit($limit)
    {
        if($limit > self::MAX_LIMIT)
        {
            throw new \Exception('Maximum number of profiles in a request exceeded');
        }
        $this->qb->setMaxResults($limit);
        return $this;
    }

    /**
     * @param $offset
     * @return $this
     */
    protected function setOffset($offset)
    {
        $this->qb->setFirstResult($offset);
        return $this;
    }

    /**
     * returns the query object.
     * @return Doctrine\Orm\Query
     */
    public function getQuery()
    {
        return $this->qb->getQuery();
    }
    /**
     * returns the results of the query.
     * @return array
     **/
    public function getResults()
    {
        return $this->getQuery()->getResult();
    }
}