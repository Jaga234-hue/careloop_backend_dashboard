import boto3
import json

client = boto3.client(
    "bedrock-runtime",
    region_name="us-east-1"
)

body = {
    "messages": [
        {
            "role": "user",
            "content": [{"text": "Hello"}]
        }
    ]
}

response = client.invoke_model(
    modelId="amazon.nova-micro-v1:0",
    body=json.dumps(body),
    contentType="application/json",
    accept="application/json"
)

result = json.loads(response["body"].read())

print(result)