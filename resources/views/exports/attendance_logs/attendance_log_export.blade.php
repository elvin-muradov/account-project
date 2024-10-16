<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tabel</title>
</head>
<body>
@php
    $cellHeadClass200Width = 'style="width:200px;border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:12px;
  overflow:hidden;padding:10px 5px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass2 = 'style="border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:12px;
  overflow:hidden;padding:10px 5px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
@endphp
<table style="border:2px solid black">
    <thead>
    <tr>
        <th @php echo $cellHeadClass2; @endphp rowspan="3">№</th>
        <th @php echo $cellHeadClass200Width; @endphp rowspan="3">Soyadı, Adı</th>
        <th @php echo $cellHeadClass200Width; @endphp rowspan="3">Vəzifəsi</th>
        <th @php echo $cellHeadClass2; @endphp colspan="31" rowspan="2">AYIN&nbsp;&nbsp;&nbsp;GÜNLƏRİ</th>
        <th @php echo $cellHeadClass200Width; @endphp rowspan="3">Ayda işlədiyi günləri</th>
        <th @php echo $cellHeadClass200Width; @endphp rowspan="3"> İstirahət və bayram&nbsp;&nbsp;&nbsp;günləri</th>
        <th @php echo $cellHeadClass200Width; @endphp rowspan="3">İşlənmiş saatlar</th>
    </tr>
    <tr>
    </tr>
    <tr>
        @foreach($attendanceLogs->first()->days as $key => $value)
            <th>{{ $key+1 }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
        @foreach($attendanceLogs as $attendanceLog)
            <tr>
                <td>1</td>
                <td>XXXXXXXXX</td>
                <td>XXXXXX</td>
                <td>8</td>
                <td>i</td>
                <td>i</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>7</td>
                <td>i</td>
                <td>i</td>
                <td>B</td>
                <td>B</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>i</td>
                <td>i</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>i</td>
                <td>i</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>8</td>
                <td>i</td>
                <td>i</td>
                <td>19</td>
                <td>12</td>
                <td>151</td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
