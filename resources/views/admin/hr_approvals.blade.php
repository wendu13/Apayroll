<!DOCTYPE html>
<html>
<head>
    <title>HR Account Approvals</title>
</head>
<body>
    <h2>Pending HR Registrations</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($pendingHrs->isEmpty())
        <p>No pending HR accounts.</p>
    @else
        <table border="1" cellpadding="8">
            <thead>
                <tr>
                    <th>Employee #</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingHrs as $hr)
                    <tr>
                        <td>{{ $hr->employee_number }}</td>
                        <td>{{ $hr->first_name }} {{ $hr->last_name }}</td>
                        <td>{{ $hr->department }}</td>
                        <td>{{ $hr->position }}</td>
                        <td>{{ $hr->email }}</td>
                        <td>
                            <form action="/admin/hr-approvals/{{ $hr->id }}/approve" method="POST" style="display:inline">
                                @csrf
                                <button type="submit">Approve</button>
                            </form>
                            <form action="/admin/hr-approvals/{{ $hr->id }}/reject" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Reject and delete this account?')">Reject</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <br>
    <a href="/admin/dashboard">Back to Dashboard</a>
</body>
</html>
