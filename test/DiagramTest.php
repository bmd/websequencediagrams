<?php
namespace Test;

use WebSequenceDiagrams\Diagram;

/**
 * Class DiagramTest
 * @package Test
 */
class DiagramTest extends \PHPUnit_Framework_TestCase
{
    public function invalidStyleOrFormatDataProvider()
    {
        return [
            ['potato', 'png'],
            ['default', 'potato'],
            [7, 'png'],
        ];
    }

    /**
     * @test
     */
    public function it_should_set_values_from_constructor()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     */
    public function it_should_set_values_via_setters()
    {
        $this->markTestIncomplete();
    }

    /**
     * @test
     * @dataProvider invalidStyleOrFormatDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function it_should_raise_exception_with_invalid_setting_params($style, $format)
    {
        new Diagram('A->B:', $style, $format);
    }
}