@if(session('success'))
    <div id="flash-message" class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div id="flash-message" class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
    </div>
@endif

@if(session('warning'))
    <div id="flash-message" class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
    </div>
@endif

@if(session('info'))
    <div id="flash-message" class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
    </div>
@endif

<script>
    // auto-dismiss after 3 seconds
    setTimeout(() => {
        let alert = document.getElementById('flash-message');
        if (alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 500); // wait sa fade-out
        }
    }, 3000);
</script>
