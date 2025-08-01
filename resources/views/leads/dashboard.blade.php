@extends('layouts.app')

@section('content')
<div>
    <div class="mb-4 d-flex align-items-start justify-content-between align-items-md-center flex-wrap" >
        <h3>Leads</h3>
        <button class="btn btn-primary text-sm align-self-end" name="add-new-lead" id="add-new-lead"> Add Lead</button>
    </div>
    <!-- FILTERS -->
    <div class="leads-filer-wrapper d-flex justify-content-between gap-3">
        <form id="filter-form" class="w-100 fs-6 text" >
            <div class="d-flex gap-3 gap-md-2 align-items-center flex-wrap lead-filter-inner-wrapper">
                <!-- Phone -->
                <div class="d-flex align-items-md-center flex-column flex-md-row ">
                    <input type="text" name="phone" id="filter-phone" placeholder="Phone" class="form-control w-100 w-md-auto">
                </div>
                <!-- Search By Trek(Product/ Page/ Taxonomy) -->
                <div class="d-flex align-items-md-center flex-column flex-md-row ">
                    <select id="multi_filter" name="multi_filter[]" multiple class="form-control w-100 w-md-auto overflow-auto">
                        <optgroup label="Trek Names" id="trek-options"></optgroup>
                        <optgroup label="Pages" id="page-options"></optgroup>
                        <optgroup label="Taxonomies" id="taxonomy-options"></optgroup>
                    </select>
                </div>
                <!-- Trek Date -->
                <div class="d-flex align-items-md-center flex-column flex-md-row ">
                    <!-- <label for="filter-trek-date" class="me-2 mb-0">Trek Date:</label> -->
                    <!-- <input type="date" name="trek_date" id="filter-trek-date" class="form-control w-100 w-md-auto"> -->
                    <input type="text" id="filter-trek-date" name="trek_date" placeholder="Select Trek Date" class="form-control w-100 w-md-auto"/>
                </div>
                <!-- Lead Date -->
                <div class="d-flex align-items-md-center flex-column flex-md-row ">
                    <!-- <label for="filter-lead-date" class="me-2 mb-0">Lead Date:</label> -->
                    <!-- <input type="date" name="lead_date" id="filter-lead-date" class="form-control w-100 w-md-auto"> -->
                    <input type="text" name="lead_date" id="filter-lead-date" placeholder="Select Lead Date" class="form-control w-100 w-md-auto">
                </div>
                <!-- source -->
                <div class="d-flex align-items-md-center flex-column flex-md-row ">
                    <select id="lead_sources" name="lead_sources[]" multiple class="form-control w-100 w-md-auto overflow-auto">
                        <option>Select Lead Source</option>
                        <option value="Popup">Popup</option>
                        <option value="Enquiry">Enquiry</option>
                        <option value="Call">Call</option>
                        <option value="Whatsapp">Whatsapp</option>
                        <option value="Meta">Meta</option>
                        <option value="Google">Google</option>
                        <option value="Abhinav">Abhinav</option>
                        <option value="Kailash">Kailash</option>
                        <option value="Khushwant">Khushwant</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <!-- Booked Only -->
                <div class="col-auto w-auto d-flex align-items-md-center gap-2 flex-row ">
                    <label for="booked_only" class="me-2 mb-0 ">Booked Only:</label>
                    <input type="checkbox" name="booked_only" id="booked_only" class="form-check-input mt-0">
                </div>
                <!-- Buttons -->
                <div class="col-auto w-auto d-flex align-items-md-center gap-2 gap-md-0 flex-column flex-md-row ">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="button" id="clear-filters" class="btn btn-secondary ms-0 ms-md-1 ">Clear </button>
                </div>
            </div>
        </form>
        <div class="leads-pagin" style="float:right">
            <select id="leads_per_page" class="form-control">
                <option value="10">10 Leads Per Page</option>
                <option value="15" selected>15 Leads Per Page</option>
                <option value="25">25 Leads Per Page</option>
                <option value="50">50 Leads Per Page</option>
            </select>
        </div>
    </div>
    <!-- <div class="leads-pagination" id="leads-pagination"></div> -->
    <div class="d-flex w-100 justify-content-between flex-column flex-md-row align-items-center gap-2 mt-4">
        <div class="left-actions w-25 d-flex gap-2">
            <select id="bulk-action" class="form-control w-100 w-md-25">
                <option value="">Bulk Actions</option>
                <option value="delete">Delete</option>
            </select>
            <button id="apply-bulk-action" class="btn btn-primary text-sm">Apply</button>
        </div>
        <div class="leads-pagination align-self-end" id="leads-pagination"></div>
    </div>
    <div class="overflow-auto w-100 ">
        <table class="lead-table-wrapper table table-bordered table-striped mt-3">
            <thead class="thead-dark">
                <tr>
                    <th style="padding-left: 16px;">
                        <input type="checkbox" id="select-all-checkbox">
                    </th>
                    <th>Lead Details</th>
                    <th>Message</th>
                    <th style="max-width: 250px;">All Messages</th>
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
</div>

<div class="modal-overlay" style="display:none;"></div>

<!-- add lead modal -->
<div class="new-lead-back-modal" style="display:none;" >
    <div class="new-lead-back-content" >
        <div class="header-lead">
            <h2>Add New Lead</h2>
            <span class="new-lead-back-close">&times;</span>
        </div>
        <form id="new-lead-back-form">
            <div class="input-container">
                <p><label>Name: <input type="text" name="name" placeholder="Name" ></label></p>
                <p><label>Email: <input type="email" name="email" placeholder="Email" ></label></p>
            </div>

            <div class="input-container">
                <p>
                    <label>Phone Number:
                        <!-- <input type="tel" name="phone_number" id="new-lead-back-phone-number" required> -->
                        <input type="tel" name="full_phone" id="phone" class="phone-input" required>
                        <input type="hidden" name="country_code" id="country_code">
                        <input type="hidden" name="phone_number" id="phone_number">
                    </label>
                </p>
                 <!-- <p>
                    <label>Country Code:
                        <input type="number" name="country_code" required>
                    </label>
                </p> -->
                <p>
                    <label>Group Size: <input type="number" name="no_of_people" placeholder="Group Size" ></label>
                </p>
            </div>

            <div class="input-container">
                 <!-- <p>
                    <label>Group Size: <input type="number" name="no_of_people" placeholder="Group Size" ></label>
                </p> -->
                <p><label>Select Trek:<br>
                        <select name="type_id" id="product-select">
                            <option value="">Select Trek</option>
                        </select>
                    </label>
                </p>
                <p><label>Select Source:<br>
                        <select name="lead_source" id="add_lead_source">
                            <option value="">Select Lead Source</option>
                            <option value="Popup">Popup</option>
                            <option value="Enquiry">Enquiry</option>
                            <option value="Call">Call</option>
                            <option value="Whatsapp">Whatsapp</option>
                            <option value="Meta">Meta</option>
                            <option value="Google">Google</option>
                            <option value="Abhinav">Abhinav</option>
                            <option value="Kailash">Kailash</option>
                            <option value="Khushwant">Khushwant</option>
                            <option value="Vendor">Vendor</option>
                            <option value="Other">Other</option>                                         
                        </select>
                    </label></p>
            </div>

            <div class="input-container">
                <p><label>Trek Date: <input type="date" name="trek_date" ></label></p>
                <p><label>Lead Date: <input type="datetime-local" name="created_at"></label></p>
            </div>

            <div class="input-container">
                <p><label>Message: <textarea name="message" placeholder="Your Message Here.."></textarea></label></p>
            </div>

            <div class="input-contaienr">
                <span class="phone-error" style="color: red; font-weight: 500; font-size: 17px;"></span>
            </div>

            <div class="submit-container">
                <p><button type="submit" class="btn btn-primary text-sm">Submit</button></p>
            </div>
        </form>
    </div>
</div>

<!-- Edit Lead Modal -->
<div class="edit-lead-modal" style="display:none;">
    <div class="edit-lead-content">
        <div class="header-lead">
            <h2>Edit Lead</h2>
            <span class="edit-lead-close">&times;</span>
        </div>
        <div id="lead-edit-loader">Loading...</div>
        <form id="edit-lead-form" style="display:none;">
            <input type="hidden" name="lead_id">
            <div class="input-container">
                <p>
                    <label>Name</label>
                    <input type="text" name="name" class="form-control">
                </p>
                <p>
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </p>
            </div>
            <div class="input-container">
                <p>
                    <label>Phone</label>
                    <!-- <input type="text" name="phone_number" class="form-control"> -->
                    <input type="tel" name="phone_number" id="edit-lead-phone-number" required>
                    <input type="hidden" name="country_code" id="edit-lead-country-code">
                </p>
                <!-- <p>
                    <label>Country Code</label>
                    <input type="text" name="country_code" class="form-control">
                </p> -->
                <p>
                    <label>No. of People</label>
                    <input type="number" name="no_of_people" class="form-control">
                </p>
            </div>
            <div class="input-container">
                <p>
                    <label>Select Trek</label>
                    <select name="type_id" id="type_id" class="form-control">
                        <option value="">Select Trek</option>
                    </select>
                </p>
                <p>
                    <label>Lead Source</label>
                    <select name="lead_source" class="form-control">
                        <option value="">Select Lead Source</option>
                        <option value="Popup">Popup</option>
                        <option value="Enquiry">Enquiry</option>
                        <option value="Call">Call</option>
                        <option value="Whatsapp">Whatsapp</option>
                        <option value="Meta">Meta</option>
                        <option value="Google">Google</option>
                        <option value="Abhinav">Abhinav</option>
                        <option value="Kailash">Kailash</option>
                        <option value="Khushwant">Khushwant</option>
                        <option value="Vendor">Vendor</option>
                        <option value="Other">Other</option>                                         
                    </select>
                </p>
            </div>
            <div class="input-container">
                <p>
                    <label>Trek Date</label>
                    <input type="date" name="trek_date" class="form-control">
                </p>
                <p>
                    <label>Lead Date</label>
                    <input type="datetime-local" name="created_at" class="form-control">
                </p>
            </div>
            <div class="input-container">
                <p>
                    <label>Message</label>
                    <textarea name="message" class="form-control"></textarea>
                </p>
            </div>
            <button type="submit" class="btn btn-primary">Update Lead</button>
        </form>
    </div>
</div>

<!-- book modal -->
<div id="bookModal" class="book-modal" style="display:none">
    <div class="book-modal-content">
        <div class="book-modal-header">
            <h2>Booking Details</h2>
            <span class="book-close">&times;</span>

        </div>
        <form name="book_lead_form" id="book_lead_form" onsubmit="return false;" > 
            <input type="hidden" id="lead_id" name="lead_id">
            <div class="book-input">
                <label for="amount">Book Amount: </label>
                <input type="number" id="amount" name="amount" placeholder="Enter book amount" min="0" step="any" required>
            </div>
            <div class="book-input">
                <label for="paid-to">Amount Paid To: </label>
                <select id="paid-to" name="paid-to" required>
                    <option>Select amount paid to</option>                 
                </select>
            </div>

            <div class="book-save-btn">
                <button type="button" id="save-book" class="save-book">Save</button>
                <button type="button" id="cancel-book" class="book-close">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- payment -->
<div id="paymentModal" class="payment-modal">
    <div class="payment-modal-content">
        <div class="payment-heading-wrapper"> 
            <h2>Lead Payments</h2>
            <span class="payment-modal-close">&times;</span>   
        </div>
        <div id="paymentContent">
            <div class="add-payment-option">
                <form id="add-payment-form" name="add-payment-form" >
                    <input type="hidden" name="lead_id" id="lead-id" >
                    <input type="number" name="amount" placeholder="Enter Amount" min="0" step="any" required>
                    <select id="payment-paid-to" name="vendor_id" required>
                        <option>Select amount paid to</option>                  
                    </select>
                    <input type="datetime-local" name="created_on" >
                    <button type="submit" class="button button-primary add-lead-payment">Add Payment</button>
                </form>
            </div>
            <table class="payment-modal-table">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Vendor</th>
                        <th>Paid On</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody id="leads-payment-list">

                </tbody>
            </table>
            <div class="modal-loader" id="modal-loader" style="display:none;"><i class="fas fa-spinner"></i></div>
        </div>
    </div>
</div>

<!-- lead call -->
<div id="callModal" class="call-modal">
    <div class="call-modal-content">
        <div class="call-heading-wrapper"> 
            <h2>Lead calls</h2>
            <span class="call-modal-close">&times;</span>   
        </div>
        <div id="callContent" class="overflow-auto">
            <!-- <h1>List of all the calls </h1> -->
            <table class="calls-modal-table">
                <thead>
                    <tr>
                        <th>Phone No.</th>
                        <th>Duration</th>
                        <th>Call Time</th>
                        <th>Recording</th>
                        <th>Called By</th>
                    </tr>
                </thead>
                <tbody id="leads-call-list">

                </tbody>
            </table>
            <div class="modal-loader" id="call-modal-loader" style="display:none;"><i class="fas fa-spinner"></i></div>
        </div>
    </div>
</div>

<!-- Log modal -->
<div id="leadLogsModal" class="lead-log-modal" style="display:none">
    <div class="lead-log-modal-content">
        <div class="lead-heading-wrapper"> 
            <h2>Lead Activities</h2>
            <span class="lead-log-modal-close">&times;</span>
            
        </div>
        <div id="leadLogsContent">
        </div>
    </div>
</div>



@push('scripts')
<script>

    // On page load, fetch all leads
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    document.addEventListener('DOMContentLoaded', function() {
        setupFlatpickrWithModeToggle('filter-trek-date');
        setupFlatpickrWithModeToggle('filter-lead-date');
    });

    //multifilter 
    $('#multi_filter').select2({
        placeholder: "Select Trek Names/ Pages",
        allowClear: true
    });

    function loadFilterOptions() {
        fetch('/admin/treksPages')
        .then(response => response.json())
        .then(response => {
            if (response) {
                console.log(response);
                const trekOptions = document.getElementById('trek-options');
                const pageOptions = document.getElementById('page-options');
                const taxonomyOptions = document.getElementById('taxonomy-options');

                trekOptions.innerHTML = '';
                pageOptions.innerHTML = '';
                taxonomyOptions.innerHTML = '';

                response.treks.forEach(trek => {
                    trekOptions.insertAdjacentHTML('beforeend',
                        `<option value="trek_${trek.id}">${trek.name}</option>`
                    );
                });

                response.pages.forEach(page => {
                    pageOptions.insertAdjacentHTML('beforeend',
                        `<option value="page_${page.id}">${page.name}</option>`
                    );
                });

                response.taxonomies.forEach(tax => {
                    taxonomyOptions.insertAdjacentHTML('beforeend',
                        `<option value="tax_${tax.id}">${tax.name}</option>`
                    );
                });
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });

    }
    loadFilterOptions();

    // source
    $('#lead_sources').select2({
        placeholder: "Select Lead Source",
        allowClear: true
    });

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
        document.getElementById('leads-pagination').innerHTML = html;

        // Re-bind click listeners after pagination update
        // bindPaginationEvents();
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('pagination-link') && e.target.dataset.page) {
            e.preventDefault();
            const page = parseInt(e.target.dataset.page);
            const perPage = document.getElementById('leads_per_page').value;
            if (!isNaN(page)) {
                fetchLeads({ page:page,per_page:perPage }); 
            }
        }
    });

    const fetchLeads = (filters = {}) => {
        // Build query string from filters
        const query = new URLSearchParams(filters).toString();
        fetch("{{ url('admin/getleads') }}" + (query ? `?${query}` : ''), {
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const tbody = document.getElementById('lead-table-body');

                if (!data.data.length) {
                    tbody.innerHTML = '<tr><td colspan="9">No leads found.</td></tr>';
                    return;
                }

                let html = '';
                data.data.forEach(lead => {
                    const bgClass = lead.is_book == 1 ? 'booked-row' : (lead.is_cancel == 1 ? 'cancelled-row' : '');
                    let bookBtnClass, bookIconClass;
                    if (lead.is_book == 1) {
                        bookBtnClass = 'unbook-btn';
                        bookIconClass = 'fa-toggle-on';
                    } else {
                        bookBtnClass = 'book-btn';
                        bookIconClass = 'fa-toggle-off';
                    }

                    let cancelBtnClass, cancelIconClass;
                    if (lead.is_cancel == 1) {
                        cancelBtnClass = 'uncancel-btn';
                        cancelIconClass = 'fa-toggle-on';
                    } else {
                        cancelBtnClass = 'cancel-btn';
                        cancelIconClass = 'fa-toggle-off';
                    }

                    // after bgClass, bookBtnClass, cancelBtnClass, etc
                    let messagesHTML = '';
                    if (lead.all_messages && Array.isArray(lead.all_messages)) {
                        lead.all_messages.forEach(msg => {
                            const formatted = msg.time;
                            const direction = msg.direction == 0 ? 'received' : 'sent';
                            const body = msg.body ? msg.body.replace(/\n/g, '<br>') : `*${msg.type}`;
                            messagesHTML += `
                                <span class="lead_msg_item ${direction}">
                                    ${body}<br>
                                    <b style="font-size: 10px;">${formatted} (${msg.device_name})</b>
                                </span>
                            `;
                        });
                    }

                    const leadMsgHTML = `
                        <div class="lead_msg_parent">
                            ${messagesHTML}
                        </div>
                        <style>
                            .lead_msg_parent {
                                max-height: 150px;
                                overflow: scroll;
                                max-width: 250px;
                            }
                            .lead_msg_item {
                                padding: 10px;
                                display: block;
                                background: green;
                                color: #fff;
                                margin-bottom: 2px;
                                border-radius: 4px;
                            }
                            .sent {
                                max-width: 80%;
                                position: relative;
                                left: 20%;
                            }
                            .received {
                                max-width: 80%;
                            }
                        </style>
                    `;
                    html += `
                        
                        <tr data-lead-id="${lead.id}" class="${bgClass}">
                            <td style="padding-left: 16px;">
                                <input type="checkbox" class="lead-checkbox" value="${lead.id}">
                            </td>
                            <td>
                                <strong>Trek:</strong> ${lead.trek_name}<br>
                                <strong>Name:</strong> ${lead.name}<br>
                                <strong>Phone:</strong> +${lead.country_code}${lead.phone}<br>
                                <strong>Email:</strong> ${lead.email}<br>
                                <strong>Group Size:</strong> ${lead.no_of_people}
                            </td>
                            <td>${lead.message}</td>
                            <td>${leadMsgHTML}</td>
                            <td>${lead.trek_date}</td>
                            <td>${new Date(lead.created_at).toLocaleString()}</td>
                            <td><span class="lead_table_source" style="background-color:${lead.backgroud_color}" > ${lead.source ? lead.source : "" }</span></td>
                            <td>
                                <select class="lead-status form-control" data-lead-id="${lead.id}" data-old-status="${lead.current_status}">
                                ${
                                    lead.statuses.length > 0
                                        ? lead.statuses.map(status => {
                                            const selected = status.status === lead.current_status ? 'selected' : '';
                                            return `<option value="${status.status}" data-status-id="${status.id}" ${selected}>${status.status}</option>`;
                                        }).join('')
                                        : `<option disabled selected>New</option>`
                                }
                                </select>
                            </td>
                            <td>
                                <button class="${bookBtnClass}" data-id="${lead.id}">
                                    <i class="fa-solid ${bookIconClass}"></i>
                                </button>
                            </td>

                            <td>
                                <button class="${cancelBtnClass}" data-id="${lead.id}">
                                    <i class="fa-solid ${cancelIconClass}"></i>
                                </button>
                            </td>
                            <td class="lead-actions">
                                <button class="btn btn-info btn-sm edit-btn mb-3" data-id="${lead.id}">Edit</button>
                                <button class="btn btn-warning btn-sm view_lead_logs_btn mb-3" data-id="${lead.id}">Logs</button>
                                <button class="btn btn-dark btn-sm payments-btn mb-3" data-id="${lead.id}">Payments</button>
                                <button class="leads_call_history_btn mb-3" data-id="${lead.id}">
                                    <i class="fa-solid fa-phone">
                                        <span class="lead_call_count">${lead.call_count}</span>
                                    </i>    
                                </button>
                            </td>

                        </tr>`;

                });

                tbody.innerHTML = html;
                const pagination = data.pagination;
                renderPagination(pagination);
            })
            .catch(error => {
                console.error('Error fetching leads:', error);
                document.getElementById('lead-table-body').innerHTML =
                    '<tr><td colspan="9">Failed to load leads</td></tr>';
            });
    };
    fetchLeads();
    // Handle form submit
    document.getElementById('filter-form').addEventListener('submit', e => {
        e.preventDefault();
        const multiSelect = document.getElementById('multi_filter');
        const selectedValues = Array.from(multiSelect.selectedOptions).map(opt => opt.value);
        const leadSources = document.getElementById('lead_sources');
        const leadSourcesValues = Array.from(leadSources.selectedOptions).map(opt => opt.value);
        const filters = {
            phone: document.getElementById('filter-phone').value.trim(),
            trek_date: document.getElementById('filter-trek-date').value,
            lead_date: document.getElementById('filter-lead-date').value,
            booked_only: document.getElementById('booked_only').checked ? 1 : 0,
            multi_filter: selectedValues,
            leadSources: leadSourcesValues,
        };
        Object.keys(filters).forEach(key => {
            const val = filters[key];
            if (
                val === '' ||
                val === null ||
                (Array.isArray(val) && val.length === 0)
            ) {
                delete filters[key];
            }
        });

        fetchLeads(filters);
    });

    //per page leads 
    document.addEventListener('change',function (event) {
        const target = event.target;
        if (target && target.matches('#leads_per_page')) {
            const noOfLeads = target.value;
            // target.disabled = true;
            fetchLeads({per_page:noOfLeads});
        }
    });

    // Clear filters
    document.getElementById('clear-filters').addEventListener('click', () => {
        document.getElementById('filter-phone').value = '';
        document.getElementById('filter-trek-date').value = '';
        document.getElementById('filter-lead-date').value = '';
        const select = document.getElementById('multi_filter');
        if (select) {
            [...select.options].forEach(o => o.selected = false);
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }
        const sources = document.getElementById('lead_sources');
        if (sources) {
            [...sources.options].forEach(o => o.selected = false);
            sources.dispatchEvent(new Event('change', { bubbles: true }));
        }
        fetchLeads();
    });

    // Bind action buttons
    document.getElementById('lead-table-body').addEventListener('click', function (e) {
        const target = e.target;
        //edit modal
        if (target.classList.contains('edit-btn')) {

            const id = target.getAttribute('data-id');
            const modal = document.querySelector('.edit-lead-modal');
            const form = document.getElementById('edit-lead-form');
            const loader = document.getElementById('lead-edit-loader');
            
            modal.style.display = 'block';
            loader.style.display = 'block';
            form.style.display = 'none';

            fetch('/admin/treks')
            .then(response => {
                if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
                }
                return response.json();
            })
            .then(result => {
                const treks = result.treks;

                if (!Array.isArray(treks)) {
                throw new Error('Expected treks to be an array');
                }

                const select = document.getElementById('type_id');
                select.innerHTML = '<option value="">Select Trek</option>';
                treks.forEach(trek => {
                const option = document.createElement('option');
                option.value = trek.ID;
                option.textContent = `${trek.post_name}`; 
                select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error.message);
            });

            fetch(`/admin/leads/${id}`)
            .then(res => res.json())
            .then(lead => {
                loader.style.display = 'none';
                form.style.display = 'block';

                // Destroy existing instance if any (prevent multiple inits)
                if (window.iti) {
                    window.iti.destroy();
                }

                // Reinitialize the plugin when the modal opens
                iti = window.intlTelInput(document.querySelector("#edit-lead-phone-number"), {
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    preferredCountries: ["in", "th"],
                    separateDialCode: true,
                });
                
                form.lead_id.value = lead.id;
                form.name.value = lead.name || '';
                form.email.value = lead.email || '';
                form.phone_number.value = lead.phone || '';
                form.country_code.value = lead.country_code || '';
                form.no_of_people.value = lead.no_of_people || '';
                form.type_id.value = lead.type_id;
                form.lead_source.value = lead.source || '';
                form.trek_date.value = lead.trek_date ? lead.trek_date.slice(0, 10) : '';
                form.created_at.value = lead.created_on ? lead.created_on : '';
                form.message.value = lead.message || '';

                // Set the country code via plugin
                const allCountries = window.intlTelInputGlobals.getCountryData();
                let countryCodeISO = '';
                if (lead.country_code) {
                    const matchedCountry = allCountries.find(
                        country => country.dialCode === lead.country_code.toString()
                    );
                    if (matchedCountry) {
                        countryCodeISO = matchedCountry.iso2;
                        iti.setCountry(countryCodeISO);
                    }
                }
                
                // Set phone number (without dial code, plugin adds it)
                const nationalNumber = lead.phone || '';
                form.phone_number.value = nationalNumber;

            });
        }
        //save book
        document.querySelector('.save-book').onclick = function (e) {
            e.preventDefault();
            const id = this.getAttribute('data-bookid');
            // Get input values
            const amount = document.getElementById('amount').value;
            const paidTo = document.getElementById('paid-to').value;

            // Validate input (optional but recommended)
            if (!amount || !paidTo || paidTo === 'Select amount paid to') {
                alert('Please fill out all fields');
                return;
            }

            const modal = document.querySelector('.book-modal');
            const form = document.getElementById('book_lead_form');

            modal.style.display = 'none';
            form.reset();
            
            fetch(`/admin/leads/${id}/book`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    lead_id: id,
                    amount: amount,
                    vendor_id: paidTo
                })
            })
            .then(res => {
                return res.json();
            })
            .then(json => {
                if (json.success) {
                let row = target.closest('tr');
                if (row) {
                    row.style.backgroundColor = '#dff2bf';
                }

                // Replace the cancel button
                let unbookBtn = document.createElement('button');
                unbookBtn.className = 'unbook-btn';
                unbookBtn.id = 'unbook-btn';
                unbookBtn.setAttribute('data-id', id);
                unbookBtn.innerHTML = '<i class="fa-solid fa-toggle-on"></i>';

                target.replaceWith(unbookBtn);

                // If the lead was unbooked, replace the book button too
                if (json.was_uncanceled) {
                    const uncancelBtn = document.querySelector(`.uncancel-btn[data-id="${id}"]`);
                    if (uncancelBtn) {
                        const newcancelBtn = document.createElement('button');
                        newcancelBtn.className = 'cancel-btn';
                        newcancelBtn.id = `cancel-btn${id}`;
                        newcancelBtn.setAttribute('data-id', id);
                        newcancelBtn.innerHTML = '<i class="fa-solid fa-toggle-off"></i>';

                        uncancelBtn.replaceWith(newcancelBtn);
                    }
                }
                } else {
                    alert(json.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });

        }
        //view logs
        if (target.classList.contains('view_lead_logs_btn')) {
            document.querySelector('.lead-log-modal').style.display = 'block';
            id = target.getAttribute('data-id');

            document.getElementById('modal-loader').style.display = 'block';

            document.getElementById('leadLogsContent').innerHTML = 'Loading logs...';

            fetch(`/admin/${id}/logs`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('leadLogsContent').innerHTML = data.html;
            })
            .catch(error => {
                document.getElementById('leadLogsContent').innerHTML = '<div class="text-danger">Error loading logs.</div>';
            });
        }
        // payments
        if (target.classList.contains('payments-btn')) {
            document.querySelector('.payment-modal').style.display = 'block';
            id = target.getAttribute('data-id');
            document.getElementById('lead-id').value = id;

            document.getElementById('modal-loader').style.display = 'block';

            fetch('/api/madtrek/v1/vendors')
            .then(response => {
                if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
                }
                return response.json();
            })
            .then(result => {
                const vendors = result.vendors;

                if (!Array.isArray(vendors)) {
                throw new Error('Expected vendors to be an array');
                }

                const select = document.getElementById('payment-paid-to');

                // Optional: Clear existing options except placeholder
                select.innerHTML = '<option>Select amount paid to</option>';

                vendors.forEach(vendor => {
                const option = document.createElement('option');
                option.value = vendor.id;
                option.textContent = `${vendor.name} (${vendor.type})`; // optional format
                select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error.message);
            });

            //save payment
            document.getElementById('add-payment-form').addEventListener('submit', function (e) {
                e.preventDefault();

                const form = e.target;
                const formData = new FormData(form); // Collect all form fields

                // Optional: Show loader
                document.querySelector('.modal-loader').style.display = 'flex';

                fetch(`/admin/addpayment`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(result => {
                    // Hide modal or reset form
                    form.reset();
                    document.querySelector('.payment-modal').style.display = 'none';
                })
                .catch(error => {
                    console.error('Save error:', error.message);
                    alert('Failed to save payment.');
                })
                .finally(() => {
                    // Hide loader
                    document.querySelector('.modal-loader').style.display = 'none';
                });
            });

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

    // Open Add lead modal
    document.addEventListener('click', function(e) {
        const target = e.target.closest('#add-new-lead');
        if (target) {
            e.preventDefault();
            fetch('/admin/treks')
            .then(response => {
                if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
                }
                return response.json();
            })
            .then(result => {
                const treks = result.treks;

                if (!Array.isArray(treks)) {
                throw new Error('Expected treks to be an array');
                }

                const select = document.getElementById('product-select');

                // Optional: Clear existing options except placeholder
                select.innerHTML = '<option value="">Select Trek</option>';

                treks.forEach(trek => {
                const option = document.createElement('option');
                option.value = trek.ID;
                option.textContent = `${trek.post_name}`; 
                select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error.message);
            });


            InitializeItiPhoneInput();

            document.querySelector('.new-lead-back-modal').style.display = 'block';
        }
    });

    // Close Add lead modal
    document.querySelector('.new-lead-back-close').addEventListener('click', () => {
        document.querySelector('.new-lead-back-modal').style.display = 'none';
    });

    // Add lead Form submission
    document.getElementById('new-lead-back-form').addEventListener('submit', function (e) {
        e.preventDefault();
        // var loader = document.getElementById('.role-loader');
        const form = e.target;
        // Email validation
        var emailField = form.email.value;
        var emailValue = emailField.trim();
        if (emailValue !== "") {
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA0-9]{2,}$/;
            if (!emailPattern.test(emailValue)) {
                alert(" Please enter a valid email address.");
                emailField.focus();
                return;
            }
        }
        const formData = {
            name: form.name.value,
            email: form.email.value,
            phone: form.phone_number.value,
            country_code: form.country_code.value,
            no_of_people: form.no_of_people.value,
            type_id: form.type_id.value,
            trek_date: form.trek_date.value,
            created_at: form.created_at.value,
            source: form.lead_source.value,
            message: form.message.value,
        };
        // loader.style.display = 'block';

        fetch(`/admin/add-lead`, {
        method: 'PUT', 
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(response => {
            // loader.fadeOut().style.display = 'none';
            document.querySelector('.new-lead-back-modal').style.display = 'none';
            fetchLeads();
            form.reset(); 
        })
        .catch(err => {
            console.error('Update error:', err);
            alert('Failed to update lead');
        });

    });

    // Handle Edit Lead Form Submission
    document.getElementById('edit-lead-form').addEventListener('submit', function (e) {
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

        fetch(`/admin/leads/${id}`, {
            method: 'PUT', // or 'POST' if your API uses POST for updates
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
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
    // close edit modal
    document.querySelector('.edit-lead-close').addEventListener('click', () => {
        const form = document.getElementById('edit-lead-form');
        if (form) {
            form.reset();
        }
        document.querySelector('.edit-lead-modal').style.display = 'none';
    });
    //book modal
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.book-btn');
        if (target) {
            e.preventDefault();
            const bookId = target.dataset.id; 
            const saveButton = document.getElementById('save-book');
            saveButton.setAttribute('data-bookId', bookId); 
            document.getElementById('lead_id').value = bookId;
            
            document.querySelector('.book-modal').style.display = 'block';
            fetch('/api/madtrek/v1/vendors')
            .then(response => {
                if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
                }
                return response.json();
            })
            .then(result => {
                const vendors = result.vendors;

                if (!Array.isArray(vendors)) {
                throw new Error('Expected vendors to be an array');
                }

                const select = document.getElementById('paid-to');

                // Optional: Clear existing options except placeholder
                select.innerHTML = '<option>Select amount paid to</option>';

                vendors.forEach(vendor => {
                const option = document.createElement('option');
                option.value = vendor.id;
                option.textContent = `${vendor.name} (${vendor.type})`; // optional format
                select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error.message);
            });
        }
    });
    //unbook
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.unbook-btn');
        if (target) {
            e.preventDefault();
            let cancelBookingReason = prompt("Please enter the reason to unbook", "");

            if (!cancelBookingReason) {
                return;
            }
            const id = target.getAttribute('data-id');

            fetch(`/admin/leads/${id}/unbook`, {
                method: 'Post', 
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    lead_id: id,
                    reason : cancelBookingReason,
                })
            })
            .then(res => {
                return res.json();
            })
            .then(json => {
                if (json.success) {

                    let row = target.closest('tr');
                    if (row) {
                        row.style.backgroundColor = 'transparent';
                    }

                    // Replace the cancel button
                    let bookBtn = document.createElement('button');
                    bookBtn.className = 'book-btn';
                    bookBtn.id = 'book-btn';
                    bookBtn.setAttribute('data-id', id);
                    bookBtn.innerHTML = '<i class="fa-solid fa-toggle-off"></i>';

                    target.replaceWith(bookBtn);

                } else {
                    alert(res.data.message);
                }
            })
            .catch(err => {
                console.error('Booking error:', err);
                alert('Booking failed. Please try again.');
            });
        }
    });
    //cancel
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.cancel-btn');
        if (target) {
            e.preventDefault();
            const id = target.getAttribute('data-id');
            const confirmBooking = confirm("Are you sure you want to mark this lead as cancelled?");
            if (!confirmBooking) {
                return;
            }

            fetch(`/admin/leads/${id}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(res => res.json())
            .then(json => {
                if (json.success) {
                    let row = target.closest('tr');
                    if (row) {
                        row.style.backgroundColor = '#f2bfbf';
                    }

                    // Replace the cancel button
                    let uncancelBtn = document.createElement('button');
                    uncancelBtn.className = 'uncancel-btn';
                    uncancelBtn.id = 'uncancel-btn';
                    uncancelBtn.setAttribute('data-id', id);
                    uncancelBtn.innerHTML = '<i class="fa-solid fa-toggle-on"></i>';
                    target.replaceWith(uncancelBtn);

                    // Replace the book button if it was unbooked
                    if (json.was_unbooked) {
                        const bookBtn = document.querySelector(`.unbook-btn[data-id="${id}"]`);
                        if (bookBtn) {
                            const unbookBtn = document.createElement('button');
                            unbookBtn.className = 'book-btn';
                            unbookBtn.id = 'book-btn';
                            unbookBtn.setAttribute('data-id', id);
                            unbookBtn.innerHTML = '<i class="fa-solid fa-toggle-off"></i>';
                            bookBtn.replaceWith(unbookBtn);
                        }
                    }

                } else {
                    alert(json.message);
                }
            })
            .catch(err => {
                console.error('cancel error:', err);
                alert('cancel failed. Please try again.');
            });
        }
    });
    //uncancel
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.uncancel-btn');
        if (target) {
            e.preventDefault();
            const id = target.getAttribute('data-id');
            const confirmBooking = confirm("Are you sure you want to mark this lead as uncancelled?");
            if (!confirmBooking) {
                return;
            }

            fetch(`/admin/leads/${id}/uncancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            })
            .then(res => res.json())
            .then(json => {
                if (json.success) {
                    let row = target.closest('tr');
                    if (row) {
                        row.style.backgroundColor = 'transparent';
                    }
                    // Replace the uncancel button
                    let cancelBtn = document.createElement('button');
                    cancelBtn.className = 'cancel-btn';
                    cancelBtn.id = 'cancel-btn';
                    cancelBtn.setAttribute('data-id', id);
                    cancelBtn.innerHTML = '<i class="fa-solid fa-toggle-off"></i>';
                    target.replaceWith(cancelBtn);

                } else {
                    alert(json.message);
                }
            })
            .catch(err => {
                console.error('uncancel error:', err);
                alert('uncancel failed. Please try again.');
            });
        }
    });
    //close book modal
    document.querySelector('.book-close').addEventListener('click', () => {
        document.querySelector('.book-modal').style.display = 'none';
        const form = document.getElementById('book_lead_form');
        if (form) {
            form.reset();
        }
        
    });
    //close payment modal
    document.querySelector('.payment-modal-close').addEventListener('click', () => {
        document.querySelector('.payment-modal').style.display = 'none';
        const form = document.getElementById('add-payment-form');
        if (form) {
            form.reset();
        }
        document.getElementById('leads-payment-list').innerHTML = "";
    });
    //close call modal
    document.querySelector('.call-modal-close').addEventListener('click', () => {
        document.querySelector('.call-modal').style.display = 'none';
        // document.getElementById('callModal').style.display = 'none';
        document.getElementById('leads-call-list').innerHTML = "";
    });
    //call log modal
    document.addEventListener('click', function(e) {
        const target = e.target.closest('.leads_call_history_btn');
        if (target) {
            e.preventDefault();
            const id = target.dataset.id; 
            
            document.getElementById('callModal').style.display = 'block';
            document.getElementById('call-modal-loader').style.display = 'flex';
            fetch(`/admin/${id}/call`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                })
            .then(response => {
                if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
                }
                return response.json();
            })
            .then(result => {
                document.getElementById('leads-call-list').innerHTML = result.html;
            })
            .catch(error => {
                console.error('Fetch error:', error.message);
            })
            .finally(()=>{
                document.getElementById('call-modal-loader').style.display = 'none';
            });
        }
    });
    //close log modal
    document.querySelector('.lead-log-modal-close').addEventListener('click', () => {
        document.querySelector('.lead-log-modal').style.display = 'none';
        document.getElementById('leadLogsContent').innerHTML = "";
    });
    //change status 
    document.addEventListener('change',function (event) {
        const target = event.target;
        if (target && target.matches('.lead-status')) {
            const leadId = target.dataset.leadId;
            const newStatus = target.value;
            const oldStatus = target.getAttribute('data-old-status') || 'New';
            const selectedOption = target.options[target.selectedIndex];
            const statusId = selectedOption.dataset.statusId;
            target.disabled = true;
            target.setAttribute('data-old-status', newStatus);

            fetch(`/admin/${leadId}/update-lead-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    lead_id: leadId,
                    status_id: statusId,
                    status_changed: `${oldStatus} to ${newStatus}`
                }),
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    alert(response.message || 'Status updated successfully');
                } else {
                    console.error('Response Error:', response);
                    alert('Error: ' + (typeof response.message === 'object' ? JSON.stringify(response.message) : response.message));
                    target.value = oldStatus; // rollback
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('An error occurred. Please try again.');
                target.value = oldStatus; // rollback
            })
            .finally(() => {
                target.disabled = false;
            });
        }
    });

    function showError($phoneError, message) {
        $phoneError.text(message).show();
    }

    function hideError($phoneError) {
        $phoneError.hide();
    }

    function validatePhoneNumber(iti, $phoneError) {
        if (!iti) {
            return false;
        }

        var phoneNumber = iti.getNumber();
        var selectedCountryData = iti.getSelectedCountryData();

        if (!iti.isValidNumber()) {
            showError($phoneError,
                `Invalid phone number for ${selectedCountryData.name}. Please check the format.`);
            return false;
        }

        var phoneLength = phoneNumber.replace(/\D/g, '').length;
        if (phoneLength < 8 || phoneLength > 15) {
            showError($phoneError, `Invalid phone number length for ${selectedCountryData.name}.`);
            return false;
        }

        hideError($phoneError);
        return true;
    }

    function InitializeItiPhoneInput() {
        var phoneInput = jQuery("#phone");
        var countryCodeInput = jQuery("#country_code");
        var phoneNumberInput = jQuery("#phone_number");
        var phoneError = jQuery("<span class='phone-error' style='color:red; font-weight:500; display:none;'></span>");
        phoneInput.after(phoneError);
        var iti = window.intlTelInput(phoneInput[0], {
            initialCountry: "in",
            separateDialCode: true,
            preferredCountries: ["in", "th"],
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.min.js",
        });
        phoneInput.on("input", function () {
            countryCodeInput.val(iti.getSelectedCountryData().dialCode);
            phoneNumberInput.val(phoneInput.val().replace(/\D/g, ''));
            validatePhoneNumber(iti, phoneError);
        });
        phoneInput.on("countrychange", function () {
            countryCodeInput.val(iti.getSelectedCountryData().dialCode);
            validatePhoneNumber(iti, phoneError);
        });
        return;
    }

    // bulk delete
    document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const leadCheckboxes = () => document.querySelectorAll('.lead-checkbox');

    // Toggle all checkboxes
    selectAllCheckbox?.addEventListener('change', function () {
        leadCheckboxes().forEach(cb => cb.checked = this.checked);
    });

    // Uncheck "Select All" if any single checkbox is unchecked
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('lead-checkbox')) {
            const all = leadCheckboxes();
            const checked = [...all].filter(cb => cb.checked).length;
            selectAllCheckbox.checked = all.length === checked;
        }
    });

    // Bulk Action Handler
    document.getElementById('apply-bulk-action')?.addEventListener('click', function () {
        const selectedAction = document.getElementById('bulk-action').value;
        const selectedLeads = [...leadCheckboxes()]
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value, 10));

        if (!selectedAction || selectedLeads.length == 0) {
            alert('Please select an action and at least one lead.');
            return;
        }

        if (selectedAction === 'delete') {
            if (!confirm('Are you sure you want to delete the selected leads?')) return;

            fetch(`/admin/delete_leads_bulk`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    leads: selectedLeads
                })
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    selectedLeads.forEach(id => {
                        const row = document.querySelector('.lead-checkbox[value="' + id + '"]')?.closest('tr');
                        if (row) row.remove();
                    });
                } else {
                    alert('Failed to delete leads: ' + response.data);
                }
            })
            .catch(() => alert('AJAX error occurred.'));
        }
    });
});




function setupFlatpickrWithModeToggle(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    let currentMode = 'single';
    let picker;

    function createModeToggle() {
        const container = document.createElement('div');
        container.className = 'mode-toggle';
        container.innerHTML = `
            <button data-mode="single" ${currentMode === 'single' ? 'disabled' : ''}>Single</button>
            <button data-mode="range" ${currentMode === 'range' ? 'disabled' : ''}>Range</button>
        `;

        container.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                const mode = btn.getAttribute('data-mode');
                if (mode !== currentMode) {
                    currentMode = mode;
                    picker.set('mode', mode);
                    picker.clear();
                    updateToggleButtons(container);
                }
            });
        });

        return container;
    }

    function updateToggleButtons(container) {
        container.querySelectorAll('button').forEach(btn => {
            btn.disabled = btn.getAttribute('data-mode') === currentMode;
        });
    }

    picker = flatpickr(input, {
        mode: currentMode,
        dateFormat: "Y-m-d",
        onOpen: function (_, __, instance) {
            const calendarContainer = instance.calendarContainer;
            if (!calendarContainer.querySelector('.mode-toggle')) {
                const toggleUI = createModeToggle();
                calendarContainer.insertBefore(toggleUI, calendarContainer.firstChild);
            }
        }
    });
}

</script>
<style>
    .container{
        /* max-width: 1400px; */
        max-width: 100%;
    }

    table .booked-row td,
    table .booked-row th {
        background-color: #dff2bf !important;
        box-shadow: none !important;
    }
    table .cancelled-row td,
        table .cancelled-row th {
        background-color: #f2bfbf !important;
        box-shadow: none !important;
    }

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
        background: rgba(0,0,0,0.6);
        z-index: 1040;
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

    .lead_table_source {
        padding: 8px;
        color: #ffff;
        border-radius: 4px;
    }

    /* Loader */
    #lead-edit-loader {
        text-align: center;
        font-weight: bold;
        padding: 20px;
    }

    .book-btn, .cancel-btn{
        background-color: transparent;
        border-radius: 8px;
        font-size: 34px;
        border: unset !important;
    }

    .book-btn i {
        color: #027502;
    }
    .cancel-btn i{
        color:rgb(134, 13, 9);
    }

    .unbook-btn,
    .uncancel-btn {
        background-color: transparent;
        border-radius: 8px;
        font-size: 34px;
        border: unset !important;
    }

    .unbook-btn i {
        color: #027502;
    }
    .uncancel-btn i{
        color:rgb(134, 13, 9);
    }

    /* book modal Styles */
    .book-modal {
        display: none;
        position: fixed;
        z-index: 99999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgb(0 0 0 / 66%);
        justify-content: center;
        align-items: center;
    }

    .book-modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        text-align: center;
        box-shadow: 0px 4px 6px rgb(0 0 0 / 71%);
        position: absolute;
        top: 30%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .book-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .book-modal-header h2{
        font-size:20px;
    }
    .book-close {
        font-size: 30px;
        cursor: pointer;
    }

    .book-input {
        margin: 15px 0;
        display: flex;
        flex-direction: column;
        text-align: left;
        font-size: 14px;
    }

    .book-input label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .book-input input,
    .book-input select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    .book-save-btn {
        display: flex;
        gap: 10px;
        justify-content: end;
    }

    .book-save-btn button {
        font-size: 16px;
        font-weight: 500;
    }

    /* payments */
    .payment_lead_btn {
        margin-bottom: 10px;
        background-color: #b15230;
        color: #ffff;
        font-size: 20px;
        padding: 8px;
        border-radius: 8px;
        font-size: 13px;
        border: unset !important;
    }

    /* logs */
    .payment-modal,
    .call-modal {
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

    .payment-modal-content,
    .call-modal-content {
        background: #fff;
        margin: 6% auto;
        width: 80%;
        max-width: 700px;
        border-radius: 8px;
        position: relative;
    }

    .payment-heading-wrapper,
    .call-heading-wrapper {
        display: flex;
        justify-content: space-between;
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
        /* background-color: #2271b1; */
    }
    .payment-heading-wrapper h2,
    .call-heading-wrapper h2 {
        font-size: 20px;
        font-weight: 600;
    }
    #paymentContent,
    #callContent {
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
    .payment-modal-close,
    .call-modal-close {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 24px;
        cursor: pointer;
        /* color: #ffff; */
        /* border: 1px solid #2f2e2e; */
        border-radius: 100%;
        /* padding: 4px 6px; */
    }

    .payment-modal-table,
    .calls-modal-table {
        width: 100% !important;
        max-width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        background: white;
        border-radius: 8px;
        /* overflow: hidden; */
        overflow-x: scroll;
        font-size: 14px;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;
    }

    /* Style table header */
    .payment-modal-table thead,
    .calls-modal-table thead {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    /* Improve table cell spacing */
    .payment-modal-table th,
    .payment-modal-table td,
    .calls-modal-table th,
    .calls-modal-table td {
        padding: 10px 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    /* Remove last row's border-bottom */
    .payment-modal-table tbody tr:last-child td,
    .calls-modal-table tbody tr:last-child td {
        border-bottom: none;
    }

    .add-payment-option form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
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
    .modal-loader {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 80px;
        font-size: 24px;
        color: #0073aa;
    }

    .modal-loader i {
        animation: spin 1s linear infinite;
    }

    button:focus {
        outline: none !important;
    }

    /* Keyframes for the spin animation */
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .leads_call_history_btn {
        position: relative;
        margin-bottom: 10px;
        background-color: transparent;
        color: #1634a3;
        font-size: 18px;
        border: unset !important;
    }

    .leads_call_history_btn i {
        position: relative;
        display: inline-block;
        /* Ensure it can contain the absolutely positioned child */
    }

    .lead_call_count {
        position: absolute;
        top: -14px;
        right: -8px;
        background: #af0707;
        color: #ffff;
        border-radius: 50%;
        padding: 5px 6px;
        font-size: 8px;
        line-height: 1;
        font-weight: 900;
    }


    /* logs */
    .lead-log-modal {
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

    .lead-log-modal-content {
        background: #fff;
        margin: 6% auto;
        width: 80%;
        max-width: 700px;
        border-radius: 8px;
        position: relative;
    }

    .lead-log-modal-close {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 20px;
        cursor: pointer;
        color: #ffff;
        border: 1px solid #ffff;
        border-radius: 100%;
        padding: 0px 7px;
    }

    button {
        cursor: pointer;
    }

    .lead-heading-wrapper {
        display: flex;
        justify-content: space-between;
        padding: 8px 20px;
        border-radius: 8px 8px 0 0;
        background-color: #2271b1;
    }

    .lead-heading-wrapper h2 {
        font-size: 20px;
        color: #ffff;
        font-weight: 600;
        margin:16px;
    }

    #leadLogsContent {
        padding: 20px;
    }

    .single-lead-log-wrapper {
        position: relative;
        margin-bottom: 18px;
        padding: 12px;
        border-radius: 8px;
        background-color: #f4f4f4;
        display: flex;
        flex-direction: column;
        font-weight: 500;
        max-height: 190px;
        gap: 6px;
    }

    .single-lead-log-wrapper .single-lead-log-type {
        position: absolute;
        align-self: end;
        top: -10px;
        background-color: grey;
        padding: 4px 9px;
        border-radius: 6px;
        color: #ffff;
        font-weight: 500;
        font-size:14px;
    }

    .lead-log-action-date-wrapper {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .single-lead-log-wrapper .single-lead-log-action {
        background-color: rgb(237, 172, 172);
        width: fit-content;
        padding: 6px;
        border-radius: 8px;
        font-weight: 600;
        color: #ffff;
        font-size: 14px;
    }

    .single-lead-log-action-done-by {
        align-self: end;
    }

    .single-lead-log-date {
        font-size: 14px;
    }

    .single-lead-log-details {
        max-height: 200px;
        overflow: auto;
    }
    .single-lead-log-details ul li {
        font-size: 14px;
    }
    select{
        /* padding: 4px; */
        border-radius: 5px;
        /* background-color: white; */
        font-size: 14px !important;
    }
    .form-control:focus {
        border-color: transparent !important;
    }
    .leads-pagination{
        float: right;
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

    /* Modal container */

    .edit-lead-modal ,
    .new-lead-back-modal{ 
        position: fixed;
        z-index: 99999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0 0 0 / 75%);
    }

    /* Modal content */
    .new-lead-back-content,
    .edit-lead-content {
        background-color: white;
        margin: 9% auto;
        padding: 30px;
        border-radius: 18px;
        width: 70%;
        max-width: 600px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    /* Flexbox layout for the form fields */
    .input-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    .input-container label{
        width:100%;
    }
    .input-container p {
        flex: 1;
        margin: 0;
    }

    .input-container p input,
    .input-container p select,
    .input-container p textarea {
        width: 100%;
        padding: 8px;
        background-color: white;
        border: 1px solid #CED4DA;
        border-radius: 5px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        margin-bottom: 15px;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }

    .input-container p input:focus,
    .input-container p select:focus,
    .input-container p textarea:focus {
        border-color: #007cba;
        outline: none;
    }

    /* Textarea styling */
    textarea {
        resize: vertical;
        height: 95px;
    }
    .header-lead {
        display: flex;
        justify-content: space-between;
        /* Aligns items to the left and right */
        align-items: center;
        /* Vertically centers the items */
        margin-bottom: 30px;
        /* Adds spacing between header and form */
    }

    .header-lead h2 {
        margin: 0;
        /* Remove default margin for h2 */
        font-size: 24px;
        font-weight: bold;
        color: #333;
    }

    .new-lead-back-close,
    .edit-lead-close {
        font-size: 30px;
        color: #333;
        cursor: pointer;
        transition: color 0.3s;
    }

    .new-lead-back-close:hover {
        color: #ff0000;
        /* Change color when hovered */
    }
    .table-bordered>:not(caption)>*>* {
        border-width: 0;
    }
    .lead-table-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        /* border-collapse: separate; */
        border-spacing: 0;
        background: white;
        border-radius: 8px;
        overflow-x: scroll;
        font-size: 14px;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;
    }

    .lead-table-wrapper th{
        background-color: #2271b1;
        color: white;
    }
    /* .lead-filter-inner-wrapper select{
        height: 50px !important;
        width: 100% !important;
    } */
    .lead-filter-inner-wrapper .select2-container{
        height: 38px !important;
        overflow: auto;
        width: 100% !important;
        border: 1px solid #CED4DA;
        border-radius: 5px;
    }

    .lead-filter-inner-wrapper .select2-container--default .select2-search--inline .select2-search__field,
    .lead-filter-inner-wrapper .select2-container--default .select2-selection--multiple{
        height: 28px !important;
        border: unset !important;    
    }
    /* Responsive enhancements */
    @media (max-width: 768px) {
        .new-lead-back-content, .edit-lead-content, .lead-log-modal-content,.payment-modal-content, .call-modal-content{
            width: 95%;
        }
        .edit-lead-modal {
            width: 100%;
            padding: 15px;
        }
        .input-container{
            flex-direction: column;
        }
        .edit-lead-close {
            top: 8px;
            right: 10px;
        }
        main{
            overflow: auto;
        }
        .leads-filer-wrapper {
            flex-wrap: wrap;
        }
        .lead-filter-inner-wrapper div{
            width: 100% !important;
        }
        .select-pagination-page {
            float: unset;
        }
        label{
            font-size:14px;
        }
        .left-actions{
            width: 100% !important;
        }
    }

    @media (min-width: 768px) {
        .lead-filter-inner-wrapper div{
            width: 220px;
        }
        
    }
</style>
@endpush

@endsection