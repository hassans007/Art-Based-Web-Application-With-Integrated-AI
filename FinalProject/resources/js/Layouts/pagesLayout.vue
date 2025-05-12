<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

const showingNavigationDropdown = ref(false)
</script>

<template>
  <div class="layoutContent min-h-screen bg-gray-100">
    <!-- Top Header Image -->
    <div class="homeTopImage">
      <img src="/images/homeTop.jpg" alt="Header Image" />
    </div>

    <!-- NavBar -->
    <nav class="nav-control">
      <div class="nav-logo">
        <Link href="/theArt">
          <img src="/images/navLogos.png" alt="Logo" />
        </Link>
      </div>

      <!-- Hamburger Icon (Mobile) -->
      <div id="openIcon" class="menu-icon-mobile sm:hidden" @click="showingNavigationDropdown = !showingNavigationDropdown">
        <svg width="35" viewBox="0 0 10 10" fill="none">
          <path d="M1 1h8 M1 4h8 M1 7h8" stroke="white" stroke-width="0.75" />
        </svg>
      </div>

      <!-- Navigation Links -->
      <div class="nav-bar hidden sm:flex">
        <div class="links flex space-x-4 items-center">
          <Link href="/theArt" class="nav-link">Home</Link>
          <Link href="/theArt/artCatalog" class="nav-link">Artworks Catalog</Link>
          <Link href="/theArt/artistCatalog" class="nav-link">Artists Catalog</Link>
          <Link href="/theArt/savedCollection" class="nav-link">My Saved Collection</Link>
          <Link href="/theArt/testYourself" class="nav-link">Test your knowledge</Link>
          <Link href="/theArt/upload" class="nav-link">AI Critique</Link>
          <Link href="/theArt/about" class="nav-link">About</Link>
        </div>
      </div>

      <!-- Dropdown (Right-Aligned) -->
      <div class="hidden sm:flex sm:items-center sm:ml-auto">
        <Dropdown align="right" width="48">
          <template #trigger>
            <span class="inline-flex rounded-md">
              <button
                type="button"
                class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none"
              >
                {{ $page.props.auth.user.name }}
                <svg class="ml-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path
                    fill-rule="evenodd"
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </span>
          </template>
          <template #content>
            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
            <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
          </template>
        </Dropdown>
      </div>
    </nav>

    <!-- Responsive Nav (Mobile) -->
    <div v-show="showingNavigationDropdown" class="sm:hidden bg-white border-t border-gray-200">
      <div class="px-4 py-2 space-y-1">
        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
          Dashboard
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
        <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
      </div>
    </div>

    <!-- Page Header (slot) -->
    <header v-if="$slots.header" class="bg-white shadow">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Main Page Content -->
    <main class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>
