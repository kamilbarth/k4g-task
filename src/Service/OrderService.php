<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\OrderRequest;
use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\SaleOrder;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

readonly class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FinancialCalculatorService $financialCalculator
    ) {
    }

    public function getOrderDetails(int $orderId): ?array
    {
        $order = $this->entityManager->getRepository(SaleOrder::class)->find($orderId);

        if (!$order) {
            return null;
        }

        $orderItemsData = [];
        $totalNet = 0;
        $totalVat = 0;
        $totalGross = 0;

        foreach ($order->getOrderProducts() as $item) {
            $product = $item->getProduct();
            $price = $product->getPrice();
            $vatRate = $product->getVatRate();

            $prices = $this->financialCalculator->calculatePrices($price, $vatRate);

            $orderItemsData[] = [
                'productId' => $product->getId(),
                'quantity' => $item->getQuantity(),
                'netPrice' => $this->convertToFloat($prices['netPrice']),
                'vatAmount' => $this->convertToFloat($prices['vatAmount']),
                'grossPrice' => $this->convertToFloat($prices['grossPrice'])
            ];

            $totalNet += $prices['netPrice'] * $item->getQuantity();
            $totalVat += $prices['vatAmount'] * $item->getQuantity();
            $totalGross += $prices['grossPrice'] * $item->getQuantity();
        }

        return [
            'orderId' => $order->getId(),
            'items' => $orderItemsData,
            'totalNet' => $this->convertToFloat($totalNet),
            'totalVat' => $this->convertToFloat($totalVat),
            'totalGross' => $this->convertToFloat($totalGross)
        ];
    }

    private function convertToFloat(int $value): float
    {
        return round($value / 100.0, 2);
    }

    public function createOrder(OrderRequest $orderRequest): SaleOrder
    {
        $order = new SaleOrder();
        $order->setCreatedAt(new DateTimeImmutable);
        $order->setStatus('new');

        foreach ($orderRequest->getProducts() as $productId => $quantity) {
            $product = $this->entityManager->getRepository(Product::class)->find($productId);

            if (!$product) {
                throw new Exception("Product with ID $productId not found");
            }

            $orderProduct = new OrderProduct();
            $orderProduct->setProduct($product);
            $orderProduct->setQuantity($quantity);

            $order->addOrderProduct($orderProduct);
        }

        $this->entityManager->persist($order);

        foreach ($order->getOrderProducts() as $item) {
            $this->entityManager->persist($item);
        }

        $this->entityManager->flush();

        return $order;
    }
}