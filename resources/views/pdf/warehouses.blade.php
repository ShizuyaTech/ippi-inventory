<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Warehouses Report</title>
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
        <h3>Warehouses Report</h3>
        <p>Generated: {{ date('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Code</th>
                <th width="30%">Name</th>
                <th width="35%">Location</th>
                <th width="10%">Capacity</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($warehouses as $warehouse)
            <tr>
                <td>{{ $warehouse->warehouse_code }}</td>
                <td>{{ $warehouse->warehouse_name }}</td>
                <td>{{ $warehouse->location }}</td>
                <td style="text-align: right;">{{ number_format($warehouse->capacity) }}</td>
                <td>{{ $warehouse->is_active ? 'Active' : 'Inactive' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© 2026 Material Control System - Stamping Manufacturing</p>
    </div>
</body>
</html>
