<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'
import { ref } from 'vue'

defineOptions({
  layout: AuthenticatedLayout,
})

const props = defineProps({
  title: {
    type: String,
    default: 'My Profile',
  },
  userArtworks: Array,
  likedArtworks: Array,
  savedArtworks: Array,
  gameScores: Array,
})

// Clone artworks into a reactive list
const artworks = ref([...props.userArtworks])
const deletingId = ref(null)

const deleteArtwork = async (id) => {
  if (confirm('Are you sure you want to delete this artwork?')) {
    deletingId.value = id
    try {
      await axios.delete(`/theArt/community/${id}/delete`)
      artworks.value = artworks.value.filter(art => art.id !== id)
    } catch (error) {
      console.error('Failed to delete artwork:', error)
    } finally {
      deletingId.value = null
    }
  }
}
</script>

<template>
  <Head :title="`${title} | The Art`" />

  <div class="p-6 space-y-12">
    <!-- Games Section -->
    <section>
      <h2 class="text-2xl font-bold mb-4">Your Game Scores</h2>
      <div v-if="gameScores.length" class="overflow-x-auto">
        <table class="min-w-full bg-white shadow rounded-lg">
          <thead>
            <tr class="bg-gray-100 text-gray-700">
              <th class="py-2 px-4 text-left">Game</th>
              <th class="py-2 px-4 text-left">Score</th>
              <th class="py-2 px-4 text-left">Out of</th>
              <th class="py-2 px-4 text-left">Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="score in gameScores" :key="score.id" class="border-t">
              <td class="py-2 px-4">{{ score.game_name }}</td>
              <td class="py-2 px-4">{{ score.score }}</td>
              <td class="py-2 px-4">{{ score.total_questions }}</td>
              <td class="py-2 px-4">{{ new Date(score.created_at).toLocaleDateString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <p v-else class="text-gray-500">You haven't played any games yet.</p>
    </section>

    <!-- Uploaded Artworks -->
    <section>
      <h2 class="text-2xl font-bold mb-4">Your Uploads</h2>
      <div v-if="artworks.length" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div
          v-for="art in artworks"
          :key="art.id"
          class="border rounded-lg overflow-hidden shadow relative"
        >
          <a :href="`/theArt/community/${art.id}/communityArtworkDetail`">
            <img :src="`/${art.image_path}`" alt="" class="w-full h-48 object-cover" />
            <div class="p-2 text-center font-medium">{{ art.title }}</div>
          </a>
          <button
            @click="deleteArtwork(art.id)"
            :disabled="deletingId === art.id"
            class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 text-xs rounded hover:bg-red-700 flex items-center justify-center"
          >
            <span v-if="deletingId === art.id" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
            <span v-else>Delete</span>
          </button>
        </div>
      </div>
      <p v-else class="text-gray-500">You haven't uploaded any artworks yet.</p>
    </section>

    <!-- Liked Artworks -->
    <section>
      <h2 class="text-2xl font-bold mb-4">Liked Artworks</h2>
      <div v-if="likedArtworks.length" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="art in likedArtworks" :key="art.id" class="border rounded-lg overflow-hidden shadow">
          <a :href="`/theArt/${art.id}/artworkDetail`">
            <img :src="art.image_path" alt="" class="w-full h-48 object-cover" />
            <div class="p-2 text-center font-medium">{{ art.title }}</div>
          </a>
        </div>
      </div>
      <p v-else class="text-gray-500">You haven't liked any artworks yet.</p>
    </section>

    <!-- Saved Artworks -->
    <section>
      <h2 class="text-2xl font-bold mb-4">Saved Artworks</h2>
      <div v-if="savedArtworks.length" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="art in savedArtworks" :key="art.id" class="border rounded-lg overflow-hidden shadow">
          <a :href="`/theArt/${art.id}/artworkDetail`">
            <img :src="art.image_path" alt="" class="w-full h-48 object-cover" />
            <div class="p-2 text-center font-medium">{{ art.title }}</div>
          </a>
        </div>
      </div>
      <p v-else class="text-gray-500">You haven't saved any artworks yet.</p>
    </section>
  </div>
</template>
