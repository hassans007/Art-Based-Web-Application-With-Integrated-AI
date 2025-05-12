<x-layout title="{{ $artist->name }} - Artist Page" homeImage="images/catalogTop.jpg">
    <div class="catalogContent">
        <!-- Artist Info -->
        <div class="artistDetails">
            <div class="artistDTop">
                <p><a class="back" href="/theArt/artistCatalog"><img src="{{ asset('images/back.png') }}" alt="Back" width="20"> Artists</a></p>
                <h1>{{ $artist->name }}</h1>
                <p>{{ $artist->birth_year ?? 'Unknown' }} - {{ $artist->death_year ?? 'Unknown' }}</p>
                <p>{{ $artist->nationality ?? 'Unknown' }}</p>
            </div>

            <img src="{{ asset($artist->profile_image) }}" alt="{{ $artist->name }}" width="400">
            <p>{{ $artist->biography }}</p>
        </div>

        <!-- Artworks by Artist -->
        <div class="relatedArtworks">
            <h2>Artworks by {{ $artist->name }}</h2>
            <div class="relatedContent">
                @forelse ($artist->artworks as $artwork)
                    <div class="relatedItem">
                        <a href="/theArt/{{ $artwork->id }}/artworkDetail">
                            <img src="{{ asset($artwork->image_path) }}" alt="{{ $artwork->title }}" width="200">
                            <h4>{{ $artwork->title }}</h4>
                            <p><strong>Artist:</strong> {{ $artist->name }}</p>
                            <p><strong>Style:</strong> {{ $artwork->style->name ?? 'Unknown' }}</p>
                        </a>
                    </div>
                @empty
                    <p>No artworks found for this artist.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layout>
