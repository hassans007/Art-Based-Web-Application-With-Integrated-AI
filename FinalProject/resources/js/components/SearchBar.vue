<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'
import useDebouncedSearch from '@/composables/DebouncedSearch'
import FilterControls from '@/components/FiltersControls.vue'
import LoadingSpinner from '@/components/Spinner.vue'
import '../../css/searchBar.css'

const emit = defineEmits(['toggle-results', 'update:results'])

const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: value => ['artwork', 'artist'].includes(value)
  }
})

const page = usePage()
const {
  query,
  results,
  isLoading,
  error,
  selectedStyle,
  selectedTimePeriod,
  selectedArtist,
  selectedNationality,
  sortBy,
} = useDebouncedSearch(props.type, 300)

const showFullResults = ref(false)
const isDropdownOpen = ref(false)
const highlightedIndex = ref(-1)
const formRef = ref(null)

const hasFiltersActive = () => {
  return selectedStyle.value || selectedTimePeriod.value || selectedArtist.value || selectedNationality.value
}

// Watch query and filters together
watch([query, selectedStyle, selectedTimePeriod, selectedArtist, selectedNationality], () => {
  if (query.value.trim() || hasFiltersActive()) {
    showFullResults.value = true
    isDropdownOpen.value = !showFullResults.value
  } else {
    showFullResults.value = false
    isDropdownOpen.value = false
  }
  emit('update:results', results.value)
})

// Reset when page URL changes (navigation)
watch(() => page.url, () => resetSearch())

const resetSearch = () => {
  query.value = ''
  results.value = []
  selectedStyle.value = null
  selectedTimePeriod.value = null
  selectedArtist.value = null
  selectedNationality.value = null
  sortBy.value = 'newest'
  showFullResults.value = false
  isDropdownOpen.value = false
  highlightedIndex.value = -1
}

const highlightNext = () => {
  if (highlightedIndex.value < results.value.length - 1) highlightedIndex.value++
}
const highlightPrev = () => {
  if (highlightedIndex.value > 0) highlightedIndex.value--
}

const selectResult = (index) => {
  const selectedItem = results.value[index]
  if (!selectedItem) return
  window.location.href = selectedItem.type === 'artwork'
    ? `/theArt/${selectedItem.id}/artworkDetail`
    : `/theArt/${selectedItem.id}/artistDetail`
}

const handleClickOutside = (e) => {
  if (formRef.value && !formRef.value.contains(e.target)) {
    isDropdownOpen.value = false
  }
}

const handleSearchSubmit = () => {
  if (results.value.length > 0 && highlightedIndex.value >= 0) {
    selectResult(highlightedIndex.value)
  } else {
    showFullResults.value = true
  }
  isDropdownOpen.value = false
}

const highlightMatch = (text) => {
  if (!query.value) return text
  const pattern = new RegExp(`(${query.value})`, 'gi')
  return text.replace(pattern, '<strong>$1</strong>')
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})
onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div>
    <form @submit.prevent="handleSearchSubmit" class="search-form" ref="formRef">
      <div class="search-wrapper">
        <input
          v-model="query"
          type="text"
          class="search-input"
          placeholder="Search..."
          @keydown.enter.prevent="handleSearchSubmit"
          @keydown.down.prevent="highlightNext"
          @keydown.up.prevent="highlightPrev"
          @focus="isDropdownOpen = true"
        />
        <button type="submit" class="search-button">
          <img src="/images/search.png" alt="Search" width="20" height="20" loading="lazy" />
        </button>

        <!-- Dropdown Results -->
        <transition name="fade">
          <ul v-if="isDropdownOpen && results.length > 0 && !showFullResults" class="search-dropdown">
            <li
              v-for="(item, index) in results.slice(0, 5)"
              :key="item.type + '-' + (item.title || item.name)"
              class="search-item"
              :class="{ 'bg-gray-200': index === highlightedIndex }"
              @click="selectResult(index)"
            >
              <img
                v-if="item.image_path || item.profile_image"
                :src="item.image_path || item.profile_image"
                alt="Thumbnail"
                loading="lazy"
              />
              <div>
                <div v-if="item.type === 'artwork'">
                  <span v-html="highlightMatch(item.title)"></span>
                  <div class="text-gray-500 text-xs">
                    by <span v-html="highlightMatch(item.artist.name)"></span>
                  </div>
                </div>
                <div v-else-if="item.type === 'artist'">
                  <span v-html="highlightMatch(item.name)"></span>
                </div>
              </div>
            </li>
          </ul>
        </transition>
      </div>
    </form>

    <!-- Filters -->
    <FilterControls
      :type="type"
      @update:style="selectedStyle = $event"
      @update:timePeriod="selectedTimePeriod = $event"
      @update:artist="selectedArtist = $event"
      @update:nationality="selectedNationality = $event"
      @update:sortBy="sortBy = $event"
      @reset-all="resetSearch"
      class="filter-controls"
    />

    <!-- Skeleton Loading -->
    <section v-if="isLoading" class="skeleton-loading">
      <div v-for="n in 6" :key="n" class="skeleton-card"></div>
    </section>

    <!-- Full Results -->
    <section v-else-if="showFullResults && results.length > 0" class="search-results">
      <h2>Search Results</h2>
      <div class="results-grid">
        <div
          v-for="item in results"
          :key="item.type + '-' + (item.title || item.name)"
          class="result-card"
        >
          <a
            :href="item.type === 'artwork'
              ? `/theArt/${item.id}/artworkDetail`
              : `/theArt/${item.id}/artistDetail`"
          >
            <img
              v-if="item.image_path || item.profile_image"
              :src="item.image_path || item.profile_image"
              alt="Result Image"
              loading="lazy"
            />
            <div>
              <h3 v-html="highlightMatch(item.type === 'artwork' ? item.title : item.name)"></h3>
              <p v-if="item.type === 'artwork'">
                by <span v-html="highlightMatch(item.artist.name)"></span>
              </p>
            </div>
          </a>
        </div>
      </div>
    </section>

    <!-- No Results Message -->
    <section v-else-if="showFullResults && results.length === 0 && (query || hasFiltersActive())" class="no-results">
      No matching results found.
    </section>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

.skeleton-loading {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 1rem;
}
.skeleton-card {
  height: 200px;
  background: #e0e0e0;
  border-radius: 8px;
  animation: pulse 1.5s infinite ease-in-out;
}
@keyframes pulse {
  0% { background-color: #e0e0e0; }
  50% { background-color: #f0f0f0; }
  100% { background-color: #e0e0e0; }
}
</style>
