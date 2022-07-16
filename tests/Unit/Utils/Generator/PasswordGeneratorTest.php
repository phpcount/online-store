<?php

namespace App\Tests\Unit\Utils\Generator;

use App\Utils\Generator\PasswordGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class PasswordGeneratorTest extends TestCase
{
    public function testGeneratePassword(): void
    {
        $count = 1000;
        $arrPasswords = [];

        for ($i = 0; $i < $count; $i++) {
            $password = PasswordGenerator::generatePassword(8);

            self::assertNotEmpty($password);

            self::assertSame(8, strlen($password));

            // self::assertNotContains($password, $arrPasswords);
            self::assertEquals(isset($arrPasswords[$password]), false);

            $arrPasswords[$password] = 1;
        }

        /*
        las test:
        Testing
        Password Generator (App\Tests\Unit\Utils\Generator\PasswordGenerator)
        ✔ Generate password

        Time: 00:30.638, Memory: 1.26 GB

        OK (1 test, 30000000 assertions)
         */
    }
}
