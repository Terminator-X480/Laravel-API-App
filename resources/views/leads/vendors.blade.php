@extends('layouts.app') 
@section('content')
    <!-- add vendor -->
    
    <div id="add-vendor-modal" class="add-vendor-modal position-fixed top-0 start-0 w-100 h-100 overflow-auto" style="z-index: 9999; background-color: rgba(0, 0, 0, 0.5); max-height: 100vh; display:none">
        <div class="add-vendor-modal-content bg-white mx-auto position-relative p-3 rounded" style="margin-top: 6%; width: 25%; max-width: 700px;">
            <div class="add-vendor-heading-wrapper d-flex justify-content-between">
                <h4>Vendors</h4>
                <span class="add-vendor-modal-close position-absolute top-5 end-0 translate-middle mt-2 me-2 fs-3" style="cursor:pointer">&times;</span>
            </div>
            <div id="addVendorContent">
                <form method="POST" id="addVendorForm">
                    <div class="add-vender-input-wrapper d-flex flex-column mt-3" style="font-size:14px">
                        <label>Vendor Name:</label>
                        <input type="text" name="name" id="vendor-name" placeholder="Enter Vendor Name" class="w-100 p-2 my-1 rounded border border-secondary" required>

                        <label>Phone:</label>
                        <input type="tel" name="phone" id="vendor-phone" placeholder="Enter Phone" class="w-100 p-2 my-1 rounded border border-secondary" required>

                        <label>Location:</label>
                        <select id="vendor-location" name="location" class="w-100 p-2 my-1 rounded border border-secondary bg-white" required>
                            <option value="">Select Location</option>
                            <option value="add_new_location">➕ Add New Location</option>
                        </select>

                        <div id="new-location-wrapper" style="display:none; margin-top: 10px;">
                            <input type="text" name="locationname" id="new-location-name" placeholder="Enter New Location Name" class="w-100 p-2 my-1 rounded border border-secondary">
                            <button type="submit" id="save-new-location">Save</button>
                        </div>

                        <label>Type:</label>
                        <select id="vendor-type" name="type" required class="w-100 p-2 my-1 rounded border border-secondary bg-white" >
                            <option value="">Select Vendor Type</option>
                            <option value="Guide">Guide</option>
                            <option value="Driver">Driver</option>
                            <option value="Porter">Porter</option>
                            <option value="Cook">Cook</option>
                            <option value="Tent Provider">Tent Provider</option>
                            <option value="Vehicle Provider">Vehicle Provider</option>
                            <option value="Paragliding Instructor">Paragliding Instructor</option>
                            <option value="Homestay / Lodge Partner">Homestay / Lodge Partner</option>
                        </select>

                    </div>

                    <div class="add-vender-button-wrapper d-flex gap-3 justify-content-end mt-3">
                        <button type="submit" id="add-new-vendor-btn"
                            class="add-new-vendor-btn btn btn-primary">Add</button>
                        <!-- <button id="vendor-cancel-add-btn" class="vendor-cancel-add-btn btn btn-danger">Cancel</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- edit vendor -->
    <div id="edit-vendor-modal" class="edit-vendor-modal position-fixed top-0 start-0 w-100 h-100 overflow-auto" style="z-index: 9999; background-color: rgba(0, 0, 0, 0.5) !important; max-height: 100vh; display:none">
        <div class="edit-vendor-modal-content bg-white mx-auto position-relative p-3 rounded" style="margin-top: 6%; width: 25%; max-width: 700px;">
            <div class="edit-vendor-heading-wrapper d-flex justify-content-between">
                <h4>Edit Vendor</h4>
                <span class="edit-vendor-modal-close position-absolute top-5 end-0 translate-middle mt-2 me-2 fs-3" style="cursor:pointer">&times;</span>
            </div>
            <div id="editVendorContent">
                <form method="POST" id="vendor-edit-form" class="d-flex flex-column mt-3" style="font-size:14px">
                    <input type="hidden" id="vendor-id" name="vendor_id">

                    <div class="edit-vender-input-wrapper">
                        <label>Vendor Name:</label>
                        <input type="text" name="name" id="edit-vendor-name" placeholder="Enter Vendor Name" class="w-100 p-2 my-1 rounded border border-secondary" required>

                        <label>Phone:</label>
                        <input type="tel" name="phone" id="edit-vendor-phone" placeholder="Enter Phone" class="w-100 p-2 my-1 rounded border border-secondary" required>

                        <label>Location:</label>
                        <select id="edit-vendor-location" name="location" class="w-100 p-2 my-1 rounded border border-secondary bg-white" required>
                            <option value="">Select Location</option>
                            <!-- <option value="add_new_location">➕ Add New Location</option> -->
                        </select>

                        <div id="edit-new-location-wrapper" style="display:none; margin-top: 10px;">
                            <input type="text" id="edit-new-location-name" placeholder="Enter New Location Name">
                            <button type="button" id="edit-save-new-location">Save</button>
                        </div>

                        <label>Type:</label>
                        <select id="edit-vendor-type" name="type" class="w-100 p-2 my-1 rounded border border-secondary bg-white" required>
                        <option value="">Select Vendor Type</option>
                            <option value="Guide">Guide</option>
                            <option value="Driver">Driver</option>
                            <option value="Porter">Porter</option>
                            <option value="Cook">Cook</option>
                            <option value="Tent Provider">Tent Provider</option>
                            <option value="Vehicle Provider">Vehicle Provider</option>
                            <option value="Paragliding Instructor">Paragliding Instructor</option>
                            <option value="Homestay / Lodge Partner">Homestay / Lodge Partner</option>
                        </select>
                    </div>

                    <div class="edit-vendor-button-wrapper  d-flex gap-3 justify-content-end mt-3" >
                        <button type="submit" id="vendor-save-edit-btn" class="btn btn-primary">Save Changes</button>
                        <!-- <button type="button" id="vendor-cancel-edit-btn" class="btn btn-secondary">Cancel</button> -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="vendor-main-wrapper px-md-2">
        <div class="vendor-header-wrapper d-flex justify-content-between mb-4 flex-wrap">
            <h3>Vendors</h3>
            <button class="add_vendor_button btn btn-primary text-sm " id="add_vendor_button">Add Vendor</button>
        </div>

        <div class="vendor-content-table-wrapper overflow-auto">
            <table class="table table-bordered table-striped mt-3" style="font-size:14px">
                <thead>
                    <tr>
                        <th class="text-white" >Vendor</th>
                        <th class="text-white" >Location</th>
                        <th class="text-white" >Phone</th>
                        <th class="text-white" >Type</th>
                        <th class="text-white" >Created On</th>
                        <th class="text-white" >Actions</th>
                    </tr>
                </thead>
                <tbody id="vendor-listing-table-body"></tbody>
            </table>
        </div>
    </div>
    <style>
        .vendor-content-table-wrapper table th{
            background-color: #2271b1;
            font-size:14px;
            padding: 12px;
        }
        .vendor-content-table-wrapper table td{
            padding: 12px;
        }
        .table-bordered>:not(caption)>*>* {
            border-width: 0;
        }
        @media(max-width:768px){
            .add-vendor-modal-content, .edit-vendor-modal-content{
                width: 94% !important;
            }
        }
    </style>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const fetchVendors = (filters = {}) => {
        fetch("{{ url('/admin/all-vendors') }}" , {
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const tbody = document.getElementById('vendor-listing-table-body');

                if (!data.vendors.length) {
                    tbody.innerHTML = '<tr><td colspan="9">No leads found.</td></tr>';
                    return;
                }

                let html = '';
                data.vendors.forEach(vendor => {
                    html += `
                        
                        <tr>
                            <td class="p-3">${vendor.name}</td>
                            <td class="p-3">${vendor.location_name}</td>
                            <td class="p-3">${vendor.phone}</td>
                            <td class="p-3">${vendor.type}</td>
                            <td class="p-3">${vendor.created_at}</td>
                            <td class="p-3">
                                <i class="fa-solid fa-pen-to-square text-success fs-6 edit-vendor-btn" data-id="${vendor.id}"></i> 
                                <i class="fa-solid fa-trash text-danger fs-6 dlt-vendor-btn" data-id="${vendor.id}"></i> 
                            </td>
                        </tr>`;
                });

                tbody.innerHTML = html;
            })
            .catch(error => {
                console.error('Error fetching leads:', error);
                document.getElementById('vendor-listing-table-body').innerHTML =
                    '<tr><td colspan="9">Failed to load leads</td></tr>';
            });
        };
        fetchVendors();

        //open add vendor form
        document.querySelector('.add_vendor_button').addEventListener('click', () => {
            document.querySelector('.add-vendor-modal').style.display = 'block';

            fetch('/api/madtrek/v1/locations')
            .then(response => {
                if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
                }
                return response.json();
            })
            .then(result => {
                const locations = result.locations;

                if (!Array.isArray(locations)) {
                throw new Error('Expected locations to be an array');
                }

                const select = document.getElementById('vendor-location');

                // Optional: Clear existing options except placeholder
                // select.innerHTML = '<option>Select amount paid to</option>';

                locations.forEach(location => {
                const option = document.createElement('option');
                option.value = location.id;
                option.textContent = `${location.name}`; // optional format
                select.prepend(option);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error.message);
            });

        });

        //submit add vendor form
        document.getElementById('addVendorForm').addEventListener('submit', function (e) {
            e.preventDefault();
            // alert();
            const form = e.target;
            const formData = new FormData(form); // Collect all form fields

            // Optional: Show loader
            // document.querySelector('.modal-loader').style.display = 'flex';

            fetch(`/admin/addvendor`, {
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
                document.querySelector('.add-vendor-modal').style.display = 'none';
                fetchVendors();
            })
            .catch(error => {
                console.error('Save error:', error.message);
                alert('Failed to save payment.');
            })
            .finally(() => {
                // document.querySelector('.modal-loader').style.display = 'none';
            });
        });

        //new location
        document.querySelector('#vendor-location').addEventListener('change', function () {
            const newLocationWrapper = document.getElementById('new-location-wrapper');
            if (this.value === 'add_new_location') {
                newLocationWrapper.style.display = 'block';
            } else {
                newLocationWrapper.style.display = 'none';
            }
        });

        document.querySelector('#save-new-location').addEventListener('click', () => {
            const input = document.getElementById('new-location-name');
            const newLoc = input.value.trim();
            if (newLoc === '') {
                alert('Please enter a location name.');
                return;
            }
            const formData = new FormData();
            formData.append('locationname', newLoc);

            fetch(`/admin/addlocation`, {
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
            .then(response => {
                if (response.success) {
                    const select = document.getElementById('vendor-location');
                    const wrapper = document.getElementById('new-location-wrapper');
                    const { id, name } = response.location;

                    const option = document.createElement('option');
                    option.value = id;
                    option.textContent = name;
                    option.selected = true;

                    // Insert before the last option
                    select.insertBefore(option, select.lastElementChild);

                    input.value = '';
                    wrapper.style.display = 'none';
                } else {
                    alert('Error adding location: ' + response.data);
                }
            })
            .catch(() => {
                alert('AJAX request failed.');
            });
        });

        // close add modal
        document.querySelector('.add-vendor-modal-close').addEventListener('click', () => {
            document.querySelector('.add-vendor-modal').style.display = 'none';
        });

        // delete vendor
        document.querySelector('#vendor-listing-table-body').addEventListener('click', (e) => {
            const target = e.target.closest('.dlt-vendor-btn');
            if (!target) return;

            e.preventDefault();

            const id = target.getAttribute('data-id');
            if (!id) return;

            if (!confirm("Are you sure you want to delete this vendor?")) return;

            fetch(`/admin/${id}/deletevendor`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    fetchVendors();
                } else {
                    alert(response.message);
                }
            })
            .catch(err => {
                console.error('cancel error:', err);
                alert('cancel failed. Please try again.');
            });
        });

        // edit modal
        document.querySelector('#vendor-listing-table-body').addEventListener('click', (e) => {
            e.preventDefault();
            const target = e.target.closest('.edit-vendor-btn');
            if (!target) return;

            fetch('/api/madtrek/v1/locations')
            .then(response => {
                if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
                }
                return response.json();
            })
            .then(result => {
                const locations = result.locations;

                if (!Array.isArray(locations)) {
                throw new Error('Expected locations to be an array');
                }

                const select = document.getElementById('edit-vendor-location');
                locations.forEach(location => {
                const option = document.createElement('option');
                option.value = location.id;
                option.textContent = `${location.name}`; // optional format
                select.prepend(option);
                });
            })
            .catch(error => {
                console.error('Fetch error:', error.message);
            });

            const id = target.getAttribute('data-id');
            if (!id) return;
            document.getElementById('edit-vendor-modal').style.display="block";
            
            fetch(`/admin/${id}/getvendor`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    const form = document.getElementById('vendor-edit-form');
                    form.vendor_id.value = response.vendor.id;
                    form.name.value = response.vendor.name || '';
                    form.phone.value = response.vendor.phone || '';
                    form.location.value = response.vendor.location || '';
                    form.type.value = response.vendor.type || '';
                } else {
                    alert(response.message);
                }
            })
            .catch(err => {
                console.error('cancel error:', err);
                alert('cancel failed. Please try again.');
            });
        });

        // submit edit vendor form
        document.getElementById('vendor-edit-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form); 
            console.log(formData);
            const id = document.getElementById('vendor-id').value;
            fetch(`/admin/${id}/editvendor`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    document.getElementById('edit-vendor-modal').style.display="none";
                    fetchVendors();
                } else {
                    alert(response.message);
                }
            })
            .catch(err => {
                console.error('cancel error:', err);
                alert('cancel failed. Please try again.');
            });
        });

        // close edit modal
        document.querySelector('.edit-vendor-modal-close').addEventListener('click', () => {
            document.querySelector('.edit-vendor-modal').style.display = 'none';
            const form = document.getElementById('vendor-edit-form');
            form.reset();
        });

    </script>
@endsection