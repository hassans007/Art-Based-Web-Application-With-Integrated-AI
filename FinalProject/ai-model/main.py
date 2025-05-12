from fastapi import FastAPI, UploadFile, File
from fastapi.middleware.cors import CORSMiddleware
from PIL import Image
from styleClassifier import classify_art_style
from feedbackGenerator import generate_feedback
from dotenv import load_dotenv
load_dotenv()

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# --- API Route ---
@app.post("/analyze-art")
async def analyze_art(file: UploadFile = File(...)):
    try:
        # Step 1: Read and process the image
        image = Image.open(file.file).convert("RGB")

        # Step 2: Predict art style
        style = classify_art_style(image)

        # Step 3: Generate dynamic feedback
        feedback_data = generate_feedback(file.file, style)

        # Step 4: Return everything neatly
        return {
            "style": style,
            "description": feedback_data["description"],
            "feedback": feedback_data["feedback"]
        }

    except Exception as e:
        print("❌ Error during analysis:", e)
        return {"error": str(e)}
