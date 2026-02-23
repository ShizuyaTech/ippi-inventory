<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customers Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #4a5568; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .footer { margin-top: 20px; font-size: 9px; text-align: center; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Material Control System</h2>
        <h3>Customers Report</h3>
        <p>Generated: {{ date('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Code</th>
                <th width="25%">Name</th>
                <th width="18%">Contact Person</th>
                <th width="15%">Phone</th>
                <th width="20%">Email</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr>
                <td>{{ $customer->customer_code }}</td>
                <td>{{ $customer->customer_name }}</td>
                <td>{{ $customer->contact_person }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->is_active ? 'Active' : 'Inactive' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© 2026 Material Control System - Stamping Manufacturing</p>
    </div>
</body>
</html>
