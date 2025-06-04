{{-- ✅ Lead Logs Modal --}}
<div id="leadLogsModal" class="lead-log-modal">
    <div class="lead-log-modal-content">
        <div class="lead-heading-wrapper">
            <h2>Lead Activities</h2>
            <span class="lead-log-modal-close">&times;</span>
        </div>
        <div id="leadLogsContent"></div>
    </div>
</div>

{{-- ✅ Book Lead Modal --}}
<div id="bookModal" class="book-modal">
    <div class="book-modal-content">
        <div class="book-modal-header">
            <h2>Booking Details</h2>
            <span class="book-close">&times;</span>
        </div>
        <form method="POST" id="book_lead_form" action="{{ route('leads.book') }}">
            @csrf
            <input type="hidden" name="lead_id" id="lead_id">

            <div class="book-input">
                <label for="amount">Book Amount:</label>
                <input type="number" id="amount" name="amount" placeholder="Enter book amount" min="0" step="any" required>
            </div>

            <div class="book-input">
                <label for="paid_to">Amount Paid To:</label>
                <select id="paid_to" name="paid_to" required>
                    <option value="">Select amount paid to</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="book-save-btn">
                <button type="submit" class="button button-primary">Save</button>
                <button type="button" class="book-close button button-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Payment Modal --}}
<div id="paymentModal" class="payment-modal">
    <div class="payment-modal-content">
        <div class="payment-heading-wrapper">
            <h2>Lead Payments</h2>
            <span class="payment-modal-close">&times;</span>
        </div>

        <div id="paymentContent">
            <div class="add-payment-option">
                <form method="POST" id="add-payment-form" action="{{ route('leads.addPayment') }}">
                    @csrf
                    <input type="hidden" name="lead_id" id="payment_lead_id">
                    <input type="number" name="amount" placeholder="Enter Amount" min="0" step="any" required>

                    <select id="payment_paid_to" name="paid_to" required>
                        <option value="">Select amount paid to</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>

                    <input type="datetime-local" name="created_on_time" required>

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
                    {{-- Payments will be dynamically filled via JS --}}
                </tbody>
            </table>
            <div class="modal-loader" style="display:none;">
                <i class="fas fa-spinner"></i>
            </div>
        </div>
    </div>
</div>
