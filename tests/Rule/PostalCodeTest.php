<?php
/**
 * @copyright Copyright © 2022 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PostalCode\Validator\Tests\Rule;

use BeastBytes\PostalCode\PHP\PostalCodeData;
use BeastBytes\PostalCode\Validator\Rule\PostalCode;
use PHPUnit\Framework\Attributes\BeforeClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Validator;

class PostalCodeTest extends TestCase
{
    private static PostalCodeData $postalCodeData;

    #[BeforeClass]
    public static function init(): void
    {
        self::$postalCodeData = new PostalCodeData();
    }

    #[DataProvider('badCountriesProvider')]
    public function test_bad_countries(array|string $countries, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);
        new PostalCode(self::$postalCodeData, countries: $countries);
    }

    #[DataProvider('dataValidationPassed')]
    public function test_validation_passed(bool|array|string $country, string $postalCode): void
    {
        $rule = new PostalCode(self::$postalCodeData, $country);
        $result = (new Validator())->validate($postalCode, $rule);

        $this->assertTrue($result->isValid());
    }

    #[DataProvider('dataValidationFailed')]
    public function test_validation_failed(
        bool|array|string $countries,
        string $postalCode
    ): void
    {
        $rule = new PostalCode(self::$postalCodeData, $countries);
        $result = (new Validator())->validate($postalCode, $rule);

        $this->assertFalse($result->isValid());
        $this->assertEquals(
            strtr(PostalCode::INVALID_POSTAL_CODE_MESSAGE, [
                '{value}' => $postalCode
            ]),
            $result->getErrorMessagesIndexedByPath()['postalCode'][0]
        );
    }

    // End Tests

    // Data providers
    public static function dataValidationPassed(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/dataValidationPassed.php';

        foreach ($data as $country => $datum) {
            yield $country => $datum;
        }
    }

    public static function dataValidationFailed(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/dataValidationFailed.php';

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }


    public static function badCountriesProvider(): \Generator
    {
        $data = [
            'Invalid country - array' => [['XX'], '"XX" is not a valid country'],
            'Invalid country - string' => ['XX', '"XX" is not a valid country'],
            'List containing invalid country' => [['GB', 'FR', 'XX'], '"XX" is not a valid country'],
            'Wrong length' => ['GBR', '"GBR" is not a valid country'],
        ];

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }
    public static function optionsDataProvider(): \Generator
    {
        $postalCodeData = new PostalCodeData();
        $countries = ['GB', 'FR', 'DE', 'US'];

        $data = [
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

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }
}
