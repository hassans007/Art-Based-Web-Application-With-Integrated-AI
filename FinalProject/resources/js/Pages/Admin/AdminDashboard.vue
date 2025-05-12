<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { UserIcon, ImageIcon, ClockIcon } from 'lucide-vue-next'
import { ref } from 'vue'
import axios from 'axios'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  title: String,
  stats: Object,
  mainArtworks: Array,
  publicArtworks: Array,
})

const publicArts = ref([...props.publicArtworks]) // make it reactive
const deletingId = ref(null)

function deleteArtwork(id) {
  if (!confirm('Are you sure you want to delete this artwork?')) return

  deletingId.value = id

  axios.delete(`/theArt/community/${id}`)
    .then(() => {
      publicArts.value = publicArts.value.filter(a => a.id !== id)
    })
    .catch(error => {
      console.error('Delete failed:', error)
      alert('Something went wrong while deleting. Please try again.')
    })
    .finally(() => {
      deletingId.value = null
    })
}
</script>

<template>
  <Head :title="title" />

  <div class="p-6">
    <h1 class="text-4xl font-bold mb-8 text-gray-800">Welcome, Admin!!</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
        <ImageIcon class="w-8 h-8 text-indigo-500" />
        <div>
          <p class="text-sm text-gray-500">Total Artworks</p>
          <p class="text-2xl font-bold text-gray-800">{{ stats.totalArtworks }}</p>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
        <ClockIcon class="w-8 h-8 text-yellow-500" />
        <div>
          <p class="text-sm text-gray-500">Pending Public Artworks</p>
          <p class="text-2xl font-bold text-gray-800">{{ stats.pendingPublic }}</p>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow p-6 flex items-center space-x-4">
        <UserIcon class="w-8 h-8 text-green-500" />
        <div>
          <p class="text-sm text-gray-500">Total Users</p>
          <p class="text-2xl font-bold text-gray-800">{{ stats.totalUsers }}</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-10">
      <h2 class="text-2xl font-semibold text-gray-700 mb-4">Quick Actions</h2>
      <ul class="space-y-3">
        <li><a href="/theArt/adminUpload" class="text-indigo-600 hover:underline text-lg">Upload New Artwork</a></li>
        <li><a href="/admin/managePublicArtworks" class="text-indigo-600 hover:underline text-lg">Manage Public Artworks</a></li>
      </ul>
    </div>

    <!-- Main Artworks -->
    <div class="mt-16">
      <h2 class="text-2xl font-semibold text-gray-700 mb-4">Main Artworks</h2>
      <div v-if="props.mainArtworks.length" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div
          v-for="art in props.mainArtworks"
          :key="art.id"
          class="bg-white rounded-xl shadow overflow-hidden relative group"
        >
          <a :href="`/theArt/${art.id}/artworkDetail`">
            <img :src="art.image_path" class="w-full h-48 object-cover transition-transform group-hover:scale-105 duration-300" />
          </a>
          <div class="p-3">
            <p class="font-semibold truncate">{{ art.title }}</p>
            <p class="text-sm text-gray-500">By: {{ art.artist?.name || 'Unknown' }}</p>
          </div>
        </div>
      </div>
      <p v-else class="text-gray-500">No main artworks found.</p>
    </div>

    <!-- Public/User Uploaded Artworks -->
    <div class="mt-16">
      <h2 class="text-2xl font-semibold text-gray-700 mb-4">Public Artworks (User Uploads)</h2>
      <div v-if="publicArts.length" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div
          v-for="art in publicArts"
          :key="art.id"
          class="bg-white rounded-xl shadow overflow-hidden relative group"
        >
          <a :href="`/theArt/community/${art.id}/artworkDetail`">
            <img :src="art.image_path" class="w-full h-48 object-cover transition-transform group-hover:scale-105 duration-300" />
          </a>
          <div class="p-3">
            <p class="font-semibold truncate">{{ art.title }}</p>
            <p class="text-sm text-gray-500">By: {{ art.user?.name || 'Unknown' }}</p>
          </div>

          <span
            class="absolute top-2 left-2 text-xs px-2 py-1 rounded-full"
            :class="art.is_approved ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
          >
            {{ art.is_approved ? 'Approved' : 'Pending' }}
          </span>

          <button
            @click="deleteArtwork(art.id)"
            :disabled="deletingId === art.id"
            class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 text-xs rounded hover:bg-red-700"
          >
            <span v-if="deletingId === art.id" class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
            <span v-else>Delete</span>
          </button>
        </div>
      </div>
      <p v-else class="text-gray-500">No user-uploaded artworks found.</p>
    </div>
  </div>
</template>
