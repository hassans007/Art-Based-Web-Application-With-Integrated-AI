<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  title: String,
  uploads: Array,
})

const page = usePage()
const authUser = page.props.auth.user
const commentInputs = ref({})
const likeStates = ref({})

onMounted(() => {
  props.uploads.forEach(upload => {
    commentInputs.value[upload.id] = ''
  })
})

const toggleLike = async (uploadId) => {
  try {
    const response = await axios.post(`/theArt/community/${uploadId}/like`)
    likeStates.value[uploadId] = {
      liked: response.data.liked,
      likes_count: response.data.likes_count,
    }
  } catch (error) {
    console.error('Error liking artwork:', error)
  }
}

const submitComment = async (uploadId) => {
  const content = commentInputs.value[uploadId]?.trim()
  if (!content) return

  try {
    const response = await axios.post(`/theArt/community/${uploadId}/comment`, { content })
    const newComment = response.data.comment

    const upload = props.uploads.find((u) => u.id === uploadId)
    if (upload) {
      upload.comments.push(newComment)
    }

    commentInputs.value[uploadId] = ''
  } catch (error) {
    console.error('Error adding comment:', error)
  }
}

const deleteComment = async (commentId, uploadId) => {
  // if (!confirm('Delete this comment?')) return
  try {
    await axios.delete(`/theArt/community/${commentId}/comment`)
    const upload = props.uploads.find((u) => u.id === uploadId)
    if (upload) {
      upload.comments = upload.comments.filter(c => c.id !== commentId)
    }
  } catch (error) {
    console.error('Error deleting comment:', error)
  }
}

import '../../css/community.css' 
</script>


<template>
  <Head :title="title" />

  <div class="page-container">
    <!-- Header -->
    <div class="page-header">
      <h1 class="page-title">{{ title }}</h1>
      <a :href="route('uploadArt')" class="upload-button">Upload Artwork</a>
    </div>

    <!-- No Uploads -->
    <div v-if="!uploads.length" class="empty-text">
      No public artworks yet. Be the first to upload!
    </div>

    <!-- Artwork Feed -->
    <div
      v-for="upload in uploads"
      :key="upload.id"
      class="upload-card"
    >
      <img
        :src="`/${upload.image_path}`"
        :alt="upload.title"
        class="upload-image"
      />

      <div class="upload-info">
        <!-- Title & Like -->
        <div class="upload-title-box">
          <div>
            <h2 class="upload-title">{{ upload.title }}</h2>
            <p class="upload-meta">
              By <span class="font-medium">{{ upload.user?.name ?? 'Anonymous' }}</span> •
              Style: {{ upload.style?.name ?? 'Unknown' }}
            </p>
          </div>

          <!-- Like -->
          <button @click="toggleLike(upload.id)" class="like-button">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              :class="[
                (likeStates[upload.id]?.liked || upload.likes.some(like => like.id === authUser?.id))
                  ? 'like-icon-liked'
                  : 'like-icon-unliked'
              ]"
              viewBox="0 0 24 24"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M3.172 5.172a4.001 4.001 0 015.656 0L12 8.343l3.172-3.171a4.001 4.001 0 015.656 5.656L12 20.343l-8.828-8.829a4.001 4.001 0 010-5.656z"
              />
            </svg>
            <span>{{ likeStates[upload.id]?.likes_count ?? upload.likes.length }}</span>
          </button>
        </div>

        <!-- Description -->
        <p class="upload-description">{{ upload.description }}</p>

        <!-- Comments Section -->
        <div class="comment-section">
          <h3 class="comment-header">Comments</h3>

          <div v-if="upload.comments.length" class="space-y-2 mb-4">
            <div
              v-for="comment in upload.comments"
              :key="comment.id"
              class="comment"
            >
              <span>
                <span class="comment-user">{{ comment.user.name }}:</span>
                {{ comment.content }}
              </span>
              <button
                v-if="authUser && (authUser.id === comment.user_id || authUser.is_admin)"
                @click="deleteComment(comment.id, upload.id)"
                class="comment-delete"
              >
                Remove
              </button>
            </div>
          </div>

          <p v-else class="no-comments">No comments yet.</p>

          <div v-if="authUser" class="comment-input">
            <input
              type="text"
              v-model="commentInputs[upload.id]"
              placeholder="Add a comment..."
              class="comment-box"
              @keyup.enter="submitComment(upload.id)"
            />
            <button
              @click="submitComment(upload.id)"
              class="comment-send"
            >
              Send
            </button>
          </div>
          <p v-else class="login-message"><a href="/login">log in</a> to comment</p>
        </div>
      </div>
    </div>
  </div>
</template>

