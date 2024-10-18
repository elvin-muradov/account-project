<?php

use App\Enums\AttendanceLogDayTypes;
use App\Models\Orders\HiringOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use NumberToWords\Exception\NumberToWordsException;
use NumberToWords\NumberToWords;

if (!function_exists("getElementByKey")) {
    function getElementByKey($array, $searchKey, $value)
    {
        foreach ($array as $element) {
            if ($element[$searchKey] === $value) {
                return $element;
            }
        }

        return null;
    }
}
if (!function_exists("returnFilesArray")) {
    function returnFilesArray(array $files, $strForPath): array
    {
        $returnedArray = [];
        $s3 = App::make('aws')->createClient('s3');
        $listBuckets = $s3->listBuckets()->search('Buckets[].Name');
        $checkIfBucketExists = in_array($strForPath, $listBuckets);

        if (!$checkIfBucketExists) {
            $s3->createBucketAsync([
                'Bucket' => $strForPath
            ]);
        }

        foreach ($files as $file) {
            $fileName = $strForPath . uniqid() . '.' . $file->getClientOriginalExtension();

            $s3->putObject(array(
                'Bucket' => $strForPath,
                'Key' => $fileName,
                'SourceFile' => $file->getRealPath(),
            ));

            $returnedArray[] = [
                'path' => env('BASE_URL') . '/api/show-s3-file/' . $strForPath . '/' . $fileName,
                'bucket' => $strForPath,
                'generated_name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return $returnedArray;
    }
}
if (!function_exists("returnOrderFile")) {
    function returnOrderFile($file, $fileName, $bucket): array
    {
        $returnedArray = [];
        $s3 = App::make('aws')->createClient('s3');
        $listBuckets = $s3->listBucketsAsync();
        $listBuckets = $listBuckets->wait();
        $listBuckets = $listBuckets->search('Buckets[].Name');
        $checkIfBucketExists = in_array($bucket, $listBuckets);

        if (!$checkIfBucketExists) {
            $s3->createBucketAsync([
                'Bucket' => $bucket
            ]);
        }

        $s3->putObject(array(
            'Bucket' => $bucket,
            'Key' => $fileName,
            'SourceFile' => $file,
        ));

        $returnedArray[] = [
            'path' => env('BASE_URL') . '/api/show-s3-file/' . $bucket . '/' . $fileName,
            'bucket' => $bucket,
            'generated_name' => $fileName,
            'original_name' => $fileName,
        ];

        return $returnedArray;
    }
}
if (!function_exists('deleteFiles')) {
    function deleteFiles($deletedFiles, $currentFiles, bool $notEmpty = false)
    {
        $s3 = App::make('aws')->createClient('s3');

        foreach ($deletedFiles as $file) {
            if ($notEmpty && count($deletedFiles) >= count($currentFiles)) {
                return false;
            }

            $deletedFile = getElementByKey($currentFiles, 'generated_name', $file);
            $key = array_search($deletedFile, $currentFiles);

            if ($deletedFile != null) {
                $s3->deleteObject(array(
                    'Bucket' => $deletedFile['bucket'],
                    'Key' => $deletedFile['generated_name']
                ));

                $s3->waitUntil('ObjectNotExists', array(
                    'Bucket' => $deletedFile['bucket'],
                    'Key' => $deletedFile['generated_name']
                ));

                unset($currentFiles[$key]);
            }
        }

        return $currentFiles;
    }

}
if (!function_exists('checkFilesAndDeleteFromStorage')) {
    function checkFilesAndDeleteFromStorage($files): void
    {
        $s3 = App::make('aws')->createClient('s3');

        foreach ($files as $file) {
            $s3->deleteObject(array(
                'Bucket' => $file['bucket'],
                'Key' => $file['generated_name']
            ));
        }
    }
}
if (!function_exists('getNumberEnd')) {
    function getNumberEnd($char, $lastChar = null): string
    {
        $lastChar .= match ($char) {
            '06', '16', '26', '36', '40', '46', '56', '60', '66', '76', '86', '90', '96' => '-cı',
            '04', '03', '13', '14', '23', '24', '33', '34', '43', '44',
            '53', '54', '63', '64', '73', '74', '83', '84', '93', '94' => '-cü',
            '09', '10', '19', '29', '30', '39', '49', '59', '69', '79', '89', '99' => '-cu',
            default => '-ci',
        };

        return $lastChar;
    }
}
if (!function_exists('getGender')) {
    function getGender($gender): string|null
    {
        return match ($gender) {
            'MALE' => 'oğlu',
            'FEMALE' => 'qızı',
            default => null,
        };
    }
}
if (!function_exists('getCbaRates')) {
    function getCbaRates($today)
    {
        $xml = simplexml_load_file("https://cbar.az/currencies/$today.xml") or die("Error");

        $exchangesArray = $xml->children()[1]->children();

        $azn = [
            'code' => 'AZN',
            'name' => 'Azərbaycan manatı',
            'rate' => "1",
            'symbol' => '₼',
            'bank' => 'CBAR'
        ];

        $usd = [
            'code' => $exchangesArray[0]->attributes()->Code->__toString(),
            'name' => trim(preg_replace('/\d+/', '', $exchangesArray[0]->Name->__toString())),
            'rate' => $exchangesArray[0]->Value->__toString(),
            'symbol' => '$',
            'bank' => 'CBAR'
        ];

        $eur = [
            'code' => $exchangesArray[1]->attributes()->Code->__toString(),
            'name' => trim(preg_replace('/\d+/', '', $exchangesArray[1]->Name->__toString())),
            'rate' => $exchangesArray[1]->Value->__toString(),
            'symbol' => '€',
            'bank' => 'CBAR'
        ];

        $gbp = [
            'code' => $exchangesArray[16]->attributes()->Code->__toString(),
            'name' => trim(preg_replace('/\d+/', '', $exchangesArray[16]->Name->__toString())),
            'rate' => $exchangesArray[16]->Value->__toString(),
            'symbol' => '£',
            'bank' => 'CBAR'
        ];

        $try = [
            'code' => $exchangesArray[38]->attributes()->Code->__toString(),
            'name' => trim(preg_replace('/\d+/', '', $exchangesArray[38]->Name->__toString())),
            'rate' => $exchangesArray[38]->Value->__toString(),
            'symbol' => '₺',
            'bank' => 'CBAR'
        ];

        $rub = [
            'code' => $exchangesArray[34]->attributes()->Code->__toString(),
            'name' => trim(preg_replace('/\d+/', '', $exchangesArray[34]->Name->__toString())),
            'rate' => $exchangesArray[34]->Value->__toString(),
            'symbol' => '₽',
            'bank' => 'CBAR'
        ];

        return [$azn, $usd, $eur, $gbp, $try, $rub];
    }
}
if (!function_exists('getLabelValue')) {
    function getLabelValue(string $type, array $arr): array
    {
        return [
            'label' => collect($arr)->where('value', $type)->first()['label'],
            'value' => $type
        ];
    }
}
if (!function_exists('generateOrderNumber')) {
    function generateOrderNumber($model, $companyName): string
    {
        $count = $model::count() + 1;

        return $companyName . '-' . $count . '/' . date('Y');
    }
}
if (!function_exists('toFloat')) {
    function toFloat(float|int|string $value): float
    {
        return number_format(floatval($value), 2, '.', '');
    }
}
if (!function_exists('returnMonthDaysAsArray')) {
    function returnMonthDaysAsArray(int $count): array
    {
        $days = [];

        for ($i = 0; $i < $count; $i++) {
            $days[] = $i + 1;
        }

        return $days;
    }
}
if (!function_exists('checkMonthDaysUnique')) {
    function checkMonthDaysUnique(int $count, array $requestDays): bool
    {
        $monthDays = returnMonthDaysAsArray($count);

        //dd($monthDays);

        foreach ($requestDays as $key => $day) {
            if (count(array_unique($requestDays)) !== count($monthDays)) {
                return false;
            }
        }

        return true;
    }
}
if (!function_exists('getMonthWorkDayHours')) {
    function getMonthWorkDayHours(array $config): float|int
    {
        return array_sum(array_column($config, 'status'));
    }
}
if (!function_exists('getCelebrationRestDaysCount')) {
    function getCelebrationRestDaysCount(array $config): int
    {
        $dayTypes = array_diff(array_values(AttendanceLogDayTypes::toArray()),
            [AttendanceLogDayTypes::NULL_DAY->value, AttendanceLogDayTypes::BUSINESS_TRIP->value]);
        $array = array_count_values(array_column($config, 'status'));

        $totalDays = 0;

        foreach ($dayTypes as $dayType) {
            if (array_key_exists($dayType, $array)) {
                $totalDays += $array[$dayType];
            }
        }

        return $totalDays;
    }
}
if (!function_exists('getMonthWorkDaysCount')) {
    function getMonthWorkDaysCount(array $config): float|int
    {
        $config = collect($config)->where('status', '!=', 'NULL_DAY')->toArray();

        return count($config) - getCelebrationRestDaysCount($config);
    }
}
if (!function_exists('getNumberAsWords')) {
    /**
     * @throws NumberToWordsException
     * @throws \NumberToWords\Exception\InvalidArgumentException
     */
    function getNumberAsWords(int|float $number): string
    {
        $numberToWords = new NumberToWords();
        $numberToWordsTransformer = $numberToWords->getNumberTransformer('az');

        return $numberToWordsTransformer->toWords($number);
    }
}
