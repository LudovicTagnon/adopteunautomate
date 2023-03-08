file = open('villesFrance.csv', "r")
lines = file.readlines()
# fermez le fichier après avoir lu les lignes
file.close()
# Itérer sur les lignes
dico = {}
n = 0
with open('villesFrance3.csv', 'w') as f:
    for line in lines:
        info = line.strip("\n").split(",")
        cp = int(info[0])
        ville = info[1]
        if cp % 1000 == 0 and ville not in dico:
            n = n + 1
            f.write(line)
