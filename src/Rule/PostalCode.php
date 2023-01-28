<?php
/**
 * @copyright Copyright (c) 2021 BeastBytes - All Rights Reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PostalCode\Validator\Rule;

use BeastBytes\PostalCode\PostalCodeDataInterface;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

final class PostalCode implements SerializableRuleInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @property bool|string|string[] $counties Defines the country formats to validate against
     *
     * * __*true*__: validate against all countries defined PostalCodeData
     * * array: list of countries to validate against
     * * string: the country to validate against
     */

    public function __construct(
        private PostalCodeDataInterface $postalCodeData,
        private null|array|string $countries = null,
        private string $message = 'Postal code not valid',
        /**
         * @var bool|callable|null $skipOnEmpty
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null $when
         */
        private ?Closure $when = null,
    ) {
        if (is_string($this->countries)) {
            $this->countries = (array)$this->countries;
        }

        if (is_array($this->countries)) {
            foreach ($this->countries as $country) {
                if (!$this->postalCodeData->hasCountry($country)) {
                    throw new \InvalidArgumentException(strtr(
                        '"{country}"  not a valid country',
                        ['{country}' => $country]
                    ));
                }
            }
        }
    }

    public function getCountries(): array
    {
        if (is_null($this->countries)) {
            return $this->postalCodeData->getCountries();
        }

        return (array)$this->countries;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getName(): string
    {
        return 'postalCode';
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

    public function getHandlerClassName(): string
    {
        return PostalCodeHandler::class;
    }
}
