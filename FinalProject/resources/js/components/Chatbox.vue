<template>
    <div>
      <!-- If NOT logged in -->
      <div v-if="!user" class="chat-login-message">
        <p>
          Please <a href="/login">log in</a> to access the chat.
        </p>
      </div>
  
      <!-- If logged in, show chatbox -->
      <div v-else>
        <!-- Toggle Button (when chat is hidden) -->
        <div v-if="!visible" class="chat-toggle-button" @click="toggleChat">
          ➤
        </div>
  
        <!-- Chat Box -->
        <div v-if="visible" class="chat-box">
          <div class="chat-header">
            <span>Global Chat</span>
            <button @click="toggleChat">Hide</button>
          </div>
  
          <!-- Messages -->
          <div class="chat-messages">
            <div
              v-for="msg in messages"
              :key="msg.id"
              :class="['chat-message', msg.user?.id === user?.id ? 'chat-message-self' : 'chat-message-other']"
            >
              <div
                :class="[
                  'chat-bubble',
                  msg.user?.id === user?.id ? 'chat-bubble-self' : 'chat-bubble-other'
                ]"
              >
                <p class="chat-sender">
                  {{ msg.user?.id === user?.id ? 'You' : msg.user?.name || 'User' }}
                </p>
                <p>{{ msg.message }}</p>
              </div>
  
              <button
                v-if="user?.role === 'admin'"
                @click="deleteMessage(msg.id)"
                class="chat-delete"
              >
                Delete
              </button>
            </div>
          </div>
  
          <!-- Input -->
          <form @submit.prevent="sendMessage" class="chat-input">
            <input
              v-model="input"
              type="text"
              placeholder="Type your message..."
            />
            <button type="submit">Send</button>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { usePage } from '@inertiajs/vue3'
  import axios from 'axios'
  import '../../css/chatbox.css'
  
  const visible = ref(false)
  const input = ref('')
  const messages = ref([])
  const user = usePage().props.auth?.user
  
  const fetchMessages = async () => {
    try {
      const res = await axios.get('/chat')
      messages.value = res.data.reverse()
    } catch (e) {
      console.error('Failed to load messages', e)
    }
  }
  
  const toggleChat = () => {
    visible.value = !visible.value
  }
  
  const sendMessage = async () => {
    if (!input.value.trim()) return
    try {
      await axios.post('/chat', { message: input.value })
      input.value = ''
      fetchMessages()
    } catch (e) {
      console.error('Failed to send message', e)
    }
  }
  
  const deleteMessage = async (id) => {
    try {
      await axios.delete(`/chat/${id}`)
      fetchMessages()
    } catch (e) {
      console.error('Failed to delete message', e)
    }
  }
  
  onMounted(() => {
    if (user) {
      fetchMessages()
    }
  })
  </script>
  