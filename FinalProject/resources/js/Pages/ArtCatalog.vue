<script setup>
import { ref, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import SearchBar from '@/components/SearchBar.vue'

defineOptions({
  layout: AuthenticatedLayout
})

const props = defineProps({
  title: { type: String, default: 'The Art' },
  artworks: Object
})

const searchResults = ref([])

const updateResults = (newResults) => {
  searchResults.value = newResults
}

const handleReset = () => {
  searchResults.value = []
}

const artworksList = computed(() => props.artworks?.data || [])
</script>

<template>
  <Head :title="`${title} | The Art`" />

  <div class="catalogContent">
    <!-- SearchBar for Artwork -->
    <SearchBar
      type="artwork"
      @update:results="updateResults"
      @reset-all="handleReset"
    />

    <!-- Artwork Grid -->
    <div class="artworkGrid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-6">
      <div
        v-for="art in (searchResults.length > 0 ? searchResults : artworksList)"
        :key="art.id"
        class="artworkCard bg-white shadow-md rounded-lg overflow-hidden"
      >
        <a :href="`/theArt/${art.id}/artworkDetail`">
          <div class="artworkImageWrapper">
            <img :src="art.image_path" :alt="art.title" class="w-full h-48 object-cover" />
          </div>
          <div class="artworkInfo p-4 flex justify-between items-center">
            <div>
              <h3 class="text-lg font-semibold">{{ art.title }}</h3>
              <p class="text-sm text-gray-600">{{ art.artist?.name || '' }}</p>
            </div>
          </div>
        </a>
      </div>
    </div>

    <!-- Pagination only if no search results -->
    <div v-if="searchResults.length === 0" class="pagination-wrapper mt-6">
      <ul class="flex flex-wrap gap-2 justify-center">
        <li v-for="link in artworks.links" :key="link.label">
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
  </div>
</template>
