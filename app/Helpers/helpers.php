<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

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
                'path' => config('app.s3_url') . $strForPath . '/' . $fileName,
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
            'path' => config('app.s3_url') . $bucket . '/' . $fileName,
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
            '6', '0' => '-cı',
            '4', '3' => '-cü',
            '9' => '-cu',
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
