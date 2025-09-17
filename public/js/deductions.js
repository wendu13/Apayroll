// Late and Absences
document.addEventListener('DOMContentLoaded', function() {
    const daysInput = document.getElementById('daysInput');
    const rateTypeSelect = document.getElementById('rateTypeSelect');
    const rateInput = document.getElementById('rateInput');
    const perDaySpan = document.getElementById('perDay');
    const perHourSpan = document.getElementById('perHour');
    const perMinuteSpan = document.getElementById('perMinute');
    const dailyResultContainer = document.getElementById('dailyResultContainer');
    
    // Mode elements
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');
    const viewModeButtons = document.getElementById('viewModeButtons');
    const editModeButtons = document.getElementById('editModeButtons');
    const viewDays = document.getElementById('viewDays');
    const editButton = document.getElementById('editButton');
    const cancelEditButton = document.getElementById('cancelEditButton');
    
    // ✅ Get data from data attribute
    const modal = document.getElementById('lateAbsenceModal');
    const savedDaysValue = modal.getAttribute('data-saved-days');
    const hasSavedData = savedDaysValue !== '' && parseFloat(savedDaysValue) > 0;
    let originalDaysValue = daysInput.value;
    
    function showViewMode() {
        viewMode.style.display = 'block';
        editMode.style.display = 'none';
        viewModeButtons.style.display = 'block';
        editModeButtons.style.display = 'none';
        viewDays.textContent = daysInput.value;
    }
    
    function showEditMode() {
        viewMode.style.display = 'none';
        editMode.style.display = 'block';
        viewModeButtons.style.display = 'none';
        editModeButtons.style.display = 'block';
        originalDaysValue = daysInput.value;
        updateSample();
    }
    
    // Initialize modal based on saved data
    if (hasSavedData) {
        showViewMode();
    } else {
        showEditMode();
    }
    
    // Edit button click
    editButton.addEventListener('click', function() {
        showEditMode();
    });
    
    // Cancel edit button click
    cancelEditButton.addEventListener('click', function() {
        daysInput.value = originalDaysValue;
        if (hasSavedData) {
            showViewMode();
        } else {
            // If no saved data, close modal instead
            const modal = bootstrap.Modal.getInstance(document.getElementById('lateAbsenceModal'));
            modal.hide();
        }
    });

    function updateSample() {
        const days = parseFloat(daysInput.value) || 0;
        const rate = parseFloat(rateInput.value) || 0;

        let perDay = 0;
        let perHour = 0;
        let perMinute = 0;

        if (rateTypeSelect.value === 'daily') {
            // Daily rate: days input not used
            perDay = 0;               // hide, not used
            perHour = rate / 8;       // compute per hour
            perMinute = perHour / 60;
            dailyResultContainer.style.display = 'none'; // hide per day
        } else if (rateTypeSelect.value === 'monthly') {
            // For monthly rate, divide by the number of days set (not 30)
            const dailyRate = days > 0 ? rate / days : 0;
            perDay = dailyRate;
            perHour = dailyRate / 8;
            perMinute = perHour / 60;
            dailyResultContainer.style.display = 'block';
        }

        perDaySpan.textContent = perDay.toFixed(2);
        perHourSpan.textContent = perHour.toFixed(2);
        perMinuteSpan.textContent = perMinute.toFixed(2);
    }

    if (daysInput) daysInput.addEventListener('input', updateSample);
    if (rateTypeSelect) rateTypeSelect.addEventListener('change', updateSample);
    if (rateInput) rateInput.addEventListener('input', updateSample);

    if (daysInput) updateSample(); // initialize
});

// SSS Brackets functionality
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('sssBrackets');
    const addBtn = document.getElementById('addSSSBracket');

    const editBtn = document.getElementById('editSSSBtn');
    const cancelBtn = document.getElementById('cancelSSSBtn');
    const saveBtn = document.getElementById('saveSSSBtn');
    const closeBtn = document.getElementById('closeSSSBtn');
    const sssForm = document.getElementById('sssForm');

    if (sssForm) {
        console.log('SSS Form found!');
        console.log('Form action:', sssForm.action);
        console.log('Form method:', sssForm.method);
        
        sssForm.addEventListener('submit', function(e) {
            console.log('Form is being submitted!');
            console.log('Action URL:', this.action);
            console.log('Method:', this.method);
            
            // Check form data
            const formData = new FormData(this);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            // Let the form submit normally (removed e.preventDefault())
        });
    } else {
        console.log('SSS Form NOT found!');
    }

    // Switch to read-only mode by default
    function setReadOnlyMode() {
        tableBody.querySelectorAll('input:not([type="hidden"])').forEach(input => {
            input.setAttribute('readonly', true);
            input.classList.add('bg-light');
        });
        tableBody.querySelectorAll('.removeBracket').forEach(btn => btn.classList.add('d-none'));
        addBtn.classList.add('d-none');

        editBtn.classList.remove('d-none');
        cancelBtn.classList.add('d-none');
        saveBtn.classList.add('d-none');
        closeBtn.classList.remove('d-none');
    }

    // Switch to edit mode
    function setEditMode() {
        tableBody.querySelectorAll('input:not([type="hidden"]):not([readonly])').forEach(input => {
            input.removeAttribute('readonly');
            input.classList.remove('bg-light');
        });
        // Keep total field readonly
        tableBody.querySelectorAll('input[name*="[total]"]').forEach(input => {
            input.setAttribute('readonly', true);
            input.classList.add('bg-light');
        });
        
        tableBody.querySelectorAll('.removeBracket').forEach(btn => btn.classList.remove('d-none'));
        addBtn.classList.remove('d-none');

        editBtn.classList.add('d-none');
        cancelBtn.classList.remove('d-none');
        saveBtn.classList.remove('d-none');
        closeBtn.classList.add('d-none');
    }

    // Compute Total (EE + ER)
    function computeTotal(row) {
        const er = parseFloat(row.querySelector('input[name*="[er]"]').value) || 0;
        const ee = parseFloat(row.querySelector('input[name*="[ee]"]').value) || 0;
        row.querySelector('input[name*="[total]"]').value = (er + ee).toFixed(2);
    }

    // Attach listeners for auto-compute
    function attachComputeListeners(row) {
        const erInput = row.querySelector('input[name*="[er]"]');
        const eeInput = row.querySelector('input[name*="[ee]"]');

        if (erInput && eeInput) {
            erInput.addEventListener('input', () => computeTotal(row));
            eeInput.addEventListener('input', () => computeTotal(row));
        }
    }

    // Add new row
    addBtn?.addEventListener('click', function () {
        const index = tableBody.querySelectorAll('tr').length;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="number" step="0.01" name="brackets[${index}][from]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="brackets[${index}][to]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="brackets[${index}][er]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="brackets[${index}][ee]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="brackets[${index}][total]" class="form-control" required readonly></td>
            <td><input type="text" name="brackets[${index}][others]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeBracket">Remove</button></td>
        `;
        tableBody.appendChild(row);
        attachComputeListeners(row);
    });

    // Replace the existing "Remove row" section with this:

    // Remove row
    tableBody?.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeBracket')) {
            const row = e.target.closest('tr');
            const idInput = row.querySelector('input[name*="[id]"]');
            
            if (idInput && idInput.value) {
                // This is an existing record, delete from database
                const bracketId = idInput.value;
                
                if (confirm('Are you sure you want to delete this SSS bracket?')) {
                    // Send DELETE request
                    fetch(`/sss/${bracketId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove row from table
                            row.remove();
                            // Reindex remaining rows
                            reindexRows();
                            alert('SSS bracket deleted successfully');
                        } else {
                            alert('Failed to delete SSS bracket: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error deleting SSS bracket');
                    });
                }
            } else {
                // This is a new row that hasn't been saved yet, just remove from DOM
                row.remove();
                reindexRows();
            }
        }
    });

    // Reindex rows after removal
    function reindexRows() {
        tableBody.querySelectorAll('tr').forEach((row, index) => {
            // Update all input names with new index
            row.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/brackets\[\d+\]/, `brackets[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
    }

    // Attach compute listeners to existing rows
    tableBody.querySelectorAll('tr').forEach(row => attachComputeListeners(row));

    // Button events
    editBtn?.addEventListener('click', setEditMode);
    cancelBtn?.addEventListener('click', setReadOnlyMode);

    // Init mode
    setReadOnlyMode();
});

// PHIC Contribution functionality
document.addEventListener('DOMContentLoaded', function() {
    const phicRateInput = document.getElementById('phicRateInput');
    const phicMinInput = document.getElementById('phicMinInput');
    const phicMaxInput = document.getElementById('phicMaxInput');
    const phicSampleSalary = document.getElementById('phicSampleSalary');
    const phicTotalPremium = document.getElementById('phicTotalPremium');
    const phicEmployerShare = document.getElementById('phicEmployerShare');
    const phicEmployeeShare = document.getElementById('phicEmployeeShare');
    const phicComputationNote = document.getElementById('phicComputationNote');
    
    // PHIC Mode elements
    const phicViewMode = document.getElementById('phicViewMode');
    const phicEditMode = document.getElementById('phicEditMode');
    const phicViewModeButtons = document.getElementById('phicViewModeButtons');
    const phicEditModeButtons = document.getElementById('phicEditModeButtons');
    const phicViewRate = document.getElementById('phicViewRate');
    const phicViewMin = document.getElementById('phicViewMin');
    const phicViewMax = document.getElementById('phicViewMax');
    const phicEditButton = document.getElementById('phicEditButton');
    const phicCancelEditButton = document.getElementById('phicCancelEditButton');
    
    if (!phicRateInput) return; // Exit if PHIC elements don't exist on this page
    
    // Get data from data attributes
    const phicModal = document.getElementById('phicModal');
    const savedRate = phicModal.getAttribute('data-saved-rate');
    const savedMin = phicModal.getAttribute('data-saved-min');
    const savedMax = phicModal.getAttribute('data-saved-max');
    const phicHasSavedData = savedRate !== '' && parseFloat(savedRate) > 0;
    
    let phicOriginalValues = {
        rate: phicRateInput.value,
        min_salary: phicMinInput.value,
        max_salary: phicMaxInput.value
    };
    
    function showPhicViewMode() {
        phicViewMode.style.display = 'block';
        phicEditMode.style.display = 'none';
        phicViewModeButtons.style.display = 'block';
        phicEditModeButtons.style.display = 'none';
        phicViewRate.textContent = phicRateInput.value + '%';
        phicViewMin.textContent = '₱' + parseFloat(phicMinInput.value).toLocaleString('en-US', {minimumFractionDigits: 2});
        phicViewMax.textContent = '₱' + parseFloat(phicMaxInput.value).toLocaleString('en-US', {minimumFractionDigits: 2});
    }
    
    function showPhicEditMode() {
        phicViewMode.style.display = 'none';
        phicEditMode.style.display = 'block';
        phicViewModeButtons.style.display = 'none';
        phicEditModeButtons.style.display = 'block';
        phicOriginalValues = {
            rate: phicRateInput.value,
            min_salary: phicMinInput.value,
            max_salary: phicMaxInput.value
        };
        updatePhicSample();
    }
    
    // Initialize PHIC modal based on saved data
    if (phicHasSavedData) {
        showPhicViewMode();
    } else {
        showPhicEditMode();
    }
    
    // PHIC Edit button click
    if (phicEditButton) {
        phicEditButton.addEventListener('click', function() {
            showPhicEditMode();
        });
    }
    
    // PHIC Cancel edit button click
    if (phicCancelEditButton) {
        phicCancelEditButton.addEventListener('click', function() {
            phicRateInput.value = phicOriginalValues.rate;
            phicMinInput.value = phicOriginalValues.min_salary;
            phicMaxInput.value = phicOriginalValues.max_salary;
            if (phicHasSavedData) {
                showPhicViewMode();
            } else {
                // If no saved data, close modal instead
                const modal = bootstrap.Modal.getInstance(document.getElementById('phicModal'));
                if (modal) modal.hide();
            }
        });
    }

    function updatePhicSample() {
        const rate = parseFloat(phicRateInput.value) || 0;
        const minSalary = parseFloat(phicMinInput.value) || 0;
        const maxSalary = parseFloat(phicMaxInput.value) || 0;
        const sampleSalary = parseFloat(phicSampleSalary.value) || 0;
        const employerPercent = parseFloat(phicEmployerPercent.value) || 0;
        const employeePercent = parseFloat(phicEmployeePercent.value) || 0;
    
        let totalPremium = 0;
        let computationNote = '';
    
        if (sampleSalary <= minSalary && minSalary > 0) {
            totalPremium = (minSalary * rate) / 100;
            computationNote = `Salary ≤ ₱${minSalary.toLocaleString()}, using minimum salary for computation.`;
        } else if (sampleSalary >= maxSalary && maxSalary > 0) {
            totalPremium = (maxSalary * rate) / 100;
            computationNote = `Salary ≥ ₱${maxSalary.toLocaleString()}, using maximum salary for computation.`;
        } else if (sampleSalary > 0) {
            totalPremium = (sampleSalary * rate) / 100;
            computationNote = `₱${sampleSalary.toLocaleString()} × ${rate}% = ₱${totalPremium.toFixed(2)}`;
        }
    
        const employerShare = (totalPremium * employerPercent) / 100;
        const employeeShare = (totalPremium * employeePercent) / 100;
    
        phicTotalPremium.textContent = totalPremium.toFixed(2);
        phicEmployerShare.textContent = employerShare.toFixed(2);
        phicEmployeeShare.textContent = employeeShare.toFixed(2);
        phicComputationNote.textContent = computationNote;
    }
    

    // Add event listeners for PHIC inputs
    if (phicRateInput) phicRateInput.addEventListener('input', updatePhicSample);
    if (phicMinInput) phicMinInput.addEventListener('input', updatePhicSample);
    if (phicMaxInput) phicMaxInput.addEventListener('input', updatePhicSample);
    if (phicSampleSalary) phicSampleSalary.addEventListener('input', updatePhicSample);

});

// HDMF Contribution functionality
document.addEventListener('DOMContentLoaded', function() {
    const hdmfModal = document.getElementById('hdmfModal');
    const viewMode = document.getElementById('hdmfViewMode');
    const editMode = document.getElementById('hdmfEditMode');
    const viewButtons = document.getElementById('hdmfViewButtons');
    const editButtons = document.getElementById('hdmfEditButtons');

    const viewEmployee = document.getElementById('viewEmployee');
    const viewEmployer = document.getElementById('viewEmployer');
    const hdmfEmployee = document.getElementById('hdmfEmployee');
    const hdmfEmployer = document.getElementById('hdmfEmployer');

    const editButton = document.getElementById('hdmfEditButton');
    const cancelButton = document.getElementById('hdmfCancelButton');

    hdmfModal.addEventListener('show.bs.modal', function() {
        const employee = hdmfModal.getAttribute('data-employee');
        const employer = hdmfModal.getAttribute('data-employer');
    
        hdmfEmployee.value = employee || 0;
        hdmfEmployer.value = employer || 0;
    
        if (employee && employer) {
            // Show view mode
            viewEmployee.textContent = employee;
            viewEmployer.textContent = employer;
            viewMode.style.display = 'block';
            viewButtons.style.display = 'block';
            editMode.style.display = 'none';
            editButtons.style.display = 'none';
        } else {
            // Show edit mode
            viewMode.style.display = 'none';
            viewButtons.style.display = 'none';
            editMode.style.display = 'block';
            editButtons.style.display = 'block';
        }
    });
    
    // Cancel edit
    cancelButton.addEventListener('click', function() {
        const employee = hdmfModal.getAttribute('data-employee') || 0;
        const employer = hdmfModal.getAttribute('data-employer') || 0;
    
        hdmfEmployee.value = employee;
        hdmfEmployer.value = employer;
    
        if (employee && employer) {
            viewEmployee.textContent = employee;
            viewEmployer.textContent = employer;
            viewMode.style.display = 'block';
            viewButtons.style.display = 'block';
            editMode.style.display = 'none';
            editButtons.style.display = 'none';
        } else {
            bootstrap.Modal.getInstance(hdmfModal).hide();
        }
    });    
});

// Income TAX functionality
document.addEventListener('DOMContentLoaded', function () {
    const taxForm = document.getElementById('taxForm');
    const tableBody = document.getElementById('taxBrackets');
    const addBtn = document.getElementById('addTaxBracket');

    const editBtn = document.getElementById('editTaxBtn');
    const cancelBtn = document.getElementById('cancelTaxBtn');
    const saveBtn = document.getElementById('saveTaxBtn');
    const closeBtn = document.getElementById('closeTaxBtn');

    // Switch to read-only by default
    function setReadOnlyMode() {
        tableBody.querySelectorAll('input').forEach(input => {
            input.setAttribute('readonly', true);
            input.classList.add('bg-light');
        });
        tableBody.querySelectorAll('.removeBracket').forEach(btn => btn.classList.add('d-none'));
        addBtn.classList.add('d-none');

        editBtn.classList.remove('d-none');
        cancelBtn.classList.add('d-none');
        saveBtn.classList.add('d-none');
        closeBtn.classList.remove('d-none');
    }

    // Switch to edit mode
    function setEditMode() {
        tableBody.querySelectorAll('input').forEach(input => {
            input.removeAttribute('readonly');
            input.classList.remove('bg-light');
        });
        tableBody.querySelectorAll('.removeBracket').forEach(btn => btn.classList.remove('d-none'));
        addBtn.classList.remove('d-none');

        editBtn.classList.add('d-none');
        cancelBtn.classList.remove('d-none');
        saveBtn.classList.remove('d-none');
        closeBtn.classList.add('d-none');
    }

    // Add row
    addBtn?.addEventListener('click', function () {
        const index = tableBody.querySelectorAll('tr').length;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="number" step="0.01" name="brackets[${index}][from]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="brackets[${index}][to]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="brackets[${index}][percentage]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="brackets[${index}][fixed_amount]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm removeBracket">Remove</button></td>
        `;
        tableBody.appendChild(row);
    });

    // Remove row
    tableBody?.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeBracket')) {
            e.target.closest('tr').remove();
        }
    });

    // Events
    editBtn?.addEventListener('click', setEditMode);
    cancelBtn?.addEventListener('click', setReadOnlyMode);

    // Init
    setReadOnlyMode();
});







