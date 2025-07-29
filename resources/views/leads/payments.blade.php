@extends('layouts.app')

@section('content')

<div class="lead-payments-main-wrapper px-md-3">
    <div class="lead-payments-header-wrapper mb-4">
        <h3>Lead Payments</h3>
    </div>
    <!-- payment lead -->
    <div id="paymentModal" class="payment-modal" style="display:none">
        <div class="payment-modal-content">
            <div class="payment-heading-wrapper"> 
                <h2>Payments</h2>
                <span class="view-payment-modal-close">&times;</span>   
            </div>
            <div id="paymentContent" class="overflow-auto">
                <table class="payment-modal-table">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Vendor</th>
                            <th>Created On</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody id="leads-payment-list">

                    </tbody>
                </table>
                <div class="modal-loader" style="display:none;"><i class="fas fa-spinner"></i></div>
            </div>
        </div>
    </div>

    <div class="lead-payments-content-main-wrapper">
        <div class="lead-payments-filters d-flex justify-content-between flex-column flex-md-row gap-3">
            <form id="payments-filters-form" method="get">
                <!-- Search By Value -->
                <input class="w-100 w-md-none mb-3 " type="text" id="search_payment" name="search_payment" placeholder="Search Keyword" value="" />
                <!-- Trek Date -->
                Trek Date: <input class="w-100 w-md-none mb-3 " type="date" id="trek_date" name="trek_date" placeholder="Select Trek Date" value="" />
                <!-- Payment Date -->
                Payment Date: <input class="w-100 w-md-none mb-3 " type="date" id="payment_date" name="payment_date" placeholder="Select Payment Date" value="" />
                <!-- amount -->
                <input class="w-100 w-md-none mb-3 " type="number" id="amount" name="amount" placeholder="Enter Amount" min="0" step="any" value="" />
                <!-- Apply Filters Button -->
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <button type="button" class="btn btn-secondary ms-2" id="clear-payment-filters">Clear</button>

            </form>

            <!-- Leads Per Page Dropdown -->
            <div class="payments-per-page" >
                <select id="leads_per_page">
                    <option value="10">10 Leads Per Page</option>
                    <option value="15" selected>15 Leads Per Page</option>
                    <option value="25">25 Leads Per Page</option>
                    <option value="50">50 Leads Per Page</option>
                </select>
            </div>
        </div>
        <div class="payments-pagination" id="payments-pagination"></div>
        <div class="lead-payments-content-table-wrapper w-100">
            <table class="table table-bordered table-striped mt-3">
            <thead class="thead-dark">
                    <tr>
                        <th>Lead Details</th>
                        <th>Trek Date</th>
                        <th>Vendor</th>
                        <th>Payment</th>                        
                        <th>Payment Date</th>                        
                        <th>By</th>                        
                        <th>Actions</th>                        
                    </tr>
                </thead>
                <tbody id="lead-payments-listing-table-body"></tbody>
            </table>
           
        </div>
        <div class="payments-pagination" id="payments-pagination"></div>
    </div>
</div>

<script>
    // On page load, fetch all leads
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function renderPagination(pagination) {
        const { current_page, last_page } = pagination;

        let html = '<div class="select-pagination-page">';

        // Previous
        if (current_page > 1) {
            html += `<a href="#" class="pagination-link" data-page="${current_page - 1}">&laquo;</a>`;
        }

        let start_page = Math.max(1, current_page - 1);
        let end_page = Math.min(last_page, current_page + 1);

        if (current_page === 1) {
            end_page = Math.min(last_page, 3);
        }
        if (current_page === last_page) {
            start_page = Math.max(1, last_page - 2);
        }

        // First page and "..."
        if (start_page > 1) {
            html += `<a href="#" class="pagination-link" data-page="1">1</a>`;
            if (start_page > 2) {
                html += `<a href="#" class="pagination-link">...</a>`;
            }
        }

        // Middle page numbers
        for (let i = start_page; i <= end_page; i++) {
            let active = (i === current_page) ? 'active' : '';
            html += `<a href="#" class="pagination-link ${active}" data-page="${i}">${i}</a>`;
        }

        // Last page and "..."
        if (end_page < last_page) {
            if (end_page < last_page - 1) {
                html += `<a href="#" class="pagination-link">...</a>`;
            }
            html += `<a href="#" class="pagination-link" data-page="${last_page}">${last_page}</a>`;
        }

        // Next
        if (current_page < last_page) {
            html += `<a href="#" class="pagination-link" data-page="${current_page + 1}">&raquo;</a>`;
        }
        html += `</div> `
        document.getElementById('payments-pagination').innerHTML = html;

        // Re-bind click listeners after pagination update
        // bindPaginationEvents();
    }

    // pagination button click
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('pagination-link') && e.target.dataset.page) {
            e.preventDefault();
            const page = parseInt(e.target.dataset.page);
            const perPage = document.getElementById('leads_per_page').value;
            if (!isNaN(page)) {
                fetchPaymentListing({ page:page,per_page:perPage }); 
            }
        }
    });

    const fetchPaymentListing = (filters = {}) => {
        // Build query string from filters
        const query = new URLSearchParams(filters).toString();
        fetch("{{ url('admin/paymentListing') }}" + (query ? `?${query}` : ''), {
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const tbody = document.getElementById('lead-payments-listing-table-body');

                if (!data.data.length) {
                    tbody.innerHTML = '<tr><td colspan="9">No Payments found.</td></tr>';
                    return;
                }

                let html = '';
                data.data.forEach(lead => {
                    html += `
                        
                        <tr data-lead-id="${lead.id}" >
                            <td>
                                <strong>Trek:</strong> ${lead.trek_name}<br>
                                <strong>Name:</strong> ${lead.name}<br>
                                <strong>Phone:</strong> +${lead.phone}<br>
                                <strong>Email:</strong> ${lead.email}<br>
                                <strong>Group Size:</strong> ${lead.no_of_people}
                            </td>
                            <td>${lead.trek_date}</td>
                            <td>${lead.vendor_name}</td>
                            <td>${lead.amount}</td>
                            <td>${lead.created_on}</td>
                            <td>${lead.user_name}</td>
                            <td class="lead-actions">
                                <button class=" view-payments-btn btn btn-success btn-sm edit-btn" data-id="${lead.lead_id}">View</button>
                            </td>
                        </tr>`;

                });

                tbody.innerHTML = html;
                const pagination = data.pagination;
                renderPagination(pagination);
            })
            .catch(error => {
                console.error('Error fetching payments:', error);
                document.getElementById('lead-payments-listing-table-body').innerHTML =
                    '<tr><td colspan="9">Failed to load payments</td></tr>';
            });
    };
    fetchPaymentListing();

    // Handle form submit
    document.getElementById('payments-filters-form').addEventListener('submit', e => {
        e.preventDefault();
        const filters = {
            search_payment: document.getElementById('search_payment').value.trim(),
            trek_date: document.getElementById('trek_date').value,
            payment_date: document.getElementById('payment_date').value,
            amount: document.getElementById('amount').value,
        };

        // Remove empty filters
        Object.keys(filters).forEach(key => {
            if (!filters[key]) delete filters[key];
        });

        fetchPaymentListing(filters);
    });

    //per page leads 
    document.addEventListener('change',function (event) {
        const target = event.target;
        if (target && target.matches('#leads_per_page')) {
            const noOfLeads = target.value;
            // target.disabled = true;
            fetchPaymentListing({per_page:noOfLeads});
        }
    });

    // Clear filters
    document.getElementById('clear-payment-filters').addEventListener('click', () => {
        document.getElementById('search_payment').value = '';
        document.getElementById('trek_date').value = '';
        document.getElementById('payment_date').value = '';
        document.getElementById('amount').value = '';
        fetchPaymentListing();
    });

    //payment view modal
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.view-payments-btn');
        if (target) { 
            document.getElementById('leads-payment-list').innerHTML = "<p>Loading...</p>";
            document.querySelector('.payment-modal').style.display = 'block';
            id = target.getAttribute('data-id');

            // payment list 
            fetch(`/admin/${id}/payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(response => {
                if(!response.ok){
                    return response.text().then(text => {
                        throw new Error(`HTTP ${response.status}: ${text}`)
                    });
                }
                return response.json();
            })
            .then(result => {
                document.getElementById('leads-payment-list').innerHTML = result.html;
            })
            .catch(error => {
                console.error('Payment list error:', error.message);
            })
            .finally(() => {
                // Always hide loader when done (success or error)
                document.getElementById('modal-loader').style.display = 'none';
            });
        }
    });
    
    //close payment view modal
    document.querySelector('.view-payment-modal-close').addEventListener('click', () => {
        document.querySelector('.payment-modal').style.display = 'none';
        document.getElementById('leads-payment-list').innerHTML = "";
    });

</script>
<style>
    .lead-payments-content-table-wrapper {
        overflow: auto;
        font-size: 15px;
    }

    .lead-payments-content-table-wrapper table th{
        background-color: #2271b1;
        color: white;
    }
    .lead-payments-content-table-wrapper table th,
    .lead-payments-content-table-wrapper table td {
        padding: 12px 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .lead-payments-filters input,
    .lead-payments-filters select {
        margin-bottom: 10px;
        height: 40px !important;
        min-width: 180px !important;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 14px;
        outline: none;
    }

    .payments-pagination {
        min-height: 42px;
    }

    .select-pagination-page {
        display: inline-flex;
        border: 2px solid #acbfd3;
        border-radius: 10px;
        overflow: hidden;
        margin: 12px 12px 12px 4px;
        float: right;
    }

    .pagination-link {
        display: inline-block;
        padding: 3px 10px;
        text-decoration: none;
        color:rgb(54, 53, 53);
        font-weight: bold;
        border-right: 1px solid #9ac4f0;
    }
    .pagination-link:hover, .pagination-link.active {
        background: #2271B1;
        color: #fff;
    }
    a:hover {
        color: white;
        text-decoration: none;
    }

    .payment-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        overflow: auto;
        max-height: 100vh;
    }

    .payment-modal-content {
        background: #fff;
        margin: 6% auto;
        width: 80%;
        max-width: 700px;
        border-radius: 8px;
        position: relative;
    }

    .payment-heading-wrapper {
        display: flex;
        justify-content: space-between;
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
    }
    .payment-heading-wrapper h2 {
        font-size: 20px;
        font-weight: 600;
    }
    #paymentContent{
        padding: 20px;
    }
    .add-payment-option form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    .add-payment-option form input,
    .add-payment-option form select,
    .add-payment-option form select, option{
        width: 200px;
    }
    .payment-modal-close{
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 24px;
        cursor: pointer;
        border-radius: 100%;
    }

    .payment-modal-table {
        width: 100% !important;
        max-width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 8px;
        overflow-x: scroll;
        font-size: 14px;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;
    }

    .payment-modal-table thead,
    .calls-modal-table thead {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    /* Improve table cell spacing */
    .payment-modal-table th,
    .payment-modal-table td {
        padding: 10px 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .view-payment-modal-close {
        position: absolute;
        top: 10px;
        right: 14px;
        background: none;
        border: none;
        font-size: 24px;
        color: #888;
        cursor: pointer;
    }
    .table-bordered>:not(caption)>*>* {
        border-width: 0;
    }
    @media (min-width: 768px) {
        .lead-payments-filters input,
        .lead-payments-filters select{
            width:auto !important;
            margin-bottom: 10px;
        }
    }
</style>
@endsection