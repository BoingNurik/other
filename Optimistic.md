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


3. Endi koddagi usul bilan 6×6 Polybius square ni aynan qayta quramiz.
   - keyworddagi belgilar alphabetdan olib tashlanadi (takror bo‘lmasligi uchun)
   - keyword boshiga qo‘yiladi
   - 36 ta belgi 6×6 jadvalga bo‘linadi
4. Shifrdan qaytarish uchun formulani teskari yozamiz:
   pt[i] = ct[i] - key[i % 36]
5. Key periodi 36 bo‘lgani uchun ciphertextni 36 ta guruhga bo‘lib chiqamiz.
6. ndi har bir p uchun k ni valid koordinatalar ichidan sinab ko‘ramiz:
   - k valid bo‘ladi, agar vals(p) ichidagi barcha v-k lar ham valid coord bo‘lsa.
   - Shu filtrdan keyin odatda 1–2 ta kandidat qoladi.
7. Eng kuchli constraint: STARSTREAM_KEY permutatsiya bo‘lgani uchun 36 pozitsiyadagi k lar takrorlanmaydi. Shuning uchun kandidatlarni birlashtirishda:
   - bir koordinata bitta pozitsiyaga ishlatiladi
   - backtracking (yoki assignment) bilan hammasini joylashtiramiz
8. Key koordinatalari topilgach, ularni coord -> char orqali key stringga o‘giramiz:
   <img width="569" height="40" alt="Screenshot_20251225_022054" src="https://github.com/user-attachments/assets/ff7c142d-e68b-4363-84a7-d8be12fdcbb8" />
9. Plaintexni tiklab, source.py bo‘yicha AES kalit plaintextdan olamiz
10. Oxirgi qadam: WRAPPED_STARSHARD ni AES-ECB bilan ochamiz:
   - hex -> bytes
   - AES-ECB decrypt
   - PKCS#7 unpad
7. Shifrni yuqoridagi veb sahifada tushuntirilgani kabi yechamiz:

![image_2025-06-18_17-51-34 (3)](https://github.com/user-attachments/assets/76946090-fb5e-4586-8741-b72ea6630d01)

trstX
r4coX
ynbdX
tmoaX
u1tyX

trytur4nm1scbottoday - try tur4nm1scbot today

4. Telegramga kirib tur4nm1scbot deb izlasak bot chiqadi. Botning description qismida "i love c4tzzzzzzzzzz" ushbu iboralar berilgan, bizdan so'ralgan narsa what i love, shuning uchun "c4tzzzzzzzzzz"ning md5 hashini olamiz va flag sifatida kiritamiz:

![image_2025-06-18_18-03-49](https://github.com/user-attachments/assets/7a8de311-6e2b-4f0c-9797-3dcb0f3d5b09)

![image_2025-06-18_18-03-49 (2)](https://github.com/user-attachments/assets/5296bae8-3799-4861-8f21-34fee22ecd78)
