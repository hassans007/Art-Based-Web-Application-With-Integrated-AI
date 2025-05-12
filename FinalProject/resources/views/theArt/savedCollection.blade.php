<x-layout title="Saved Collection Page" homeImage="images/savedTop.jpg">


<div class="savedContent">

    <div id="searchContainer">
        <form id="searchForm">
            <div class="searchItem">
                <input type="text" id="searchBar" class="searchBar" placeholder="Search artwork, artist and more..">
                <button id="searchButton" type="submit" class="searchButton"><img class="searchImg" src="{{ asset('images/search.png') }}" alt="Logo"></button>
                <div id="searchMessageSaved"></div>
            </div>    
        </form>        
    </div>

</div>


</x-layout>