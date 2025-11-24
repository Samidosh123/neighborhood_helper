# predict.py
import sys
import json
import joblib

# 1️  Load the trained model
MODEL_PATH = "trained_issue_classifier.pkl"
model = joblib.load(MODEL_PATH)

# 2️  Read input JSON from PHP or command line
# Example JSON input: {"text": "Streetlight is not working at Elm Street"}
input_json = sys.stdin.read()

try:
    data = json.loads(input_json)
    text = data.get("text", "")
except json.JSONDecodeError:
    print(json.dumps({"error": "Invalid JSON input"}))
    sys.exit(1)

# 3️ Predict the category
if text:
    prediction = model.predict([text])[0]
    output = {"category": prediction}
else:
    output = {"error": "No text provided"}

# 4️ Return result as JSON
print(json.dumps(output))
