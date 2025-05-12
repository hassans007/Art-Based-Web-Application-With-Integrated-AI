<x-layout title="Art Detail Page" homeImage="images/catalogTop.jpg">
    <div class="catalogContent">

        <!-- Main Artwork Details -->
        <div class="artDetails">
            <p><a class="back" href="/theArt/artCatalog"><img src="{{ asset('images/back.png') }}" alt="Back" width="20"> Artworks</a></p>
            <h1>{{ $artwork->title }}</h1>
            <img src="{{ asset($artwork->image_path) }}" alt="{{ $artwork->title }}" width="500">

            <p><strong>Artist:</strong>
                <a href="/theArt/{{ $artwork->artist->id }}/artistDetail">{{ $artwork->artist->name }}</a>
            </p>
            <p><strong>Style:</strong> {{ $artwork->style->name ?? 'Unknown' }}</p>
            <p><strong>Year Created:</strong> {{ $artwork->year_created }}</p>
            <p>{{ $artwork->description }}</p>
        </div>

        <!-- Related Artworks -->
        @if($relatedArtworks->count())
        <div class="relatedArtworks">
            <h2>Related Artworks</h2>
            <div class="relatedContent">
                @foreach($relatedArtworks as $related)
                    <div class="relatedItem">
                        <a href="/theArt/{{ $related->id }}/artworkDetail">
                            <img src="{{ asset($related->image_path) }}" alt="{{ $related->title }}" width="200">
                            <h4>{{ $related->title }}</h4>
                            <p><strong>Artist:</strong> {{ $related->artist->name }}</p>
                            <p><strong>Style:</strong> {{ $related->style->name }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-layout>
