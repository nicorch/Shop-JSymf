<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
     * @Route("/api")
     */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="api")
     */
    public function index(): Response
    {
        // $product = new Product();

        // $product->setName('Chauzure');
        // $product->setPrice(10);
        // $product->setDateCreated(new \Datetime());
        // $product->setIsVisible(true);

        // $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($product);
        // $entityManager->flush();

        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/products", name="products")
     */
    public function products(EntityManagerInterface $em): Response
    {
        return $this->json(array_map(function ($product)
        {
            return $product->toArray();
        }, $em->getRepository(Product::class)->findAll()));
    }

    /**
     * @Route("/product/{id}", name="product_item")
     */
    public function productItem(EntityManagerInterface $em,$id): Response
    {
        return $this->json(array_map(function ($product)
        {
            return $product->toArray();
        }, $em->getRepository(Product::class)->findById($id)));
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit_item")
     */
    public function productEditItem(EntityManagerInterface $em,$id): Response
    {
        return $this->json(array_map(function ($product)
        {
            return $product->toArray();
        }, $em->getRepository(Product::class)->findById($id)));
    }
}
