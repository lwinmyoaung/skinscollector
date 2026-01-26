@extends('admin.layout')

@section('content')
<div class="content-wrapper bg-light">
    <!-- Content Header -->
    <section class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center mb-3">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0 text-gray-800 fw-bold">Edit Payment Method</h1>
                </div>
                <div class="col-sm-6 text-end">
                    <a href="{{ url('admin/paymentmethod') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary">Edit Details: {{ $paymentmethod->name }}</h5>
                        </div>

                        <div class="card-body p-4">
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ url('/admin/paymentmethod/'.$paymentmethod->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold text-secondary">Method Name</label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" value="{{ old('name', $paymentmethod->name) }}" required>
                                </div>

                                <div class="mb-4">
                                    <label for="phone_number" class="form-label fw-bold text-secondary">Phone Number</label>
                                    <input type="text" class="form-control form-control-lg" id="phone_number" name="phone_number" value="{{ old('phone_number', $paymentmethod->phone_number) }}">
                                </div>

                                <div class="mb-4">
                                    <label for="image" class="form-label fw-bold text-secondary">Logo / QR Code</label>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 bg-light border-0">
                                                <div class="card-body text-center d-flex align-items-center justify-content-center">
                                                    <div>
                                                        <p class="small text-muted mb-2">Current Image</p>
                                                        <img src="{{ asset('adminimages/images/paymentmethodphoto/'.$paymentmethod->image) }}" alt="Current" class="img-fluid rounded shadow-sm" style="max-height: 150px;" onerror="this.src='https://placehold.co/400x300?text=No+Image'">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card h-100 bg-light border-dashed text-center p-4" onclick="document.getElementById('image').click()" style="cursor: pointer; border: 2px dashed #dee2e6;">
                                                <div id="image-preview-container">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                                    <p class="text-muted mb-0 small">Click to replace image</p>
                                                </div>
                                                <input type="file" name="image" id="image" class="d-none" accept="image/*" onchange="previewImage(this)">
                                                <img id="image-preview" src="#" alt="Preview" class="img-fluid mt-3 d-none" style="max-height: 150px; border-radius: 8px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Update Payment Method
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview').classList.remove('d-none');
                document.getElementById('image-preview-container').classList.add('d-none');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
