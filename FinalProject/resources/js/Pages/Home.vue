<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  title: String,
  featuredArtworks: Array,
  popularArtworks: Array,
  mostSavedArtworks: Array,
  publicArtworks: Array,
})

const page = usePage()
const isAdmin = computed(() => !!page.props.auth.admin)
</script>


<template>
  <Head :title="`${title} | The Art`" />

  <!-- Flash success message -->
  <div v-if="$page.props.flash?.success" class="px-6 mt-6">
    <div class="bg-green-100 text-green-800 px-4 py-3 rounded border border-green-300">
      {{ $page.props.flash.success }}
    </div>
  </div>

  <!-- Featured Artworks -->
  <section class="px-6 py-10 bg-gray-50">
    <h2 class="text-2xl font-semibold mb-6 text-center">Featured Artworks</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="art in featuredArtworks" :key="art.id" class="bg-white shadow rounded p-4">
        <a :href="`/theArt/${art.id}/artworkDetail`">
          <img :src="art.image_path" alt="Artwork" class="w-full h-48 object-cover rounded" />
        </a>
        <h3 class="text-lg mt-2 font-semibold">{{ art.title }}</h3>
        <p class="text-sm text-gray-600">{{ art.artist.name }}</p>
      </div>
    </div>
  </section>

  <!-- Public Uploads -->
  <section class="px-6 py-12 bg-white">
    <h2 class="text-2xl font-semibold mb-6 text-center">Community Uploads</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        v-for="upload in publicArtworks"
        :key="upload.id"
        class="bg-gray-50 p-4 rounded shadow"
      >
      <a :href="`/theArt/community/${upload.id}/communityArtworkDetail`">
        <img :src="`/${upload.image_path}`" alt="User Upload" class="w-full h-48 object-cover rounded" />
      </a>
        <h3 class="text-lg font-bold mt-2">{{ upload.title }}</h3>
        <p class="text-sm text-gray-600">Style: {{ upload.style }}</p>
        <p class="text-sm text-gray-500">By {{ upload.user?.name ?? 'Anonymous' }}</p>
      </div>
    </div>
    <p v-if="!publicArtworks.length" class="text-center text-gray-500 mt-4">
      No public artworks have been uploaded yet.
    </p>

    <div class="mt-8 text-center">
      <a
        :href="route('uploadArt')"
        class="inline-block bg-indigo-600 text-white font-semibold px-6 py-3 rounded hover:bg-indigo-700 transition"
      >
        Upload Your Artwork
      </a>
    </div>
  </section>

  <!-- Explore Section -->
  <section class="px-6 py-16 bg-white text-center">
    <h2 class="text-3xl font-bold mb-4">Explore the Depth of Art</h2>
    <p class="max-w-2xl mx-auto text-gray-700">
      Dive into a curated world of creativity — from classic masterpieces to contemporary marvels.
    </p>
  </section>

  <!-- Popular & Most Saved -->
  <section class="px-6 py-10 grid grid-cols-1 lg:grid-cols-2 gap-8 bg-gray-50">
    <div>
      <h2 class="text-xl font-semibold mb-4">Popular Artworks</h2>
      <div v-for="art in popularArtworks" :key="art.id" class="mb-4 bg-white shadow rounded p-4 flex gap-4">
        <a :href="`/theArt/${art.id}/artworkDetail`">
          <img :src="art.image_path" alt="Artwork" class="w-24 h-24 object-cover rounded" />
        </a>
        <div>
          <h3 class="font-medium">{{ art.title }}</h3>
          <p class="text-sm text-gray-600">{{ art.artist.name }}</p>
        </div>
      </div>
    </div>

    <div>
      <h2 class="text-xl font-semibold mb-4">Most Saved Artworks</h2>
      <div v-for="art in mostSavedArtworks" :key="art.id" class="mb-4 bg-white shadow rounded p-4 flex gap-4">
        <a :href="`/theArt/${art.id}/artworkDetail`">
          <img :src="art.image_path" alt="Artwork" class="w-24 h-24 object-cover rounded" />
        </a>
        <div>
          <h3 class="font-medium">{{ art.title }}</h3>
          <p class="text-sm text-gray-600">{{ art.artist.name }}</p>
        </div>
      </div>
    </div>
  </section>

</template>
