<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\OrderProcessingService;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route("/order/{id}", name: "order_show", methods: ["GET"])]
    public function show(int $id, OrderService $orderService): JsonResponse
    {
        $orderDetails = $orderService->getOrderDetails($id);

        if ($orderDetails === null) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($orderDetails);
    }

    #[Route("/order", name: "order_store", methods: ["POST"])]
    public function store(Request $request, OrderProcessingService $orderProcessingService): JsonResponse
    {
        if (empty($request->getContent())) {
            return new JsonResponse(['message' => 'Request body cannot be empty.'], Response::HTTP_BAD_REQUEST);
        }

        $result = $orderProcessingService->processOrder($request->getContent());

        if (isset($result['errors'])) {
            return new JsonResponse(['errors' => (string) $result['errors']], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'Order created successfully.',
            'orderId' => $result['order']->getId()
        ], Response::HTTP_CREATED);
    }
}
