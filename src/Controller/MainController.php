<?php

namespace App\Controller;

use App\Entity\Cart;
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
        $products = $em->getRepository(Product::class)->findAll();

        $nb = 0;
        foreach ($products as $produ) {
            if ($produ->getIsVisible()) $nb += 1;
        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'productsVisibles' => ($nb<=1) ? $nb.' produit disponible' : $nb.' produits disponibles',
            'products' => $products,
            'cart' => $this->getCart($em)
        ]);
    }

    /**
     * @Route("/cart/validation", name="cart_validation")
     */
    public function cartValidation(EntityManagerInterface $em)
    {
        return $this->render('main/cart_validation.html.twig', [
            'cart' => $this->getCart($em)
        ]);
    }

    public function getCart($em)
    {
        return $cart = $em->getRepository(Cart::class)->findOneBy(['id' => 1]);
    }
}
