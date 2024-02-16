<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>İdxal Excel Export</title>
</head>
<body>
@php
    $mainStatuses = [
        [
            'value' => 'active',
            'label' => trans('messages.po.main_statuses.active')
        ],
        [
            'value' => 'completed',
            'label' => trans('messages.po.main_statuses.completed')
        ],
        [
            'value' => 'cancelled',
            'label' => trans('messages.po.main_statuses.cancelled')
        ],
        [
            'value' => 'in_signing',
            'label' => trans('messages.po.main_statuses.in_signing')
        ]
    ];

    $cellClass = 'style="word-wrap:break-word;border: 2px solid #000;font-style: italic;font-weight: bold;text-align: center;vertical-align: middle;background-color: #9BC2E6;"';
    $wordWrap = 'style="word-wrap:break-word;"'
@endphp


<table>
    <tbody>
    <tr>
        <td @php echo $cellClass; @endphp rowspan="2">N
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Şirkət adı
        </td>
        <td @php echo $cellClass; @endphp colspan="2">Tarixi
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Sorğu №
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Invoice dəyəri
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Gömrük dəyəri
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Statistik dəyər
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Məzənnə
        </td>
        <td @php echo $cellClass; @endphp colspan="3">Gömrük rüsumları
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Materiallar
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Metr
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Vahidin qiyməti
        </td>
        <td @php echo $cellClass; @endphp rowspan="2">Məbləğ
        </td>
    </tr>
    <tr>
        <td @php echo $cellClass; @endphp>Gəlmə tarixi</td>
        <td @php echo $cellClass; @endphp>Gömrük vaxtı</td>
        <td @php echo $cellClass; @endphp>N</td>
        <td @php echo $cellClass; @endphp>%</td>
        <td @php echo $cellClass; @endphp>Məbləğ</td>
    </tr>
    <tr>
        <td rowspan="8">1</td>
        <td @php echo $wordWrap; @endphp rowspan="8">İSA TEKSTİL SANAYİ VE TİCARET LTD.ŞTİ</td>
        <td rowspan="8">04.01.2023</td>
        <td rowspan="8">05.01.2023</td>
        <td rowspan="8">01231000001246</td>
        <td>78878,80</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>1.7</td>
        <td>2</td>
        <td>&nbsp;</td>
        <td>600,00</td>
        <td>Tül Kumaş 300 - 5804109000 - Tül</td>
        <td>18520</td>
        <td>1,60</td>
        <td>29632,00</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>19</td>
        <td>&nbsp;</td>
        <td>0,00</td>
        <td>Dekor Kumaş 300 - 5407520000 - Dekor neylon,poliamid</td>
        <td>18520</td>
        <td>1,60</td>
        <td>29632,00</td>
    </tr>
    <tr style="height: 30px;">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>20</td>
        <td>15%</td>
        <td>27517,04</td>
        <td>Döşəməlik Kumaş 240 - 5407730000-Divan üzlüyü</td>
        <td>18520</td>
        <td>1,60</td>
        <td>29632,00</td>
    </tr>
    <tr style="height: 29px;">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>32</td>
        <td>18%</td>
        <td>37973,53</td>
        <td>Çarşaflı Kumaş 240 - 5407820000-Çit parça (boyanmış)
        </td>
        <td>18520</td>
        <td>1,60</td>
        <td>29632,00</td>
    </tr>
    <tr>
        <td>Fərq</td>
        <td>&nbsp;</td>
        <td>29031,20</td>
        <td>&nbsp;</td>
        <td>75</td>
        <td>0</td>
        <td>90,00</td>
        <td>Kumaş 220 - 5801909000 - Geyim</td>
        <td>18520</td>
        <td>1,60</td>
        <td>29632,00</td>
    </tr>
    <tr style="height: 20px;">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>85</td>
        <td>18%</td>
        <td>16,20</td>
        <td>Kartela - 4911101000-Kataloq və aksesuar</td>
        <td>18520</td>
        <td>1,60</td>
        <td>29632,00</td>
    </tr>
    <tr style="height: 21px;">
        <td>Çəki net</td>
        <td>&nbsp;</td>
        <td>19347,00</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td @php echo $cellClass; @endphp colspan="5">Cəmi</td>
        <td @php echo $cellClass; @endphp>&nbsp;</td>
        <td @php echo $cellClass; @endphp>İstanbul</td>
        <td @php echo $cellClass; @endphp>FCA</td>
        <td @php echo $cellClass; @endphp>120</td>
        <td @php echo $cellClass; @endphp>&nbsp;</td>
        <td @php echo $cellClass; @endphp>&nbsp;</td>
        <td @php echo $cellClass; @endphp>66196,77</td>
        <td @php echo $cellClass; @endphp>511005230101035</td>
        <td @php echo $cellClass; @endphp>&nbsp;</td>
        <td @php echo $cellClass; @endphp>&nbsp;</td>
        <td @php echo $cellClass; @endphp>78878,80</td>
    </tr>
    </tbody>
</table>
</body>
</html>
