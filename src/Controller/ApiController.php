<?php

namespace App\Controller;

use App\Entity\Option;
use App\Entity\Product;
use App\Form\OptionType;
use App\Form\ProductType;
use App\Controller\MainController;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
     * @Route("/api")
     */
class ApiController extends AbstractController
{
    /**
     * @Route("/", name="api")
     */
    public function index(EntityManagerInterface $em, MainController $mn): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
            'cart' => $mn->getCart($em)
        ]);
    }

    /**
     * @Route("/product/getlast", name="product_last",methods={"GET"})
     */
    public function productLast(EntityManagerInterface $em): Response
    {
        $product = $em->getRepository(Product::class)->findOneBy(array(),array('id'=>'DESC'),1,0);

        return $this->json($product->toArray(), Response::HTTP_OK);
    }

    /**
     * @Route("/cart/add/{id}", name="cartadd",methods={"PUT","GET"})
     */
    public function cartadd(MainController $mn, Request $request,EntityManagerInterface $em, Product $product,ValidatorInterface $validator): Response
    {
        $cart = $mn->getCart($em);
        $hasOpt = false;
        $optionsStrings = $request->query->get('options');
        if (!$optionsStrings) $optionsStrings = $request->query->get('amp;options');

        $optionsId = explode(',',$optionsStrings);
        if ($optionsId[0] != "") $hasOpt = true;

        // if ($cart->getProducts()->contains($product)) {
            $product2 = new Product();
            $product2->setName($product->getName());
            $product2->setDescription($product->getDescription());
            $product2->setPrice($product->getPrice());
            $product2->setImgUrl($product->getImgUrl());
            $product2->setIsVisible(false);
            $product2->setIsClone(true);
            $product2->setPriceTotal($product2->getPrice());
            if ($hasOpt) {
                foreach ($optionsId as $id) {
                    $optionB = $em->getRepository(Option::class)->findOneBy(['id' => $id]);
                    $option = new Option();
                    $option->setName($optionB->getName());
                    $option->setType($optionB->getType());
                    $option->setPriceSupp($optionB->getPriceSupp());
                    $option->setPricePerc($optionB->getPricePerc());
                    $product2->addOption($option);
                    $product2->setPriceTotal($product2->getPrice() + $optionB->getPriceSupp());
                    $em->persist($option);
                }
            }
            $cart->addProduct($product2);
            $em->persist($product2);
            $em->flush();
            if ($request->query->get('redirect')) return $this->redirectToRoute('main_index');
            return $this->json("added +", Response::HTTP_CREATED);
        // } else {
        //     $cart->addProduct($product);
        //     $em->flush();
        //     if ($request->query->get('redirect')) return $this->redirectToRoute('main_index');
        //     return $this->json("added", Response::HTTP_CREATED);
        // }
    }

    /**
     * @Route("/cart/rm/{id}", name="cartrm",methods={"DELETE","GET"})
     */
    public function cartrm(MainController $mn, Request $request,EntityManagerInterface $em, Product $product,ValidatorInterface $validator): Response
    {
        $cart = $mn->getCart($em);
        $cart->removeProduct($product);
        $options = $product->getOptions();
        if ($options) {
            foreach ($options as $opt) {
                $product->removeOption($opt);
                $em->remove($opt);
            }   
        }
        if ($product->getIsClone()) {
            $em->remove($product);
            $em->flush();
            if ($request->query->get('redirect')) return $this->redirectToRoute('main_index');
            if ($request->query->get('redirect-cart')) return $this->redirectToRoute('cart_validation');
            return $this->json("Product removed", Response::HTTP_OK);
        } else $em->flush();
        if ($request->query->get('redirect')) return $this->redirectToRoute('main_index');
        return $this->json("nothing removed", Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/cart/empty", name="cartempty",methods={"DELETE","GET"})
     */
    public function cartEmpty(MainController $mn, Request $request,EntityManagerInterface $em): Response
    {
        $cart = $mn->getCart($em);
        $products = $cart->getProducts();
        foreach ($cart->getProducts() as $productCurr) {
            $cart->removeProduct($productCurr);
            foreach ($productCurr->getOptions() as $opt) {
                $productCurr->removeOption($opt);
                $em->remove($opt);
            }
            $em->remove($productCurr);
        } 
        $em->flush();
        if ($request->query->get('redirect')) return $this->redirectToRoute('main_index');
        return $this->json("removed", Response::HTTP_OK);
    }

    /**
     * @Route("/product/{id}/rm-opt/{idopt}", name="productremoveopt",methods={"DELETE"})
     */
    public function optionDeleteItem(Product $product, int $idopt, Request $request,EntityManagerInterface $em, SerializerInterface $serial,ValidatorInterface $validator): Response
    {
        $option = $em->getRepository(Option::class)->findOneBy(['id' => $idopt]);

        if ($option) {
            $product->removeOption($option);

            $em->remove($option);
            $em->flush();
            return $this->json('deleted', Response::HTTP_OK);
        }
        return $this->json('nothing to delete', Response::HTTP_ACCEPTED);
    }
    
    /**
     * @Route("/cart/extract", name="cartextract")
     */
    public function cartExtract(MainController $mn,EntityManagerInterface $em): Response
    {
        return $this->json($mn->getCart($em)->toArray());
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
     * @Route("/product/new", name="productnewitem",methods={"PUT"})
     */
    public function productNewItem(Request $request,EntityManagerInterface $em,ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(),true);
        $form = $this->createForm(ProductType::class, new Product, ['csrf_protection' => false]);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json( $form->getErrors(), Response::HTTP_BAD_REQUEST); // Code 400
        }
        $product = $form->getData();
        $em->persist($product);
        $em->flush();
        return $this->json($product->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @Route("/product/rm/{id}", name="product_delete", methods={"DELETE","GET"})
     */
    public function deleteProduct(Request $request, Product $product): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
        if (!empty($product)) {
            $entityManager = $this->getDoctrine()->getManager();
            $options = $product->getOptions();
            foreach ($options as $opt) {
                $product->removeOption($opt);
                $entityManager->remove($opt);
            }
            $entityManager->remove($product);
            $entityManager->flush();    
            if ($request->query->get('redirect')) return $this->redirectToRoute('main_index');
            return $this->json('deleted', Response::HTTP_OK);
        }
        // }
        if ($request->query->get('redirect')) return $this->redirectToRoute('main_index');
        return $this->json('nothing to delete', Response::HTTP_ACCEPTED);
    }

    /**
     * @Route("/options", name="options")
     */
    public function options(EntityManagerInterface $em): Response
    {
        return $this->json(array_map(function ($option)
        {
            return $option->toArray();
        }, $em->getRepository(Option::class)->findAll()));
    }

    /**
     * @Route("/option/{id}", name="option_item")
     */
    public function optionItem(EntityManagerInterface $em,$id): Response
    {
        return $this->json(array_map(function ($option)
        {
            return $option->toArray();
        }, $em->getRepository(Option::class)->findById($id)));
    }

    /**
     * @Route("/option/new", name="productnewopt",methods={"PUT"})
     */
    public function optionNewItem(Request $request,EntityManagerInterface $em, SerializerInterface $serial,ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(),true);
        $form = $this->createForm(OptionType::class, new Option, ['csrf_protection' => false]);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json( 'Error', Response::HTTP_BAD_REQUEST); // Code 400
        }
        $option = $form->getData();
        $em->persist($option);
        $em->flush();
        return $this->json($option->toArray(), Response::HTTP_CREATED);
    }

    // /**
    //  * @Route("/product/edit/{id}", name="product_edit_item")
    //  */
    // public function productEditItem(EntityManagerInterface $em,$id): Response
    // {
    //     return $this->json(array_map(function ($product)
    //     {
    //         return $product->toArray();
    //     }, $em->getRepository(Product::class)->findById($id)));
    // }
}
