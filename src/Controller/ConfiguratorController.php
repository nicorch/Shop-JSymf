<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfiguratorController extends AbstractController
{
    /**
     * @Route("/configurator", name="configurator")
     */
    public function index(): Response
    {
        return $this->render('configurator/index.html.twig', [
            'controller_name' => 'ConfiguratorController',
        ]);
    }
}
