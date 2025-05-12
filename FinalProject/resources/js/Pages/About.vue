<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineOptions({
  layout: AuthenticatedLayout
})

defineProps({
  title: {
    type: String,
    default: 'About Page',
  },
})
</script>

<template>
  <Head :title="`${title} | The Art`" />

  <div class="max-w-5xl mx-auto py-12 px-6">
    <h1 class="text-4xl font-bold text-center mb-8">About The Art</h1>

    <div class="space-y-8 text-lg leading-relaxed text-gray-700">
      <p>
        <strong>The Art</strong> is a next-generation web application blending creativity and technology. 
        Designed for art students, enthusiasts, and educators, it provides a dynamic space to explore historical and contemporary artworks, receive AI-powered critiques, and engage with an interactive, learning-driven community.
      </p>

      <p>
        Built with <strong>Laravel</strong> and <strong>Vue.js</strong>, and powered by artificial intelligence models like 
        <em>Convolutional Neural Networks (CNN)</em> and <em>BLIP (Bootstrapped Language-Image Pretraining)</em>, 
        the platform bridges traditional art education with modern innovation. 
        Whether you're uploading your own art for feedback, participating in educational games, or chatting with fellow creatives, 
        <strong>The Art</strong> turns every interaction into a step forward in your artistic journey.
      </p>

      <p>
        We believe in a world where art education is accessible, intelligent, and interactive. 
        By using cutting-edge AI and thoughtful design principles, <strong>The Art</strong> empowers users to learn, grow, and connect — anywhere, anytime.
      </p>
    </div>
  </div>
</template>
