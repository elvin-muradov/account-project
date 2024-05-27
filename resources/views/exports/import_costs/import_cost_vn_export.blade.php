<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>İdxal Maya VN</title>
</head>
<body>
@php
    $cellHeadClass = 'style="word-wrap:break-word;border: 2px solid #000;vertical-align:middle;text-align:center;font-weight:bold;width:100px;font-size:12px;"';
    $cellBodyClass = 'style="word-wrap:break-word;border: 2px solid #000;vertical-align:middle;text-align:center;"';
    $totalInvoiceValues = 0;
    $totalCustomsTransactionFee = 0;
    $totalCustomsTransactionFee24 = 0;
    $totalImportFee = 0;
    $totalElectronicCustomsFee = 0;
@endphp
<table>
    <thead>
    <tr>
        <th @php echo $cellHeadClass; @endphp rowspan="2">S/N</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Adı</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Gəlmə tarixi</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Gömrük tarixi</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">No</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Faktura</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Qısa İdxal rüsum</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">YGB + Qısa</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Digər xərclər</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Nəqliyyat xərci</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">2 Gömrük əməliyyat haqqı</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">19 Gömrük əməliyyat haqqı - 24 saat</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">20 İdxal rüsumu - 15 %</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">75 Elektron gömrük haqqı</th>
        <th @php echo $cellHeadClass; @endphp rowspan="2">Maya dəyəri</th>
        <th @php echo $cellHeadClass; @endphp colspan="2">Daşınma şərtləri</th>
    </tr>
    <tr>
        <th @php echo $cellHeadClass; @endphp>Şəhər</th>
        <th @php echo $cellHeadClass; @endphp>İ-Şərt</th>
    </tr>
    </thead>
    <tbody>
    @foreach($importCosts as $key => $importCost)
        @php $totalInvoiceValues += $importCost->importQuery->invoice_value; @endphp
        @php $totalCustomsTransactionFee += $importCost->importQuery->customs_transaction_fee; @endphp
        @php $totalCustomsTransactionFee24 += $importCost->importQuery->customs_transaction_fee_24_hours; @endphp
        @php $totalImportFee += $importCost->importQuery->import_fee; @endphp
        @php $totalElectronicCustomsFee += $importCost->importQuery->electronic_customs_fee; @endphp
        <tr>
            <td @php echo $cellHeadClass; @endphp>{{ $key+1 }}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->seller_company_name}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->delivery_date}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->customs_date}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->query_number}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->invoice_value}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->total_short_import_duty}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->total_customs_short_and_import_duty}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->total_other_expenses}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->total_transport_expenses}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->customs_transaction_fee}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->customs_transaction_fee_24_hours}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->import_fee}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->electronic_customs_fee}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->total_amount_azn}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->shipping_from}}</td>
            <td @php echo $cellBodyClass; @endphp>{{$importCost->importQuery->transport_type}}</td>
        </tr>
    @endforeach
    <tr>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellHeadClass; @endphp>Yekun</td>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellHeadClass; @endphp>{{ $totalInvoiceValues }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importCosts->sum('total_short_import_duty') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importCosts->sum('total_customs_short_and_import_duty') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importCosts->sum('total_other_expenses') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importCosts->sum('total_transport_expenses') }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $totalCustomsTransactionFee }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $totalCustomsTransactionFee24 }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $totalImportFee }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $totalElectronicCustomsFee }}</td>
        <td @php echo $cellHeadClass; @endphp>{{ $importCosts->sum('total_amount_azn') }}</td>
        <td @php echo $cellBodyClass; @endphp></td>
        <td @php echo $cellBodyClass; @endphp></td>
    </tr>
    </tbody>
</table>
</body>
</html>
