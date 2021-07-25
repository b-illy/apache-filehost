import random, string, hashlib

print("Key generator for billy's file uploader")
print("NOTE - this script must be ran as root to work properly\n")

keyfile_path = "/var/www/html/data/keys.txt"

with open(keyfile_path, "r") as keyfile:
	keys = keyfile.read()

keys = keys.split("\n")

while True:
	name = str(input("Enter a nickname for the key: "))

	name_exists = False
	for x in keys:
		if x.split("|")[0] == name:
			name_exists = True
	if name_exists:
		print("Key with this nickname already exists.")
		if input("Enter another nickname (y) or continue anyway (n, default)? ") != "y":
			break
	else:
		break

m = hashlib.sha256()
m.update(bytes(name, "utf-8"))
hash = m.hexdigest()

key = name + "|" + str(hash)[:10] + "." + "".join(random.choice(string.ascii_letters) for x in range (32))

with open(keyfile_path, "a") as keyfile:
	keyfile.write(key + "\n")

print("\nKey generated: " + key.split("|")[1])
print("(key successfully added to key file and will now work)")
