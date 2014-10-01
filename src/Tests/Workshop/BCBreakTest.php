<?php

namespace Tests\Workshop;

use Workshop\BCBreak;

class BCBreakTest extends \PHPUnit_framework_TestCase
{
    /**
     * @dataProvider provideArrayInput 
     */
    public function testCombine($a, $b, $result)
    {
        $bcb = new BCBreak();
        
        $this->assertSame($result, $bcb->combine($a, $b));
    }
    
    public function provideArrayInput()
    {
        return array(
            array(array(), array(), array()),
            array(array('foo'), array('bar'), array('foo' => 'bar'))
        );
    }
}