<?php

declare(strict_types=1);

namespace Mazur\Console\Commands;

use Illuminate\Console\Command;

final class TestCommand extends Command
{
    protected $signature = 'test-code:run';

    public function handle(): void
    {
        $from = 0;
        $to = 300;
        for ($i = $from; $i <= $to; $i++) {
            rename('/var/www/html/resources/images/markers/m-air-quality-us-' . $i . '.svg', '/var/www/html/resources/images/markers/m-air-quality-aqi_us-' . $i . '.svg');
//            $referenceFile = '/var/www/html/resources/images/markers/m-air-quality-201.svg';
//            $newFile = '/var/www/html/resources/images/markers/m-air-quality-' . $i . '.svg';
//            copy($referenceFile, $newFile);
        }
//        $from = 202;
//        $to = 300;
//        $referenceFile = '/var/www/html/resources/images/markers/m-air-quality-201.svg';
//
//        for ($i = $from; $i <= $to; $i++) {
//            $newFile = '/var/www/html/resources/images/markers/m-air-quality-' . $i . '.svg';
//            copy($referenceFile, $newFile);
//        }
    }
}
