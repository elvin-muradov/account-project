<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tabel</title>
</head>
<body>
@php
    $cellHeadClass200Width = 'style="border-collapse: collapse;width:200px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass2 = 'style="border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellDays = 'style="border-collapse: collapse;font-weight:bold;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;text-align:center;vertical-align:middle;background-color:#FFFF00"';
@endphp
@if($attendanceLogs->count() > 0)
    <table style="border-collapse: collapse;border:2px solid black">
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
                <th id="thclass{{ $key+1 }}"
                    style="width: 30px;text-align: center;border-collapse: collapse;border: 2px solid black">{{ $key+1 }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($attendanceLogs as $key => $attendanceLog)
            <tr>
                <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black">
                    {{ $key + 1 }}
                </td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black">{{ $attendanceLog->employee?->name . ' ' . $attendanceLog->employee?->surname }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black">{{ $attendanceLog->employee?->position?->name }}</td>
                @foreach($attendanceLog->days as $value)
                    <td @php echo $cellDays; @endphp @if(gettype($value['status']) == 'integer') style="text-align: center;vertical-align: middle;background-color: #fff !important;" @endif>
                        @if($value['status'] == 'REST_DAY')
                            İ
                        @elseif($value['status'] == 'NULL_DAY')
                        @elseif($value['status'] == 'DAY_OF_CELEBRATION')
                            B
                        @elseif($value['status'] == 'LEAVING_WORK')
                            İ/Ç
                        @elseif($value['status'] == 'ILLNESS')
                            X
                        @elseif($value['status'] == 'BUSINESS_TRIP')
                            E
                        @elseif($value['status'] == 'HOLIDAY')
                            M
                        @else
                            {{ $value['status'] }}
                        @endif
                    </td>
                @endforeach
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle">{{ $attendanceLog->month_work_days }}</td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle">{{ $attendanceLog->celebration_days }}</td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle">{{ $attendanceLog->month_work_day_hours }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
