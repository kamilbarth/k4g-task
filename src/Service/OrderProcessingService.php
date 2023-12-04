<?php

namespace App\Service;

use App\DTO\OrderRequest;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class OrderProcessingService
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private OrderService $orderService
    ) {}

    public function processOrder(string $content): array
    {
        try {
            $orderRequest = $this->serializer->deserialize($content, OrderRequest::class, 'json');
        } catch (NotNormalizableValueException $e) {
            return ['errors' => 'Invalid request format: ' . $e->getMessage()];
        }

        $errors = $this->validator->validate($orderRequest);
        if (count($errors) > 0) {
            return ['errors' => $errors];
        }

        $order = $this->orderService->createOrder($orderRequest);
        return ['order' => $order];
    }
}