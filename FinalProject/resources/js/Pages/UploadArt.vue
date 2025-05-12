<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, usePage, router } from '@inertiajs/vue3'
import { computed, ref, watch, onBeforeUnmount } from 'vue'
import { route } from 'ziggy-js'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  title: String,
  styles: Array,
  artists: Array,
  role: String,
})

const page = usePage()
const isAdmin = computed(() => props.role === 'admin')
const FORM_KEY = 'upload-artwork-form'

const savedForm = JSON.parse(localStorage.getItem(FORM_KEY)) || {}

const uploadTarget = ref(savedForm.uploadTarget || (isAdmin.value ? 'main' : 'community'))

const form = useForm({
  title: savedForm.title || '',
  style_id: savedForm.style_id || '',
  custom_style: savedForm.custom_style || '',
  description: savedForm.description || '',
  year_created: savedForm.year_created || new Date().getFullYear(),
  artist_id: savedForm.artist_id || '',
  custom_artist: savedForm.custom_artist || '',
  artist_name: savedForm.artist_name || '',
  uploadTarget: uploadTarget.value,
  image: null,
})

const isCustomStyle = computed(() => form.style_id === 'custom')
const isCustomArtist = computed(() => form.artist_id === 'custom')

function handleFileUpload(e) {
  form.image = e.target.files[0]
}

watch(
  () => ({
    title: form.title,
    style_id: form.style_id,
    custom_style: form.custom_style,
    artist_id: form.artist_id,
    custom_artist: form.custom_artist,
    artist_name: form.artist_name,
    description: form.description,
    year_created: form.year_created,
    uploadTarget: uploadTarget.value,
  }),
  (newForm) => {
    localStorage.setItem(FORM_KEY, JSON.stringify(newForm))
  },
  { deep: true }
)

watch(
  () => form.custom_artist,
  (newVal) => {
    if (isAdmin.value && uploadTarget.value === 'main' && isCustomArtist.value) {
      form.artist_name = newVal
    }
  }
)

onBeforeUnmount(() => {
  localStorage.removeItem(FORM_KEY)
})

function submit() {
  const endpoint = isAdmin.value && uploadTarget.value === 'main'
    ? route('admin.artwork.store')
    : route('artwork.store')

  router.post(endpoint, form, {
    forceFormData: true,
    onSuccess: () => {
      form.reset()
      localStorage.removeItem(FORM_KEY)
    },
    onError: (errors) => {
      Object.entries(errors).forEach(([key, message]) => {
        form.setError(key, message)
      })
    },
  })
}
</script>

<template>
  <Head :title="title" />

  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Upload Your Artwork</h1>

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
        <input v-model="form.title" type="text" class="w-full border rounded px-4 py-2" required />
        <div v-if="form.errors.title" class="text-red-600 text-sm">{{ form.errors.title }}</div>
      </div>

      <!-- Style -->
      <div>
        <label class="block mb-1 font-medium">Style</label>
        <select v-model="form.style_id" class="w-full border rounded px-4 py-2" required>
          <option value="" disabled>Select a style</option>
          <option v-for="style in styles" :key="style.id" :value="style.id">{{ style.name }}</option>
          <option value="custom">Other (Add Your Own)</option>
        </select>
        <div v-if="form.errors.style_id" class="text-red-600 text-sm">{{ form.errors.style_id }}</div>
      </div>

      <div v-if="isCustomStyle">
        <label class="block mb-1 font-medium">Custom Style Name</label>
        <input v-model="form.custom_style" type="text" class="w-full border rounded px-4 py-2" />
        <div v-if="form.errors.custom_style" class="text-red-600 text-sm">{{ form.errors.custom_style }}</div>
      </div>

      <!-- Year -->
      <div>
        <label class="block mb-1 font-medium">Year Created</label>
        <input v-model="form.year_created" type="number" min="1000" max="3000" class="w-full border rounded px-4 py-2" required />
        <div v-if="form.errors.year_created" class="text-red-600 text-sm">{{ form.errors.year_created }}</div>
      </div>

      <!-- Description -->
      <div>
        <label class="block mb-1 font-medium">Description</label>
        <textarea v-model="form.description" rows="4" class="w-full border rounded px-4 py-2" required />
        <div v-if="form.errors.description" class="text-red-600 text-sm">{{ form.errors.description }}</div>
      </div>

      <!-- Image -->
      <div>
        <label class="block mb-1 font-medium">Upload Image</label>
        <input type="file" accept="image/*" @change="handleFileUpload" class="w-full" required />
        <div v-if="form.errors.image" class="text-red-600 text-sm">{{ form.errors.image }}</div>
      </div>

      <!-- Upload Target for Admin -->
      <div v-if="isAdmin" class="pt-4">
        <label class="block mb-1 font-medium">Upload To</label>
        <select v-model="uploadTarget" class="w-full border rounded px-4 py-2">
          <option value="main">Main Gallery</option>
          <option value="community">Community Submissions</option>
        </select>
      </div>

      <!-- Artist Section -->
      <div v-if="!isAdmin || uploadTarget === 'community'">
        <label class="block mb-1 font-medium">Artist Name</label>
        <input
          v-model="form.artist_name"
          type="text"
          placeholder="Enter artist name"
          class="w-full border rounded px-4 py-2"
          required
        />
        <div v-if="form.errors.artist_name" class="text-red-600 text-sm">{{ form.errors.artist_name }}</div>
      </div>

      <div v-else>
        <label class="block mb-1 font-medium">Artist</label>
        <select v-model="form.artist_id" class="w-full border rounded px-4 py-2" required>
          <option value="" disabled>Select an artist</option>
          <option v-for="artist in artists" :key="artist.id" :value="artist.id">{{ artist.name }}</option>
          <option value="custom">Other (Add Your Own)</option>
        </select>
        <div v-if="form.errors.artist_id" class="text-red-600 text-sm">{{ form.errors.artist_id }}</div>

        <div v-if="isCustomArtist">
          <label class="block mb-1 font-medium">Custom Artist Name</label>
          <input
            v-model="form.custom_artist"
            type="text"
            class="w-full border rounded px-4 py-2"
            required
          />
          <div v-if="form.errors.custom_artist" class="text-red-600 text-sm">{{ form.errors.custom_artist }}</div>
        </div>
      </div>

      <!-- Submit -->
      <div>
        <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
          Upload Artwork
        </button>
      </div>
    </form>
  </div>
</template>
