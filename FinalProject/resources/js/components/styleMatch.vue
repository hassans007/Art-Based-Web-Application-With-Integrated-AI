<template>
    <div class="quizApp text-center">
      <transition name="fade">
        <div v-if="stage === 'quiz'" key="quiz">
          <h2 class="text-2xl font-bold mb-2 text-pink-700">Match the Style</h2>
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
  
          <div v-if="feedback" class="feedbackContent mt-4">
            <span
              class="badge"
              :class="feedback.includes('Correct') ? 'correct' : 'wrong'"
            >
              {{ feedback }}
            </span>
          </div>
  
          <button v-if="feedback" @click="nextQuestion" class="quiz-next-button mt-6">
            Next
          </button>
        </div>
      </transition>
  
      <transition name="fade">
        <div v-if="stage === 'result'" key="result">
          <h2 class="text-3xl font-bold text-green-700 mb-4">🎉 All Done!</h2>
          <p class="text-xl font-semibold text-gray-800 bounce-in">
            You got {{ score }} out of {{ questions.length }} correct!
          </p>
          <button @click="restart" class="quiz-restart-button mt-6">
            🔁 Play Again
          </button>
        </div>
      </transition>
    </div>
  </template>
  
  <script>
  import '../../css/quizStyle.css';
  import { styleQuizData } from '../data/styleQuizData.js'; 
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
        this.questions = styleQuizData.sort(() => 0.5 - Math.random());
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
          this.feedback = "Correct! Great match!";
        } else {
          this.feedback = `Oops, it's ${this.current.correctAnswer}!`;
        }
      },
      async nextQuestion() {
        this.feedback = '';
        this.currentIndex++;
        if (this.currentIndex < this.questions.length) {
          this.current = this.questions[this.currentIndex];
        } else {
          await this.saveScore();
          this.stage = 'result';
        }
      },
      async saveScore() {
        if (this.score === 0) return;
        try {
          await axios.post('/theArt/gameScore', {
            game_name: 'Style Match',
            score: this.score,
            total_questions: this.questions.length,
          });
        } catch (error) {
          console.error('Failed to save game score', error);
        }
      },
      restart() {
        this.startQuiz();
      },
    },
    created() {
      if (this.autoStart) {
        this.startQuiz();
      }
    }
  };
  </script>
  