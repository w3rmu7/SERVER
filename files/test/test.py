fstClass = int(input("fstClass: "))
b2bClass = int(input("b2bclass: "))
hwClass = int(input("hwClass: "))

awakeTime = fstClass - 2

hwTime = awakeTime - (b2bClass * (5 / 600) * hwClass + 0.5) 
print(hwTime)
