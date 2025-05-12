<template>
  <div class="quizApp text-center">
    <div v-if="stage === 'quiz'" class="quizProgress">
      <h2 class="text-2xl font-bold mb-2 text-indigo-700">{{ current.question }}</h2>
      <p class="mb-4 text-gray-600 font-medium">Question {{ currentIndex + 1 }} of {{ questions.length }}</p>

      <img :src="current.image" alt="Artwork" class="max-w-sm mx-auto border mb-4 rounded shadow-md" />

      <div class="quiz-options">
        <button v-for="option in shuffledOptions" :key="option" @click="handleAnswer(option)">
          {{ option }}
        </button>
      </div>

      <!-- Updated Feedback -->
      <div v-if="feedback" class="feedbackContent mt-4">
        <span
          class="badge"
          :class="feedback.includes('Correct') ? 'correct' : 'wrong'"
        >
          {{ feedback }}
        </span>

        <button @click="nextQuestion" class="quiz-next-button mt-6">
          Next
        </button>
      </div>
    </div>

    <div v-if="stage === 'result'" class="quizResults">
      <h2 class="text-3xl font-bold text-green-700 mb-4">🎉 Quiz Finished!</h2>

      <p class="text-xl font-semibold text-gray-800">You got {{ score }} out of {{ questions.length }} correct!</p>

      <p class="text-md font-medium text-gray-600 mt-2">
        🏆 Best Score: {{ bestScore }}
      </p>

      <button @click="restart" class="quiz-restart-button mt-6">🔁 Play Again</button>
    </div>
  </div>
</template>


<script>
import { timelineQuizData } from '../data/timelineQuizData.js';
import axios from 'axios'; 

async function saveScore() {
  try {
    await axios.post('/theArt/gameScore', {
      game_name: 'Timeline Game', 
      score: this.score,
      total_questions: this.questions.length,
    });
  } catch (error) {
    console.error('Failed to save game score', error);
  }
}


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
    this.questions = timelineQuizData.sort(() => 0.5 - Math.random());
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
      this.feedback = "Correct! " + this.current.fact;
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
      localStorage.setItem('timelineBestScore', this.bestScore);
    }
  },
  async saveScore() {
  if (this.score === 0) {
    return;
  }
  try {
    await axios.post('/theArt/gameScore', {
      game_name: 'Timeline Game',
      score: this.score,
      total_questions: this.questions.length,
    });
    console.log('Score saved successfully!'); 
  } catch (error) {
    console.error('Failed to save game score', error);
  }
},
  restart() {
    this.stage = 'quiz';
    this.startQuiz();
  }
},
  created() {
    const saved = localStorage.getItem('timelineBestScore');
    this.bestScore = saved ? parseInt(saved) : 0;

    if (this.autoStart) {
      this.startQuiz();
    }
  }
};
</script>
