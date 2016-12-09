<?php

namespace Test\ValueObject;

use PHPUnit\Framework\TestCase;
use Test\Unit\Stub\SimplestValueObject;
use ValueObject\Exception\ParameterNotFoundException;

class SimplestValueObjectTest extends TestCase
{
    /**
     * @test
     */
    public function allValidAndMagicMethodsWorksCorrect()
    {
        $params = ['message' => 'test'];
        $expectedResult = $params['message'];

        $vo = new SimplestValueObject($params);
        $actualResult = $vo->getMessage();

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @test
     */
    public function allValidAndToArrayWorks()
    {
        $params = ['message' => 'another test'];

        $vo = new SimplestValueObject($params);

        $this->assertSame($vo->toArray(), $params);
    }

    /**
     * @test
     */
    public function allValidButMagicMethodFailedBecauseParameterNotFound()
    {
        $params = ['message' => 'one more test'];

        $vo = new SimplestValueObject($params);

        try {
            $vo->getCode();
        } catch (ParameterNotFoundException $e) {
            $this->assertInstanceOf(ParameterNotFoundException::class, $e);
        }
    }
}
