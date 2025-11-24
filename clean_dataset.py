import json

# Load dataset
with open("realistic_synthetic_dataset.json", "r") as f:
    data = json.load(f)

cleaned = []
seen_titles = set()

for item in data:
    title = item["title"].strip()
    description = item["description"].strip()
    category = item["category"].strip().title()

    # Skip duplicates based on title
    if title not in seen_titles:
        cleaned.append({
            "title": title,
            "description": description,
            "category": category
        })
        seen_titles.add(title)

# Save as cleaned dataset
with open("cleaned_dataset.json", "w") as f:
    json.dump(cleaned, f, indent=4)

print("Cleaned dataset saved to cleaned_dataset.json")
