<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineOptions({
  layout: AuthenticatedLayout
})

defineProps({
  title: String,
  artist: Object,
})
</script>

<template>
  <Head :title="`${title} | The Art`" />

  <div class="catalogContent">
    <!-- Back Button -->
    <p>
      <a class="backLink" href="/theArt/artistCatalog">
        <img src="/images/back.png" alt="Back" /> Back to Artists
      </a>
    </p>

    <!-- Artist Info -->
    <div class="artDetails">
      <div class="artHeader">
        <h1>{{ artist.name }}</h1>
      </div>

      <img
        v-if="artist.profile_image"
        :src="`${artist.profile_image}`"
        :alt="artist.name"
        class="artImage"
      />

      <p><strong>Birth Year:</strong> {{ artist.birth_year ?? 'Unknown' }}</p>
      <p><strong>Death Year:</strong> {{ artist.death_year ?? 'Unknown' }}</p>
      <p><strong>Nationality:</strong> {{ artist.nationality ?? 'Unknown' }}</p>
      <p class="artDescription">{{ artist.biography }}</p>
    </div>

    <!-- Related Artworks -->
    <div v-if="artist.artworks?.length" class="relatedArtworks">
      <h2>Artworks by {{ artist.name }}</h2>
      <div class="relatedContent">
        <div
          v-for="artwork in artist.artworks"
          :key="artwork.id"
          class="relatedItem"
        >
          <a :href="`/theArt/${artwork.id}/artworkDetail`">
            <img
              :src="artwork.image_path"
              :alt="artwork.title"
              width="200"
            />
            <h4>{{ artwork.title }}</h4>
            <p><strong>Style:</strong> {{ artwork.style?.name ?? 'Unknown' }}</p>
          </a>
        </div>
      </div>
    </div>

    <p v-else class="noArtworks">No artworks found for this artist.</p>
  </div>
</template>
