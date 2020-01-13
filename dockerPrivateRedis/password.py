import random
import string

def randomStringwithDigitsAndSymbols(stringLength=10):
    """Generate a random string of letters, digits and special characters """

    password_characters = string.ascii_letters + string.digits
    return ''.join(random.choice(password_characters) for i in range(stringLength))

print("Generating Random String password with letters, digits and special characters ")
print ("First Random String ", randomStringwithDigitsAndSymbols(200) )
