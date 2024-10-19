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
    $cellHeadClass100Width = 'style="border-collapse: collapse;width:100px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
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
            <td style="width: 400px;font-size: 14px;font-weight: bold;font-family: 'Times New Roman', Times, serif">
                Direktor: {{ $attendanceLogs->first()->company?->director?->name.' '.$attendanceLogs->first()->company?->director?->surname }}
            </td>
        </tr>
        </tbody>
    </table>

    <table style="border-collapse: collapse;border:4px solid black">
        <thead>
        <tr>
            <th @php echo $cellHeadClass2; @endphp>№</th>
            <th @php echo $cellHeadClass200Width; @endphp>Soyadı, Adı</th>
            <th @php echo $cellHeadClass100Width; @endphp>Vəzifəsi</th>
            <th @php echo $cellHeadClass100Width; @endphp>Maaş</th>
            <th @php echo $cellHeadClass100Width; @endphp>Ayda iş <br/>saatları</th>
            <th @php echo $cellHeadClass100Width; @endphp>Faktiki <br/>iş saatları</th>
            <th @php echo $cellHeadClass100Width; @endphp>Hesablanmış <br/>əmək haqqı</th>
            <th @php echo $cellHeadClass100Width; @endphp>Mükafat</th>
            <th @php echo $cellHeadClass100Width; @endphp>Məzuniyyət</th>
            <th @php echo $cellHeadClass100Width; @endphp>CƏMİ</th>
            <th @php echo $cellHeadClass100Width; @endphp>Gəlir <br/>Vergisi (14%)</th>
            <th @php echo $cellHeadClass100Width; @endphp>Pensiya <br/>Fondu (10%)</th>
            <th @php echo $cellHeadClass100Width; @endphp>İ.S.H (0.5%)</th>
            <th @php echo $cellHeadClass100Width; @endphp>İ.T.S.H (2%)</th>
            <th @php echo $cellHeadClass100Width; @endphp>Cəmi <br/>Tutulmuşdur</th>
            <th @php echo $cellHeadClass100Width; @endphp>Ödəniləsi <br/>Məbləğ</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totalMonthWorkDays = 0;
            $totalCelebrationDays = 0;
            $totalMonthWorkDayHours = 0;
        @endphp
        @foreach($attendanceLogs as $key => $attendanceLog)
            @php
                $holidaysCount = 0;

                foreach ($attendanceLog->days as $day){
                    if ($day['status'] == \App\Enums\AttendanceLogDayTypes::DEFAULT_HOLIDAY->value) {
                        $holidaysCount+=1;
                    }
                }

                $awardedSalary = 0.00;
                $calculatedSalary = $attendanceLog->employee?->salary / $attendanceLog->month_work_hours * $attendanceLog->month_work_day_hours;
                $holidaySalary = \App\Models\Company\AttendanceLog::query()->where('employee_id', $attendanceLog->employee_id)->sum('salary')/12/30.4*$holidaysCount;
                $totalSalary = $awardedSalary + $calculatedSalary + $holidaySalary;

                $gTax = 0;
                $pFund = 0;
                $iSH = 0;
                $iTSH = 0;

                if($totalSalary > 8000){
                    $gTax = number_format(($totalSalary - 8000)*0.14, 2, ',', '');
                }

                if($totalSalary <= 8000){
                    $gTax = 0;
                }

                if($totalSalary <= 200){
                    $pFund = $totalSalary * 0.03;
                }

                if($totalSalary > 200){
                    $pFund = 6 + ($totalSalary - 200) * 0.1;
                }

                $iSH = $totalSalary * 0.005;

                if($totalSalary <= 8000){
                    $iTSH =  $totalSalary * 0.02;
                }

                if($totalSalary > 8000){
                    $iTSH =  160 + ($totalSalary - 8000) * 0.005;
                }
            @endphp
            <tr>
                <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                    {{ $key + 1 }}
                </td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ $attendanceLog->employee?->name . ' ' . $attendanceLog->employee?->surname }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ $attendanceLog->employee?->position?->name }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ $attendanceLog->employee?->salary }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                    {{ $attendanceLog->month_work_hours }}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {{ $attendanceLog->month_work_day_hours }}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! $calculatedSalary !!}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {{ $awardedSalary }}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($holidaySalary, 2, ',', '') !!}
                </td>
                <td style="font-weight: bold;border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($totalSalary, 2, ',', '')  !!}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($gTax, 2, ',', '') !!}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($pFund, 2, ',', '') !!}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($iSH, 2, ',', '') !!}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($iTSH, 2, ',', '') !!}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($gTax + $pFund + $iSH + $iTSH, 2, ',', '') !!}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($totalSalary - ($gTax + $pFund + $iSH + $iTSH), 2, ',', '') !!}
                </td>
            </tr>
            @php
                $totalMonthWorkDays += $attendanceLog->month_work_days;
                $totalCelebrationDays += $attendanceLog->celebration_days;
                $totalMonthWorkDayHours += $attendanceLog->month_work_day_hours;
            @endphp
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
