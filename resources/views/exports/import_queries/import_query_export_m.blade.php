<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>İdxal M</title>
</head>
<body>
@php
    $cellHeadClass = 'style="word-wrap:break-word;border: 2px solid #000;vertical-align:middle;text-align:center;font-weight:bold;width:100px;font-size:12px;"';
    $cellBodyClass = 'style="word-wrap:break-word;border: 2px solid #000;vertical-align:middle;text-align:center;"';
    $totalCustomsValues = 0;
    $subtotalCustomsValues = 0;
@endphp
<table>
    <thead>
    <tr>
        <th @php echo $cellHeadClass; @endphp rowspan="2">№</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Şirkət adı</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Gəlmə tarixi</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Gömrük tarixi</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">YGB nömrəsi</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">İnvoys məbləği</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Qısa idxal rüsumu</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">YGB + Qısa</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Digər xərclər</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Nəqliyyat xərcləri</th>
        <th @php echo $cellHeadClass; @endphp>Gömrük RSM yığımı</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">İdxal və digər xərclər</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">ƏDV</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Ümumi məbləğ AZN</th>
    </tr>
    <tr>
        <th @php echo $cellHeadClass; @endphp>Gömrük əməliyyat haqqı</th>
        <th @php echo $cellHeadClass; @endphp>Gömrük əməliyyat haqqı - 24 saat</th>
        <th @php echo $cellHeadClass; @endphp>Elektron gömrük haqqı</th>
    </tr>
    </thead>
    <tbody>
    @foreach($importQueries as $key => $importQuery)
        @php
            $subtotalCustomsValues = $importQuery->customs_transaction_fee +
            $importQuery->customs_transaction_fee_24_hours +
            $importQuery->import_fee + $importQuery->vat +
            $importQuery->electronic_customs_fee + $importQuery->vat_for_electronic_customs_fee;
            $totalCustomsValues+=$subtotalCustomsValues;
        @endphp
        <tr>
            <td @php echo $cellHeadClass; @endphp>{{ $key+1 }}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->seller_company_name}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->delivery_date}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->customs_date}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->query_number}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->invoice_value}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->statistic_value}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->customs_value}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->customs_transaction_fee}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->customs_transaction_fee_24_hours}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->import_fee}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->vat}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->electronic_customs_fee}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->vat_for_electronic_customs_fee}}</td>
            <td @php echo $cellBodyClass; @endphp>
                {{ $subtotalCustomsValues }}
            </td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->net_weight}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->shipping_from}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importQuery->transport_type}}</td>
        </tr>
    @endforeach
    <tr>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellHeadClass; @endphp>Yekun</td>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('invoice_value') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('statistic_value') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('customs_value') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('customs_transaction_fee') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('customs_transaction_fee_24_hours') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('import_fee') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('vat') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('electronic_customs_fee') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('vat_for_electronic_customs_fee') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{$totalCustomsValues}}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importQueries->sum('net_weight') }}</td>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellBodyClass; @endphp></td>
    </tr>
    </tbody>
</table>
</body>
</html>
