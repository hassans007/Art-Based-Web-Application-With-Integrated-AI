<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>
        @isset($title)
            {{ $title }} | The Art
        @else
            The Art
        @endisset
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('images/icon/main.ico') }}" type="image/x-icon"/>
    <link rel="stylesheet" href="{{ asset('stylee.css') }}">
</head>
<body>

    <div class="layoutContent">
        @php
            $homeImage = $homeImage ?? 'images/homeTop.jpg'; // default background image
        @endphp

        <div class="homeTopImage">
            <img src="{{ asset($homeImage) }}" alt="Header Image">
        </div>

        <nav class="nav-control">
            <div class="nav-logo">
                <a href="/theArt"><img src="{{ asset('images/navLogos.png') }}" alt="Logo"></a>
            </div>

            <!-- Hamburger Icon (ALWAYS VISIBLE ON MOBILE) -->
            <div id="openIcon" class="menu-icon-mobile">
                <svg width="35" viewBox="0 0 10 10" fill="none">
                    <path d="M1 1h8 M1 4h8 M1 7h8" stroke="white" stroke-width="0.75" />
                </svg>
            </div>

            <!-- Sliding Menu -->
            <div class="nav-bar">
                <!-- Close Icon -->
                <div id="closeIcon" class="menu-icon-mobile hide-mobile">
                    <svg width="20" viewBox="0 0 10 10" fill="none">
                        <path d="M1 1 L9 9" stroke="white" stroke-width="1.5" />
                        <path d="M9 1 L1 9" stroke="white" stroke-width="1.5" />
                    </svg>
                </div>
                <div class="links">
                    <div class="nav-box"><a href="/theArt" class="nav-link">Home</a></div>
                    <div class="nav-box"><a href="/theArt/artCatalog" class="nav-link">Artworks Catalog</a></div>
                    <div class="nav-box"><a href="/theArt/artistCatalog" class="nav-link">Artists Catalog</a></div>
                    <div class="nav-box"><a href="/theArt/savedCollection" class="nav-link">My Saved Collection</a></div>
                    <div class="nav-box"><a href="/theArt/testYourself" class="nav-link">Test your knowledge</a></div>
                    <div class="nav-box"><a href="/theArt/upload" class="nav-link">AI Critique</a></div>
                    <div class="nav-box"><a href="/theArt/about" class="nav-link">About</a></div>
                </div>
            </div>

        </nav>
        
    </div>    

    <main>
        {{ $slot }}
    </main>
    @vite('resources/js/app.js')
    @stack('scripts') 
    <script src="{{ asset('js/imageAnalysis.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>
    <script src="{{ asset('js/hamburger.js') }}"></script>


</body>
</html>
</html>
