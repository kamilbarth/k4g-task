<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderRequest
{
    #[Assert\NotBlank(message: "Products list cannot be blank.")]
    #[Assert\Type(type: "array", message: "Products should be an array.")]
    #[Assert\All([
        new Assert\Type(type: "integer", message: "Quantity must be an integer."),
        new Assert\GreaterThan(value: 0, message: "Quantity must be greater than 0.")
    ])]
    private array $products = [];

    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): self
    {
        $this->products = $products;
        return $this;
    }
}
