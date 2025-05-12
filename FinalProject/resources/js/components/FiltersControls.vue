<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import '../../css/searchBar.css'  // ✅ import your common SearchBar.css

defineProps({
  type: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['update:style', 'update:timePeriod', 'update:artist', 'update:nationality', 'update:sortBy', 'reset-all'])

const styles = ref([])
const nationalities = ref([])

const timePeriods = [
  { range: '1400-1499', label: '1400s' },
  { range: '1500-1599', label: '1500s' },
  { range: '1800-1899', label: '1800s' },
  { range: '1900-1999', label: '1900s' },
  { range: '2000-2099', label: '2000s' },
]

onMounted(async () => {
  try {
    const [stylesResponse, nationalitiesResponse] = await Promise.all([
      axios.get('/styles'),
      axios.get('/nationalities')
    ])
    styles.value = stylesResponse.data
    nationalities.value = nationalitiesResponse.data
  } catch (error) {
    console.error('Failed to load filters:', error)
  }
})

const resetFilters = () => {
  emit('update:style', '')
  emit('update:timePeriod', '')
  emit('update:artist', '')
  emit('update:nationality', '')
  emit('update:sortBy', 'newest')
  emit('reset-all')  
}
</script>

<template>
  <div class="filter-controls">
    <!-- Artwork Filters -->
    <template v-if="type === 'artwork'">
      <select @change="emit('update:style', $event.target.value)" class="filter-select">
        <option value="">All Styles</option>
        <option v-for="style in styles" :key="style.id" :value="style.id">{{ style.name }}</option>
      </select>

      <select @change="emit('update:timePeriod', $event.target.value)" class="filter-select">
        <option value="">All Periods</option>
        <option v-for="period in timePeriods" :key="period.range" :value="period.range">{{ period.label }}</option>
      </select>

      <select @change="emit('update:sortBy', $event.target.value)" class="filter-select">
        <option value="newest">Newest</option>
        <option value="oldest">Oldest</option>
        <option value="most_viewed">Most Viewed</option>
      </select>
    </template>

    <!-- Artist Filters -->
    <template v-else-if="type === 'artist'">
      <select @change="emit('update:nationality', $event.target.value)" class="filter-select">
        <option value="">All Nationalities</option>
        <option v-for="nation in nationalities" :key="nation" :value="nation">{{ nation }}</option>
      </select>

      <select @change="emit('update:timePeriod', $event.target.value)" class="filter-select">
        <option value="">All Periods</option>
        <option v-for="period in timePeriods" :key="period.range" :value="period.range">{{ period.label }}</option>
      </select>
    </template>

    <!-- Reset Button -->
    <button @click="resetFilters" type="button" class="reset-button">
      Reset Filters
    </button>
  </div>
</template>
