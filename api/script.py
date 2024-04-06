import json
import numpy as np
from keras.preprocessing import image
from keras.applications.inception_resnet_v2 import InceptionResNetV2, preprocess_input

with open('dataset.json', 'r') as file:
    disease_data = json.load(file)

model = InceptionResNetV2(weights='imagenet')

def detect_skin(image_path):
    img = image.load_img(image_path, target_size=(299, 299))
    img_array = image.img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0)
    img_array = preprocess_input(img_array)

    preds = model.predict(img_array)
    
    predicted_class_index = np.argmax(preds)
    probability = np.max(preds)

    tolerance = 0.01
    matched_data = [data for data in disease_data if data["predicted_class"] == predicted_class_index]


    if matched_data:
        matched_disease = None
        matched_description = None
        for data in matched_data:
            if abs(data["probability"] - probability) < tolerance:
                matched_disease = data["disease"]
                matched_description = data["description"]
                break
        
        if not matched_disease:
            matched_disease = matched_data[0]["disease"]
            matched_description = matched_data[0]["description"]
    else:
        matched_disease = "No disease recognized"
        matched_description = "No matching disease was found in the dataset."

    response = {
        "predicted_disease": matched_disease,
        "probability": float(probability),
        "description": matched_description
    }

    return response


if __name__ == "__main__":
    import sys
    image_path = sys.argv[1]
    result = detect_skin(image_path)
    json_response = json.dumps(result)
    print(json_response)