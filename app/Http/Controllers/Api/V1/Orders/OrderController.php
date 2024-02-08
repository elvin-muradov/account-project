<?php

namespace App\Http\Controllers\Api\V1\Orders;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\Orders\AwardOrder;
use App\Models\Orders\BusinessTripOrder;
use App\Models\Orders\DefaultHolidayOrder;
use App\Models\Orders\HiringOrder;
use App\Models\Orders\MotherhoodHolidayOrder;
use App\Models\Orders\PregnantOrder;
use App\Models\Orders\TerminationOrder;
use App\Traits\HttpResponses;
use Aws\Laravel\AwsFacade as AWS;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class OrderController extends Controller
{
    use HttpResponses;

    public function companyDirectorOrMainUser(Request $request): JsonResponse
    {
        $company = Company::query()->with(['director', 'mainUser'])->find($request->input('company_id'));

        if (!$company) {
            return $this->error(message: 'Şirkət tapılmadı', code: 404);
        }

        return $this->success(data: [
            'director' => $company->director ?? null,
            'mainUser' => $company->mainUser ?? null,
        ]);
    }

    private function downloadOrderFile($order, $type): Throwable|JsonResponse|StreamedResponse
    {
        $order = match ($type) {
            'hiring' => HiringOrder::query()->find($order),
            'business_trip' => BusinessTripOrder::query()->find($order),
            'termination' => TerminationOrder::query()->find($order),
            'pregnant' => PregnantOrder::query()->find($order),
            'default_holiday' => DefaultHolidayOrder::query()->find($order),
            'motherhood_holiday' => MotherhoodHolidayOrder::query()->find($order),
            'award' => AwardOrder::query()->find($order),
            default => null,
        };

        if (!$order) {
            return $this->error(message: 'Əmr tapılmadı', code: 404);
        }

        $s3 = App::make('aws')->createClient('s3');

        $file = $s3->getObject([
            'Bucket' => $type . '_orders',
            'Key' => $order->generated_file[0]['generated_name'],
        ]);

        if (!$file) {
            return $this->error(message: 'Fayl tapılmadı', code: 404);
        }

        $file = $file->get('Body');

        return response()->streamDownload(function () use ($file) {
            echo $file;
        }, $order->generated_file[0]['original_name']);
    }

    public function getOrderFile($order, $type)
    {
        $order = match ($type) {
            'hiring' => HiringOrder::query()->find($order),
            'business_trip' => BusinessTripOrder::query()->find($order),
            'termination' => TerminationOrder::query()->find($order),
            'pregnant' => PregnantOrder::query()->find($order),
            'default_holiday' => DefaultHolidayOrder::query()->find($order),
            'motherhood_holiday' => MotherhoodHolidayOrder::query()->find($order),
            'award' => AwardOrder::query()->find($order),
            default => null,
        };

        if (!$order) {
            return $this->error(message: 'Əmr tapılmadı', code: 404);
        }

        $s3 = App::make('aws')->createClient('s3');

        $file = $s3->getObject([
            'Bucket' => $type . '_orders',
            'Key' => $order->generated_file[0]['generated_name'],
        ]);

        if (!$file) {
            return $this->error(message: 'Fayl tapılmadı', code: 404);
        }

        $file = $file->get('Body');

        return response()->stream(function () use ($file) {
            echo $file;
        });
    }

    public function downloadHiringOrderFile($hiringOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->downloadOrderFile($hiringOrder, 'hiring');
    }

    public function getHiringOrderFile($hiringOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->getOrderFile($hiringOrder, 'hiring');
    }

    public function downloadBusinessTripOrderFile($businessTripOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->downloadOrderFile($businessTripOrder, 'business_trip');
    }

    public function getBusinessTripOrderFile($businessTripOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->getOrderFile($businessTripOrder, 'business_trip');
    }

    public
    function downloadTerminationOrderFile($terminationOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->downloadOrderFile($terminationOrder, 'termination');
    }

    public function getTerminationOrderFile($terminationOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->getOrderFile($terminationOrder, 'termination');
    }

    public function downloadPregnantOrderFile($pregnantOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->downloadOrderFile($pregnantOrder, 'pregnant');
    }

    public function getPregnantOrderFile($pregnantOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->getOrderFile($pregnantOrder, 'pregnant');
    }

    public
    function downloadDefaultHolidayOrderFile($defaultHolidayOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->downloadOrderFile($defaultHolidayOrder, 'default_holiday');
    }

    public function getDefaultHolidayOrderFile($defaultHolidayOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->getOrderFile($defaultHolidayOrder, 'default_holiday');
    }

    public
    function downloadMotherhoodHolidayOrderFile($motherhoodHolidayOrder):
    Throwable|JsonResponse|StreamedResponse
    {
        return $this->downloadOrderFile($motherhoodHolidayOrder, 'motherhood_holiday');
    }

    public function getMotherhoodHolidayOrderFile($motherhoodHolidayOrder):
    Throwable|JsonResponse|StreamedResponse
    {
        return $this->getOrderFile($motherhoodHolidayOrder, 'motherhood_holiday');
    }

    public
    function downloadAwardOrderFile($awardOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->downloadOrderFile($awardOrder, 'award');
    }

    public function getAwardOrderFile($awardOrder): Throwable|JsonResponse|StreamedResponse
    {
        return $this->getOrderFile($awardOrder, 'award');
    }
}
