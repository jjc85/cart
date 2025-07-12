<?php

declare(strict_types=1);

namespace App\Application\Command;

/**
 * @codeCoverageIgnore
 */
final class CartRemoveProductSessionCommand implements CommandInterface
{
    private int $productId;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}
