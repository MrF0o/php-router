<?php

declare(strict_types=1);
require './vendor/autoload.php';

use Mrfoo\PHPRouter\Core\HashTable;
use Mrfoo\PHPRouter\Router;
use PHPUnit\Framework\TestCase;
use Mrfoo\PHPRouter\Core\URI;

final class HashTableTest extends TestCase
{
    /* 
    * @runInSeparateProcess
    */
    public function testHash(): void
    {
        $table = new HashTable();

        $hash1 = $table->hash(new URI('/hello'));
        $hash2 = $table->hash(new URI('hello'));

        $this->assertNotEmpty($hash1);
        $this->assertNotEmpty($hash2);

        $this->assertEquals($hash2, $hash1);
    }
}
