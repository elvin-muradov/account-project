<?php

use App\Models\Orders\HiringOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

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
            '06', '00', '40', '60', '90' => '-cı',
            '04', '03' => '-cü',
            '09', '10', '30' => '-cu',
            default => '-ci',
        };

        return $lastChar;
    }
}
if (!function_exists('getGender')) {
    function getGender($gender): string
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
