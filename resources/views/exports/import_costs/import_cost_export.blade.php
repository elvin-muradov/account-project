<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>İdxal Maya</title>
</head>
<body>
@php
    $cellGreenClass = 'style="word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #92D050;"';
    $cellBlueClass = 'style="word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #9BC2E6;"';
    $cellBlue100WidthClass = 'style="width:100px;word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #9BC2E6;"';
    $cellBlue300WidthClass = 'style="width:300px;word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #9BC2E6;"';
    $cellTDClass = 'style="word-wrap:break-word;text-align: center;vertical-align: middle;border: 2px solid #000"';
    $cellBoldClass = 'style="font-weight:bold;"';

    $totalInvoiceValues = 0;
@endphp

<table style="border:2px solid black">
    <thead>
    @foreach($importCosts as $key => $importCost)
        <tr>
            <td @php echo $cellBlue100WidthClass; @endphp rowspan="{{$importCost->importCostDetails->count() + 2}}">
                {{ ucfirst(\Carbon\Carbon::parse($importCost->created_at)->isoFormat('MMMM/Y')) }}
            </td>
            <td @php echo $cellTDClass; @endphp rowspan="{{$importCost->importCostDetails->count() + 2}}">{{ $key + 1 }}</td>
            <td @php echo $cellTDClass; @endphp rowspan="{{$importCost->importCostDetails->count() + 2}}">{{ $importCost->importQuery->seller_company_name }}</td>
            <td @php echo $cellBlue300WidthClass; @endphp rowspan="2">Malın adı</td>
            <td @php echo $cellBlueClass; @endphp rowspan="2">Sayı</td>
            <td @php echo $cellBlueClass; @endphp rowspan="2">Qiymət</td>
            <td @php echo $cellBlueClass; @endphp rowspan="2">Məbləğ</td>
            <td @php echo $cellBlueClass; @endphp rowspan="2">Nisbət</td>
            <td @php echo $cellBlueClass; @endphp>Qısa İdxal <br>rüsum</td>
            <td @php echo $cellBlueClass; @endphp>YGB + Qısa</td>
            <td @php echo $cellBlueClass; @endphp>Digər xərclər</td>
            <td @php echo $cellBlueClass; @endphp>Gömrük RSM Yığımı</td>
            <td @php echo $cellBlueClass; @endphp>Nəqliyyat</td>
            <td @php echo $cellBlueClass; @endphp>İdxal və digər xərclər</td>
            <td @php echo $cellBlueClass; @endphp>ƏDV</td>
            <td @php echo $cellBlueClass; @endphp>Ümumi məbləğ</td>
            <td @php echo $cellBlueClass; @endphp>Vahidin qiyməti</td>
            <td @php echo $cellBlueClass; @endphp rowspan="2">Sayı</td>
            <td @php echo $cellBlueClass; @endphp>Məbləğ</td>
        </tr>
        <tr>
            <td @php echo $cellBlueClass; @endphp>{{ $importCost->total_short_import_duty }}</td>
            <td @php echo $cellBlueClass; @endphp>{{ $importCost->total_customs_short_and_import_duty }}</td>
            <td @php echo $cellBlueClass; @endphp>{{ $importCost->total_other_expenses }}</td>
            <td @php echo $cellBlueClass; @endphp>{{ $importCost->total_customs_collection }}</td>
            <td @php echo $cellBlueClass; @endphp>{{ $importCost->total_transport_expenses }}</td>
            <td @php echo $cellBlueClass; @endphp>{{ $importCost->total_import_fee_and_other_expenses }}</td>
            <td @php echo $cellBlueClass; @endphp>{{ $importCost->total_vat }}</td>
            <td @php echo $cellBlueClass; @endphp>AZN</td>
            <td @php echo $cellBlueClass; @endphp>AZN</td>
            <td @php echo $cellBlueClass; @endphp>AZN</td>
        </tr>
        @foreach($importCost->importCostDetails as $importCostDetail)
            <tr>
                <td>
                    {{$importCostDetail->importQueryDetail->material_title_local}}-
                    {{$importCostDetail->importQueryDetail->material_barcode}}-
                    {{$importCostDetail->importQueryDetail->material_title_az}}
                </td>
                <td>{{ $importCostDetail->quantity }}</td>
                <td>{{ $importCostDetail->importQueryDetail->price_per_unit_of_measure }}</td>
                <td>{{ $importCostDetail->importQueryDetail->subtotal_amount }}</td>
                <td>{{ $importCostDetail->ratio }}</td>
                <td>{{ $importCostDetail->short_import_duty }}</td>
                <td>{{ $importCostDetail->customs_short_and_import_duty }}</td>
                <td>{{ $importCostDetail->other_expenses }}</td>
                <td>{{ $importCostDetail->customs_collection }}</td>
                <td>{{ $importCostDetail->transport_expenses }}</td>
                <td>{{ $importCostDetail->import_fee_and_other_expenses }}</td>
                <td>{{ $importCostDetail->vat }}</td>
                <td>{{ $importCostDetail->subtotal_amount_azn }}</td>
                <td>{{ $importCostDetail->price_per_unit_of_measure_azn }}</td>
                <td>{{ $importCostDetail->quantity }}</td>
                <td>{{ $importCostDetail->subtotal_amount_azn }}</td>
            </tr>
        @endforeach
        <tr>
            <td @php echo $cellBlueClass; @endphp></td>
            <td @php echo $cellBlueClass; @endphp></td>
            <td @php echo $cellBlueClass; @endphp></td>
            <td @php echo $cellBlueClass; @endphp></td>
            <td @php echo $cellBlueClass; @endphp></td>
            <td @php echo $cellBlueClass; @endphp></td>
            <td @php echo $cellBlueClass; @endphp><span
                    style="font-weight:bold">{{ $importCost->total_amount }}</span></td>
            <td @php echo $cellBlueClass; @endphp><span style="font-weight:bold">100</span></td>
            <td @php echo $cellBlueClass; @endphp><span
                    style="font-weight:bold">{{ $importCost->total_short_import_duty }}</span></td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importCost->total_customs_short_and_import_duty }}</td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importCost->total_other_expenses }}
            </td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importCost->total_customs_collection }}
            </td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importCost->total_transport_expenses }}
            </td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importCost->total_import_fee_and_other_expenses }}
            </td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importCost->total_vat }}
            </td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importCost->total_amount_azn }}
            </td>
            <td @php echo $cellBlueClass; @endphp></td>
            <td @php echo $cellBlueClass; @endphp></td>
            <td @php echo $cellBlueClass; @endphp>
                {{ $importCost->total_amount_azn }}
            </td>
        </tr>
    @endforeach
    </thead>
</table>
</body>
</html>
