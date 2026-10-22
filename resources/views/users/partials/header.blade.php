<div class="header pb-0 pt-5 pt-lg-6 d-flex align-items-center">
    <!-- Mask -->
    {{-- <span class="mask opacity-8"></span> --}}
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
    <div class="col-12 pl-0">
        {{-- {{ $class ?? '' }} --}}
            <div class="mb-4 px-3 py-2 bg-white rounded shadow border-dark">
                @isset($title)
                <h1 class="lw-page-title display-4 my-2 text-secondary font-weight-bolder">{{ $title }}</h1>
                @endisset
                @if (isset($description) && $description)
                <p class="mt-0 mb-1 text-muted">{{ $description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>