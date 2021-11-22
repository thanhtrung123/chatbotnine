from gensim.models import TfidfModel
import sys
import json

args = sys.argv

f = open(args[1], 'r')
jsonData = json.load(f)
f.close()

clist = []

for aid,rows in jsonData.items():
    cr = []
    for row in rows:
        cr.append(tuple(row))
    clist.append(cr)

# print(clist)


# for aid, row in jsonData:
#     for i, val in row:
#         jsonData[aid][i] = tuple(val)

model = TfidfModel(clist)
corpus_tfidf = model[clist]

olist = []
for doc in corpus_tfidf:
    olist.append(doc)
#     print(type(doc))

out = json.dumps(olist)
print(out)

# print(model)

# print(type(jsonData))
# print (jsonData)

