<x-layout title="Art Catalog Page" homeImage="images/catalogTop.jpg">
    <div class="catalogContent">

        <!-- Search Section -->
        <div id="searchContainer">
            <form id="searchForm">
                <div class="searchItem">
                    <input type="text" id="searchBar" class="searchBar" placeholder="Search artwork and more..">
                    <button id="searchButton" type="submit" class="searchButton">
                        <img class="searchImg" src="{{ asset('images/search.png') }}" alt="Search">
                    </button>
                    <div id="searchMessageSaved"></div>
                </div>    
            </form>        
        </div>

        <div id="searchResults"></div>

        <!-- Artwork Grid -->
        <div class="artworkGrid">
            @foreach ($artworks as $art)
                <div class="artworkCard">
                    <a href="/theArt/{{ $art->id }}/artworkDetail">
                        <div class="artworkImageWrapper">
                            <img src="{{ asset($art->image_path) }}" alt="{{ $art->title }}">
                        </div>
                        <div class="artworkInfo">
                            <h3>{{ $art->title }}</h3>
                            <p>{{ $art->artist->name ?? '' }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $artworks->links() }}
        </div>

    </div>
</x-layout>
