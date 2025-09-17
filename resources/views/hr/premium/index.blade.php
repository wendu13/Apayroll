{{-- resources/views/hr/premium/index.blade.php --}}
@extends('layouts.hr')

@section('page-title', 'Premium Setup')

@section('hr-content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <small class="text-muted">Configure premium pay rates for work scenarios</small>
            </div>
            
            <div class="card-body">
                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form id="premiumForm" action="{{ route('premium.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="accordion" id="premiumAccordion">
                        @foreach($categories as $category)
                            <div class="accordion-item mb-3">
                                {{-- Category Header --}}
                                <h2 class="accordion-header" id="heading-{{ $category->id }}">
                                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $category->id }}">
                                        {{ $category->name }}
                                    </button>
                                </h2>

                                {{-- Category Content --}}
                                <div id="collapse-{{ $category->id }}" class="accordion-collapse collapse" data-bs-parent="#premiumAccordion">
                                    <div class="accordion-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 5%">#</th>
                                                        <th style="width: 50%">Premium Type</th>
                                                        <th style="width: 25%">Percent (%)</th>
                                                        <th style="width: 20%" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- Category 1: Fixed 5 rows --}}
                                                    @if($category->id == 1)
                                                        @php
                                                            $fixedTypes = [
                                                                'Restday',
                                                                'Holiday Regular',
                                                                'Holiday Special',
                                                                'Holiday on RD (Regular)',
                                                                'Holiday on RD (Special)'
                                                            ];
                                                        @endphp
                                                        @foreach($fixedTypes as $i => $typeName)
                                                            @php
                                                                $premiumType = $category->premiumTypes->firstWhere('name', $typeName);
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $i + 1 }}</td>
                                                                <td>{{ $typeName }}</td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="number" 
                                                                            class="form-control d-none premium-input" 
                                                                            name="premium_types[{{ $category->id }}][{{ $premiumType?->id }}][rate]" 
                                                                            value="{{ $premiumType?->regular_rate ?? 0 }}" 
                                                                            step="0.01" min="0" max="999.99">
                                                                        <input type="text" 
                                                                            class="form-control premium-display" 
                                                                            value="{{ $premiumType?->regular_rate ?? 0 }} %" 
                                                                            readonly>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-btn" onclick="toggleEdit(this)">
                                                                        <i class="bx bx-edit-alt"></i> Edit
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-success d-none save-btn" onclick="toggleSave(this)">
                                                                        <i class="bx bx-save"></i> Save
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        {{-- Other categories: dynamic --}}
                                                        @foreach($category->premiumTypes as $index => $premiumType)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $premiumType->name }}</td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="number" 
                                                                            class="form-control d-none premium-input" 
                                                                            name="premium_types[{{ $category->id }}][{{ $premiumType->id }}][rate]" 
                                                                            value="{{ $premiumType->regular_rate }}" 
                                                                            step="0.01" min="0" max="999.99">
                                                                        <input type="text" 
                                                                            class="form-control premium-display" 
                                                                            value="{{ $premiumType->regular_rate }} %" 
                                                                            readonly>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" class="btn btn-sm btn-outline-primary edit-btn" onclick="toggleEdit(this)">
                                                                        <i class="bx bx-edit-alt"></i> Edit
                                                                    </button>
                                                                    <button type="button" class="btn btn-sm btn-success d-none save-btn" onclick="toggleSave(this)">
                                                                        <i class="bx bx-save"></i> Save
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="location.reload()">
                            <i class="bx bx-reset me-1"></i> Reset
                        </button>
                        <button type="button" class="btn btn-primary" id="submitBtn" onclick="updatePremiumRates()">
                            <i class="bx bx-save me-1"></i> Save All Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEdit(btn) {
    const row = btn.closest('tr');
    row.querySelector('.premium-display').classList.add('d-none');
    row.querySelector('.premium-input').classList.remove('d-none');
    btn.classList.add('d-none');
    row.querySelector('.save-btn').classList.remove('d-none');
}

function toggleSave(btn) {
    const row = btn.closest('tr');
    const input = row.querySelector('.premium-input');
    const display = row.querySelector('.premium-display');

    display.value = input.value + " %";
    input.classList.add('d-none');
    display.classList.remove('d-none');

    btn.classList.add('d-none');
    row.querySelector('.edit-btn').classList.remove('d-none');
}
</script>
@endsection
