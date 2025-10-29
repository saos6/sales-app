<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>請求一覧</title>
    <style>
        body { font-family: "ipaexg", sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>請求一覧</h1>
    <table>
        <thead>
            <tr>
                <th>区分</th>
                <th>伝票番号</th>
                <th>顧客ID</th>
                <th>顧客名</th>
                <th>日付</th>
                <th>税抜金額</th>
                <th>消費税</th>
                <th>税込金額</th>
            </tr>
        </thead>
        <tbody>
            @foreach($claims as $claim)
                <tr>
                    <td>{{ $claim->type }}</td>
                    <td>{{ $claim->voucher_no }}</td>
                    <td>{{ $claim->cust_id }}</td>
                    <td>{{ $claim->cust_name }}</td>
                    <td>{{ $claim->voucher_date }}</td>
                    <td style="text-align: right;">{{ number_format($claim->subtotal) }}</td>
                    <td style="text-align: right;">{{ number_format($claim->tax) }}</td>
                    <td style="text-align: right;">{{ number_format($claim->total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
