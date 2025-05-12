<x-layout title="Home Page" homeImage="images/homeTop.jpg">
    <div class="homeContent">
        <div id="searchContainer" style="position: relative;">
            <form id="searchForm">
                <div class="searchItem">
                    <input type="text" id="searchBar" class="searchBar" autocomplete="off" placeholder="Search artwork, artist and more..">
                    <div id="liveResults" class="dropdown"></div>
                    <button id="searchButton" type="submit" class="searchButton">
                        <img class="searchImg" src="{{ asset('images/search.png') }}" alt="Search Icon">
                    </button>
                </div>
            </form>
        </div>

        <div id="searchResults"></div>
    </div>
</x-layout>
