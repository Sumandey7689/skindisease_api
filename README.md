# Skin Disease Detection API

## Overview

The Skin Disease Detection API is a tool for detecting skin diseases from images. It utilizes a pre-trained deep learning model to classify skin disease images into different categories. This repository contains the code for the API along with a pre-trained model and a dataset of skin diseases.

## Installation

To set up the API, follow these steps:

1. Clone this repository to your local machine:

```bash
git clone https://github.com/Sumandey7689/skindisease_api.git
```

2. Navigate to the project directory:

```bash
cd skindisease_api
```

3. Install the required Python packages using pip:

```bash
pip install tensorflow keras numpy
```

## Usage

To use the API, follow these steps:

1. Start the php server

2. Send a POST request to the `/detect` endpoint with an image file as input. You can use tools like Postman or cURL to send the request. Make sure to include the image file in the request body.


3. The API will process the image and return a JSON response containing the predicted disease, probability, and description.

## Response
The API returns a JSON response containing the predicted skin disease, probability score, and description.

Example response:
```json
{
    "predicted_disease": "Acne",
    "probability": 0.812,
    "description": "Acne is a common skin condition that occurs when hair follicles become clogged with oil and dead skin cells."
}
```

## Dataset

The dataset used for training the model is included in the `dataset.json` file. It contains information about various skin diseases along with their corresponding class labels and descriptions.

## Model

The API utilizes a pre-trained InceptionResNetV2 model for image classification. The model is trained on a large dataset of skin disease images and achieves high accuracy in disease detection.
