<?php

namespace Test\ValueObject;

use PHPUnit\Framework\TestCase;
use ValueObject\ValueObject;

class ValueObjectAbstractMethodsTest extends TestCase
{
    /**
     * @test
     */
    public function afterValidation()
    {
        $params = ['message' => 'hi'];
        $rules = ['message' => ['NotBlank']];
        $expectedResult = 'performed';

        $mock = $this->getMockBuilder(ValueObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRules', 'afterValidation'])
            ->getMockForAbstractClass()
        ;
        $mock->method('getRules')->will($this->returnValue($rules));
        $mock->method('afterValidation')->will($this->returnValue($expectedResult));
        $mock->__construct($params);
        $actualResult = $mock->afterValidation($params);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
