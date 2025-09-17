@extends('layouts.hr')

@section('page-title', 'Deduction Types')

@section('hr-content')

<table class="table table-hover align-middle">
    <thead>
        <tr>
            <th>Deduction Type</th>
            <th>Last Update</th>
        </tr>
    </thead>
    <tbody>
        <!-- Late and Absences -->
        <tr data-bs-toggle="modal" data-bs-target="#lateAbsenceModal" style="cursor:pointer;">
            <td>Late and Absences</td>
            <td>{{ $lateAbsence?->updated_at?->format('M. d, Y h:i A') ?? 'Not yet set' }}</td>
        </tr>

        <!-- Loans and Advances -->
        <tr data-bs-toggle="modal" data-bs-target="#loanAdvancesModal" style="cursor:pointer;">
            <td>Loans and Advances</td>
            <td>{{ $loanAdvances?->updated_at?->format('M. d, Y h:i A') ?? 'Not yet set' }}</td>
        </tr>

        <!-- SSS Contribution -->
        <tr data-bs-toggle="modal" data-bs-target="#sssModal" style="cursor:pointer;">
            <td>SSS Contribution</td>
            <td>
                {{ $sssBrackets && count($sssBrackets) > 0 
                    ? $sssBrackets->last()->updated_at->format('M. d, Y h:i A') 
                    : 'Not yet set' }}
            </td>
        </tr>

        <!-- PHIC Contribution -->
        <tr data-bs-toggle="modal" data-bs-target="#phicModal" style="cursor:pointer;">
            <td>PHIC Contribution</td>
            <td>{{ $phicSetting?->updated_at?->format('M. d, Y h:i A') ?? 'Not yet set' }}</td>
        </tr>

        <!-- HDMF Contribution -->
        <tr data-bs-toggle="modal" data-bs-target="#hdmfModal" style="cursor:pointer;">
            <td>HDMF Contribution</td>
            <td>{{ $hdmfSetting?->updated_at?->format('M. d, Y h:i A') ?? 'Not yet set' }}</td>
        </tr>

        <!-- SSS Loan -->
        <tr data-bs-toggle="modal" data-bs-target="#sssLoanModal" style="cursor:pointer;">
            <td>SSS Loan</td>
            <td>{{ $sssLoanSetting?->updated_at?->format('M. d, Y h:i A') ?? 'Not yet set' }}</td>
        </tr>

        <!-- HDMF Loan -->
        <tr data-bs-toggle="modal" data-bs-target="#hdmfLoanModal" style="cursor:pointer;">
            <td>HDMF Loan</td>
            <td>{{ $hdmfLoanSetting?->updated_at?->format('M. d, Y h:i A') ?? 'Not yet set' }}</td>
        </tr>

        <!-- Income Tax -->
        <tr data-bs-toggle="modal" data-bs-target="#taxModal" style="cursor:pointer;">
            <td>Income Tax</td>
            <td>
                {{ $taxBrackets && count($taxBrackets) > 0 
                    ? $taxBrackets->last()->updated_at->format('M. d, Y h:i A') 
                    : 'Not yet set' }}
            </td>
        </tr>

    </tbody>
</table>

<!-- Modal for Late and Absences -->
<div class="modal fade" id="lateAbsenceModal" tabindex="-1" aria-hidden="true"
     data-saved-days="{{ $lateAbsence->settings['days'] ?? '' }}">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('deductions.update', $lateAbsence->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setup: Late and Absences</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- VIEW MODE -->
                    <div id="viewMode" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label"><strong>Number of Days:</strong></label>
                            <p id="viewDays" class="form-control-plaintext">{{ $lateAbsence->settings['days'] ?? 0 }}</p>
                        </div>
                    </div>

                    <!-- EDIT MODE -->
                    <div id="editMode">
                        <!-- Number of Days -->
                        <div class="mb-3">
                            <label class="form-label">Number of Days</label>
                            <input type="number" step="0.01" name="days" id="daysInput"
                                   value="{{ $lateAbsence->settings['days'] ?? 0 }}"
                                   class="form-control" required>
                        </div>

                        <hr>
                        <!-- Sample Computation -->
                        <h6>Sample Computation</h6>

                        <div class="mb-3">
                            <label class="form-label">Rate Type</label>
                            <select id="rateTypeSelect" class="form-select">
                                <option value="daily">Daily</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" step="0.01" id="rateInput" class="form-control format-number" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Computed Deduction:</label>
                            <p id="dailyResultContainer">Per Day: <span id="perDay">0</span></p>
                            <p>Per Hour: <span id="perHour">0</span></p>
                            <p>Per Minute: <span id="perMinute">0</span></p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- VIEW MODE BUTTONS -->
                    <div id="viewModeButtons" style="display: none;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="editButton">Edit</button>
                    </div>
                    
                    <!-- EDIT MODE BUTTONS -->
                    <div id="editModeButtons">
                        <button type="button" class="btn btn-outline-secondary" id="cancelEditButton">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Loans and Advances Modal -->
<div class="modal fade" id="loanAdvancesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Loans and Advances</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>
                Loans and Advances are employee-specific deductions. 
                Each loan or advance is linked to an individual employee and tracked through employee deductions, 
                including the amount, date, and payment schedule.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- SSS Contribution Modal -->
<div class="modal fade" id="sssModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form method="POST" action="{{ route('sss.update') }}" id="sssForm">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">SSS Contribution Brackets</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>From (₱)</th>
                                <th>To (₱)</th>
                                <th>ER (₱)</th>
                                <th>EE (₱)</th>
                                <th>Total (₱)</th>
                                <th>Others</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="sssBrackets">
                            @foreach($sssBrackets as $i => $bracket)
                            <tr>
                                <!-- Add hidden ID field if you want to preserve existing records -->
                                <input type="hidden" name="brackets[{{ $i }}][id]" value="{{ $bracket->id }}">
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][from]" value="{{ $bracket->from }}" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][to]" value="{{ $bracket->to }}" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][er]" value="{{ $bracket->er }}" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][ee]" value="{{ $bracket->ee }}" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][total]" value="{{ $bracket->total }}" class="form-control" required readonly></td>
                                <td><input type="text" name="brackets[{{ $i }}][others]" value="{{ $bracket->others }}" class="form-control"></td>
                                <td><button type="button" class="btn btn-danger btn-sm removeBracket">Remove</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn-sm d-none" id="addSSSBracket">Add Bracket</button>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" id="editSSSBtn">Edit</button>
                    <button type="button" class="btn btn-outline-secondary d-none" id="cancelSSSBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary d-none" id="saveSSSBtn">Save</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="closeSSSBtn">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- PHIC Contribution Modal -->
<div class="modal fade" id="phicModal" tabindex="-1" aria-hidden="true"
     data-saved-rate="{{ $phicSetting->settings['rate'] ?? '' }}"
     data-saved-min="{{ $phicSetting->settings['min_salary'] ?? '' }}"
     data-saved-max="{{ $phicSetting->settings['max_salary'] ?? '' }}">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('deductions.update', $phicSetting->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setup: PHIC Contribution</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- VIEW MODE -->
                    <div id="phicViewMode" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label"><strong>Premium Rate:</strong></label>
                            <p id="phicViewRate" class="form-control-plaintext">{{ $phicSetting->settings['rate'] ?? 5 }}%</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Minimum Salary:</strong></label>
                            <p id="phicViewMin" class="form-control-plaintext">₱{{ number_format($phicSetting->settings['min_salary'] ?? 10000, 2) }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Maximum Salary:</strong></label>
                            <p id="phicViewMax" class="form-control-plaintext">₱{{ number_format($phicSetting->settings['max_salary'] ?? 100000, 2) }}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Employer Share:</strong></label>
                            <p class="form-control-plaintext">50%</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Employee Share:</strong></label>
                            <p class="form-control-plaintext">50%</p>
                        </div>
                    </div>
                    <!-- EDIT MODE -->
                    <div id="phicEditMode">
                        <!-- Premium Rate -->
                        <div class="mb-3">
                            <label class="form-label">Premium Rate (%)</label>
                            <input type="number" step="0.01" name="rate" id="phicRateInput"
                                value="{{ $phicSetting->settings['rate'] ?? 0 }}"
                                class="form-control" required>
                        </div>

                        <!-- Minimum Salary -->
                        <div class="mb-3">
                            <label class="form-label">Minimum Salary</label>
                            <input type="number" step="0.01" name="min_salary" id="phicMinInput"
                                value="{{ $phicSetting->settings['min_salary'] ?? 0 }}"
                                class="form-control" required>
                        </div>

                        <!-- Maximum Salary -->
                        <div class="mb-3">
                            <label class="form-label">Maximum Salary</label>
                            <input type="number" step="0.01" name="max_salary" id="phicMaxInput"
                                value="{{ $phicSetting->settings['max_salary'] ?? 0 }}"
                                class="form-control" required>
                        </div>

                        <!-- Employer/Employee Share -->
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Employer Share (%)</label>
                                <input type="number" step="0.01" name="employer_share" id="phicEmployerPercent"
                                    value="{{ $phicSetting->settings['employer_share'] ?? 0 }}"
                                    class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Employee Share (%)</label>
                                <input type="number" step="0.01" name="employee_share" id="phicEmployeePercent"
                                    value="{{ $phicSetting->settings['employee_share'] ?? 0 }}"
                                    class="form-control">
                            </div>
                        </div>

                        <hr>
                        <!-- Sample Computation -->
                        <h6>Sample Computation</h6>

                        <div class="mb-3">
                            <label class="form-label">Sample Monthly Salary</label>
                            <input type="number" step="0.01" id="phicSampleSalary" class="form-control" value="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Computed Premium:</label>
                            <p><strong>Total Premium:</strong> ₱<span id="phicTotalPremium">0.00</span></p>
                            <p><strong>Employer Share:</strong> ₱<span id="phicEmployerShare">0.00</span></p>
                            <p><strong>Employee Share:</strong> ₱<span id="phicEmployeeShare">0.00</span></p>
                            <p class="text-muted small" id="phicComputationNote"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- VIEW MODE BUTTONS -->
                    <div id="phicViewModeButtons" style="display: none;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="phicEditButton">Edit</button>
                    </div>
                    <!-- EDIT MODE BUTTONS -->
                    <div id="phicEditModeButtons">
                        <button type="button" class="btn btn-outline-secondary" id="phicCancelEditButton">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- HDMF Contribution Modal -->
<div class="modal fade" id="hdmfModal" tabindex="-1" aria-hidden="true"
     data-employee="{{ $hdmfSetting->settings['employee'] ?? '' }}"
     data-employer="{{ $hdmfSetting->settings['employer'] ?? '' }}">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('deductions.update', $hdmfSetting->id) }}">
        @csrf
        @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Setup: HDMF Contribution</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- VIEW MODE -->
                    <div id="hdmfViewMode" style="display: none;">
                        <p><strong>Employee Contribution:</strong> <span id="viewEmployee"></span></p>
                        <p><strong>Employer Contribution:</strong> <span id="viewEmployer"></span></p>
                    </div>

                    <!-- EDIT MODE -->
                    <div id="hdmfEditMode">
                        <div class="mb-3">
                            <label class="form-label">Employee Contribution</label>
                            <input type="number" step="0.01" name="employee" id="hdmfEmployee" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Employer Contribution</label>
                            <input type="number" step="0.01" name="employer" id="hdmfEmployer" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <!-- VIEW MODE BUTTONS -->
                    <div id="hdmfViewButtons" style="display: none;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="hdmfEditButton">Edit</button>
                    </div>

                    <!-- EDIT MODE BUTTONS -->
                    <div id="hdmfEditButtons">
                        <button type="button" class="btn btn-outline-secondary" id="hdmfCancelButton">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SSS Loan Modal -->
<div class="modal fade" id="sssLoanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Social Security System Loan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>SSS Loan is a fixed government loan deduction linked to an individual employee. 
                    The system tracks the loan amount and the payment schedule, 
                    and the monthly deduction is automatically applied to the employee’s payslip.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- HDMF Loan Modal -->
<div class="modal fade" id="hdmfLoanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pag-IBIG Fund Loan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>HDMF or Pag-IBIG Loan is a fixed government loan deduction linked to an individual employee. 
                    The system tracks the loan amount and the payment schedule, 
                    and the monthly deduction is automatically applied to the employee’s payslip.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Income Tax Modal -->
<div class="modal fade" id="taxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('tax.update') }}" id="taxForm">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Income Tax Brackets</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>From (₱)</th>
                                <th>To (₱)</th>
                                <th>Percentage (%)</th>
                                <th>Fixed Amount (₱)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="taxBrackets">
                            @foreach($taxBrackets as $i => $bracket)
                            <tr>
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][from]" value="{{ $bracket->from }}" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][to]" value="{{ $bracket->to }}" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][percentage]" value="{{ $bracket->percentage }}" class="form-control" required></td>
                                <td><input type="number" step="0.01" name="brackets[{{ $i }}][fixed_amount]" value="{{ $bracket->fixed_amount }}" class="form-control"></td>
                                <td><button type="button" class="btn btn-danger btn-sm removeBracket">Remove</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn-sm d-none" id="addTaxBracket">Add Bracket</button>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" id="editTaxBtn">Edit</button>
                    <button type="button" class="btn btn-outline-secondary d-none" id="cancelTaxBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary d-none" id="saveTaxBtn">Save</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="closeTaxBtn">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>window.csrfToken = '{{ csrf_token() }}';</script>
<script src="{{ asset('js/deductions.js') }}"></script>
@endsection