<x-layout title="AI Critique" homeImage="images/savedTop.jpg">

<div class="uploadContent">

    <div class="uploadHeader">
        <h2>Upload Your Artwork for Analysis</h2>
    </div>

    <form id="artForm" enctype="multipart/form-data" class="uploadForm">
        @csrf
        <input type="file" name="art" id="art" required accept="image/*">
        <button type="submit">Analyze</button>
    </form>

    {{-- Image Preview --}}
    <div id="previewContainer" style="display: none; margin-top: 20px;">
        <h4>Preview:</h4>
        <img id="imagePreview" src="#" alt="Artwork Preview" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 10px;" />
    </div>

    {{-- Loading Spinner --}}
    <div id="loading" style="display:none; margin-top: 20px;">
        <div class="spinner"></div>
        <p>Analyzing artwork, please wait...</p>
    </div>

    {{-- Result Section --}}
    <div id="result" class="uploadResult" style="display: none;">
        <h3>Detected Style: <span id="style"></span></h3>
        <!-- <p><strong>Feedback:</strong> <span id="feedback"></span></p> -->
    </div>

</div>

</x-layout>
