<?php

namespace Gendiff\PHPUnit\Tests;

use PHPUnit\Framework\TestCase;

use function Gendiff\Differ\differ;

class Test extends TestCase
{
    public function testDiffer(): void
    {
        $t1path1 = __DIR__ . "/fixtures/file1.json";
        $t1path2 = __DIR__ . "/fixtures/file2.json";
        $shouldBe1 = file_get_contents(__DIR__ . "/fixtures/TestResult1.txt", 0, null, null);
        $this->assertEquals($shouldBe1, differ($t1path1, $t1path2, null));
        $t2path1 = __DIR__ . "/fixtures/file3.json";
        $t2path2 = __DIR__ . "/fixtures/file4.json";
        $shouldBe2 = file_get_contents(__DIR__ . "/fixtures/TestResult2.txt", 0, null, null);
        $this->assertEquals($shouldBe2, differ($t2path1, $t2path2, null));
        echo "\n\033[32m Tests passed! \033[0m";
    }
}
