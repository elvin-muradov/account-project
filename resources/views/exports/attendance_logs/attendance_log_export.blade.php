<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tabel</title>
</head>
<body>
@php
    $cellHeadClass300Width = 'style="width:300px;border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:12px;
  overflow:hidden;padding:10px 5px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $cellHeadClass2 = 'style="border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:12px;
  overflow:hidden;padding:10px 5px;word-break:normal;font-weight:bold;text-align:center;vertical-align:middle"';
    $rotated = 'style="-ms-writing-mode: tb-rl;-webkit-writing-mode: vertical-rl;writing-mode: vertical-rl;transform: rotate(180deg);white-space: nowrap;"'
@endphp
<table style="border:2px solid black">
    <thead>
    <tr>
        <th @php echo $cellHeadClass2; @endphp rowspan="3">№</th>
        <th @php echo $cellHeadClass300Width; @endphp rowspan="3">Soyadı, Adı</th>
        <th @php echo $cellHeadClass300Width; @endphp rowspan="3">Vəzifəsi</th>
        <th @php echo $cellHeadClass2; @endphp colspan="31" rowspan="2">AYIN&nbsp;&nbsp;&nbsp;GÜNLƏRİ</th>
        <th @php echo $rotated; @endphp rowspan="3">Ayda işlədiyi günləri</th>
        <th @php echo $cellHeadClass300Width; @endphp rowspan="3"> İstirahət və bayram&nbsp;&nbsp;&nbsp;günləri</th>
        <th @php echo $cellHeadClass300Width; @endphp rowspan="3">İşlənmiş saatlar</th>
    </tr>
    <tr>
    </tr>
    <tr>
        <th>1</th>
        <th>2</th>
        <th>3</th>
        <th>4</th>
        <th>5</th>
        <th>6</th>
        <th>7</th>
        <th>8</th>
        <th>9</th>
        <th>10</th>
        <th>11</th>
        <th>12</th>
        <th>13</th>
        <th>14</th>
        <th>15</th>
        <th>16</th>
        <th>17</th>
        <th>18</th>
        <th>19</th>
        <th>20</th>
        <th>21</th>
        <th>22</th>
        <th>23</th>
        <th>24</th>
        <th>25</th>
        <th>26</th>
        <th>27</th>
        <th>28</th>
        <th>29</th>
        <th>30</th>
        <th>31</th>
    </tr>
    </thead>
    <tbody>
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
    <tr>
        <td>2</td>
        <td>XXXXXXXXX</td>
        <td>Direktor</td>
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
    </tbody>
</table>
</body>
</html>
