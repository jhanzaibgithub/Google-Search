<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laravel Google Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .search-container { margin-top: 80px; margin-bottom: 50px; }
        .result-card { margin-bottom: 20px; border-radius: 15px; transition: transform 0.3s ease; }
        .result-card:hover { transform: translateY(-5px); }
        .result-title { font-size: 18px; font-weight: 600; color: #0d6efd; text-decoration: none; }
        .loader {
            display: none;
            border: 8px solid #f3f3f3;
            border-top: 8px solid #0d6efd;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 30px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s forwards;
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">Laravel Google Search</a>
    </div>
</nav>

<div class="container search-container">
    <h2 class="text-center mb-4">Search Anything from Google</h2>

    <form method="GET" action="{{ route('google.search') }}" class="d-flex mb-3 shadow-sm p-3 bg-white rounded position-relative">
        <div class="w-100 position-relative">
            <input type="text" name="q" value="{{ $query ?? '' }}" class="form-control me-2" placeholder="Type to search..." autocomplete="off" required>
            <ul id="suggestion-box" class="list-group position-absolute w-100" style="z-index: 999; display: none;"></ul>
        </div>
        <button type="submit" class="btn btn-primary ms-2">Search</button>
    </form>

    @if(!empty($query))
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ $searchType === 'all' ? 'active' : '' }}" href="{{ route('google.search', ['q' => $query, 'type' => 'all']) }}">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $searchType === 'image' ? 'active' : '' }}" href="{{ route('google.search', ['q' => $query, 'type' => 'image']) }}">Images</a>
            </li>
        </ul>
    @endif

    <div class="loader" id="loader"></div>

    @if(!empty($results))
        <h5 class="mb-3">Search Results:</h5>
        <div class="row">
            @foreach($results as $item)
                @if($searchType === 'image')
                    <div class="col-md-3 fade-in">
                        <div class="card result-card shadow-sm p-2 bg-white text-center">
                            <a href="{{ $item['image']['contextLink'] }}" target="_blank">
                                <img src="{{ $item['link'] }}" class="img-fluid rounded mb-2" alt="{{ $item['title'] }}">
                            </a>
                            <p class="small text-muted">{{ $item['title'] }}</p>
                        </div>
                    </div>
                @else
                    <div class="col-md-6 fade-in">
                        <div class="card result-card shadow-sm p-3 bg-white">
                            <a href="{{ $item['link'] }}" target="_blank" class="result-title">{{ $item['title'] }}</a>
                            <p class="text-muted mt-2">{{ $item['snippet'] ?? '' }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @elseif(request()->has('q'))
        <div class="alert alert-warning">No results found for "{{ $query }}".</div>
    @endif
</div>

<!-- Footer -->
<footer class="bg-dark text-light py-3 mt-5">
    <div class="container text-center">
        &copy; {{ date('Y') }} Laravel Google Search | Powered by Google Custom Search API
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const form = document.querySelector('form');
    const loader = document.getElementById('loader');

    form.addEventListener('submit', function() {
        loader.style.display = 'block';
    });

    // Animate fade-in for results
    document.addEventListener('DOMContentLoaded', () => {
        const results = document.querySelectorAll('.fade-in');
        results.forEach((el, index) => {
            el.style.animationDelay = `${index * 0.1}s`;
        });
    });

    // Google Suggestion API
    $(document).ready(function(){
        $('input[name="q"]').on('keyup', function(){
            let query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: 'https://suggestqueries.google.com/complete/search?client=firefox&q=' + encodeURIComponent(query),
                    dataType: 'jsonp',
                    success: function(data) {
                        let suggestions = data[1];
                        let suggestionList = '';
                        suggestions.forEach(function(item){
                            suggestionList += '<li class="list-group-item suggestion-item" style="cursor:pointer;">' + item + '</li>';
                        });
                        $('#suggestion-box').html(suggestionList).show();
                    }
                });
            } else {
                $('#suggestion-box').hide();
            }
        });

        // Click suggestion to select
        $(document).on('click', '.suggestion-item', function(){
            $('input[name="q"]').val($(this).text());
            $('#suggestion-box').hide();
        });

        // Hide on blur
        $('input[name="q"]').on('blur', function() {
            setTimeout(function() { $('#suggestion-box').hide(); }, 200);
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
