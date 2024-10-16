<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tabel</title>
</head>
<body>
@php
    $cellHeadClass300Width = 'style="border-collapse: collapse;width:300px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass200Width = 'style="border-collapse: collapse;width:200px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass2 = 'style="border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellDays = 'style="border-collapse: collapse;font-weight:bold;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;text-align:center;vertical-align:middle;background-color:#FFFF00"';
@endphp
@if($attendanceLogs->count() > 0)
    <table>
        <thead>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th style="width: 400px;font-size: 18px;font-weight: bold">
                "{{ $attendanceLogs->first()->company?->company_name }}"
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th style="width: 400px;font-size: 18px;font-weight: bold">
                Təsdiq edirəm
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="width: 400px;font-size: 18px;font-weight: bold">
                {{ ucfirst(\Carbon\Carbon::parse($attendanceLogs->first()->year.'-'.$attendanceLogs->first()->month.'-01')->isoFormat('MMMM Y')) }}
                - TABEL
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="width: 400px;font-size: 18px;font-weight: bold">
                Direktor: {{ $attendanceLogs->first()->company?->director?->name.' '.$attendanceLogs->first()->company?->director?->surname }}
            </td>
        </tr>
        </tbody>
    </table>

    <table style="border-collapse: collapse;border:4px solid black">
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
                <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                    {{ $key + 1 }}
                </td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ $attendanceLog->employee?->name . ' ' . $attendanceLog->employee?->surname }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ $attendanceLog->employee?->position?->name }}</td>
                @foreach($attendanceLog->days as $value)
                    @if($value['status'] == 'REST_DAY')
                        <td @php echo $cellDays; @endphp>
                            İ
                        </td>
                    @elseif($value['status'] == 'NULL_DAY')
                        <td>
                        </td>
                    @elseif($value['status'] == 'DAY_OF_CELEBRATION')
                        <td @php echo $cellDays; @endphp>
                            B
                        </td>
                    @elseif($value['status'] == 'LEAVING_WORK')
                        <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                            İ/Ç
                        </td>
                    @elseif($value['status'] == 'ILLNESS')
                        <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                            X
                        </td>
                    @elseif($value['status'] == 'BUSINESS_TRIP')
                        <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                            E
                        </td>
                    @elseif($value['status'] == 'HOLIDAY')
                        <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                            M
                        </td>
                    @else
                        <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                            {{ $value['status'] }}
                        </td>
                    @endif
                @endforeach
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $attendanceLog->month_work_days }}</td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $attendanceLog->celebration_days }}</td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $attendanceLog->month_work_day_hours }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
