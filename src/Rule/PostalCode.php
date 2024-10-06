<?php
/**
 * @copyright Copyright (c) 2024 BeastBytes - All Rights Reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PostalCode\Validator\Rule;

use BeastBytes\PostalCode\PostalCodeDataInterface;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

final class PostalCode implements RuleInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public const INVALID_COUNTRY_EXCEPTION_MESSAGE = '"{country}" is not a valid country';
    public const INVALID_POSTAL_CODE_MESSAGE = '"{value}" is not a valid postal code';

    /**
     * @property bool|string|string[] $counties Defines the country formats to validate against
     *
     * * __*null*__: validate against all countries defined PostalCodeData
     * * array: list of countries to validate against
     * * string: the country to validate against
     */

    public function __construct(
        private readonly PostalCodeDataInterface $postalCodeData,
        private bool|array|string $countries = true,
        private readonly string $message = self::INVALID_POSTAL_CODE_MESSAGE,
        callable|bool|null $skipOnEmpty = null,
        private readonly bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null $when
         */
        private readonly ?Closure $when = null,
    ) {
        if ($this->countries === true) {
            $this->countries = $this->postalCodeData->getCountries();
        } elseif (is_string($this->countries)) {
            $this->countries = [$this->countries];
        }

        /** @psalm-var list<string> $this->countries */
        foreach ($this->countries as $country) {
            if (!$this->postalCodeData->hasCountry($country)) {
                throw new \InvalidArgumentException(strtr(
                    self::INVALID_COUNTRY_EXCEPTION_MESSAGE,
                    ['{country}' => $country]
                ));
            }
        }

        $this->skipOnEmpty = $skipOnEmpty;
    }

    /** @psalm-return list<string> */
    public function getCountries(): array
    {
        return $this->countries;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getPostalCodeData(): PostalCodeDataInterface
    {
        return $this->postalCodeData;
    }

    #[ArrayShape([
        'countries' => 'array',
        'message' => 'array',
        'postalCodeData' => PostalCodeDataInterface::class,
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'countries' => $this->countries,
            'message' => [
                'message' => $this->message,
            ],
            'postalCodeData' => $this->postalCodeData,
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return PostalCodeHandler::class;
    }
}
