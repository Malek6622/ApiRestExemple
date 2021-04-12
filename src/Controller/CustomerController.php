<?php

namespace App\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CustomerController
 * @package App\Controller
 */
class CustomerController
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @Route("/addCustomer", name="add_customer", methods={"POST"}),
     * @OA\Post(
     *     path="/addCustomer",
     *     tags={"addCustomer"},
     *     operationId="addCustomer",
     *     summary="Adds a new customer",
     *     description="Adds a new customer",
     *      @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="application/x-www-form-urlencoded",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="firstName",
     *                  type="string",
     *                  description="customers firstName"),
     *              @OA\Property(
     *                  property="lastName",
     *                  type="string",
     *                  description="customers firstName"),
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  description="customers mail"),
     *              @OA\Property(
     *                  property="phoneNumber",
     *                  type="string",
     *                  description="customers phoneNumber")
     *       )
     *     )
     *   )
     * ),
     * @OA\Response(response="201",description="Customer created!"),
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $email = $data['email'];
        $phoneNumber = $data['phoneNumber'];
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->customerRepository->saveCustomer($firstName, $lastName, $email, $phoneNumber);
        return new JsonResponse(['status' => 'Customer created!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/getCustomers", name="get_customers", methods={"GET"}),
     * @OA\Get(
     *     path="/getCustomers",
     *     tags={"getCustomers"},
     *     operationId="getCustomers",
     *     summary="Get the list of customers",
     *     description="Get the list of customers"
     * ),
     * @OA\Response(
     *     response="200",
     *     @OA\JsonContent(ref="#/components/schemas/Customer")
     * ),
     * @OA\Response(
     *     response="200",
     *     description="No customers found"
     * )
     */
    public function get(): JsonResponse
    {
        $costumers = $this->customerRepository->findAll();
        $dataCostumers = [];
        foreach ($costumers as $costumer) {
            $dataCostumer['id'] = $costumer->getId();
            $dataCostumer['firstName'] = $costumer->getFirstName();
            $dataCostumer['lastName'] = $costumer->getLastName();
            $dataCostumer['email'] = $costumer->getEmail();
            $dataCostumer['phoneNumber'] = $costumer->getPhoneNumber();
            $dataProducts = [];
            foreach ($costumer->getProducts() as $product) {
                $dataProduct['name'] = $product->getName();
                $dataProduct['price'] = $product->getPrice();
                array_push($dataProducts, $dataProduct);
            }
            $dataCostumer['products'] = $dataProducts;
            array_push($dataCostumers, $dataCostumer);
        }
        if (empty($costumers)) {
            return new JsonResponse(['status' => 'No customers found'], Response::HTTP_OK);
        }
        return new JsonResponse(['cosutumers' => $dataCostumers],Response::HTTP_OK);
    }

    /**
     * @Route("/getCustomer/{id}", name="get_customer", methods={"GET"}),
     * @OA\Get(
     *     path="/getCustomer/{id}",
     *     tags={"getOneCustomerById"},
     *     operationId="getOneCustomerById",
     *     summary="get One Customer By Id",
     *     description="get One Customer By Id",
     *     @OA\Parameter(
     *         description="Id of the customer",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *           format="int64"
     *         )
     *      )
     * ),
     * @OA\Response(
     *     response="200",
     *     @OA\JsonContent(ref="#/components/schemas/Customer")
     * ),
     * @OA\Response(
     *     response="200",
     *     description="customer not found"
     * ),
     */
    public function getBYId($id): JsonResponse
    {
        $costumer = $this->customerRepository->findOneById($id);
        $dataCostumer['id'] = $costumer->getId();
        $dataCostumer['firstName'] = $costumer->getFirstName();
        $dataCostumer['lastName'] = $costumer->getLastName();
        $dataCostumer['email'] = $costumer->getEmail();
        $dataCostumer['phoneNumber'] = $costumer->getPhoneNumber();
        if (!$costumer) {
            return new JsonResponse(['status' => 'customer not found'], Response::HTTP_OK);
        }
        return new JsonResponse(['cosutumer' => $dataCostumer],Response::HTTP_OK);
    }

    /**
     * @Route("/updateCustomer/{id}", name="update_customer", methods={"PUT"})
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $email = $data['email'];
        $phoneNumber = $data['phoneNumber'];
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->customerRepository->updateCustomer($id, $firstName, $lastName, $email, $phoneNumber);
        return new JsonResponse(['status' => 'Customer updated!'], Response::HTTP_CREATED);
    }
}
