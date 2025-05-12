<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Dropdown from '@/components/Dropdown.vue'
import DropdownLink from '@/components/DropdownLink.vue'
import ApplicationLogo from '@/components/ApplicationLogo.vue'
import ChatBox from '@/components/Chatbox.vue'

const page = usePage()
const navOpen = ref(false)

const currentUser = computed(() => page.props.auth.user)
const isAdmin = computed(() => currentUser.value?.role === 'admin')
const headerImage = computed(() => page.props.headerImage || '/images/homeTop.jpg')

function toggleNav() {
  navOpen.value = !navOpen.value
}

onMounted(() => {
  const currentUrl = window.location.href
  const pathname = window.location.pathname
  if (!['/login', '/register'].includes(pathname)) {
    const previousUrl = sessionStorage.getItem('currentPage')
    if (previousUrl !== currentUrl) {
      sessionStorage.setItem('lastVisitedPage', previousUrl || '/theArt/artCatalog')
      sessionStorage.setItem('currentPage', currentUrl)
    }
  }
})
</script>
<template>
  <div class="layoutContent min-h-screen bg-gray-100">
    <!-- Header Image -->
    <div class="homeTopImage">
      <img :src="headerImage" alt="Header Image" />
    </div>

    <!-- Navigation -->
    <nav class="nav-control">
      <!-- Logo -->
      <div class="nav-logo">
        <Link href="/theArt">
          <ApplicationLogo />
        </Link>
      </div>

      <!-- Mobile Menu Icon -->
      <button @click="toggleNav" class="menu-icon-mobile sm:hidden">
        &#9776;
      </button>

      <!-- Desktop Nav -->
      <div class="nav-bar hidden sm:flex">
        <div class="links flex space-x-4 items-center">
          <Link href="/theArt/home" class="nav-link" :class="{ 'active': route().current('home') }">Home</Link>
          <Link href="/theArt/artCatalog" class="nav-link" :class="{ 'active': route().current('artCatalog') }">Artworks Catalog</Link>
          <Link href="/theArt/artistCatalog" class="nav-link" :class="{ 'active': route().current('artistCatalog') }">Artists Catalog</Link>
          <Link href="/theArt/community" class="nav-link" :class="{ 'active': route().current('community') }">Community Content</Link>
          
          <template v-if="currentUser">
            <Link href="/theArt/testYourself" class="nav-link" :class="{ 'active': route().current('testYourself') }">Test your knowledge</Link>
            <Link href="/theArt/aimodel" class="nav-link" :class="{ 'active': route().current('aimodel') }">AI Analysis</Link>
          </template>

          <Link href="/theArt/about" class="nav-link" :class="{ 'active': route().current('about') }">About</Link>
        </div>

        <!-- Desktop User Dropdown -->
        <div v-if="currentUser" class="ml-6 flex items-center">
          <Dropdown align="right" width="48">
            <template #trigger>
              <span class="inline-flex rounded-md">
                <button type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                  {{ currentUser.name }}
                  <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </span>
            </template>

            <template #content>
              <template v-if="isAdmin">
                <DropdownLink :href="route('admin.dashboard')">Admin Dashboard</DropdownLink>
                <DropdownLink :href="route('profile.info')">My Profile</DropdownLink>
              </template>
              <template v-else>
                <DropdownLink :href="route('profile.info')">My Profile</DropdownLink>
              </template>
              <DropdownLink :href="route('profile.edit')">Account</DropdownLink>
              <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
            </template>
          </Dropdown>
        </div>
      </div>

      <!-- Mobile Nav -->
      <div v-if="navOpen" class="mobile-nav sm:hidden">
        <button @click="toggleNav" class="close-button">
          ✕
        </button>

        <div class="mobile-links flex flex-col space-y-4 mt-8">
          <Link href="/theArt/home" class="nav-link" @click="toggleNav">Home</Link>
          <Link href="/theArt/artCatalog" class="nav-link" @click="toggleNav">Artworks Catalog</Link>
          <Link href="/theArt/artistCatalog" class="nav-link" @click="toggleNav">Artists Catalog</Link>
          <Link href="/theArt/community" class="nav-link" @click="toggleNav">Community Content</Link>

          <template v-if="currentUser">
            <Link href="/theArt/testYourself" class="nav-link" @click="toggleNav">Test your knowledge</Link>
            <Link href="/theArt/aimodel" class="nav-link" @click="toggleNav">AI Analysis</Link>
          </template>

          <Link href="/theArt/about" class="nav-link" @click="toggleNav">About</Link>

          <template v-if="!currentUser">
            <Link :href="route('login')" class="nav-link" @click="toggleNav">Log in</Link>
            <Link :href="route('register')" class="nav-link" @click="toggleNav">Register</Link>
          </template>
          

          <!-- Mobile User Account Links -->
          <template v-if="currentUser">
            <hr class="my-4 border-gray-300" />
            <template v-if="isAdmin">
              <Link :href="route('admin.dashboard')" class="nav-link" @click="toggleNav">Admin Dashboard</Link>
              <Link :href="route('profile.info')" class="nav-link" @click="toggleNav">My Profile</Link>
            </template>
            <template v-else>
              <Link :href="route('profile.info')" class="nav-link" @click="toggleNav">My Profile</Link>
            </template>
            <Link :href="route('profile.edit')" class="nav-link" @click="toggleNav">Account</Link>
            <form :action="route('logout')" method="post">
              <button type="submit" class="nav-link w-full text-left">Log Out</button>
            </form>
          </template>
        </div>
      </div>
      <!-- Mobile Background Overlay -->
      <div 
          v-if="navOpen" 
          @click="toggleNav" 
          class="mobile-overlay sm:hidden"
        ></div>
    </nav>

    <!-- Page Header -->
    <header v-if="$slots.header" class="pagesheader">
      <div class="pagesheaderContent">
        <slot name="header" />
      </div>
    </header>

    <!-- Main Page Content -->
    <main class="mx-auto max-w-7xl sm:px-6 lg:px-8 mt-6">
      <slot />
    </main>

    <!-- ChatBox -->
    <ChatBox />
  </div>
</template>
