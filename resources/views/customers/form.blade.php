{{--
    customers/_form.blade.php
    Shared partial used by the Add modal (inline).
    For Edit, the form is built dynamically via JS in the index blade.
--}}
<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label-c">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
               placeholder="e.g. Muhammad Ali" value="{{ old('name') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label-c">Guardian / Father Name</label>
        <input type="text" name="guardian_name" class="form-control {{ $errors->has('guardian_name') ? 'is-invalid' : '' }}"
               placeholder="Father's name" value="{{ old('guardian_name') }}">
        @error('guardian_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label-c">CNIC <span class="text-danger">*</span></label>
        <input type="text" name="cnic" class="form-control {{ $errors->has('cnic') ? 'is-invalid' : '' }}"
               placeholder="XXXXX-XXXXXXX-X" value="{{ old('cnic') }}" required>
        @error('cnic')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label-c">Phone <span class="text-danger">*</span></label>
        <input type="text" name="phone" class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
               placeholder="03XX-XXXXXXX" value="{{ old('phone') }}" required>
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label-c">Email</label>
        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
               placeholder="email@example.com" value="{{ old('email') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label-c">City</label>
        <input type="text" name="city" class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}"
               placeholder="e.g. Lahore" value="{{ old('city') }}">
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label-c">Address</label>
        <textarea name="address" class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}"
                  rows="2" placeholder="Full address...">{{ old('address') }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label-c">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select {{ $errors->has('status') ? 'is-invalid' : '' }}" required>
            <option value="active"   {{ old('status', 'active') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Photo Upload --}}
    <div class="col-md-6">
        <label class="form-label-c">Profile Photo</label>
        <div class="upload-box" onclick="document.getElementById('addCustomerPic').click()">
            <img id="addPicPreview" src="" alt="" class="upload-preview" style="display:none;">
            <i class="bi bi-camera" style="font-size:1.3rem;color:#94a3b8;display:block;margin-bottom:4px;"></i>
            <span style="font-size:11px;color:#94a3b8;">Click to upload photo (JPG/PNG)</span>
            <input type="file" id="addCustomerPic" name="customer_pic"
                   accept="image/*" style="display:none;"
                   onchange="previewImage(this,'addPicPreview')">
        </div>
        @error('customer_pic')<div style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</div>@enderror
    </div>

    {{-- CNIC Doc Upload --}}
    <div class="col-md-6">
        <label class="form-label-c">CNIC Document</label>
        <div class="upload-box" onclick="document.getElementById('addCnicPic').click()">
            <i class="bi bi-file-earmark-image" style="font-size:1.3rem;color:#94a3b8;display:block;margin-bottom:4px;"></i>
            <span style="font-size:11px;color:#94a3b8;" id="cnicFileLabel">Click to upload CNIC photo/PDF</span>
            <input type="file" id="addCnicPic" name="cnic_pic"
                   accept="image/*,.pdf" style="display:none;"
                   onchange="document.getElementById('cnicFileLabel').textContent = this.files[0]?.name || 'File selected'">
        </div>
        @error('cnic_pic')<div style="color:#dc2626;font-size:11px;margin-top:4px;">{{ $message }}</div>@enderror
    </div>

</div>
