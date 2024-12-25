<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amortizasiyanın hesablanması</title>
</head>
<body>
@php
    $cellHeadClass300Width = 'style="border-collapse: collapse;width:300px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass200Width = 'style="border-collapse: collapse;width:200px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass100Width = 'style="border-collapse: collapse;width:100px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass2 = 'style="border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellDays = 'style="border-collapse: collapse;font-weight:bold;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;text-align:center;vertical-align:middle;background-color:#ededed"';
@endphp
@if($fixedAssets->count() > 0)
    <table style="border-collapse: collapse;border:4px solid black;text-align: center">
        <thead>
        <tr>
            <th @php echo $cellHeadClass2; @endphp>№</th>
            <th @php echo $cellHeadClass300Width; @endphp>Əsas vəsaitlərin adı</th>
            <th @php echo $cellHeadClass100Width; @endphp>İlkin<br> dəyər</th>
            <th @php echo $cellHeadClass100Width; @endphp>İstifadə<br> müddəti</th>
            <th @php echo $cellHeadClass100Width; @endphp>İlin əvvəlinə<br> qalıq</th>
            <th @php echo $cellHeadClass200Width; @endphp>Amortizasiya<br> norması</th>
            <th @php echo $cellHeadClass200Width; @endphp>Hesablanmış<br> amortizasiya məbləği</th>
            <th @php echo $cellHeadClass200Width; @endphp>İlin sonuna<br> qalıq dəyəri</th>
            <th @php echo $cellHeadClass200Width; @endphp>Alınma tarixi</th>
            <th @php echo $cellHeadClass200Width; @endphp>Qeyd</th>
        </tr>
        </thead>
        <tbody>
        @foreach($fixedAssets as $key => $fixedAsset)
            <tr>
                <td @php echo $cellDays; @endphp>{{ $key + 1 }}</td>
                <td @php echo $cellDays; @endphp>{{ $fixedAsset->name }}</td>
                <td @php echo $cellDays; @endphp>{{ $fixedAsset->initial_value }}</td>
                <td @php echo $cellDays; @endphp>{{ $fixedAsset->useful_life_years }} il</td>
                <td @php echo $cellDays; @endphp>{{ $fixedAsset->getResidualValue($selectedYear - 1) < 0 ? 0 : $fixedAsset->getResidualValue($selectedYear - 1) }}</td>
                <td @php echo $cellDays; @endphp>{{ $fixedAsset->percentage }}%</td>
                <td @php echo $cellDays; @endphp>{{ $fixedAsset->initial_value * ($fixedAsset->percentage / 100) }}</td>
                <td @php echo $cellDays; @endphp>{{ $fixedAsset->getResidualValue($selectedYear) < 0 ? 0 : $fixedAsset->getResidualValue($selectedYear) }}</td>
                <td @php echo $cellDays; @endphp>{{ \Carbon\Carbon::parse($fixedAsset->start_of_using)->format('d.m.Y') }}</td>
                <td @php echo $cellDays; @endphp></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
