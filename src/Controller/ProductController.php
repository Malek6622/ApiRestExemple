<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    public function __construct(ProductRepository $productRepository, CustomerRepository $customerRepository)
    {
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Route("/addProduct/{id}", name="add_product", methods={"POST"})
     */
    public function add(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $price = $data['price'];
        $customer = $this->customerRepository->findOneById($id);
        if (empty($name) || empty($price)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->productRepository->saveProduct($name, $price, $customer);
        return new JsonResponse(['status' => 'product created!'], Response::HTTP_CREATED);
    }
}
