@extends('layouts.app')

@section('title', 'My Document Access')
@section('page_title', 'My Document Access')

@section('content')
<div class="container mt-4">
    <div id="alertBox"></div>
    
    <div class="row">
        <div class="col-md-12">
            <h4>My Document Access</h4>
            <p class="text-muted">User: {{ $user->name }} ({{ $user->email }})</p>
        </div>
    </div>

    @if($categories->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        You don't have access to any documents yet.
    </div>
    @else
    <div class="row mt-3">
        @foreach($categories as $category)
        <div class="col-md-3 mb-3">
            <div class="card document-category-card" onclick="openCategory('{{ $category->slug }}')">
                <div class="card-body text-center">
                    <i class="bi {{ $category->icon }} text-{{ $category->color }}" style="font-size: 2.5rem;"></i>
                    <h5 class="card-title mt-2">{{ $category->name }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($category->description, 50) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<style>
.document-category-card {
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: 1px solid #dee2e6;
}

.document-category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-color: #0d6efd;
}

.document-category-card .card-body {
    padding: 1.5rem;
}
</style>

<script>
function openCategory(categorySlug) {
    window.location.href = '{{ url("/document") }}/' + categorySlug;
}
</script>
@endsection
