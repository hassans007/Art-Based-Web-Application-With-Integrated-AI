<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import GuessArtist from '@/components/guessArtist.vue'
import TimelineGame from '@/components/timelineGame.vue'
import StyleMatch from '@/components/styleMatch.vue'
import ArtPuzzle from '@/components/artPuzzle.vue'



defineOptions({
  layout: AuthenticatedLayout
})

defineProps({
  title: {
    type: String,
    default: 'Test Your Knowledge'
  }
})

const selectedGame = ref(null)

function startGame(type) {
  selectedGame.value = type
}
</script>

<template>
  <Head :title="`${title} | The Art`" />

  <div class="gameContent">
    <h1 class="gameTitle">Test Your Knowledge</h1>
    <p class="gameSubtitle">(How well do you know your art and artists?)</p>

    <transition name="fade" mode="out-in">
      <div v-if="!selectedGame" key="menu" id="gameMenu" class="gameMenu">
        <h2 class="gameChooseTitle">🎯 Choose a Game</h2>

        <div class="gameButtons">
          <button @click="startGame('artist')" class="gameButton gameButton-artist">
            🎨 Guess the Artist
          </button>
          <button @click="startGame('timeline')" class="gameButton gameButton-timeline">
            🕰️ Art History Timeline
          </button>
          <button @click="startGame('stylematch')" class="gameButton gameButton-artist">
            🎨 Style Match
          </button>
          <button @click="startGame('puzzle')" class="gameButton gameButton-timeline">
            🧩 Art Puzzle
          </button>

        </div>
      </div>

      <div v-else key="game" class="gameOptions">
        <!-- NEW BACK BUTTON -->
        <button @click="selectedGame = null" class="backButton">
          <img src="/images/back.png" alt="Back" /> Back to Menu
        </button>

        <GuessArtist v-if="selectedGame === 'artist'" :autoStart="true" />
        <TimelineGame v-if="selectedGame === 'timeline'" :autoStart="true" />
        <StyleMatch v-if="selectedGame === 'stylematch'" :autoStart="true" />
        <ArtPuzzle v-if="selectedGame === 'puzzle'" :autoStart="true" />

      </div>
    </transition>
  </div>
</template>
