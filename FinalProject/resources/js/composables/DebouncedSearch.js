import { ref, watch, computed } from 'vue'
import axios from 'axios'
import debounce from 'lodash.debounce'

export default function useDebouncedSearch(initialType, delay = 300) {
  const type = ref(initialType)

  const query = ref('')
  const selectedStyle = ref(null)
  const selectedTimePeriod = ref(null)
  const selectedArtist = ref(null)
  const selectedNationality = ref(null)
  const sortBy = ref('newest')
  const currentPage = ref(1)

  const results = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  const searchParams = computed(() => ({
    query: query.value,
    style_id: selectedStyle.value,
    time_period: selectedTimePeriod.value,
    artist_id: selectedArtist.value,
    nationality: selectedNationality.value,
    sort_by: sortBy.value,
    type: type.value,
    page: currentPage.value,
  }))

  const fetchResults = async () => {
    isLoading.value = true
    error.value = null
    try {
      const response = await axios.get('/search', { params: searchParams.value })
      results.value = response.data
    } catch (err) {
      error.value = err
    } finally {
      isLoading.value = false
    }
  }

  const debouncedFetch = debounce(fetchResults, delay)

  watch(searchParams, () => {
    debouncedFetch()
  }, { deep: true })

  return {
    query,
    results,
    isLoading,
    error,
    selectedStyle,
    selectedTimePeriod,
    selectedArtist,
    selectedNationality,
    sortBy,
    currentPage,
  }
}
