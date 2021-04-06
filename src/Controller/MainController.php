<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Option;
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
        if (!$products) $this->inti_data($em);

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

    public function inti_data($em)
    {
        $chaussures = new Product();
        $chaussures->setName('Chaussures');
        $chaussures->setPrice(99.99);
        $chaussures->setPriceTotal(99.99);
        $chaussures->setDescription('Des chaussures simples');
        $chaussures->setIsVisible(true);
        $chaussures->setImgUrl('https://images.asos-media.com/products/nike-air-max-90-recraft-baskets-noir-gris/14752436-1-black?$XXL$&wid=513&fit=constrain');
        $chaussures->setDateCreated(new \DateTime());

        $option_1 = new Option();
        $option_1->setName('Signature spéciale');
        $option_1->setPriceSupp(155.99);
        
        $coque = new Product();
        $coque->setName('Coque');
        $coque->setPrice(15.99);
        $coque->setDescription('Coque de couleur ou image personnalisable');
        $coque->setIsVisible(true);
        $coque->setImgUrl('https://tinyurl.com/45b2e2ps');
        $option_3 = new Option();
        $option_3->setName('Couleur perso');
        $option_3->setPriceSupp(3.99);
        $option_4 = new Option();
        $option_4->setName('Image perso');
        $option_4->setPriceSupp(5.99);
        
        $tel = new Product();
        $tel->setName('Téléphone');
        $tel->setPrice(499.99);
        $tel->setDescription('Une téléphone serviable');
        $tel->setIsVisible(true);
        $tel->setImgUrl('https://www.mac4ever.com/images-articles/62927_.jpg');
        $option_5 = new Option();
        $option_5->setName('Extension de stockage');
        $option_5->setPriceSupp(100);
        
        
        $em->persist($tel);
        $em->persist($coque);
        $em->persist($chaussures);
        
        $option_3->setProduct($coque);
        $option_4->setProduct($coque);
        $option_5->setProduct($tel);
        $option_1->setProduct($chaussures);
        $em->persist($option_1);
        $em->persist($option_3);
        $em->persist($option_4);
        $em->persist($option_5);
        $em->flush();
    }
}
