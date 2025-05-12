<x-layout title="Artist Catalog Page" homeImage="images/catalogTop.jpg">

<div class="catalogContent">

    <div id="searchContainer">
        <form id="searchForm">
            <div class="searchItem">
                <input type="text" id="searchBar" class="searchBar" placeholder="Search artists...">
                <button id="searchButton" type="submit" class="searchButton">
                    <img class="searchImg" src="{{ asset('images/search.png') }}" alt="Search">
                </button>
                <div id="searchMessageSaved"></div>
            </div>    
        </form>        
    </div>

    <div id="searchResults"></div>

    <div class="artworksContent">
        @foreach ($artists as $artist)
            <div class="artCard">
                <a href="/theArt/{{ $artist->id }}/artistDetail">
                    <img class="profileImage" src="{{ asset($artist->profile_image) }}" alt="{{ $artist->name }}" width="300">
                </a>
                <h2>{{ $artist->name }}</h2>
                <!-- <p><strong>Nationality:</strong> {{ $artist->nationality ?? 'Unknown' }}</p>
                <p><strong>Famous For:</strong> {{ $artist->famous_for ?? 'N/A' }}</p> -->
            </div>
        @endforeach
    </div>

</div>
<div class="pagination-wrapper">
    {{ $artists->links() }}
</div>
</x-layout>
