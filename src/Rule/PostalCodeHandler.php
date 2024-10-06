<?php
/**
 * @copyright Copyright (c) 2024 BeastBytes - All Rights Reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PostalCode\Validator\Rule;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class PostalCodeHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('Postal Code must be a string');
        }

        if (!$rule instanceof PostalCode) {
            throw new UnexpectedRuleException(PostalCode::class, $rule);
        }

        $result = new Result();

        $postalCodeData = $rule->getPostalCodeData();

        $valid = false;
        /** @var string $country */
        foreach ($rule->getCountries() as $country) {
            if (preg_match($postalCodeData->getPattern($country), $value)) {
                $valid = true;
            }
        }

        if (!$valid) {
            $result->addError(
                $rule->getMessage(),
                [
                    'attribute' => $context->getTranslatedProperty(),
                    'value' => $value,
                ],
                ['postalCode']
            );
        }

        return $result;
    }
}
