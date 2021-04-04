<?php

namespace App\Controller;

use DateTime;
use App\Entity\Option;
use App\Entity\Product;
use App\Form\OptionType;
use App\Form\ProductType;
use App\Controller\MainController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConfiguratorController extends AbstractController
{
    /**
     * @Route("/configurator", name="configurator", methods={"GET","POST"})
     */
    public function index(Request $request,EntityManagerInterface $em, MainController $mn): Response
    {
        // Product 
        $product = new Product();
        $product->setDateCreated(new \DateTime());
        $product->setIsVisible(true);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('configurator');
        }

        // Option
        $option = new Option();
        $formOpt = $this->createForm(OptionType::class,$option);
        $formOpt->handleRequest($request);

        if ($formOpt->isSubmitted() && $formOpt->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $product = $option->getProduct();
            $option->setProduct(null);
            $product->addOption($option); // to call setters
            $entityManager->persist($option);   
            $entityManager->flush();

            return $this->redirectToRoute('configurator');
        }
        return $this->render('configurator/index.html.twig', [
            'controller_name' => 'ConfiguratorController',
            'cart' => $mn->getCart($em),
            'product' => $product,
            'form' => $form->createView(),
            'formOpt' => $formOpt->createView(),
        ]);
    }
}
