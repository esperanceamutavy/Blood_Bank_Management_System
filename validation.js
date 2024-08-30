document.addEventListener('DOMContentLoaded', function() {
    // General validation function
    function validateFields(fields) {
        for (const field of fields) {
            const element = document.getElementById(field.id);
            if (!element) continue; // Skip if the element is not found

            if (element.value.trim() === '') {
                alert(`Please enter ${field.name}`);
                return false;
            }

            // Date format check and future date check
            if (field.type === 'date') {
                if (!isValidDate(element.value)) {
                    alert(`Invalid ${field.name} format. Use YYYY-MM-DD`);
                    return false;
                }
                if (isFutureDate(element.value)) {
                    alert(`${field.name} cannot be in the future`);
                    return false;
                }
            }
        }
        return true;
    }

    // Function to check if a date string is valid
    function isValidDate(dateString) {
        const date = new Date(dateString);
        return date.toISOString().startsWith(dateString);
    }

    // Function to check if a date is in the future
    function isFutureDate(dateString) {
        const date = new Date(dateString);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return date > today;
    }

    // Blood testing form validation
    document.querySelector('#bloodTestingForm')?.addEventListener('submit', function(e) {
        const fields = [
            { id: 'bloodUnitID', name: 'blood unit ID' },
            { id: 'HIVAIDS', name: 'HIV/AIDS status' },
            { id: 'HepatitisB', name: 'Hepatitis B status' },
            { id: 'HepatitisC', name: 'Hepatitis C status' },
            { id: 'Syphilis', name: 'Syphilis status' },
            { id: 'test_date', name: 'test date', type: 'date' }
        ];

        if (!validateFields(fields)) {
            e.preventDefault();
        }
    });

    // Blood unit form validation
    document.querySelector('#bloodUnitForm')?.addEventListener('submit', function(e) {
        const fields = [
            { id: 'donation_id', name: 'donation ID' },
            { id: 'blood_type', name: 'blood type' },
            { id: 'expiration_date', name: 'expiration date', type: 'date' },
            { id: 'storage_location', name: 'storage location' }
        ];

        if (!validateFields(fields)) {
            e.preventDefault();
        }
    });

    // Distribution form validation
    document.querySelector('#distributionForm')?.addEventListener('submit', function(e) {
        const fields = [
            { id: 'request_id', name: 'request ID' },
            { id: 'blood_unit_id', name: 'blood unit ID' },
            { id: 'distribution_date', name: 'distribution date', type: 'date' }
        ];

        if (!validateFields(fields)) {
            e.preventDefault();
        }
    });

    // Distribution report form validation
    document.querySelector('#distributionReportForm')?.addEventListener('submit', function(e) {
        const fields = [
            { id: 'start_date', name: 'start date', type: 'date' },
            { id: 'end_date', name: 'end date', type: 'date' },
            { id: 'hospital', name: 'hospital' },
            { id: 'blood_type', name: 'blood type' }
        ];

        if (!validateFields(fields)) {
            e.preventDefault();
            return;
        }

        // Additional date range validation
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Set time to midnight for accurate comparison

        if (startDate < new Date(2015, 0, 1) || endDate > today) {
            alert('Start date must be after 2014 and end date cannot be in the future');
            e.preventDefault();
            return;
        }

        if (startDate > endDate) {
            alert('Start date cannot be after end date');
            e.preventDefault();
        }
    });

    // Staff registration form validation
    document.querySelector('#staffRegistrationForm')?.addEventListener('submit', function(e) {
        const fields = [
            { id: 'staff_name', name: 'staff name' },
            { id: 'staff_position', name: 'staff position' },
            { id: 'password', name: 'password' }
        ];

        if (!validateFields(fields)) {
            e.preventDefault();
        }
    });

    // Hide the password field text
    const passwordField = document.getElementById('password');
    if (passwordField) {
        passwordField.type = 'password';
    }
});
