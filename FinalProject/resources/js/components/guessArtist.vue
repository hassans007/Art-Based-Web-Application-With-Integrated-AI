<template>
  <div class="quizApp text-center">
    <!-- Quiz In Progress -->
    <transition name="fade">
      <div class="quizProgress" v-if="stage === 'quiz'" key="quiz">
        <h2 class="text-2xl font-bold mb-2 text-indigo-700">Guess the Artist</h2>
        <p class="mb-4 text-gray-600 font-medium">
          Question {{ currentIndex + 1 }} of {{ questions.length }}
        </p>

        <img
          :src="current.image"
          alt="Artwork"
          class="max-w-sm mx-auto border mb-4 rounded shadow-md"
        />

        <div class="quiz-options">
          <button
            v-for="option in shuffledOptions"
            :key="option"
            @click="handleAnswer(option)"
          >
            {{ option }}
          </button>
        </div>

        <!-- Feedback -->
        <div v-if="feedback" class="feedbackContent mt-4">
          <img
            :src="current.artistImage"
            alt="Artist"
            class="artist-photo"
          />
          <span
            class="badge"
            :class="feedback.includes('Correct') ? 'correct' : 'wrong'"
          >
            {{ feedback }}
          </span>
        </div>

        <button
          v-if="feedback"
          @click="nextQuestion"
          class="quiz-next-button mt-6"
        >
          Next
        </button>
      </div>
    </transition>

    <!-- Quiz Results -->
    <transition name="fade">
      <div v-if="stage === 'result'" key="result">
        <h2 class="text-3xl font-bold text-green-700 mb-4">🎉 Quiz Complete!</h2>

        <p class="text-xl font-semibold text-gray-800 bounce-in">
          You got {{ score }} out of {{ questions.length }} correct!
        </p>

        <p class="text-md font-medium text-gray-600 mt-2">
          🏆 Best Score: {{ bestScore }}
        </p>

        <button
          @click="restart"
          class="quiz-restart-button mt-6"
        >
          🔁 Play Again
        </button>
      </div>
    </transition>
  </div>
</template>

<script>
import '../../css/quizStyle.css';
import { artistQuizData } from '../data/artistQuizData.js';
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
      stage: 'menu',
      score: 0,
      bestScore: 0,
      currentIndex: 0,
      feedback: '',
      questions: [],
      current: null,
    };
  },
  computed: {
    shuffledOptions() {
      return [...this.current.options].sort(() => 0.5 - Math.random());
    },
  },
  methods: {
    startQuiz() {
      this.questions = artistQuizData.sort(() => 0.5 - Math.random());
      this.currentIndex = 0;
      this.score = 0;
      this.feedback = '';
      this.current = this.questions[this.currentIndex];
      this.stage = 'quiz';
    },
    handleAnswer(selected) {
      if (this.feedback) return;

      if (selected === this.current.correctAnswer) {
        this.score++;
        this.feedback = " Correct! " + this.current.fact;
      } else {
        this.feedback = `It was ${this.current.correctAnswer}. ${this.current.fact}`;
      }
    },
    async nextQuestion() {
      this.feedback = '';
      this.currentIndex++;
      if (this.currentIndex < this.questions.length) {
        this.current = this.questions[this.currentIndex];
      } else {
        this.updateBestScore();
        await this.saveScore(); 
        this.stage = 'result';
      }
    },
    updateBestScore() {
      if (this.score > this.bestScore) {
        this.bestScore = this.score;
        localStorage.setItem('bestScore', this.bestScore);
      }
    },
    async saveScore() {
      if (this.score === 0) {
        return;
      }
      try {
        await axios.post('/theArt/gameScore', {
          game_name: 'Guess the Artist', 
          score: this.score,
          total_questions: this.questions.length,
        });
      } catch (error) {
        console.error('Failed to save game score', error);
      }
    },
    restart() {
      this.stage = 'quiz';
      this.startQuiz();
    },
  },
  created() {
    const savedScore = localStorage.getItem('bestScore');
    this.bestScore = savedScore ? parseInt(savedScore) : 0;

    if (this.autoStart) {
      this.startQuiz();
    }
  }
};
</script>
