<?php

namespace Dpiquet\Mapping\Tests;

use PHPUnit\Framework\TestCase;

use Dpiquet\Mapping\Mapping;
use Dpiquet\Mapping\Exception\MappingOverlapException;
use Dpiquet\Mapping\Exception\MappingIncompleteException;

class MappingTest extends TestCase {


    /**
     * Mapping test
     */
    public function testMapping() {
        $mapping = new Mapping();

        $mapping->addMapping('test', ['eRReur', 'Système']);
        $mapping->addMapping('retest', ['a', 'b']);
        $mapping->addMapping('ambigu', ['érreur', 'systeme']);
        $col_names = ['système', 'b', 'SYSTEME'];
        $maps = $mapping->map($col_names);

        $this->assertEquals(0, $maps['test']);
        $this->assertEquals(1, $maps['retest']);
        $this->assertEquals(2, $maps['ambigu']);
    }


    /**
     * Get mapping keys test
     */
    public function testgetMappingKeys() {
        $mapping = new Mapping();
        $mapping->addMapping('key_a', ['a','z']);
        $mapping->addMapping('key_b', ['e','r']);

        $keys = $mapping->getMappingKeys();
        $this->assertTrue(is_array($keys));
        $this->assertEquals(2, count($keys));
        $this->assertContains('key_a', $keys);
        $this->assertContains('key_b', $keys);
    }


    /**
     * Mapping overlap test
     */
    public function testMappingOverlap() {
        $mapping = new Mapping();

        $this->expectException(MappingOverlapException::class);

        $mapping->addMapping('test', ['test', 'ok'])
            ->addMapping('retest', ['b', 'ok']) // Should throw exception
        ;
    }


    /**
     * Incomplete required mapping test
     *
     */
    public function testMissingMapping() {
        $mapping = new Mapping();

        $this->expectException(MappingIncompleteException::class);

        $mapping->addMapping('test', ['eRReur', 'Système']);
        $mapping->addMapping('retest', ['a', 'b']);
        $col_names = ['systeme', 'c'];
        $maps = $mapping->map($col_names); // Should throw exception
    }


    /**
     * Optionnal incomplete mapping test
     *
     */
    public function testMappingOptionnalMissingOk() {
        $mapping = new Mapping();

        $mapping->addMapping('test', ['eRReur', 'Système']);
        $mapping->addMapping('retest', ['a', 'b'], false);
        $col_names = ['erreur', 'c'];
        $maps = $mapping->map($col_names);

        $this->assertTrue(is_array($maps));
    }

    /**
     * @expectedException Dpiquet\Mapping\Exception\OverlapColumnException
     */
    public function testOverlapColumnException()
    {
        $mapping = new Mapping();
        $mapping->addMapping('test', ['a','b','c']);
        $mapping->map(['a', 'a']);
    }

}