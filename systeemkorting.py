kortingscodes = {
    "KORTING10": 10,
    "KORTING20": 20,
    "KORTING30": 30,
}

code = input("Kortingscode: ").upper()

if code in kortingscodes:
    korting = kortingscodes[code]
    print(f"Code geldig! Je krijgt {korting}% korting.")
else:
    print("Code ongeldig.")
