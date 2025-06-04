@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Welcome, {{ $username }} ({{ $role }})</h2>
    <a href="{{ route('leads.logout') }}" class="btn btn-danger float-right">Logout</a>

    <h3 class="mt-4">Leads Dashboard</h3>

    <!-- FILTERS -->
    <div class="mb-3">
        <form id="filter-form" class="form-inline">
            <input type="text" name="phone" id="filter-phone" placeholder="Phone" class="form-control mr-2">
            <label for="filter-trek-date" class="mr-1">Select Trek Date:</label>
            <input type="date" name="trek_date" id="filter-trek-date" class="form-control mr-3">

            <label for="filter-lead-date" class="mr-1">Select Lead Date:</label>
            <input type="date" name="lead_date" id="filter-lead-date" class="form-control mr-2">

            <button type="submit" class="btn btn-primary">Filter</button>
            <button type="button" id="clear-filters" class="btn btn-secondary ml-2">Clear</button>
        </form>
    </div>

    <table class="table table-bordered table-striped mt-3">
        <thead class="thead-dark">
            <tr>
                <th>Lead Details</th>
                <th>Message</th>
                <th>Trek Date</th>
                <th>Lead Date</th>
                <th>Source</th>
                <th>Status</th>
                <th>Book</th>
                <th>Cancel</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="lead-table-body">
            <tr>
                <td colspan="9">Loading leads...</td>
            </tr>
    </table>
</div>

<div class="modal-overlay" style="display:none;"></div>

<!-- Edit Lead Modal -->
<div class="edit-lead-modal">
    <span class="edit-lead-close" style="float:right; cursor:pointer;">&times;</span>
    <div id="lead-edit-loader">Loading...</div>
    <form id="edit-lead-form" style="display:none;">
        <input type="hidden" name="lead_id">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone_number" class="form-control">
        </div>
        <div class="form-group">
            <label>Country Code</label>
            <input type="text" name="country_code" class="form-control">
        </div>
        <div class="form-group">
            <label>No. of People</label>
            <input type="number" name="no_of_people" class="form-control">
        </div>
        <div class="form-group">
            <label>Trek Type ID</label>
            <input type="number" name="type_id" class="form-control">
        </div>
        <div class="form-group">
            <label>Lead Source</label>
            <input type="text" name="lead_source" class="form-control">
        </div>
        <div class="form-group">
            <label>Trek Date</label>
            <input type="date" name="trek_date" class="form-control">
        </div>
        <div class="form-group">
            <label>Lead Date</label>
            <input type="datetime-local" name="created_at" class="form-control">
        </div>
        <div class="form-group">
            <label>Message</label>
            <textarea name="message" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Lead</button>
    </form>
</div>
@include('components.book-modal')
@endsection


@push('scripts')
<script>
const fetchLeads = (filters = {}) => {
    // Build query string from filters
    const query = new URLSearchParams(filters).toString();
    fetch("{{ url('/api/madtrek/v1/leads') }}" + (query ? `?${query}` : ''), {
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('lead-table-body');

            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="9">No leads found.</td></tr>';
                return;
            }

            let html = '';
            data.forEach(lead => {
                html += `
            <tr data-lead-id="${lead.id}">
                <td>
                    <strong>Trek:</strong> ${lead.trek_name}<br>
                    <strong>Name:</strong> ${lead.name}<br>
                    <strong>Phone:</strong> +${lead.country_code}${lead.phone}<br>
                    <strong>Email:</strong> ${lead.email}<br>
                    <strong>Group Size:</strong> ${lead.no_of_people}
                </td>
                <td>${lead.message}</td>
                <td>${lead.trek_date}</td>
                <td>${new Date(lead.created_at).toLocaleString()}</td>
                <td>${lead.source}</td>
                <td>${lead.is_converted ? 'Converted' : 'New'}</td>
                <td><button class="btn btn-success btn-sm book-btn" data-id="${lead.id}">Book</button></td>
                <td><button class="btn btn-danger btn-sm cancel-btn" data-id="${lead.id}">Cancel</button></td>
                <td>
                    <button class="btn btn-info btn-sm edit-btn" data-id="${lead.id}">Edit</button>
                    <button class="btn btn-warning btn-sm logs-btn" data-id="${lead.id}">Logs</button>
                    <button class="btn btn-dark btn-sm payments-btn" data-id="${lead.id}">Payments</button>
                </td>

            </tr>`;

            });

            tbody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching leads:', error);
            document.getElementById('lead-table-body').innerHTML =
                '<tr><td colspan="9">Failed to load leads</td></tr>';
        });
};

// On page load, fetch all leads
fetchLeads();

// Handle form submit
document.getElementById('filter-form').addEventListener('submit', e => {
    e.preventDefault();

    const filters = {
        phone: document.getElementById('filter-phone').value.trim(),
        trek_date: document.getElementById('filter-trek-date').value,
        lead_date: document.getElementById('filter-lead-date').value,
    };

    // Remove empty filters
    Object.keys(filters).forEach(key => {
        if (!filters[key]) delete filters[key];
    });

    fetchLeads(filters);
});

// Clear filters
document.getElementById('clear-filters').addEventListener('click', () => {
    document.getElementById('filter-phone').value = '';
    document.getElementById('filter-trek-date').value = '';
    document.getElementById('filter-lead-date').value = '';
    fetchLeads();
});

// Bind action buttons
document.getElementById('lead-table-body').addEventListener('click', function(e) {
    const target = e.target;

    if (target.classList.contains('book-btn')) {
        e.preventDefault();

        const id = target.getAttribute('data-id');
        if (id) {
            openBookModal(id);
        }
    }

    if (target.classList.contains('cancel-btn')) {
        const id = target.getAttribute('data-id');
        fetch(`/api/madtrek/v1/leads/${id}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                fetchLeads();
            });
    }

    if (target.classList.contains('edit-btn')) {
        const id = target.getAttribute('data-id');
        const modal = document.querySelector('.edit-lead-modal');
        const form = document.getElementById('edit-lead-form');
        const loader = document.getElementById('lead-edit-loader');

        modal.style.display = 'block';
        loader.style.display = 'block';
        form.style.display = 'none';

        fetch(`/api/madtrek/v1/leads/${id}`)
            .then(res => res.json())
            .then(lead => {
                loader.style.display = 'none';
                form.style.display = 'block';

                form.lead_id.value = lead.id;
                form.name.value = lead.name || '';
                form.email.value = lead.email || '';
                form.phone_number.value = lead.phone || '';
                form.country_code.value = lead.country_code || '';
                form.no_of_people.value = lead.no_of_people || '';
                form.type_id.value = lead.type_id || '';
                form.lead_source.value = lead.source || '';
                form.trek_date.value = lead.trek_date ? lead.trek_date.slice(0, 10) : '';
                form.created_at.value = lead.created_at ? lead.created_at.replace(' ', 'T') : '';
                form.message.value = lead.message || '';
            });
    }

    if (target.classList.contains('logs-btn')) {
        alert(`Logs clicked for lead ${target.getAttribute('data-id')}`);
    }

    if (target.classList.contains('payments-btn')) {
        alert(`Payments clicked for lead ${target.getAttribute('data-id')}`);
    }
});

// Handle Edit Lead Form Submission
document.getElementById('edit-lead-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const id = form.lead_id.value;

    const formData = {
        name: form.name.value,
        email: form.email.value,
        phone: form.phone_number.value,
        country_code: form.country_code.value,
        no_of_people: form.no_of_people.value,
        type_id: form.type_id.value,
        lead_source: form.lead_source.value,
        trek_date: form.trek_date.value,
        created_at: form.created_at.value,
        message: form.message.value,
    };

    fetch(`/api/madtrek/v1/leads/${id}`, {
            method: 'PUT', // or 'POST' if your API uses POST for updates
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(response => {
            alert(response.message || 'Lead updated successfully');
            document.querySelector('.edit-lead-modal').style.display = 'none';
            fetchLeads(); // Refresh leads table
        })
        .catch(err => {
            console.error('Update error:', err);
            alert('Failed to update lead');
        });
});

document.querySelector('.edit-lead-close').addEventListener('click', () => {
    document.querySelector('.edit-lead-modal').style.display = 'none';
});
</script>
<style>
/* Background overlay for modal (optional if you add an overlay element) */
body.modal-open {
    overflow: hidden;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 1040;
}


/* Edit Lead Modal */
.edit-lead-modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    z-index: 1050;
    padding: 20px;
}

/* Close button */
.edit-lead-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    font-weight: bold;
    color: #999;
    cursor: pointer;
    transition: color 0.2s ease-in-out;
}

.edit-lead-close:hover {
    color: #333;
}

/* Form styling */
#edit-lead-form .form-group {
    margin-bottom: 15px;
}

#edit-lead-form label {
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
}

#edit-lead-form input,
#edit-lead-form textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
}

#edit-lead-form textarea {
    resize: vertical;
    min-height: 80px;
}

/* Submit button */
#edit-lead-form button[type="submit"] {
    margin-top: 10px;
    width: 100%;
}

/* Loader */
#lead-edit-loader {
    text-align: center;
    font-weight: bold;
    padding: 20px;
}

/* Entire screen overlay */
#bookModal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 9999; /* On top of all elements */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.4); /* Semi-transparent background */
}

/* Modal content box */
.book-modal-content {
    background-color: #fff;
    margin: 10% auto; /* Center it vertically and horizontally */
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    position: relative;
}

/* Modal header */
.book-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Close button */
.book-close {
    font-size: 24px;
    font-weight: bold;
    color: #aaa;
}

.book-close:hover {
    color: #000;
    cursor: pointer;
}

/* Input styling */
.book-input {
    margin-bottom: 15px;
}

.book-input label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.book-input input,
.book-input select {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
}

/* Buttons */
.book-save-btn {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.button {
    padding: 8px 16px;
    border: none;
    cursor: pointer;
}

.button-primary {
    background-color: #007bff;
    color: white;
}

.button-secondary {
    background-color: #6c757d;
    color: white;
}



/* Responsive enhancements */
@media (max-width: 576px) {
    .edit-lead-modal {
        width: 95%;
        padding: 15px;
    }

    .edit-lead-close {
        top: 8px;
        right: 10px;
    }
}
</style>

@endpush