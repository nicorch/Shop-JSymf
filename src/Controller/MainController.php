<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_index")
     */
    public function index(EntityManagerInterface $em)
    {

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'products' => $em->getRepository(Product::class)->findAll()
        ]);
    }

}
