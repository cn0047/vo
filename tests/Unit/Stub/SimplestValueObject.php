<?php

namespace Test\Unit\Stub;

use ValueObject\ValueObject;

/**
 * @method getMessage
 */
class SimplestValueObject extends ValueObject
{
    public function getRules()
    {
        return [
            'message' => ['NotBlank'],
        ];
    }
}
