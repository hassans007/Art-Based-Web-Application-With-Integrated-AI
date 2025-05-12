<template>
    <div class="puzzleGame text-center">
      <h2 class="text-2xl font-bold mb-6 text-blue-700">🧩 Rebuild the Artwork</h2>
  
      <div v-if="stage === 'playing'">
        <div class="text-lg font-semibold text-gray-700 mb-4">
          Time Left: {{ formattedTime }}
        </div>
  
        <div class="grid grid-cols-3 gap-2 max-w-xs mx-auto">
          <div
            v-for="(piece, index) in shuffledPieces"
            :key="index"
            class="puzzlePiece border rounded overflow-hidden"
            :class="{ 'dragging': draggedPieceIndex === index }"
            draggable="true"
            @dragstart="dragStart(index)"
            @dragend="dragEnd"
            @dragover.prevent
            @drop="drop(index)"
          >
            <img :src="piece" alt="Puzzle piece" />
          </div>
        </div>
  
        <div class="mt-4">
          <button @click="checkPuzzle" class="btnCheck">Check Puzzle</button>
        </div>
  
        <div v-if="checkMessage" class="mt-4 font-bold" :class="solved ? 'text-green-600' : 'text-red-500'">
          {{ checkMessage }}
        </div>
      </div>
  
      <div v-else-if="stage === 'won'">
        <h3 class="text-green-600 font-bold text-xl mb-2">🏆 You solved it!</h3>
        <p class="text-gray-700 mb-2">Your Time: {{ formattedUsedTime }}</p>
        <p class="text-gray-700 mb-4">Best Time: {{ formattedBestTime }}</p>
        <button @click="restart" class="btnCheck">Play Again</button>
      </div>
  
      <div v-else-if="stage === 'lost'">
        <h3 class="text-red-500 font-bold text-xl mb-2">Sorry, you couldn't solve it!</h3>
        <button @click="restart" class="btnCheck">Try Again</button>
      </div>
    </div>
  </template>
  
  <script>
  import { puzzlePieces } from '../data/artPuzzleData.js';
  import axios from 'axios';
  
  export default {
    props: {
      autoStart: {
        type: Boolean,
        default: false,
      },
    },
    data() {
      return {
        originalPieces: [],
        shuffledPieces: [],
        solved: false,
        draggedPieceIndex: null,
        checkMessage: '',
        timer: 120,
        usedTime: 0,
        timerInterval: null,
        stage: 'playing', // playing / won / lost
        bestTime: null,
      };
    },
    computed: {
      formattedTime() {
        const minutes = Math.floor(this.timer / 60);
        const seconds = this.timer % 60;
        return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
      },
      formattedUsedTime() {
        const time = this.usedTime;
        const minutes = Math.floor(time / 60);
        const seconds = time % 60;
        return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
      },
      formattedBestTime() {
        if (this.bestTime === null) return "N/A";
        const minutes = Math.floor(this.bestTime / 60);
        const seconds = this.bestTime % 60;
        return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
      },
    },
    methods: {
      shuffle(array) {
        return array.sort(() => 0.5 - Math.random());
      },
      dragStart(index) {
        this.draggedPieceIndex = index;
      },
      dragEnd() {
        this.draggedPieceIndex = null;
      },
      drop(targetIndex) {
        if (this.draggedPieceIndex === null) return;
  
        const temp = this.shuffledPieces[targetIndex];
        this.shuffledPieces[targetIndex] = this.shuffledPieces[this.draggedPieceIndex];
        this.shuffledPieces[this.draggedPieceIndex] = temp;
  
        this.draggedPieceIndex = null;
      },
      checkPuzzle() {
        const isSolved = this.shuffledPieces.every((piece, index) => piece === this.originalPieces[index]);
        this.solved = isSolved;
  
        if (isSolved) {
          this.usedTime = 120 - this.timer;
          this.checkMessage = "🎉 You solved it!";
          this.saveScore();
          this.updateBestTime();
          this.stopTimer();
          this.stage = 'won';
        } else {
          this.checkMessage = "❌ Not solved yet, keep trying!";
        }
      },
      startTimer() {
        this.timerInterval = setInterval(() => {
          if (this.timer > 0) {
            this.timer--;
          } else {
            this.stopTimer();
            this.stage = 'lost';
          }
        }, 1000);
      },
      stopTimer() {
        clearInterval(this.timerInterval);
      },
      async saveScore() {
        try {
          await axios.post('/theArt/gameScore', {
            game_name: 'Art Puzzle',
            score: 1, 
            total_questions: 1,
            time_taken: this.usedTime, // 🕒 Save how fast the user solved it
          });
        } catch (error) {
          console.error('Failed to save game score', error);
        }
      },
      restart() {
        this.originalPieces = puzzlePieces;
        this.shuffledPieces = this.shuffle([...this.originalPieces]);
        this.solved = false;
        this.checkMessage = '';
        this.timer = 120;
        this.usedTime = 0;
        this.stage = 'playing';
        this.startTimer();
      },
      updateBestTime() {
        const saved = localStorage.getItem('bestPuzzleTime');
        if (!saved || this.usedTime < parseInt(saved)) {
          localStorage.setItem('bestPuzzleTime', this.usedTime);
          this.bestTime = this.usedTime;
        } else {
          this.bestTime = parseInt(saved);
        }
      },
    },
    created() {
      this.originalPieces = puzzlePieces;
      this.shuffledPieces = this.shuffle([...this.originalPieces]);
      this.startTimer();
  
      const saved = localStorage.getItem('bestPuzzleTime');
      this.bestTime = saved ? parseInt(saved) : null;
    },
    beforeDestroy() {
      this.stopTimer();
    }
  };
  </script>
  
  <style scoped>
  .puzzlePiece {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: grab;
  }
  .puzzlePiece.dragging {
    transform: scale(1.08);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    opacity: 0.8;
  }
  .puzzlePiece img {
    width: 100%;
    height: auto;
    user-select: none;
    pointer-events: none;
  }
  </style>
  