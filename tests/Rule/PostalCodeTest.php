<?php
/**
 * @copyright Copyright © 2022 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace Tests\Rule;

use BeastBytes\PostalCode\PHP\PostalCodeData;
use BeastBytes\PostalCode\Validator\Rule\PostalCode;
use Yiisoft\Validator\SerializableRuleInterface;

class PostalCodeTest extends AbstractRuleTest
{
    /**
     * @dataProvider badCountriesProvider
     */
    public function testBadCountries($countries, $message)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        new PostalCode(new PostalCodeData(), countries: $countries);
    }

    public function badCountriesProvider(): array
    {
        return [
            'Invalid country - array' => [['XX'], '"XX"  not a valid country'],
            'Invalid country - string' => ['XX', '"XX"  not a valid country'],
            'List containing invalid country' => [['GB', 'FR', 'XX'], '"XX"  not a valid country'],
            'Wrong length' => ['GBR', '"GBR"  not a valid country'],
        ];
    }
    public function optionsDataProvider(): array
    {
        $postalCodeData = new PostalCodeData();
        $countries = ['GB', 'FR', 'DE', 'US'];

        return [
            [
                new PostalCode($postalCodeData, $countries),
                [
                    'countries' => $countries,
                    'postalCodeData' => $postalCodeData,
                    'message' => [
                        'message' => 'Postal code not valid',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new PostalCode(new PostalCodeData());
    }
}
