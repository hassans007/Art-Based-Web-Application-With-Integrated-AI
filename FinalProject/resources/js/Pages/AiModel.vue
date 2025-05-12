<script setup>
import { ref } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import '../../css/aimodel.css'

defineOptions({
  layout: AuthenticatedLayout
})

const props = defineProps({
  title: String,
  style: String,
  feedback: String,
  description: String
})

const form = useForm({
  art: null
})

const previewUrl = ref(null)
const isLoading = ref(false)
const detectedStyle = ref(props.style ?? null)
const detectedDescription = ref(props.description ?? null)
const aiFeedback = ref(props.feedback ?? null)

function handlePreview(e) {
  const file = e.target.files[0]
  form.art = file
  if (file) {
    previewUrl.value = URL.createObjectURL(file)
  }
}

function submitArtwork() {
  if (!form.art) return

  isLoading.value = true
  detectedStyle.value = null
  detectedDescription.value = null
  aiFeedback.value = null

  form.post('/theArt/aimodel', {
    preserveScroll: true,
    onSuccess: () => {
      detectedStyle.value = usePage().props.style ?? 'Unknown'
      detectedDescription.value = usePage().props.description ?? ''
      aiFeedback.value = usePage().props.feedback ?? null
    },
    onFinish: () => {
      isLoading.value = false
    }
  })
}
</script>

<template>
  <Head :title="`${title} | The Art`" />

  <div class="page-container">
    <h2 class="page-title">Upload Your Artwork for Analysis</h2>
    <div class="page-subtitle">
      <p>Currently Detects Styles:</p>
      <p>['Cubism', 'Expressionism', 'Realism', 'Japanese Art', 'Renaissance']</p>
    </div>

    <form @submit.prevent="submitArtwork" enctype="multipart/form-data" class="upload-form">
      <input
        type="file"
        name="art"
        id="art"
        required
        accept="image/*"
        @change="handlePreview"
        class="file-input"
      />
      <button type="submit" class="submit-button">
        Analyze
      </button>
    </form>

    <!-- Image Preview -->
    <div v-if="previewUrl" class="preview-section">
      <h4 class="preview-title">Preview:</h4>
      <img :src="previewUrl" alt="Artwork Preview" class="preview-image" />
    </div>

    <!-- Loading Spinner -->
    <div v-if="isLoading" class="loading-section">
      <span class="loading-spinner"></span>
      <p class="loading-text">Analyzing artwork, please wait...</p>
    </div>

    <!-- Result Section -->
    <div v-if="detectedStyle" class="result-section">
      <h3 class="result-title">
        Detected Style: <span class="detected-style">{{ detectedStyle }}</span>
      </h3>
      <div v-if="detectedDescription" class="description-section">
        <h4 class="description-title">Artwork Description:</h4>
        <p class="description-text">{{ detectedDescription }}</p>
      </div>
    </div>

    <!-- Feedback Section -->
    <div v-if="aiFeedback" class="feedback-section">
      <h3 class="feedback-title">AI Feedback:</h3>
      <p class="feedback-text">{{ aiFeedback }}</p>
    </div>

    <p v-else-if="!isLoading" class="feedback-loading">
      Feedback will appear after analysis.
    </p>
  </div>
</template>
