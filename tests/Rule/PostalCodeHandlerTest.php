<?php

declare(strict_types=1);

namespace Tests\Rule;

use BeastBytes\PostalCode\PHP\PostalCodeData;
use BeastBytes\PostalCode\Validator\Rule\PostalCode;
use BeastBytes\PostalCode\Validator\Rule\PostalCodeHandler;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\RuleHandlerInterface;

final class PostalCodeHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        return $this->validationProvider('badPostalCodes');
    }

    public function passedValidationProvider(): array
    {
        return $this->validationProvider('goodPostalCodes');
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new PostalCode(
                   new PostalCodeData(),
                    countries: 'US',
                    message: 'Custom Invalid Postal Code message'
                ),
                'SW1A 1AA',
                [new Error('Custom Invalid Postal Code message')],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new PostalCodeHandler($this->getTranslator());
    }

    private function validationProvider($filename): array
    {
        $validationProvider = [];
        $postalCodes = require dirname(__DIR__) . '/' . $filename . '.php';
        foreach ($postalCodes as $name => $postalCode) {
            $rule = new PostalCode(new PostalCodeData(), countries: $postalCode['countries'], skipOnError: true);
            $validationProvider[$name] = [
                $rule,
                $postalCode['postalCode']
            ];
            if (str_starts_with($filename, 'bad')) {
                $validationProvider[$name][] = [new Error($rule->getMessage())];
            }
        }

        return $validationProvider;
    }
}
