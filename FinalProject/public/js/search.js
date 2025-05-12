document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('searchForm');
    const searchBar = document.getElementById('searchBar');
    const resultsContainer = document.getElementById('searchResults');
    const liveResults = document.getElementById('liveResults');

    if (!form || !searchBar) return;

    // Full search on form submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchBar.value.trim();
        if (!query) {
            resultsContainer.innerHTML = '<p>Please enter a search term.</p>';
            return;
        }
        fetchResults(query, resultsContainer);
        liveResults.innerHTML = '';
        liveResults.style.display = 'none';
    });

    // Live search suggestions
    searchBar.addEventListener('input', function() {
        const query = searchBar.value.trim();
        console.log("Typing:", query);

        if (!query) {
            liveResults.innerHTML = '';
            liveResults.style.display = 'none';
            return;
        }

        fetch('/theArt/search?query=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                liveResults.innerHTML = '';
                if (data.length === 0) {
                    liveResults.style.display = 'none';
                    return;
                }

                data.slice(0, 5).forEach(item => {
                    const div = document.createElement('div');
                    div.classList.add('dropdown-item');

                    if (item.type === 'artwork') {
                        div.innerHTML = `<strong>${item.title}</strong> (Artwork)`;
                    } else if (item.type === 'artist') {
                        div.innerHTML = `<strong>${item.name}</strong> (Artist)`;
                    }

                    div.addEventListener('click', () => {
                        searchBar.value = item.type === 'artwork' ? item.title : item.name;
                        liveResults.innerHTML = '';
                        liveResults.style.display = 'none';
                        form.dispatchEvent(new Event('submit'));
                    });

                    liveResults.appendChild(div);
                });

                liveResults.style.display = 'block';
            })
            .catch(err => {
                console.error('Live search error:', err);
                liveResults.innerHTML = '';
                liveResults.style.display = 'none';
            });
    });

    document.addEventListener('click', function(e) {
        if (!searchBar.contains(e.target) && !liveResults.contains(e.target)) {
            liveResults.innerHTML = '';
            liveResults.style.display = 'none';
        }
    });

    function fetchResults(query, container) {
        fetch('/theArt/search?query=' + encodeURIComponent(query))
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                container.innerHTML = '';
                if (data.length === 0) {
                    container.innerHTML = '<p>No results found.</p>';
                    return;
                }

                data.forEach(item => {
                    const div = document.createElement('div');
                    div.classList.add('searchResult');
                    if (item.type === 'artwork') {
                        div.innerHTML = `
                            <div class="catalogContent">
                                <div class="artDetails">
                                    <h1>${item.title}</h1>
                                    <img src="${item.image_path}" alt="${item.title}" width="400">
                                    <p><strong>Artist:</strong> <a href="/theArt/${item.artist.id}/artistDetail">${item.artist.name}</a></p>
                                    <p><strong>Style:</strong> ${item.style}</p>
                                    <p><strong>Year Created:</strong> ${item.year_created}</p>
                                    <p>${item.description}</p>
                                </div>
                            </div>
                        `;
                    } else if (item.type === 'artist') {
                        div.innerHTML = `
                            <div class="catalogContent">
                                <div class="artDetails">
                                    <h1>${item.name}</h1>
                                    <img src="${item.profile_image}" alt="${item.name}" width="400">
                                    <p><strong>Birth Year:</strong> ${item.birth_year}</p>
                                    <p><strong>Death Year:</strong> ${item.death_year}</p>
                                    <p>${item.biography}</p>
                                </div>
                            </div>
                        `;
                    }

                    container.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Search failed:', error);
                container.innerHTML = '<p>Error loading results.</p>';
            });
    }
});
