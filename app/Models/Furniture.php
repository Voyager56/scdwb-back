<?php

namespace App\Models;

use App\Database\Database;
use PDOException;
use App\Helpers\Errors\DatabaseException;

class Furniture
{
    protected string $sku;
    protected string $name;
    protected float $price;

    protected string $height;
    protected string $width;
    protected string $length;

    public function __construct(string $sku, string $name, float $price, string $height, string $width, string $length)
    {
        $this->sku = $sku;
        $this->name = $name;
        $this->price = $price;
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHeight(): string
    {
        return $this->height;
    }

    public function getWidth(): string
    {
        return $this->width;
    }

    public function getLength(): string
    {
        return $this->length;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setHeight(string $height): void
    {
        $this->height = $height;
    }

    public function setWidth(string $width): void
    {
        $this->width = $width;
    }

    public function setLength(string $length): void
    {
        $this->length = $length;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }




}