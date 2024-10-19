<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Əmək haqqının hesablanması</title>
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
            <th style="width: 450px;font-size: 16px;font-weight: bold;font-family: 'Times New Roman', Times, serif">
                "{{ $attendanceLogs->first()->company?->company_name }}" -
                VÖEN: {{ $attendanceLogs->first()->company?->tax_id_number ?? '' }}
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td style="width: 450px;font-size: 14px;font-weight: bold;font-family: 'Times New Roman', Times, serif">
                {{ ucfirst(\Carbon\Carbon::parse($attendanceLogs->first()->year.'-'.$attendanceLogs->first()->month.'-01')->isoFormat('MMMM Y')) }}
                - üzrə əmək haqqının hesablanması
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="width: 400px;font-size: 14px;font-weight: bold;font-family: 'Times New Roman', Times, serif">
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
            <th @php echo $cellHeadClass200Width; @endphp rowspan="3">Əmək haqqı</th>
            <th @php echo $cellHeadClass200Width; @endphp rowspan="3">Ayda işlədiyi günləri</th>
            <th @php echo $cellHeadClass200Width; @endphp rowspan="3"> İstirahət və bayram&nbsp;&nbsp;&nbsp;günləri</th>
            <th @php echo $cellHeadClass200Width; @endphp rowspan="3">İşlənmiş saatlar</th>
        </tr>
        <tr>
        </tr>
        </thead>
        <tbody>
        @php
            $totalMonthWorkDays = 0;
            $totalCelebrationDays = 0;
            $totalMonthWorkDayHours = 0;
        @endphp
        @foreach($attendanceLogs as $key => $attendanceLog)
            <tr>
                <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                    {{ $key + 1 }}
                </td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ $attendanceLog->employee?->name . ' ' . $attendanceLog->employee?->surname }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ $attendanceLog->employee?->position?->name }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ $attendanceLog->employee?->salary }}</td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $attendanceLog->month_work_days }}</td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $attendanceLog->celebration_days }}</td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $attendanceLog->month_work_day_hours }}</td>
            </tr>
            @php
                $totalMonthWorkDays += $attendanceLog->month_work_days;
                $totalCelebrationDays += $attendanceLog->celebration_days;
                $totalMonthWorkDayHours += $attendanceLog->month_work_day_hours;
            @endphp
        @endforeach
        <tr>
            <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif"
                colspan="3">
                CƏMİ
            </td>
            <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $totalMonthWorkDays }}</td>
            <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $totalCelebrationDays }}</td>
            <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">{{ $totalMonthWorkDayHours }}</td>
        </tr>
        </tbody>
    </table>
@endif
</body>
</html>
