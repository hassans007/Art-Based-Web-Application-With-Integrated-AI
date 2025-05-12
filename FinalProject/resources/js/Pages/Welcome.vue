<script setup>
import { Head, Link } from '@inertiajs/vue3'
import '../../css/welcome.css'
defineProps({
  canLogin: Boolean,
  auth: Object,
  canRegister: Boolean,
  laravelVersion: String,
  phpVersion: String,
  title: {
    type: String,
    default: 'The Art',
  },
})

function handleImageError() {
  document.getElementById('hero-img')?.classList.add('hidden')
}
</script>

<template>
  <Head :title="`${title} | The Art`" />

  <div class="page-container">
    <!-- Header / Nav -->
    <header class="header">
      <Link href="/theArt">
        <img src="/images/navLogos.png" alt="The Art Logo" class="logo" />
      </Link>
      <nav class="nav-links">
        <Link href="/theArt/home">Home</Link>
        <Link href="/theArt/artCatalog">Artworks</Link>
        <Link href="/theArt/artistCatalog">Artists</Link>
        <Link v-if="auth?.user" href="/theArt/community">Community Content</Link>
        <Link v-if="auth?.user" href="/theArt/aimodel">AI Analysis</Link>
        <Link href="/theArt/about">About</Link>
        <Link v-if="canLogin && !auth?.user" :href="route('login')">Log in</Link>
        <Link v-if="canRegister && !auth?.user" :href="route('register')">Register</Link>
      </nav>
    </header>

    <!-- Hero Image + Welcome Text -->
    <section class="hero-section">
      <div class="hero-overlay">
        <h1>Welcome to The Art</h1>
      </div>
    </section>

    <!-- Highlights -->
    <main class="highlights">
      <div class="cards">
        <div class="card">
          <h2>Explore Artworks</h2>
          <p>Browse our curated collection of famous and lesser-known works from artists around the world.</p>
          <Link href="/theArt/artCatalog" class="card-link">Browse Art</Link>
        </div>

        <div class="card">
          <h2>Meet the Artists</h2>
          <p>Discover the stories and styles of renowned and emerging artists.</p>
          <Link href="/artistCatalog" class="card-link">View Artists</Link>
        </div>

        <div class="card">
          <h2>Test Your Knowledge</h2>
          <p>Try the interactive quiz and see how much you know about art history and artists.</p>
          <Link href="/theArt/testYourself" class="card-link">Take Quiz</Link>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      Created by Hassan Shahid
    </footer>
  </div>
</template>
