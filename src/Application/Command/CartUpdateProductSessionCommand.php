<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Domain\ValueObject\Quantity;

/**
 * @codeCoverageIgnore
 */
final class CartUpdateProductSessionCommand implements CommandInterface
{
    private int $productId;
    private Quantity $quantity;

    public function __construct(int $productId, Quantity $quantity)
    {
        $this->quantity = $quantity;
        $this->productId = $productId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }
}
