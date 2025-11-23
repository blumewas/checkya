<?php declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in(__DIR__.'/Feature');

pest()->extend(TestCase::class)
    ->in(__DIR__.'/Unit');

// Extend Pest with custom expectations
expect()->extend('toThrowIf', function (
    bool|Closure $check,
    string $exception = Exception::class,
    $message = '',
): void {
    if ($check instanceof Closure) {
        $check = $check();
    }

    if ($check) {
        // If the check is true, we expect the exception to be thrown
        $this->toThrow($exception, $message);
    } else {
        $this->not->toThrow($exception, $message);
    }
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/
