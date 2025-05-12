document.getElementById("art").addEventListener("change", function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById("imagePreview");
            img.src = e.target.result;
            document.getElementById("previewContainer").style.display = "block";
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById("artForm").addEventListener("submit", function (e) {
    e.preventDefault();

    document.getElementById("loading").style.display = "block";
    document.getElementById("result").style.display = "none";

    const fileInput = document.getElementById("art");
    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append("art", file);

    fetch("/theArt/analyze", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("loading").style.display = "none";
        document.getElementById("result").style.display = "block";
        document.getElementById("style").innerText = data.style;
        // document.getElementById("feedback").innerText = data.feedback;
    })
    .catch(err => {
        document.getElementById("loading").style.display = "none";
        alert("Something went wrong. Please try again.");
        console.error(err);
    });
});