import requests

def get_data(url, data):
    r = requests.post(url, data=data)
    return(r.json())
generate = "/api/generate/"
get = "/api/retrieve/?id="
address = "http://localhost:8080"

# len tests
headers = {"len": "8"}
is_pass = True
answer = get_data(address+generate, headers)
if answer.get("message") != "Small_len":
    print("Small_len test not pass", headers)
    is_pass = False
headers = {"len": "101"}
answer = get_data(address+generate, headers)
if answer.get("message") != "Big_len":
    print("Big_len test not pass", headers)
    is_pass = False
headers = {"len": "25"}
answer = get_data(address+generate, headers)
if len(answer.get("value")) != 25:
    print("Len is not working", headers)
    is_pass = False
if is_pass:
    print("len tests passed")
else:
    print("len tests failed")
#end of len tests

#numeric test

is_pass = True
headers = {"type": "numeric"}
answer = get_data(address+generate, headers)
if not answer.get("value").isnumeric():
    print("Numeric tests not passed", answer)
    is_pass = False
if is_pass:
    print("numeric tests passed")
else:
    print("numeric tests failed")

#end of numeric test

#from tests
is_pass = True

headers = {"type":"from", "from_values":"kek21"}
answer = get_data(address+generate, headers)
if answer.get("message") != "duplicate_symbols":
    is_pass = False
    print("Duplicate symbols")
from_values = "hey12345"
headers = {"type":"from", "from_values":from_values}
answer = get_data(address+generate, headers)
from_values = set(from_values)
for i in answer.get("value"):
    if not from_values.intersection(i):
        is_pass = False
        print("From_values add values that not in from_values")
headers = {"type":"from", "from_values":"k"}
answer = get_data(address+generate, headers)
if answer.get("message") != "Small_from":
    is_pass = False
    print(answer.get("Small_from"))
if is_pass:
    print("from_values tests passed")
else:
    print("from_values tests failed")
#end of from test

#error tests

is_pass = True

headers = {"type":"aloha"}
answer = get_data(address+generate, headers)
if answer.get("message") != "Unsupported type":
    is_pass = False;
    print("type checker doesnt work")
#end tests


# if answer.difference(from_values):
#     is_pass = False
#     print("Duplicate symbols")
# print(answer)
