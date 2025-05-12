<script setup>
import { ref, computed } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import axios from 'axios'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  title: String,
  artwork: Object,
  relatedArtworks: {
    type: Array,
    default: () => [],
  },
})

const page = usePage()
const user = page.props.auth?.user

const isAdmin = computed(() => user?.is_admin)
const isOwner = computed(() => user?.id === props.artwork.user_id)

// Check if artwork is from PublicArtwork table
const isPublicArtwork = computed(() => props.artwork.hasOwnProperty('is_approved'))

// Permission to edit/delete
const canManage = computed(() => isAdmin.value || isOwner.value)

const isLiked = ref(props.artwork.is_liked ?? false)
const isSaved = ref(props.artwork.is_saved ?? false)
const isDeleting = ref(false)

const editLink = computed(() => route('public.artwork.edit', props.artwork.id))

const deleteEndpoint = computed(() => route('user.public.destroy', props.artwork.id))

const toggleSave = async () => {
  try {
    const response = await axios.post(`/theArt/${props.artwork.id}/toggle-save`)
    isSaved.value = response.data.saved
  } catch (error) {
    console.error('Save failed', error)
  }
}

const toggleLike = async () => {
  try {
    const endpoint = isPublicArtwork.value
      ? `/theArt/community/${props.artwork.id}/like`
      : `/theArt/${props.artwork.id}/like`
    const response = await axios.post(endpoint)
    isLiked.value = response.data.liked
  } catch (error) {
    console.error('Like failed', error)
  }
}

const deleteArtwork = () => {
  if (!confirm('Are you sure you want to delete this artwork?')) return

  isDeleting.value = true

  axios.delete(deleteEndpoint.value)
    .then(() => {
      const redirectTo = '/theArt/home'
      router.visit(redirectTo, {
        preserveState: false,
        preserveScroll: false,
      })
    })
    .catch((err) => {
      console.error('Delete failed:', err)
      alert('Something went wrong while deleting the artwork.')
    })
    .finally(() => {
      isDeleting.value = false
    })
}

</script>



<template>
  <Head :title="`${title} | The Art`" />

  <div class="catalogContent">
    <div class="artDetails">
      <p>
        <a class="backLink" href="/theArt/artCatalog">
          <img src="/images/back.png" alt="Back" /> Back to Artworks
        </a>
      </p>

        <div class="artHeader">
          <h1>{{ artwork.title }}</h1>
          <div class="artActions flex items-center gap-3 mt-4">
          <!-- Save Button -->
          <button @click.prevent="toggleSave" class="iconButton">
            <svg
              v-if="isSaved"
              xmlns="http://www.w3.org/2000/svg"
              class="w-6 h-6 text-green-500"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path d="M5 3a2 2 0 012-2h10a2 2 0 012 2v18l-7-4-7 4V3z" />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="w-6 h-6 text-gray-400"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M5 3a2 2 0 012-2h10a2 2 0 012 2v18l-7-4-7 4V3z" />
            </svg>
          </button>

          <!-- Like Button -->
          <button @click.prevent="toggleLike" class="iconButton">
            <svg
              v-if="isLiked"
              xmlns="http://www.w3.org/2000/svg"
              class="w-6 h-6 text-red-500"
              viewBox="0 0 24 24"
              fill="currentColor"
            >
              <path
                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                  2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09
                  C13.09 3.81 14.76 3 16.5 3
                  19.58 3 22 5.42 22 8.5
                  c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
              />
            </svg>
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              class="w-6 h-6 text-gray-400"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                d="M16.5 3c-1.74 0-3.41.81-4.5 2.09
                  C10.91 3.81 9.24 3 7.5 3
                  4.42 3 2 5.42 2 8.5
                  c0 3.78 3.4 6.86 8.55 11.54L12 21.35
                  l1.45-1.32C18.6 15.36 22 12.28 22 8.5
                  22 5.42 19.58 3 16.5 3z"
              />
            </svg>
          </button>

          <!-- Edit / Delete Buttons -->
          <template v-if="canManage">
            <a
              :href="editLink"
              class="text-sm text-blue-600 hover:underline ml-4"
            >
              Edit
            </a>
            <button
              @click="deleteArtwork"
              class="text-sm text-red-600 hover:underline"
              :disabled="isDeleting"
            >
              <span v-if="isDeleting">Deleting...</span>
              <span v-else>Delete</span>
            </button>

          </template>
        </div>
      </div>


      <img
          v-if="artwork.image_path"
          :src="artwork.image_path.startsWith('storage') ? '/' + artwork.image_path : artwork.image_path"
          class="artImage"
        />


        <p><strong>Artist:</strong>
        <span v-if="artwork.artist">
          <a :href="`/theArt/${artwork.artist.id}/artistDetail`" class="artistLink">{{ artwork.artist.name }}</a>
        </span>
        <span v-else>
          {{ artwork.artist_name ?? 'Unknown' }}
        </span>
      </p>
      <p><strong>Style:</strong> {{ artwork.style?.name ?? 'Unknown' }}</p>
      <p class="artDescription"><strong>Description:</strong> {{ artwork.description }}</p>
    </div>

    <div v-if="relatedArtworks && relatedArtworks.length" class="relatedArtworks">
      <h2>Related Artworks</h2>
      <div class="relatedContent">
        <div class="relatedItem" v-for="related in relatedArtworks" :key="related.id">
          <a :href="`/theArt/${related.id}/artworkDetail`">
            <img :src="related.image_path" :alt="related.title" />
            <h4>{{ related.title }}</h4>
            <p><strong>Artist:</strong> {{ related.artist?.name ?? 'Unknown' }}</p>
            <p><strong>Style:</strong> {{ related.style?.name ?? 'Unknown' }}</p>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
