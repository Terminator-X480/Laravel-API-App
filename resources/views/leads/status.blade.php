@extends('layouts.app')
@section('content')
<div class="wrap p-md-3">
    <!-- add Modal-->
    <div id="statusModal" class="status-modal position-fixed top-0 start-0 w-100 h-100 overflow-auto" style="z-index: 9999; background-color: rgba(0, 0, 0, 0.5); max-height: 100vh; display:none; ">
        <div class="status-modal-content bg-white mx-auto position-relative p-3 rounded" style="margin-top: 20%;">
            <div class="status-modal-header d-flex justify-content-between align-items-end">
                <h4>Add New Status</h4>
                <span class="status-close-add-modal fs-3" style="cursor:pointer">&times;</span>
            </div>
            <div class="status-input d-flex flex-column gap-2">
                <form method="POST" id="add-status-form">
                    <label for="status">Status: </label>
                    <input type="text" id="new-status" name="status" placeholder="Enter status" class="w-100 p-2 my-1 rounded border border-secondary" required>
                    <button type="submit" id="save-status" class="btn btn-primary mt-2">Save</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Status Modal -->
    <div id="edit-status-modal" class="edit-status-modal position-fixed top-0 start-0 w-100 h-100 overflow-auto" style="z-index:9999; background-color: rgba(0,0,0,0.5); max-height: 100vh; display:none;">
        <div class="edit-status-modal-content bg-white mx-auto position-relative p-3 rounded" style="margin-top: 20%;">
            <div class="edit-status-modal-header d-flex justify-content-between align-items-end"> 
                <h4>Edit Status</h4>
                <span class="edit-status-close fs-3" style="cursor:pointer">&times;</span>
            </div>
            <div class="status-input d-flex flex-column gap-2">
                <form method="POST" id="edit-status-form">
                    <label for="edit-status">Status:</label>
                    <input type="text" name="status" id="edit-status" placeholder="Enter new status" class="w-100 p-2 my-1 rounded border border-secondary">
                    <input type="hidden" name="id" id="edit-status-id"> <!-- Hidden field for ID -->
                    <button type="submit" id="update-status" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
    <div class="leads-management-heading-head d-flex justify-content-between flex-wrap mb-4">
        <h4>Leads Status</h4>
        <!-- Add New Status Button -->
        <button type="button" name="new-lead-status" id="new-lead-status" class="btn btn-primary text-sm">Add Status</button>
    </div>   
    <div class="leads-status-table overflow-auto">
        <table class="leads-status-main-table table table-bordered table-striped mt-3" style="font-size:14px">
            <thead>
                <tr>
                    <th class="p-3">Status</th>
                    <th class="p-3">Created At</th>
                    <th class="p-3">Updated At</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody id="leads-status-body">
            </tbody>
        </table>
    </div>
</div>

<style>
    .leads-status-main-table tr th{
        background: #2271b1; 
        color: white;
    }
    .table-bordered>:not(caption)>*>* {
        border-width: 0;
    }
    .status-modal-content,.edit-status-modal-content{
        width: 25%;
    }
    @media (max-width: 768px) {
        .status-modal-content,.edit-status-modal-content{
            width: 90% !important;
        }
    }
</style>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const fetchStatus = (filters = {}) => {
        fetch("{{ url('/admin/all-status') }}" , {
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const tbody = document.getElementById('leads-status-body');

                if (!data.statuses.length) {
                    tbody.innerHTML = '<tr><td colspan="9">No status found.</td></tr>';
                    return;
                }

                let html = '';
                data.statuses.forEach(status => {
                    html += `
                        
                        <tr>
                            <td class="p-3 status-text">${status.status}</td>
                            <td class="p-3">${status.created_at}</td>
                            <td class="p-3">${status.updated_at}</td>
                            <td class="p-3">
                                <i class="fa-solid fa-pen-to-square text-success fs-6 edit-status-btn" data-id="${status.id}"></i> 
                                <i class="fa-solid fa-trash text-danger fs-6 dlt-status-btn" data-id="${status.id}"></i> 
                            </td>
                        </tr>`;
                });

                tbody.innerHTML = html;
            })
            .catch(error => {
                console.error('Error fetching leads:', error);
                document.getElementById('leads-status-body').innerHTML =
                    '<tr><td colspan="9">Failed to load leads</td></tr>';
            });
    };
    fetchStatus();

    document.querySelector('#new-lead-status').addEventListener('click', () => {
        document.querySelector('#statusModal').style.display = 'block';
    });

    document.querySelector('.status-close-add-modal').addEventListener('click', () => {
        document.querySelector('#statusModal').style.display = 'none';
    });
    document.querySelector('.edit-status-close').addEventListener('click', () => {
        document.querySelector('#edit-status-modal').style.display = 'none';
    });

    document.getElementById('add-status-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form); 
        if( document.getElementById('new-status').value == ""){
                alert("Please Fill status name");
                return;
        }
        fetch(`/admin/addStatus`,{
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if(data.success){
                form.reset();
                document.querySelector('#statusModal').style.display = 'none';
                fetchStatus();
            }
            else{
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching leads:', error);
            document.getElementById('leads-status-body').innerHTML =
                '<tr><td colspan="9">Failed to load leads</td></tr>';
        });
    });

    document.querySelector('#leads-status-body').addEventListener('click', (e) => {
        const target = e.target.closest('.edit-status-btn');
        if (!target) return;

        e.preventDefault();

        const id = target.getAttribute('data-id');
        if (!id) return;

        const editStatusInput = document.getElementById("edit-status");
        const editStatusId = document.getElementById("edit-status-id");

        editStatusId.value = id;

        const statusText = target.closest("tr").querySelector(".status-text").textContent;
        editStatusInput.value = statusText.trim();

        document.querySelector('#edit-status-modal').style.display = 'block';
    });

    document.querySelector('#leads-status-body').addEventListener('click', (e) => {
        const target = e.target.closest('.dlt-status-btn');
        if (!target) return;

        e.preventDefault();

        const id = target.getAttribute('data-id');
        if (!id) return;

        if (!confirm("Are you sure you want to delete this status?")) return;

        fetch(`/admin/${id}/deletestatus`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    fetchStatus();
                } else {
                    alert(response.message);
                }
            })
            .catch(err => {
                console.error('cancel error:', err);
                alert('cancel failed. Please try again.');
            });

    });

    document.getElementById('edit-status-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form); 
        
        fetch(`/admin/editStatus`,{
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if(data.success){
                form.reset();
                document.querySelector('#edit-status-modal').style.display = 'none';
                fetchStatus();
            }
            else{
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching leads:', error);
            document.getElementById('leads-status-body').innerHTML =
                '<tr><td colspan="9">Failed to load leads</td></tr>';
        });
    });
</script>
@endsection