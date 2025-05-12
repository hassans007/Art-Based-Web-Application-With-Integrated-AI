<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SearchBar from '@/components/SearchBar.vue'

defineOptions({
  layout: AuthenticatedLayout,
})

const props = defineProps({
  title: {
    type: String,
    default: 'Artists Catalog',
  },
  artists: Object
})

const searchResults = ref([])

const updateResults = (newResults) => {
  searchResults.value = newResults
}

const handleReset = () => {
  searchResults.value = []
}

const artistList = computed(() => props.artists?.data || [])
</script>

<template>
  <Head :title="`${title} | The Art`" />

  <div class="catalogContent">
    <!-- SearchBar for Artists -->
    <SearchBar
      type="artist"
      @update:results="updateResults"
      @reset-all="handleReset"
    />

    <!-- Artist Cards -->
    <div class="artworksContent">
      <div
        v-for="artist in (searchResults.length > 0 ? searchResults : artistList)"
        :key="artist.id"
        class="artCard"
      >
        <a :href="`/theArt/${artist.id}/artistDetail`">
          <img
            class="profileImage"
            :src="artist.profile_image"
            :alt="artist.name"
          />
        </a>
        <h2 class="text-lg font-bold text-center">{{ artist.name }}</h2>
      </div>
    </div>
  </div>

  <!-- Pagination (only if no search happening) -->
  <div v-if="searchResults.length === 0" class="pagination-wrapper mt-6">
    <ul class="flex flex-wrap gap-2 justify-center">
      <li v-for="link in artists?.links || []" :key="link.label">
        <a
          v-if="link.url"
          :href="link.url"
          v-html="link.label"
          class="px-3 py-1 border rounded text-sm"
          :class="{ 'font-bold underline': link.active }"
          ></a>
          <span
            v-else
            v-html="link.label"
            class="px-3 py-1 text-gray-400 text-sm"
          ></span>
      </li>
    </ul>
  </div>
</template>
