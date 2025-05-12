<x-layout title="Test Your Knowledge">
  <div class="gameContent text-center">
    <h1>Test Your Knowledge (How well do you know your art and artists?)</h1>

    <div id="gameMenu">
      <h2>Choose a Game</h2>
      <button onclick="startGame('artist')">🎨 Guess the Artist</button>
      <button onclick="startGame('timeline')">🕰️ Art History Timeline</button>
    </div>

    <div class="gameOptions">
      <div id="quiz-app" style="display: none;"></div>
      <div id="timeline-app" style="display: none;"></div>
    </div>
  </div>

  <script>
    function startGame(type) {
      document.getElementById('gameMenu').style.display = 'none';

      if (type === 'artist') {
        document.getElementById('quiz-app').style.display = 'block';
      } else if (type === 'timeline') {
        document.getElementById('timeline-app').style.display = 'block';
      }

      launchGame(type); // Call the Vue mounting function defined in app.js
    }
  </script>
</x-layout>
