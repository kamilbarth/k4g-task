<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\SaleOrder;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $products = $manager->getRepository(Product::class)->findAll();

        for ($i = 1; $i <= 5; $i++) {
            $order = new SaleOrder();
            $order->setCreatedAt(new DateTimeImmutable);
            $order->setStatus('new');

            foreach (array_rand($products, 2) as $index) {
                $orderProduct = new OrderProduct();
                $orderProduct->setProduct($products[$index]);
                $orderProduct->setQuantity(mt_rand(1, 3));

                $order->addOrderProduct($orderProduct);
            }

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProductFixtures::class,
        ];
    }
}