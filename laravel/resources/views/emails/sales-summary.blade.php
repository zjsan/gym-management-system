<!DOCTYPE html>
<html>
<head>
    <title>Sales Summary Report</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <h2>Gym Financial Sales Summary</h2>
    <p>Report Period: <strong>{{ $summaryData['period_label'] }}</strong></p>
    <p style="font-size: 0.85em; color: #6b7280;">Generated At: {{ $summaryData['generated_at'] }}</p>
    
    <table border="0" cellpadding="8" cellspacing="0" style="width: 100%; max-width: 500px; background: #f9f9f9; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 6px;">
        <tr>
            <td><strong>Total Transactions:</strong></td>
            <td>{{ $summaryData['total_transactions'] }}</td>
        </tr>
        <tr>
            <td><strong>Total Revenue Collected:</strong></td>
            <td><span style="font-size: 1.2em; color: #16a34a; font-weight: bold;">₱{{ number_format($summaryData['total_revenue'], 2) }}</span></td>
        </tr>
    </table>

    <h3>Breakdown by Category</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; max-width: 500px; border-collapse: collapse; border-color: #ddd;">
        <tr style="background: #f3f4f6;">
            <th style="text-align: left;">Category</th>
            <th style="text-align: center;">Count</th>
            <th style="text-align: right;">Subtotal</th>
        </tr>
        @foreach($summaryData['category_breakdown'] as $category)
        <tr>
            <td>{{ $category['label'] }}</td>
            <td style="text-align: center;">{{ $category['count'] }}</td>
            <td style="text-align: right;">₱{{ number_format($category['total'], 2) }}</td>
        </tr>
        @endforeach
    </table>

    <p style="margin-top: 30px; font-size: 0.85em; color: #6b7280;">
        Automated financial report generated from your Gym Management System via Gmail SMTP.
    </p>
</body>
</html>