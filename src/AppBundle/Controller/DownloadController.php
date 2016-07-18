<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Persistence\Model\Project;
use Persistence\Model\Provider;
use Persistence\Model\File;

/**
 * Controlador para descargar ficheros subidos
 */
class DownloadController extends Controller
{
    /**
     * Descarga un fichero de un proyecto
     *
     * @Route("/download/project/{id}/{index}", name="download_project")
     * @Security("project.hasUser(user)")
     *
     * @param Project $project
     * @param int     $index
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function projectAction(Project $project, $index)
    {
        if ($file = $project->getFile($index)) {
            return $this->handleFile($file);
        }

        throw $this->createNotFoundException();
    }

    /**
     * Descarga un fichero de un proyecto
     *
     * @Route("/download/budget/{id}/{provider}/{index}", name="download_budget")
     * @Security("project.hasUser(user) and provider.hasUser(user)")
     *
     * @param Project  $project
     * @param Provider $provider
     * @param int      $index
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function budgetAction(Project $project, Provider $provider, $index)
    {
        if ($budget = $project->getBudget($provider)) {
            if ($file = $budget->getFile($index)) {
                return $this->handleFile($file);
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * Descarga un fichero de una habilidad de un proveedor
     *
     * @Route("/download/skill/{id}/{ability}/{index}", name="download_skill")
     * @Security("provider.hasUser(user)")
     *
     * @param Provider $provider
     * @param string   $ability
     * @param int      $index
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function skillAction(Provider $provider, $ability, $index)
    {
        if ($skill = $provider->getSkill($ability)) {
            if ($file = $skill->getFile($index)) {
                return $this->handleFile($file);
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * Descarga un fichero facturación de un proveedor
     *
     * @Route("/download/billing/{id}/{index}", name="download_billing")
     * @Security("provider.hasUser(user)")
     *
     * @param Provider $provider
     * @param int      $index
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function billingAction(Provider $provider, $index)
    {
        if ($billing = $provider->getBilling()) {
            if ($file = $billing->getFile($index)) {
                return $this->handleFile($file);
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * Descarga un fichero facturación de un proveedor
     *
     * @Route(
     *     "/download/billing/{id}/file/{type}",
     *     name="download_billing_type"
     * )
     * @Security("provider.hasUser(user)")
     *
     * @param Provider $provider
     * @param string   $type
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function billingFileAction(Provider $provider, $type)
    {
        $file = null;

        if ($billing = $provider->getBilling()) {
            if ($type === 'taxIdent') {
                $file = $billing->getTaxIdentFile();
            } else if ($type === 'taxIdentAdditional') {
                $file = $billing->getTaxIdentAdditionalFile();
            } else if ($type === 'taxCertificate') {
                $file = $billing->getTaxCertificateFile();
            } else if ($type === 'socialSecurityCertificateFile') {
                $file = $billing->getSocialSecurityCertificateFile();
            }

            if ($file) {
                return $this->handleFile($file);
            }
        }
        throw $this->createNotFoundException();
    }

    /**
     * @param File $file
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    protected function handleFile(File $file)
    {
        if (! file_exists($file->getPath())) {
            throw $this->createNotFoundException();
        }

        $response = new BinaryFileResponse($file->getPath());
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file->getName()
        );

        return $response;
    }
}
