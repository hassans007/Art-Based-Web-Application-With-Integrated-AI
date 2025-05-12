import torch
import torch.nn as nn
import torchvision.transforms as transforms
from PIL import Image

# --- Config ---
CLASS_NAMES = ['Cubism', 'Expressionism', 'Realism', 'Japanese_Art','Renaissance']
MODEL_PATH = 'pro_best_model.pth'
IMAGE_SIZE = 226

device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')

# --- Defining ResidualBlock and CustomCNN ---
class ResidualBlock(nn.Module):
    def __init__(self, in_channels, out_channels, downsample=False):
        super(ResidualBlock, self).__init__()
        stride = 2 if downsample else 1
        self.conv1 = nn.Conv2d(in_channels, out_channels, kernel_size=3, stride=stride, padding=1)
        self.bn1 = nn.BatchNorm2d(out_channels)
        self.relu = nn.ReLU(inplace=True)
        self.conv2 = nn.Conv2d(out_channels, out_channels, kernel_size=3, padding=1)
        self.bn2 = nn.BatchNorm2d(out_channels)

        self.downsample = None
        if downsample or in_channels != out_channels:
            self.downsample = nn.Sequential(
                nn.Conv2d(in_channels, out_channels, kernel_size=1, stride=stride),
                nn.BatchNorm2d(out_channels)
            )

    def forward(self, x):
        identity = x
        out = self.relu(self.bn1(self.conv1(x)))
        out = self.bn2(self.conv2(out))
        if self.downsample is not None:
            identity = self.downsample(identity)
        out += identity
        out = self.relu(out)
        return out

class CustomCNN(nn.Module):
    def __init__(self, num_classes):
        super(CustomCNN, self).__init__()
        self.layer0 = nn.Sequential(
            nn.Conv2d(3, 32, kernel_size=3, stride=1, padding=1),
            nn.BatchNorm2d(32),
            nn.ReLU(inplace=True)
        )
        self.layer1 = nn.Sequential(
            ResidualBlock(32, 32),
            ResidualBlock(32, 32),
            nn.MaxPool2d(2)
        )
        self.layer2 = nn.Sequential(
            ResidualBlock(32, 64, downsample=True),
            ResidualBlock(64, 64),
            nn.MaxPool2d(2)
        )
        self.layer3 = nn.Sequential(
            ResidualBlock(64, 128, downsample=True),
            ResidualBlock(128, 128),
            nn.MaxPool2d(2)
        )
        self.layer4 = nn.Sequential(
            ResidualBlock(128, 256, downsample=True),
            ResidualBlock(256, 256),
            nn.MaxPool2d(2)
        )
        self.layer5 = nn.Sequential(
            ResidualBlock(256, 512, downsample=True),
            ResidualBlock(512, 512),
            nn.AdaptiveAvgPool2d((1, 1))
        )
        self.classifier = nn.Sequential(
            nn.Flatten(),
            nn.Linear(512, 512),
            nn.ReLU(),
            nn.Dropout(0.5),
            nn.Linear(512, num_classes)
        )

    def forward(self, x):
        x = self.layer0(x)
        x = self.layer1(x)
        x = self.layer2(x)
        x = self.layer3(x)
        x = self.layer4(x)
        x = self.layer5(x)
        x = self.classifier(x)
        return x

# --- Load model ---
model = CustomCNN(num_classes=len(CLASS_NAMES))
model.load_state_dict(torch.load(MODEL_PATH, map_location=device))
model = model.to(device)

if device.type == 'cuda':
    model = model.half()

model.eval()

# --- Preprocessing ---
transform = transforms.Compose([
    transforms.Resize((IMAGE_SIZE, IMAGE_SIZE)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406],
                         std=[0.229, 0.224, 0.225])
])

# --- Prediction function ---
def classify_art_style(image: Image.Image):
    try:
        # Step 1: Apply transform
        image = transform(image)
        
        # Step 2: Add batch dimension
        image = image.unsqueeze(0)

        # Step 3: Move to device
        image = image.to(device)

        # Step 4: If on CUDA, convert input to half
        if device.type == 'cuda':
            image = image.half()

        # Step 5: Predict
        with torch.no_grad():
            outputs = model(image)
            _, predicted = torch.max(outputs, 1)

        return CLASS_NAMES[predicted.item()]

    except Exception as e:
        print(f"Error in classify_art_style: {e}")
        return "Error"
