<div id="bookModal" class="book-modal" style="display: none;">
    <div class="book-modal-content">
        <div class="book-modal-header">
            <h2>Booking Details</h2>
            <span class="book-close" style="cursor:pointer;">&times;</span>
        </div>
        <form method="POST" id="book_lead_form">
            @csrf
            <div class="book-input">
                <label for="amount">Book Amount:</label>
                <input type="number" id="amount" name="amount" placeholder="Enter book amount" min="0" step="any"
                    required>
            </div>

            <div class="book-input">
                <label for="vendor_id">Amount Paid To:</label>
                <select id="vendor_id" name="vendor_id" required>
                    <option value="">Loading vendors...</option>
                </select>
            </div>

            <div class="book-save-btn">
                <button type="submit" class="button button-primary">Save</button>
                <button type="button" class="book-close button button-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('bookModal');
    const form = document.getElementById('book_lead_form');
    const closeButtons = modal.querySelectorAll('.book-close');
    const vendorSelect = document.getElementById('vendor_id');

    // Close modal on clicking close buttons
    closeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.display = 'none';
            form.reset();
        });
    });

    // Fetch vendors from API
async function loadVendors(vendorSelect) {
    vendorSelect.innerHTML = '<option value="">Loading vendors...</option>';
    try {
        const token = localStorage.getItem('token'); // or however you're storing it

        const response = await fetch('/madtrek/v1/vendors', {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
            }
        });

        const data = await response.json();

        if (Array.isArray(data) && data.length) {
            vendorSelect.innerHTML = '<option value="">Select amount paid to</option>';
            data.forEach(vendor => {
                const option = document.createElement('option');
                option.value = vendor.id;
                option.textContent = vendor.name;
                vendorSelect.appendChild(option);
            });
        } else {
            vendorSelect.innerHTML = '<option value="">No vendors found</option>';
        }
    } catch (error) {
        console.error('Vendor fetch failed:', error);
        vendorSelect.innerHTML = '<option value="">Error loading vendors</option>';
    }
}

    // Function to open modal for a specific lead ID
    window.openBookModal = function(leadId) {
        const modal = document.getElementById('bookModal');
        const form = document.getElementById('book_lead_form');

        modal.style.display = 'block';
        form.action = `/madtrek/v1/leads/${leadId}/book`;
    };


    // AJAX form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const url = form.action;
        const formData = new FormData(form);

        fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Booking saved successfully!');
                    modal.style.display = 'none';
                    form.reset();
                    // Refresh leads or table here if needed
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred');
            });
    });
});
</script>