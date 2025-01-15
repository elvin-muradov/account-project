<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kassa əməliyyatları</title>
</head>
<body>
@php
    $cellHeadClass300Width = 'style="border-collapse: collapse;width:300px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass200Width = 'style="border-collapse: collapse;width:200px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass100Width = 'style="border-collapse: collapse;width:100px;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass2 = 'style="border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellDays = 'style="border-collapse: collapse;font-weight:bold;border: 2px solid black;font-family:Times New Roman, Times, serif;font-size:12px;word-break:normal;text-align:center;vertical-align:middle;background-color:#FFFF00"';

            $typeOfTreasures = [
            [
                'value' => 'CASH',
                'label' => trans('treasure_types.CASH')
            ],
            [
                'value' => 'TRANSFER',
                'label' => trans('treasure_types.TRANSFER')
            ],
            [
                'value' => 'VAT_DEPOSIT',
                'label' => trans('treasure_types.VAT_DEPOSIT')
            ]
        ];
        $typeOfTransactions = [
            [
                'value' => 'INCOME',
                'label' => trans('transaction_types.INCOME')
            ],
            [
                'value' => 'EXPENSE',
                'label' => trans('transaction_types.EXPENSE')
            ],
            [
                'value' => 'REFUND',
                'label' => trans('transaction_types.REFUND')
            ]
        ];
@endphp
@if($transactions->count() > 0)
    <table>
        <thead>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th style="width: 450px;font-size: 16px;font-weight: bold;font-family: 'Times New Roman', Times, serif">
                "{{ $transactions->first()->company?->company_name }}" -
                VÖEN: {{ $transactions->first()->company?->tax_id_number ?? '' }}
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
                Kassa əməliyyatları -
                {{ ucfirst(\Carbon\Carbon::parse($req['year'].'-'.$req['month'].'-01')->isoFormat('MMMM Y')) }}
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="width: 400px;font-size: 14px;font-weight: bold;font-family: 'Times New Roman', Times, serif">
                Direktor: {{ $transactions->first()->company?->director?->name.' '.$transactions->first()->company?->director?->surname }}
            </td>
        </tr>
        </tbody>
    </table>

    <table style="border-collapse: collapse;border:4px solid black">
        <thead>
        <tr>
            <th @php echo $cellHeadClass2; @endphp>№</th>
            <th @php echo $cellHeadClass200Width; @endphp>Ay</th>
            <th @php echo $cellHeadClass100Width; @endphp>Tarix</th>
            <th @php echo $cellHeadClass100Width; @endphp>Hesab növü</th>
            <th @php echo $cellHeadClass100Width; @endphp>Əməliyyat növü</th>
            <th @php echo $cellHeadClass100Width; @endphp>Təyinat</th>
            <th @php echo $cellHeadClass100Width; @endphp>Açıqlama</th>
            <th @php echo $cellHeadClass100Width; @endphp>Giriş</th>
            <th @php echo $cellHeadClass100Width; @endphp>Çıxış</th>
            <th @php echo $cellHeadClass100Width; @endphp>Qalıq</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $key => $transaction)
            <tr>
                <td style="font-weight: bold;text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                    {{ $key + 1 }}
                </td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                    {{ ucfirst(\Carbon\Carbon::parse($req['year'].'-'.$req['month'].'-01')->isoFormat('MMMM')) }}
                </td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d.m.Y') }}
                </td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ getLabelValue($transaction->type_of_treasure, $typeOfTreasures)['label'] }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">{{ getLabelValue($transaction->type_of_transaction, $typeOfTransactions)['label'] }}</td>
                <td style="text-align: center;vertical-align: middle;border-collapse: collapse;border: 2px solid black;font-family:Times New Roman, Times, serif">
                    {{ $transaction->destination }}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {{ $transaction->note }}
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    @if($transaction->type_of_transaction == "INCOME" ||
                        $transaction->type_of_transaction == "REFUND")
                        {!! number_format($transaction->amount, 2, ',', '') !!}
                    @else
                        0
                    @endif
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    @if($transaction->type_of_transaction == "EXPENSE")
                        {!! number_format($transaction->amount, 2, ',', '') !!}
                    @endif
                </td>
                <td style="border-collapse: collapse;border: 2px solid black;text-align: center;vertical-align: middle;font-family:Times New Roman, Times, serif">
                    {!! number_format($transaction->treasure_balance, 2, ',', '') !!}
                </td>
        @endforeach
        </tbody>
    </table>
@endif
</body>
</html>
