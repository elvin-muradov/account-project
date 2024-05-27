<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>İdxal</title>
</head>
<body>
@php
    $mainStatuses = [
        [
            'value' => 'active',
            'label' => trans('messages.po.main_statuses.active')
        ],
        [
            'value' => 'completed',
            'label' => trans('messages.po.main_statuses.completed')
        ],
        [
            'value' => 'cancelled',
            'label' => trans('messages.po.main_statuses.cancelled')
        ],
        [
            'value' => 'in_signing',
            'label' => trans('messages.po.main_statuses.in_signing')
        ]
    ];

    $cellGreenClass = 'style="word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #92D050;"';
    $cellBlueClass = 'style="word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #9BC2E6;"';
    $cellBlue100WidthClass = 'style="width:100px;word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #9BC2E6;"';
    $cellBlue300WidthClass = 'style="width:300px;word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #9BC2E6;"';
    $cellTDClass = 'style="word-wrap:break-word;text-align: center;vertical-align: middle;"';
    $cellBoldClass = 'style="font-weight:bold;"';
@endphp


<table>
    <tbody>
    <tr>
        <td @php echo $cellBlueClass; @endphp rowspan="2">N
        </td>
        <td @php echo $cellBlueClass; @endphp rowspan="2">Şirkət adı
        </td>
        <td @php echo $cellBlueClass; @endphp colspan="2">Tarixi
        </td>
        <td @php echo $cellBlue100WidthClass; @endphp rowspan="2">Sorğu №
        </td>
        <td @php echo $cellBlueClass; @endphp rowspan="2">Invoice dəyəri
        </td>
        <td @php echo $cellBlueClass; @endphp rowspan="2">Gömrük dəyəri
        </td>
        <td @php echo $cellBlueClass; @endphp rowspan="2">Statistik dəyər
        </td>
        <td @php echo $cellBlueClass; @endphp rowspan="2">Məzənnə
        </td>
        <td @php echo $cellBlueClass; @endphp colspan="3">Gömrük rüsumları
        </td>
        <td @php echo $cellBlue300WidthClass; @endphp rowspan="2">Materiallar
        </td>
        <td @php echo $cellBlueClass; @endphp rowspan="2">Metr
        </td>
        <td @php echo $cellBlueClass; @endphp rowspan="2">Vahidin qiyməti
        </td>
        <td @php echo $cellBlueClass; @endphp rowspan="2">Məbləğ
        </td>
    </tr>
    <tr>
        <td @php echo $cellBlue100WidthClass; @endphp>Gəlmə tarixi</td>
        <td @php echo $cellBlue100WidthClass; @endphp>Gömrük vaxtı</td>
        <td @php echo $cellBlueClass; @endphp>N</td>
        <td @php echo $cellBlueClass; @endphp>%</td>
        <td @php echo $cellBlueClass; @endphp>Məbləğ</td>
    </tr>
    @foreach($importQueries as $key => $importQuery)
        <tr>
            <td @php echo $cellTDClass; @endphp rowspan="{{$importQuery->importQueryDetails->count() + 2}}">
                {{ $key+1 }}
            </td>
            <td @php echo $cellTDClass; @endphp rowspan="{{$importQuery->importQueryDetails->count() + 2}}">{{$importQuery->seller_company_name}}</td>
            <td @php echo $cellTDClass; @endphp rowspan="{{$importQuery->importQueryDetails->count() + 2}}">
                {{\Carbon\Carbon::parse($importQuery->delivery_date)->format('d.m.Y')}}
            </td>
            <td @php echo $cellTDClass; @endphp rowspan="{{$importQuery->importQueryDetails->count() + 2}}">
                {{\Carbon\Carbon::parse($importQuery->customs_date)->format('d.m.Y')}}
            </td>
            <td @php echo $cellTDClass; @endphp rowspan="{{$importQuery->importQueryDetails->count() + 2}}">{{$importQuery->query_number}}</td>
            <td>{{$importQuery->invoice_value}}</td>
            <td>{{$importQuery->customs_value}}</td>
            <td>{{$importQuery->statistic_value}}</td>
            <td>{{$importQuery->currency->rate}}</td>
            <td>2</td>
            <td>&nbsp;</td>
            <td>{{$importQuery->customs_transaction_fee}}</td>
            <td>
                {{$importQuery->importQueryDetails->first()->material_title_local}}-
                {{$importQuery->importQueryDetails->first()->material_barcode}}-
                {{$importQuery->importQueryDetails->first()->material_title_az}}
            </td>
            <td>{{$importQuery->importQueryDetails->first()->quantity}}</td>
            <td>{{$importQuery->importQueryDetails->first()->price_per_unit_of_measure}}</td>
            <td>{{$importQuery->importQueryDetails->first()->subtotal_amount}}</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>19</td>
            <td>&nbsp;</td>
            <td>{{$importQuery->customs_transaction_fee_24_hours}}</td>
            @if($importQuery->importQueryDetails->skip(1)->first())
                <td>
                    {{$importQuery->importQueryDetails->skip(1)->first()->material_title_local}}-
                    {{$importQuery->importQueryDetails->skip(1)->first()->material_barcode}}-
                    {{$importQuery->importQueryDetails->skip(1)->first()->material_title_az}}
                </td>
                <td>{{$importQuery->importQueryDetails->skip(1)->first()->quantity}}</td>
                <td>{{$importQuery->importQueryDetails->skip(1)->first()->price_per_unit_of_measure}}</td>
                <td>{{$importQuery->importQueryDetails->skip(1)->first()->subtotal_amount}}</td>
            @else
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            @endif
        </tr>
        <tr style="height: 30px;">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>20</td>
            <td>15%</td>
            <td>{{$importQuery->import_fee}}</td>
            @if($importQuery->importQueryDetails->skip(2)->first())
                <td>
                    {{$importQuery->importQueryDetails->skip(2)->first()->material_title_local}}-
                    {{$importQuery->importQueryDetails->skip(2)->first()->material_barcode}}-
                    {{$importQuery->importQueryDetails->skip(2)->first()->material_title_az}}
                </td>
                <td>{{$importQuery->importQueryDetails->skip(2)->first()->quantity}}</td>
                <td>{{$importQuery->importQueryDetails->skip(2)->first()->price_per_unit_of_measure}}</td>
                <td>{{$importQuery->importQueryDetails->skip(2)->first()->subtotal_amount}}</td>
            @else
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            @endif
        </tr>
        <tr style="height: 29px;">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>32</td>
            <td>18%</td>
            <td>{{$importQuery->vat}}</td>
            @if($importQuery->importQueryDetails->skip(3)->first())
                <td>
                    {{$importQuery->importQueryDetails->skip(3)->first()->material_title_local}}-
                    {{$importQuery->importQueryDetails->skip(3)->first()->material_barcode}}-
                    {{$importQuery->importQueryDetails->skip(3)->first()->material_title_az}}
                </td>
                <td>{{$importQuery->importQueryDetails->skip(3)->first()->quantity}}</td>
                <td>{{$importQuery->importQueryDetails->skip(3)->first()->price_per_unit_of_measure}}</td>
                <td>{{$importQuery->importQueryDetails->skip(3)->first()->subtotal_amount}}</td>
            @else
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            @endif
        </tr>
        <tr>
            <td @php echo $cellBoldClass @endphp>Fərq</td>
            <td>&nbsp;</td>
            <td @php echo $cellBoldClass @endphp>
                {{$importQuery->statistic_value - $importQuery->invoice_value}}
            </td>
            <td>&nbsp;</td>
            <td>75</td>
            <td>0</td>
            <td>{{$importQuery->electronic_customs_fee}}</td>
            @if($importQuery->importQueryDetails->skip(4)->first())
                <td>
                    {{$importQuery->importQueryDetails->skip(4)->first()->material_title_local}}-
                    {{$importQuery->importQueryDetails->skip(4)->first()->material_barcode}}-
                    {{$importQuery->importQueryDetails->skip(4)->first()->material_title_az}}
                </td>
                <td>{{$importQuery->importQueryDetails->skip(4)->first()->quantity}}</td>
                <td>{{$importQuery->importQueryDetails->skip(4)->first()->price_per_unit_of_measure}}</td>
                <td>{{$importQuery->importQueryDetails->skip(4)->first()->subtotal_amount}}</td>
            @else
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            @endif
        </tr>
        <tr style="height: 20px;">
            <td @php echo $cellBoldClass @endphp>Çəki net</td>
            <td>&nbsp;</td>
            <td @php echo $cellBoldClass @endphp>{{ $importQuery->net_weight }}</td>
            <td>&nbsp;</td>
            <td>85</td>
            <td>18%</td>
            <td>{{$importQuery->vat_for_electronic_customs_fee}}</td>
            @if($importQuery->importQueryDetails->skip(5)->first())
                <td>
                    {{$importQuery->importQueryDetails->skip(5)->first()->material_title_local}}-
                    {{$importQuery->importQueryDetails->skip(5)->first()->material_barcode}}-
                    {{$importQuery->importQueryDetails->skip(5)->first()->material_title_az}}
                </td>
                <td>{{$importQuery->importQueryDetails->skip(5)->first()->quantity}}</td>
                <td>{{$importQuery->importQueryDetails->skip(5)->first()->price_per_unit_of_measure}}</td>
                <td>{{$importQuery->importQueryDetails->skip(5)->first()->subtotal_amount}}</td>
            @else
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            @endif
        </tr>
        @foreach($importQuery->importQueryDetails->skip(6) as $importQueryDetail)
            <tr style="height: 20px;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                @if($importQuery->importQueryDetails->skip(5)->first())
                    <td>
                        {{$importQueryDetail->material_title_local}}-
                        {{$importQueryDetail->material_barcode}}-
                        {{$importQueryDetail->material_title_az}}
                    </td>
                    <td>{{$importQueryDetail->quantity}}</td>
                    <td>{{$importQueryDetail->price_per_unit_of_measure}}</td>
                    <td>{{$importQueryDetail->subtotal_amount}}</td>
                @else
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                @endif
            </tr>
        @endforeach
        <tr style="height: 21px;">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td @php echo $cellBlueClass; @endphp colspan="5">Cəmi</td>
            <td @php echo $cellBlueClass; @endphp>&nbsp;</td>
            <td @php echo $cellBlueClass; @endphp>{{ $importQuery->shipping_from }}</td>
            <td @php echo $cellBlueClass; @endphp>{{ $importQuery->transport_type }}</td>
            <td @php echo $cellBlueClass; @endphp>
                @switch($importQuery->payment_status)
                    @case("PAID")
                        100
                        @break
                    @case("PART_PAID")
                        110
                        @break
                    @case("NOT_PAID")
                        120
                        @break=
                @endswitch
            </td>
            <td @php echo $cellBlueClass; @endphp>&nbsp;</td>
            <td @php echo $cellBlueClass; @endphp>&nbsp;</td>
            <td @php echo $cellGreenClass; @endphp>
                {{
                    $importQuery->customs_transaction_fee + $importQuery->customs_transaction_fee_24_hours +
                    $importQuery->import_fee + $importQuery->vat +
                    $importQuery->electronic_customs_fee + $importQuery->vat_for_electronic_customs_fee
                }}
            </td>
            <td @php echo $cellBlueClass; @endphp>{{$importQuery->customs_barcode}}</td>
            <td @php echo $cellBlueClass; @endphp>&nbsp;</td>
            <td @php echo $cellBlueClass; @endphp>&nbsp;</td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importQuery->importQueryDetails->sum('subtotal_amount') }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
