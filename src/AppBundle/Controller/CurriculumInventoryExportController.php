<?php

namespace AppBundle\Controller;

use AppBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use AppBundle\Entity\CurriculumInventoryExportInterface;
use AppBundle\Entity\Manager\UserManager;
use AppBundle\Entity\UserInterface;
use AppBundle\Service\CurriculumInventory\Exporter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CurriculumInventoryExport
 * CurriculumInventoryExports can only be POSTed nothing else
 */
class CurriculumInventoryExportController extends ApiController
{
    /**
     * @var Exporter
     */
    protected $exporter;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * Register injections here so we don't have to override the ApiController constructor
     *
     * @required
     * @param Exporter $exporter
     * @param UserManager $userManager
     */
    public function setup(Exporter $exporter, UserManager $userManager)
    {
        $this->exporter = $exporter;
        $this->userManager = $userManager;
    }

    /**
     * Return a 404 response
     */
    public function fourOhFourAction()
    {
        throw new NotFoundHttpException('Curriculum Inventory Exports can only be created');
    }

    /**
     * Create the XML document for a curriculum inventory report
     *
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $json = $this->extractJsonFromRequest($request, $object, 'POST');
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        /** @var UserInterface $user */
        $user = $this->userManager->findOneBy(['id' => $sessionUser->getId()]);
        /** @var CurriculumInventoryExportInterface $export */
        foreach ($entities as $export) {
            $export->setCreatedBy($user);
            $this->authorizeEntity($export, AbstractVoter::CREATE);

            // generate and set the report document
            $document = $this->exporter->getXmlReport($export->getReport());
            $export->setDocument($document->saveXML());

            $this->validateEntity($export);
            $manager->update($export, false);
        }


        $manager->flush();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }
}
