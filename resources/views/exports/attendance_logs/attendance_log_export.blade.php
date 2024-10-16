<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tabel</title>
</head>
<body>
@php
    $cellHeadClass200Width = 'style="width:200px;border-color:black;border-style:solid;border-width:1px;font-family:Times New Roman, Times, serif;font-size:12px;
  overflow:hidden;padding:10px 5px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass2 = 'style="border-color:black;border-style:solid;border-width:1px;font-family:Times New Roman, Times, serif;font-size:12px;
  overflow:hidden;padding:10px 5px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellDays = 'style="border-color:black;border-style:solid;border-width:1px;font-family:Times New Roman, Times, serif;font-size:12px;
  overflow:hidden;padding:10px 5px;word-break:normal;text-align:center;vertical-align:middle;background-color:#FFFF00"';
@endphp
@if($attendanceLogs->count() > 0)
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
                <th id="thclass{{ $key+1 }}" style="width: 30px;text-align: center">{{ $key+1 }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($attendanceLogs as $attendanceLog)
            <tr>
                <td style="font-weight: bold">1</td>
                <td>{{ $attendanceLog->employee?->name . ' ' . $attendanceLog->employee?->surname }}</td>
                <td>{{ $attendanceLog->employee?->position?->name }}</td>
                @foreach($attendanceLog->days as $value)
                    <td @php echo $cellDays; @endphp>
                        {{ $value['status'] }}
                    </td>
                @endforeach
                <td>{{ $attendanceLog->month_work_days }}</td>
                <td>{{ $attendanceLog->celebration_days }}</td>
                <td>{{ $attendanceLog->month_work_day_hours }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
