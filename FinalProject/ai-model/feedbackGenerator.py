from transformers import BlipProcessor, BlipForConditionalGeneration
from PIL import Image
import torch
import openai
import os

caption_processor = BlipProcessor.from_pretrained("Salesforce/blip-image-captioning-base")
caption_model = BlipForConditionalGeneration.from_pretrained("Salesforce/blip-image-captioning-base")


openai.api_key = os.getenv("OPENAI_API_KEY")

def generate_feedback(file, predicted_style):
    image = Image.open(file).convert('RGB')
    inputs = caption_processor(image, return_tensors="pt")
    out = caption_model.generate(**inputs)
    description = caption_processor.decode(out[0], skip_special_tokens=True)

    prompt = (
        f"You are a professional art critic. Please write a respectful, intelligent, and short critique "
        f"for an artwork based ONLY on the visual elements described. Avoid personal, political, or sensitive topics.\n\n"
        f"Style: {predicted_style}\n"
        f"Description: {description}\n\n"
        f"Critique:"
    )

    response = openai.chat.completions.create(
        model="gpt-3.5-turbo",
        messages=[{"role": "user", "content": prompt}],
        max_tokens=200,
        temperature=0.7,
        top_p=0.95
    )

    feedback_text = response.choices[0].message.content.strip()

    return {
        "description": description,
        "style": predicted_style,
        "feedback": feedback_text
    }
