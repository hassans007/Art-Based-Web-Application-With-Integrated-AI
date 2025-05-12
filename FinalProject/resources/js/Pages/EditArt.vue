<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  title: String,
  artwork: Object,
  styles: Array,
  artists: Array,
})

const page = usePage()
const isNewArtwork = computed(() => !props.artwork.id)
const user = page.props.auth?.user

const isAdmin = computed(() => user?.is_admin)
const isPublicArtwork = computed(() => props.artwork.hasOwnProperty('is_approved'))

const form = useForm({
  id: props.artwork.id || '',
  title: props.artwork.title || '',
  style_id: props.artwork.style_id || '',
  custom_style: '',
  artist_id: props.artwork.artist_id || '',
  custom_artist: '',
  artist_name: props.artwork.artist_name || '',
  description: props.artwork.description || '',
  image: null,
  image_path: props.artwork.image_path || '',
  redirect_to: document.referrer || '/', // 🧠 smart fallback
})


const isCustomStyle = computed(() => form.style_id === 'custom')
const isCustomArtist = computed(() => form.artist_id === 'custom')

const previewUrl = ref(props.artwork.image_path || '')

function handleFileUpload(e) {
  const file = e.target.files[0]
  if (file) {
    form.image = file
    previewUrl.value = URL.createObjectURL(file)
  }
}


function submit() {
  const endpoint = isPublicArtwork.value
    ? `/theArt/community/${props.artwork.id}/update`
    : `/theArt/${props.artwork.id}/adminUpdate`

  form
    .transform((data) => {
      const formData = new FormData()
      for (const key in data) {
        formData.append(key, data[key] ?? '')
      }
      return formData
    })
    .post(endpoint, {
      preserveScroll: true,
      onSuccess: () => {
        form.reset('image')
        router.visit('/theArt/artCatalog')
      },
    })
}
</script>


<template>
  <Head :title="title" />

  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Edit Artwork</h1>

    <div
      v-if="$page.props.flash?.success"
      class="mb-4 px-4 py-2 rounded bg-green-100 text-green-800 border border-green-300"
    >
      {{ $page.props.flash.success }}
    </div>

    <form @submit.prevent="submit" class="space-y-6">

      <!-- Title -->
      <div>
        <label class="block mb-1 font-medium">Title</label>
        <input v-model="form.title" type="text" class="w-full border rounded px-4 py-2" />
        <div v-if="form.errors.title" class="text-red-600 text-sm">{{ form.errors.title }}</div>
      </div>

      <!-- Style -->
      <div>
        <label class="block mb-1 font-medium">Style</label>
        <select v-model="form.style_id" class="w-full border rounded px-4 py-2">
          <option value="" disabled>Select a style</option>
          <option v-for="style in styles" :key="style.id" :value="style.id">{{ style.name }}</option>
          <option value="custom">Other (Add Your Own)</option>
        </select>
        <div v-if="form.errors.style_id" class="text-red-600 text-sm">{{ form.errors.style_id }}</div>
      </div>

      <!-- Custom Style -->
      <div v-if="isCustomStyle">
        <label class="block mb-1 font-medium">Custom Style Name</label>
        <input v-model="form.custom_style" type="text" class="w-full border rounded px-4 py-2" />
        <div v-if="form.errors.custom_style" class="text-red-600 text-sm">{{ form.errors.custom_style }}</div>
      </div>

      <!-- Artist Info -->
      <div v-if="!isPublicArtwork && isAdmin">
        <label class="block mb-1 font-medium">Artist</label>
        <select v-model="form.artist_id" class="w-full border rounded px-4 py-2">
          <option value="" disabled>Select an artist</option>
          <option v-for="artist in artists" :key="artist.id" :value="artist.id">
            {{ artist.name }}
          </option>
          <option value="custom">Other (Add Your Own)</option>
        </select>
        <div v-if="form.errors.artist_id" class="text-red-600 text-sm">{{ form.errors.artist_id }}</div>
      </div>

      <!-- Custom Artist (for admin) -->
      <div v-if="!isPublicArtwork && isAdmin && isCustomArtist">
        <label class="block mb-1 font-medium">Custom Artist Name</label>
        <input v-model="form.custom_artist" type="text" class="w-full border rounded px-4 py-2" />
        <div v-if="form.errors.custom_artist" class="text-red-600 text-sm">{{ form.errors.custom_artist }}</div>
      </div>

      <!-- Artist Name (for public artwork) -->
      <div v-if="isPublicArtwork || !isAdmin">
        <label class="block mb-1 font-medium">Artist Name</label>
        <input v-model="form.artist_name" type="text" class="w-full border rounded px-4 py-2" />
        <div v-if="form.errors.artist_name" class="text-red-600 text-sm">{{ form.errors.artist_name }}</div>
      </div>

      <!-- Description -->
      <div>
        <label class="block mb-1 font-medium">Description</label>
        <textarea v-model="form.description" rows="4" class="w-full border rounded px-4 py-2" />
        <div v-if="form.errors.description" class="text-red-600 text-sm">{{ form.errors.description }}</div>
      </div>

      <input type="hidden" v-model="form.image_path" />

      <!-- Upload -->
      <div>
        <label class="block mb-1 font-medium">Upload New Image (optional)</label>
        <input
          type="file"
          accept="image/*"
          @change="handleFileUpload"
          class="w-full"
          :required="isNewArtwork"
        />
        <small class="text-gray-500">Current image will be used unless a new one is uploaded.</small>
        <div v-if="form.errors.image" class="text-red-600 text-sm">
          {{ isNewArtwork ? 'Image is required for new artworks.' : form.errors.image }}
        </div>

      </div>

      <!-- Preview -->
      <div v-if="previewUrl" class="mb-6">
        <label class="block mb-2 font-medium">Image Preview:</label>
        <img :src="previewUrl" alt="Preview" class="rounded shadow-md max-w-full h-auto" />
      </div>


      <!-- Submit -->
      <div>
        <button
          type="submit"
          :disabled="form.processing"
          class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition"
        >
          Save Changes
        </button>
      </div>
    </form>
  </div>
</template>
