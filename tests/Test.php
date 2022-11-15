<?php

namespace Gendiff\PHPUnit\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\gendiff;

class Test extends TestCase
{
    public function testDifferPlainStylish(): void
    {
        $t1path1 = __DIR__ . "/fixtures/file1.json";
        $t1path2 = __DIR__ . "/fixtures/file2.json";
        $shouldBe1 = rtrim(file_get_contents(__DIR__ . "/fixtures/TestResult1.txt", 0, null, null), "\r\n");
        $this->assertEquals($shouldBe1, gendiff($t1path1, $t1path2, 'stylish'));
        $t2path1 = __DIR__ . "/fixtures/file3.json";
        $t2path2 = __DIR__ . "/fixtures/file4.json";
        $shouldBe2 = rtrim(file_get_contents(__DIR__ . "/fixtures/TestResult2.txt", 0, null, null), "\r\n");
        $this->assertEquals($shouldBe2, gendiff($t2path1, $t2path2, 'stylish'));
        $t3path1 = __DIR__ . "/fixtures/file5.yml";
        $t3path2 = __DIR__ . "/fixtures/file6.yml";
        $shouldBe3 = rtrim(file_get_contents(__DIR__ . "/fixtures/TestResult1.txt", 0, null, null), "\r\n");
        $this->assertEquals($shouldBe3, gendiff($t3path1, $t3path2, 'stylish'));
    }
    public function testDifferNestedStylish(): void
    {
        $t4path1 = __DIR__ . "/fixtures/nested1.json";
        $t4path2 = __DIR__ . "/fixtures/nested2.json";
        $shouldBe4 = rtrim(file_get_contents(__DIR__ . "/fixtures/TestNestedResult1Stylish.txt", 0, null, null), "\r\n");  // phpcs:ignore
        $this->assertEquals($shouldBe4, gendiff($t4path1, $t4path2, 'stylish'));
    }
    public function testDifferNestedPlain(): void
    {
        $t5path1 = __DIR__ . "/fixtures/nested1.json";
        $t5path2 = __DIR__ . "/fixtures/nested2.json";
        $shouldBe5 = rtrim(file_get_contents(__DIR__ . "/fixtures/TestNestedResult1Plain.txt", 0, null, null), "\r\n");
        $this->assertEquals($shouldBe5, gendiff($t5path1, $t5path2, 'plain'));
        $t6path1 = __DIR__ . "/fixtures/nested11.json";
        $t6path2 = __DIR__ . "/fixtures/nested12.json";
        $shouldBe6 = rtrim(file_get_contents(__DIR__ . "/fixtures/TestNestedResult2Plain.txt", 0, null, null), "\r\n");
        $this->assertEquals($shouldBe6, gendiff($t6path1, $t6path2, 'plain'));
    }
    public function testDifferNestedJSON(): void
    {
        $t7path1 = __DIR__ . "/fixtures/file7j.json";
        $t7path2 = __DIR__ . "/fixtures/file8j.json";
        $shouldBe7 = rtrim(file_get_contents(__DIR__ . "/fixtures/TestNestedResult3Json.txt", 0, null, null), "\r\n");
        $this->assertEquals($shouldBe7, gendiff($t7path1, $t7path2, 'json'));
        echo "\n\033[32m Tests passed! \033[0m";
    }
}
