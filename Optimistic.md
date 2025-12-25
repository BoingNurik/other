# Optimistic

## Challenge info

```text
Level: Very easy
Category: Crypto

Tavsif:
In Tinselwick's first magical mishap, Lottie Thimblewhisk discovers a strange peppermint-coded message whose enchanted structure hides something far more important. This challenge explores a festive ciphering mechanism and a seasonally wrapped secret. Your first step in uncovering what happened to the wandering Starshard.

```

## Solution

1. Avval zip ichidagi fayllarni ko‘rib chiqamiz. Asosiy ikkita fayl bo‘ladi:
   - `source.py` (shifrlash logikasi)
   - `output.txt` (bizga berilgan ciphertext va AES blob)

<img width="832" height="101" alt="Screenshot_20251225_015937" src="https://github.com/user-attachments/assets/38b7c1d6-78c9-43ec-88cd-78dabb79f04d" />

2. `source.py` ni ochib, shifrlash nimadan iborat ekanini tushunib olamiz. Bu yerda 3 ta muhim detal bor:
   - Alphabet: `A-Z0-9` (36 ta belgi)
   - 6×6 Polybius square `PEPPERMINT_KEYWORD` bilan quriladi
   - Shifrlash formulasi koordinatalar ustida ishlaydi:

   ```text
   ct[i] = coord(key[i % 36]) + coord(pt[i])
   ```
   Formulaga qo'yilganda PEPPERMINT_CIPHERTEXT degan sonlar listi paydo bo‘ladi.
4. Endi koddagi usul bilan 6×6 Polybius square ni aynan qayta quramiz.
   - keyworddagi belgilar alphabetdan olib tashlanadi (takror bo‘lmasligi uchun)
   - keyword boshiga qo‘yiladi
   - 36 ta belgi 6×6 jadvalga bo‘linadi
   <img width="473" height="386" alt="Screenshot_20251225_041315" src="https://github.com/user-attachments/assets/46b47026-acd7-43b0-8cde-d327cca4757a" />
5. Shifrdan qaytarish uchun formulani teskari yozamiz:
   pt[i] = ct[i] - key[i % 36]
   - i % 36 bir xil bo‘lgan joylarni guruhlaymiz: ct[p], ct[p+36], ct[p+72]...
   - biror k sinab ko‘ramiz: hamma v-k valid coord bo‘lishi shart (11..66, raqamlar 1..6)
   - 36 pozitsiyada k lar takrorlanmasligi shart → backtracking bilan yagona yechim chiqadi
   <img width="974" height="836" alt="Screenshot_20251225_040536" src="https://github.com/user-attachments/assets/951ea087-7618-40b7-af7e-da1cf26c8964" />
7. Key periodi 36 bo‘lgani uchun ciphertextni 36 ta guruhga bo‘lib chiqamiz.
8. Endi har bir p uchun k ni valid koordinatalar ichidan sinab ko‘ramiz:
   - k valid bo‘ladi, agar vals(p) ichidagi barcha v-k lar ham valid coord bo‘lsa.
   - Shu filtrdan keyin odatda 1–2 ta kandidat qoladi.
9. Eng kuchli constraint: STARSTREAM_KEY permutatsiya bo‘lgani uchun 36 pozitsiyadagi k lar takrorlanmaydi. Shuning uchun kandidatlarni birlashtirishda:
   - bir koordinata bitta pozitsiyaga ishlatiladi
   - backtracking (yoki assignment) bilan hammasini joylashtiramiz
10. Key koordinatalari topilgach, ularni coord -> char orqali key stringga o‘giramiz:
   <img width="569" height="40" alt="Screenshot_20251225_022054" src="https://github.com/user-attachments/assets/ff7c142d-e68b-4363-84a7-d8be12fdcbb8" />
11. Plaintexni tiklab, source.py bo‘yicha AES kalit plaintextdan olamiz
12. Oxirgi qadam: WRAPPED_STARSHARD ni AES-ECB bilan ochamiz:
   - hex -> bytes
   - AES-ECB decrypt
   - PKCS#7 unpad

Ishlaydigan solve script
```python
import re, ast, string, hashlib
from Crypto.Cipher import AES
from Crypto.Util.Padding import unpad

with open("output.txt", "r", encoding="utf-8") as f:
    out = f.read()

keyword = re.search(r"PEPPERMINT_KEYWORD\s*=\s*'([^']+)'", out).group(1)

start = out.find("PEPPERMINT_CIPHERTEXT")
lb = out.find("[", start)
rb = out.find("]\nWRAPPED_STARSHARD", start)
ct = ast.literal_eval(out[lb:rb+1])

wrapped_hex = re.search(r"WRAPPED_STARSHARD\s*=\s*'([^']+)'", out).group(1)
wrapped = bytes.fromhex(wrapped_hex)

# ---- 6x6 Polybius square ----
alphabet = string.ascii_uppercase + string.digits
SZ = 6

flat = alphabet
for c in keyword:
    flat = flat.replace(c, "")
flat = keyword + flat
square = [list(flat[i:i+SZ]) for i in range(0, len(flat), SZ)]

char_to_coord = {}
for i in range(SZ):
    for j in range(SZ):
        char_to_coord[square[i][j]] = int(f"{i+1}{j+1}")

coord_to_char = {v: k for k, v in char_to_coord.items()}
valid = set(coord_to_char.keys())

# ---- find key coords (period 36, all unique) ----
period = 36
cand = []
for p in range(period):
    vals = ct[p::period]
    opts = []
    for k in sorted(valid):
        if all((v - k) in valid for v in vals):
            opts.append(k)
    cand.append(opts)

order = sorted(range(period), key=lambda p: len(cand[p]))
assign = {}

def bt(i, used):
    if i == period:
        return True
    p = order[i]
    for k in cand[p]:
        if k in used:
            continue
        assign[p] = k
        used.add(k)
        if bt(i + 1, used):
            return True
        used.remove(k)
        del assign[p]
    return False

assert bt(0, set())
key_coords = [assign[p] for p in range(period)]

# ---- recover key string ----
starstream_key = "".join(coord_to_char[k] for k in key_coords)

# ---- recover plaintext ----
pt_coords = [ct[i] - key_coords[i % period] for i in range(len(ct))]
plaintext = "".join(coord_to_char[x] for x in pt_coords)

# ---- AES decrypt ----
aes_key = hashlib.sha256(plaintext.encode()).digest()
flag = unpad(AES.new(aes_key, AES.MODE_ECB).decrypt(wrapped), 16).decode()

print("[STARSTREAM_KEY]", starstream_key)
print("[PLAINTEXT_LEN]", len(plaintext))
print("[PLAINTEXT_HEAD]", plaintext[:120])
print("[PLAINTEXT_TAIL]", plaintext[-120:])
print("[AES_KEY_SHA256_HEX]", hashlib.sha256(plaintext.encode()).hexdigest())
print("[FLAG]", flag)
```
javob
<img width="1115" height="130" alt="Screenshot_20251225_052348" src="https://github.com/user-attachments/assets/3a108663-950e-4478-b39c-f87aff0dce09" />


