@php
    $photoUrl = null;
    if (!empty($photo) && \Illuminate\Support\Facades\Storage::disk('public')->exists('leads/' . $photo)) {
        $photoUrl = asset('storage/leads/' . $photo);
    }
@endphp
@if($photoUrl)
    <img src="{{ $photoUrl }}" alt="{{ $name ?? 'User' }}" class="avatar-img">
@else
    <span class="avatar-icon" aria-hidden="true">
        <i class="bx bx-user"></i>
    </span>
@endif
