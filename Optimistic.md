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
