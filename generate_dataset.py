import json


data = [  # 50 entries
    {"title": "Streetlight out at Maple and 3rd", "description": "The streetlight at the intersection has been non-functional for the past week, creating unsafe conditions for pedestrians.", "category": "Streetlight"},
    {"title": "Severe water leak in my kitchen", "description": "I noticed water leaking from the pipe under my kitchen sink. It has been increasing steadily and needs urgent repairs.", "category": "Water Leakage"},
    {"title": "Overflowing garbage cans on Elm Street", "description": "Garbage collection did not occur last week, and the bins are now overflowing with trash.", "category": "Garbage"},
    {"title": "Potholes causing accidents on Oak Avenue", "description": "Several potholes on Oak Avenue are causing cars to swerve and could lead to accidents.", "category": "Road Issue"},
    {"title": "Vandalism reported in community park", "description": "Graffiti has appeared on the playground equipment in the community park. It detracts from the area’s appearance.", "category": "Security"},
    {"title": "Lack of school supplies for students", "description": "The local school is short on basic supplies like pencils, paper, and notebooks for students.", "category": "Education"},
    {"title": "Broken streetlight on Pine Boulevard", "description": "The streetlight has been flickering on Pine Boulevard for more than two weeks, making it difficult to see at night.", "category": "Streetlight"},
    {"title": "Sewage odor near community center", "description": "An unpleasant sewage smell has been emanating from the drains near the community center. It has persisted for days.", "category": "Water Leakage"},
    {"title": "Trash scattered around downtown area", "description": "Trash is scattered around the downtown area, especially near the bus stops, creating a mess.", "category": "Garbage"},
    {"title": "Sidewalk damage along Broad Street", "description": "Several tiles on the sidewalk along Broad Street are cracked, posing a tripping hazard.", "category": "Road Issue"},
    {"title": "Frequent noise complaints in my neighborhood", "description": "Loud music and parties on the weekends are causing disturbances at night, affecting sleep.", "category": "Security"},
    {"title": "Need for outdoor learning space in local school", "description": "The school's outdoor area is underutilized and lacks seating, which is necessary for outdoor classes.", "category": "Education"},
    {"title": "Streetlight flickering on 5th Avenue", "description": "The streetlight has been flickering all week, causing visibility issues for drivers.", "category": "Streetlight"},
    {"title": "Persistent water leak on 2nd Street", "description": "There's a significant leak from the water main, causing flooding on 2nd Street.", "category": "Water Leakage"},
    {"title": "Garbage truck missed our pickup last week", "description": "The garbage truck did not come last week, and the bins are overflowing with waste.", "category": "Garbage"},
    {"title": "Dangerous potholes on Main Street", "description": "The potholes on Main Street are causing flat tires and accidents.", "category": "Road Issue"},
    {"title": "Unreported theft at the local gas station", "description": "A theft occurred at the gas station last night, and it needs to be reported for community safety.", "category": "Security"},
    {"title": "Supply shortage at Westside School", "description": "Westside School is running low on essential supplies for students and needs donations.", "category": "Education"},
    {"title": "Streetlight issues at the community entrance", "description": "The streetlights at the entrance of our community are frequently malfunctioning.", "category": "Streetlight"},
    {"title": "Water pooling around the fountain", "description": "There is water pooling around the community fountain, possibly indicating a leak.", "category": "Water Leakage"},
    {"title": "Uncollected yard waste in my area", "description": "Yard waste has not been picked up for weeks, creating an eyesore in the neighborhood.", "category": "Garbage"},
    {"title": "Cracked driveway causing hazards", "description": "My driveway is cracked and uneven, posing a trip hazard for visitors.", "category": "Road Issue"},
    {"title": "Security camera needed at local park", "description": "Installing cameras would improve safety at the local park, which has seen increased vandalism.", "category": "Security"},
    {"title": "Lack of educational toys for children's programs", "description": "The local community center lacks enough educational toys for children's programs.", "category": "Education"},
    {"title": "Streetlight malfunction on Crescent Road", "description": "A streetlight at the intersection of Crescent Road has been flickering for several nights.", "category": "Streetlight"},
    {"title": "Water flow issues from community irrigation", "description": "The irrigation system isn't functioning properly, resulting in dry patches in our community garden.", "category": "Water Leakage"},
    {"title": "Recycling bins overflowing in public spaces", "description": "The public recycling bins have not been collected, making the area look untidy.", "category": "Garbage"},
    {"title": "Cracked pavement near the playground", "description": "The pavement near the children's playground is cracked, presenting a safety concern.", "category": "Road Issue"},
    {"title": "Security concerns at the library after dark", "description": "The library needs increased security measures during evening hours due to recent incidents.", "category": "Security"},
    {"title": "Kids need more playground equipment", "description": "The playground lacks sufficient equipment for the number of kids in the neighborhood.", "category": "Education"},
    {"title": "Streetlight out on Elm Street", "description": "The streetlight on Elm Street has been out for a month, making it hard to navigate at night.", "category": "Streetlight"},
    {"title": "Water leak near city library", "description": "A water leak near the city library is causing damage and needs immediate repairs.", "category": "Water Leakage"},
    {"title": "Trash attracting wildlife in the park", "description": "Excess trash in the park is attracting raccoons and other wildlife, creating a nuisance.", "category": "Garbage"},
    {"title": "Dangerous curves on Hilltop Road", "description": "The sharp curves on Hilltop Road are dangerous, especially in rainy weather.", "category": "Road Issue"},
    {"title": "Car vandalism in residential areas", "description": "There have been reports of vandalism and car break-ins in the residential areas.", "category": "Security"},
    {"title": "Need for better internet service at local school", "description": "The local school’s internet service is unreliable, hindering students' learning opportunities.", "category": "Education"},
    {"title": "Broken streetlight on 8th Avenue", "description": "The streetlight on 8th Avenue has been broken since last month, causing safety concerns.", "category": "Streetlight"},
    {"title": "Flooding on Main and 1st Streets", "description": "Heavy rains have caused flooding at the intersection of Main and 1st Streets, blocking access.", "category": "Water Leakage"},
    {"title": "Overflowing recycling bins in front of City Hall", "description": "The recycling bins outside City Hall are overflowing, making the area look neglected.", "category": "Garbage"},
    {"title": "Badly potholes on Riverside Drive", "description": "The potholes on Riverside Drive are causing damage to vehicles and need immediate repair.", "category": "Road Issue"},
    {"title": "Crime alert for local shopping area", "description": "Residents have reported a surge in petty crimes around the local shopping area.", "category": "Security"},
    {"title": "Lack of extracurricular activities in local schools", "description": "The schools need more extracurricular activities to keep students engaged and active.", "category": "Education"}
]

# Save to a JSON file
file_path = 'realistic_synthetic_dataset.json'
with open(file_path, 'w') as f:
    json.dump(data, f, indent=4)

print(f"Data saved to {file_path}")