<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class S3ApiGatewayController extends Controller
{
    use HttpResponses;

    protected function getObjectOfS3(string $bucket, string $key)
    {
        $s3 = App::make('aws')->createClient('s3');

        $object = null;

        if ($this->checkBucketOfS3($s3, $bucket)) {
            $object = $s3->getObject([
                'Bucket' => $bucket,
                'Key' => $key
            ]);
        }

        return $object;
    }

    protected function checkBucketOfS3(mixed $s3, string $bucket): bool
    {
        $listBuckets = $s3->listBucketsAsync();
        $listBuckets = $listBuckets->wait();
        $listBuckets = $listBuckets->search('Buckets[].Name');

        return in_array($bucket, $listBuckets);
    }

    public function getObjectUrl(string $bucket, string $key): Response|JsonResponse
    {
        $object = $this->getObjectOfS3($bucket, $key);

        if (empty($object)) {
            return $this->error(message: "Fayl tapılmadı", code: 404);
        }

        return response($object->get('Body'), 200, [
            'Content-Type' => $object['@metadata']['headers']['content-type'],
            'Content-Length' => $object['@metadata']['headers']['content-length']
        ]);
    }


}
