<script setup>
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineOptions({ layout: AuthenticatedLayout })

defineProps({
  title: String,
  submissions: Array,
})

function approve(id) {
  router.patch(route('admin.public.approve', id), {}, {
    onSuccess: () => console.log('Approved artwork ID:', id),
  })
}

function reject(id) {
  if (confirm('Are you sure you want to delete this artwork?')) {
    router.delete(route('admin.public.destroy', id))
  }
}
</script>

<template>
  <Head :title="title" />

  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6">Review Public Artworks</h1>

    <div v-if="submissions.length === 0" class="text-gray-600">
      No pending submissions. You're all caught up! 
    </div>

    <div v-else class="space-y-6">
      <div
        v-for="art in submissions"
        :key="art.id"
        class="bg-white rounded-xl shadow-md p-4 flex space-x-4"
      >
        <img :src="'/' + art.image_path" class="w-40 h-auto rounded-md border" />

        <div class="flex-1">
          <h2 class="text-xl font-semibold">{{ art.title }}</h2>
          <p class="text-gray-700">{{ art.description }}</p>
          <p class="text-sm text-gray-500 mt-1">Style: {{ art.style?.name }}</p>
          <p class="text-sm text-gray-400 mt-1">By: {{ art.user.name }}</p>

          <div class="mt-4 flex space-x-2">
            <button
              @click="approve(art.id)"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
            >
              Approve
            </button>
            <button
              @click="reject(art.id)"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
