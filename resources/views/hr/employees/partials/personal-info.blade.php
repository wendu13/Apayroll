<!-- Personal Info Tab -->
<div class="tab-pane fade show active" id="personal" role="tabpanel">
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Personal Details</div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Employee Number</th>
                                <td>{{ $employee->employee_number }}</td>
                                <th>Department</th>
                                <td>{{ $employee->department }}</td>
                            </tr>
                            <tr>
                                <th>Position</th>
                                <td>{{ $employee->position }}</td>
                                <th>Last Name</th>
                                <td>{{ $employee->last_name }}</td>
                            </tr>
                            <tr>
                                <th>First Name</th>
                                <td>{{ $employee->first_name }}</td>
                                <th>Middle Name</th>
                                <td>{{ $employee->middle_name }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $employee->address }}</td>
                                <th>ZIP Code</th>
                                <td>{{ $employee->zip_code }}</td>
                            </tr>
                            <tr>
                                <th>Contact</th>
                                <td>{{ $employee->contact }}</td>
                                <th>Email</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $employee->phone }}</td>
                                <th>Birthplace</th>
                                <td>{{ $employee->birthplace }}</td>
                            </tr>
                            <tr>
                                <th>Birthdate</th>
                                <td>{{ $employee->birthdate }}</td>
                                <th>Age</th>
                                <td>{{ $employee->age }}</td>
                            </tr>
                            <tr>
                                <th>Sex</th>
                                <td>{{ $employee->gender }}</td>
                                <th>Civil Status</th>
                                <td>{{ $employee->civil_status }}</td>
                            </tr>
                            <tr>
                                <th>Religion</th>
                                <td>{{ $employee->religion }}</td>
                                <th>Nationality</th>
                                <td>{{ $employee->nationality }}</td>
                            </tr>
                            <tr>
                                <th>SSS No.</th>
                                <td>{{ $employee->sss }}</td>
                                <th>PhilHealth No.</th>
                                <td>{{ $employee->philhealth }}</td>
                            </tr>
                            <tr>
                                <th>PAG-IBIG No.</th>
                                <td>{{ $employee->pagibig }}</td>
                                <th>TIN No.</th>
                                <td>{{ $employee->tin }}</td>
                            </tr>
                            <tr>
                                <th>Bank Name</th>
                                <td>{{ $employee->bank_name }}</td>
                                <th>Bank Account</th>
                                <td>{{ $employee->bank_account }}</td>
                            </tr>
                            <tr>
                                <th>Basic Rate</th>
                                <td>{{ number_format($employee->basic_rate, 2) }} ({{ ucfirst($employee->rate_type) }})</td>
                                <th>Allowance</th>
                                <td>{{ number_format($employee->allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Other Pay</th>
                                <td colspan="3">{{ number_format($employee->other_pay, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column (Photo) -->
        <div class="col-md-4 text-center">
            @if ($employee->photo)
                <img src="{{ asset('storage/' . $employee->photo) }}" 
                     alt="Profile Photo" 
                     class="img-thumbnail mb-2"
                     style="width:150px; height:150px; object-fit:cover;">
            @else
                <p>No photo uploaded</p>
            @endif

            <div class="mt-2">
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-secondary">
                    Edit
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    background: #f8f9fa;        /* light gray bg sa labels */
    width: 20%;                 /* mas maliit space para labels */
    padding: 4px 8px;           /* mas dikit label at value */
    text-align: left;
    vertical-align: middle;
    white-space: nowrap;        /* para hindi bumaliktad ang text */
}

.table td {
    padding: 4px 10px;          /* mas maliit padding */
    vertical-align: middle;
}

.table td + th {
    border-left: 2px solid #dee2e6; /* separator sa gitna ng pares */
}

.table th, .table td {
    font-size: 14px;
    line-height: 1.4;
}
</style>
