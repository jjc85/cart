<?php

declare(strict_types=1);

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @codeCoverageIgnore
 */
final class CartAddProductPayload
{
    #[Assert\NotBlank(message: 'The product id cannot be empty.')]
    #[Assert\Type('int', message: 'The product id must be an integer.')]
    #[Assert\Positive(message: 'The product id must be greater than 0.')]
    public int $productId;
}
