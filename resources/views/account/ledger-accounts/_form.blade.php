@php $p = $prefix ?? ''; @endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Account Name *</label>
        <input type="text" name="name" id="{{ $p }}name" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Type *</label>
        <select name="type" id="{{ $p }}type" class="form-control" required>
            <option value="cash">Cash</option>
            <option value="bank">Bank</option>
        </select>
    </div>
    <div class="col-md-4 bank-fields-{{ $p ?: 'add' }}" style="display:none">
        <label class="form-label">Bank Name</label>
        <input type="text" name="bank_name" id="{{ $p }}bank_name" class="form-control">
    </div>
    <div class="col-md-4 bank-fields-{{ $p ?: 'add' }}" style="display:none">
        <label class="form-label">Account Number</label>
        <input type="text" name="account_number" id="{{ $p }}account_number" class="form-control">
    </div>
    <div class="col-md-4 bank-fields-{{ $p ?: 'add' }}" style="display:none">
        <label class="form-label">IFSC Code</label>
        <input type="text" name="ifsc_code" id="{{ $p }}ifsc_code" class="form-control">
    </div>
    
    <div class="col-md-4">
        <label class="form-label">Status *</label>
        <select name="status" id="{{ $p }}status" class="form-control" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" id="{{ $p }}description" class="form-control" rows="2"></textarea>
    </div>
</div>
