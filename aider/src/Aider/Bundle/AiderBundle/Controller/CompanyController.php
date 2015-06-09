<?php

namespace Aider\Bundle\AiderBundle\Controller;

use Aider\Bundle\AiderBundle\Entity\Company;
use Aider\Bundle\AiderBundle\Form\CompanyType;

use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use FOS\RestBundle\Request\ParamFetcher,
    FOS\RestBundle\Controller\Annotations;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class CompanyController
 * @package Aider\Bundle\AiderBundle\Controller
 */
class CompanyController extends BaseController
{
    private $entityName  = 'AiderBundle:Company';
    private $tableName   = 'Company';

    /**
     * List all Companies
     * @ApiDoc(
     *   resource=true,
     *  output = "Aider\AiderBundle\Entity\Company",
     *   description="List all Companies registered",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing profiles.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many profiles to return.")
     *
     * @Annotations\QueryParam(name="id", requirements="(\d+,*)+", nullable=true, description="Company ids to search for")
     * @Annotations\QueryParam(name="name", nullable=true, description="Company names to search for")
     *
     * @param Request $request
     * @param ParamFetcher $paramFetcher
     *
     * @return mixed
     */
    public function getCompaniesAction(Request $request, ParamFetcher $paramFetcher)
    {
        return $this->searchWithParam(
            $this->tableName,
            $paramFetcher->all()
        );
    }

    /**
     * Finds and displays a Company by id.
     * @ApiDoc(
     *   resource=true,
     *   output = "Aider\AiderBundle\Entity\Company",
     *   description="Finds and displays a Company by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the note is not found"
     *   }
     * )
     *
     * @param integer $id
     * @param Request $request
     *
     * @return Response
     */
    public function getCompanyAction(Request $request, $id)
    {
        return $this->listDataId( $this->entityName, $this->tableName, $id );
    }

    /**
     * Create a Company.
     * @ApiDoc(
     *   resource=true,
     *   output = "Aider\AiderBundle\Entity\Company",
     *   description="Create a company",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when fail any validation"
     *   }
     * )
     *
     * @param Request $request
     * @return mix
     */
    public function postCompanyAction(Request $request)
    {
        $company = new Company();

        $form = $this->createForm(new CompanyType(), $company);

        $form->submit($request);

        if( $form->isValid() ){

            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();

            return $company;

        }
        return $form;
    }

    /**
     * Update a Company.
     * @ApiDoc(
     *   resource=true,
     *   output = "Aider\AiderBundle\Entity\Company",
     *   description="Update a company",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *   }
     * )
     *
     * @param Request $request
     * @param $id
     *
     * @return mix
     */
    public function patchCompanyAction(Request $request, $id)
    {
        $company = $this->getDoctrine()
            ->getRepository($this->entityName)
            ->find($id);

        if($company === NULL)
        {
            throw $this->createNotFoundException("Company {$id} does not exist");
        }

        $form = $this->createForm(new CompanyType(), $company);
        $form->submit($request, false);

        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();
            return $company;
        }
        return $form;
    }

    /**
     * Removes a company.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param int $id
     *
     * @return RouteRedirectView
     */
    public function deleteCompanyAction(Request $request, $id)
    {
        $company = $this->getDoctrine()
            ->getRepository($this->entityName)
            ->find($id);

        if($company){

            $em = $this->getDoctrine()->getManager();
            $em->remove($company);
            $em->flush();

            return $this->routeRedirectView('company_list', array(), Codes::HTTP_NO_CONTENT);

        }else
            throw $this->createNotFoundException("Company {$id} does not exist");


    }
}

