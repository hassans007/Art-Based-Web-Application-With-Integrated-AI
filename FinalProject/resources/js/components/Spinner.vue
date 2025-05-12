<template>
    <div class="flex justify-center items-center py-10">
      <div class="text-2xl font-semibold">
        Finding...<span>{{ dots }}{{ dots }}</span>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onUnmounted } from 'vue'
  
  const dots = ref('')
  
  let interval = null
  
  onMounted(() => {
    interval = setInterval(() => {
      if (dots.value.length >= 3) {
        dots.value = ''
      } else {
        dots.value += '.'
      }
    }, 400) 
  })
  
  onUnmounted(() => {
    clearInterval(interval)
  })
  </script> 