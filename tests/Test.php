<?php

namespace Gendiff\PHPUnit\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Differ\differ;

class Test extends TestCase
{
    public function testDifferPlainStylish(): void
    {
        $t1path1 = __DIR__ . "/fixtures/file1.json";
        $t1path2 = __DIR__ . "/fixtures/file2.json";
        $shouldBe1 = file_get_contents(__DIR__ . "/fixtures/TestResult1.txt", 0, null, null);
        $this->assertEquals($shouldBe1, differ($t1path1, $t1path2, 'stylish'));
        $t2path1 = __DIR__ . "/fixtures/file3.json";
        $t2path2 = __DIR__ . "/fixtures/file4.json";
        $shouldBe2 = file_get_contents(__DIR__ . "/fixtures/TestResult2.txt", 0, null, null);
        $this->assertEquals($shouldBe2, differ($t2path1, $t2path2, 'stylish'));
        $t3path1 = __DIR__ . "/fixtures/file5.yml";
        $t3path2 = __DIR__ . "/fixtures/file6.yml";
        $shouldBe3 = file_get_contents(__DIR__ . "/fixtures/TestResult1.txt", 0, null, null);
        $this->assertEquals($shouldBe3, differ($t3path1, $t3path2, 'stylish'));
    }
    public function testDifferNestedStylish(): void
    {
        $t4path1 = __DIR__ . "/fixtures/nested1.json";
        $t4path2 = __DIR__ . "/fixtures/nested2.json";
        $shouldBe4 = file_get_contents(__DIR__ . "/fixtures/TestNestedResult1Stylish.txt", 0, null, null);
        $this->assertEquals($shouldBe4, differ($t4path1, $t4path2, 'stylish'));
    }
    public function testDifferNestedPlain(): void
    {
        $t5path1 = __DIR__ . "/fixtures/nested1.json";
        $t5path2 = __DIR__ . "/fixtures/nested2.json";
        $shouldBe5 = file_get_contents(__DIR__ . "/fixtures/TestNestedResult1Plain.txt", 0, null, null);
        $this->assertEquals($shouldBe5, differ($t5path1, $t5path2, 'plain'));
        $t6path1 = __DIR__ . "/fixtures/nested11.json";
        $t6path2 = __DIR__ . "/fixtures/nested12.json";
        $shouldBe6 = file_get_contents(__DIR__ . "/fixtures/TestNestedResult2Plain.txt", 0, null, null);
        $this->assertEquals($shouldBe6, differ($t6path1, $t6path2, 'plain'));
    }
    public function testDifferNestedJSON(): void
    {
        $t7path1 = __DIR__ . "/fixtures/file7j.json";
        $t7path2 = __DIR__ . "/fixtures/file8j.json";
        $shouldBe7 = file_get_contents(__DIR__ . "/fixtures/TestNestedResult3Json.txt", 0, null, null);
        $this->assertEquals($shouldBe7, differ($t7path1, $t7path2, 'json'));
        echo "\n\033[32m Tests passed! \033[0m";
    }
}
