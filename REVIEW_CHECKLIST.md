# Translation & Localization Native Speaker Review Checklist

## Overview

The Youth Policy Tracker supports multi-language capabilities for sub-national civic engagement. To maintain legal accuracy, policy terminology consistency, and cultural authenticity, non-English translation files are marked **PENDING NATIVE REVIEW** and are disabled in production by default (`TRACKER_ENABLE_ALL_LOCALES=false`).

This document specifies the review protocol, domain vocabulary standards, and sign-off requirements before any locale may be enabled.

---

## 1. Inventory of Files Requiring Native Review

| Language | Code | File Path | Writing Direction | Status | Required Reviewer Profile |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **French** | `fr` | `lang/fr/tracker.php` | LTR | Pending Review | Native Francophone public policy / legal translator |
| **Hausa** | `ha` | `lang/ha/tracker.php` | LTR | Pending Review | Native Hausa speaker with Northern Nigeria governance context |
| **Yoruba** | `yo` | `lang/yo/tracker.php` | LTR | Pending Review | Native Yoruba speaker with Southwest Nigeria civic policy context |
| **Igbo** | `ig` | `lang/ig/tracker.php` | LTR | Pending Review | Native Igbo speaker with Southeast Nigeria civic policy context |
| **Arabic** | `ar` | `lang/ar/tracker.php` | RTL | Pending Review | Native Arabic legal/governance translator |

---

## 2. Core Domain Vocabulary & Standard Terminology

Reviewers must verify that technical public governance terms are rendered consistently across all keys:

### Governance & Policy Terms

| English Concept | Standard Definition | French (`fr`) | Hausa (`ha`) | Yoruba (`yo`) | Igbo (`ig`) | Arabic (`ar`) |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Domestication** | Formal state-level adoption of national policy | Domestication | Karɓar Manufofi / Aiwatarwa | Sisọdọmọ ti Ilana | Nnabata Iwu | التوطين / الاعتماد المحلي |
| **Official Gazette** | State government statutory journal publication | Journal Officiel | Jaridar Gwamnati | Iwe Iroyin Ijoba | Akwukwo Okwu Gọọmenti | الجريدة الرسمية |
| **Certified True Copy (CTC)** | Legally authenticated legal document | Copie Certifiée Conforme (CTC) | Sahihancin Kwafi (CTC) | Eto Ifọwọsi Pẹlu (CTC) | Ezigbo Akwụkwọ Eziokwu (CTC) | نسخة طبق الأصل مصدقة |
| **Executive Council Approval** | State Governor & Cabinet formal sanction | Approbation du Conseil Exécutif | Amincewar Majalisar Zartarwa | Ifọwọsi Igbimọ Alase | Nkwado Ndị Isi Nchịkwa | موافقة المجلس التنفيذي |
| **Public Hearing** | Legislative public consultation & stakeholder input | Audience Publique | Jin Ra'ayin Jama'a | Igbejo Gbangba | Nnọkọ Nzukọ Ọha | جلسة استماع عامة |
| **State House of Assembly** | Sub-national legislative chamber | Assemblée Législative de l'État | Majalisar Dokoki ta Jiha | Ile-Igbimo Asofin Ipinle | Ụlọ Mgbakọ Ndị Omeiwu Steeti | مجلس نواب الولاية |
| **Sub-national Government** | State or regional administrative authority | Gouvernement Sub-National / Étatique | Gwamnatin Jiha | Ijoba Ipinle | Ọchịchị Steeti | الحكومة دون الوطنية / الولائية |

---

## 3. Reviewer Checklist & Verification Steps

For each language file:

- [ ] **1. Grammar & Syntax**: Verify grammatical correctness, appropriate formal register, and natural readability for civic and youth audiences.
- [ ] **2. Domain Terminology**: Ensure all statutory legal terms (Gazette, CTC, Policy Stages, Domestication) match accepted sub-national administrative terminology.
- [ ] **3. Variable Placeholder Integrity**: Verify that replacement tokens (e.g. `:country`, `:policy`, `:count`, `:date`) are preserved verbatim with exact colon syntax and not translated or removed.
- [ ] **4. Text Direction & Punctuation**: For Arabic (`ar`), confirm correct right-to-left layout, punctuation placement, and bidirectional text rendering.
- [ ] **5. Diacritics & Tonal Marks**: For Yoruba (`yo`) and Igbo (`ig`), verify correct vowel diacritics and tonal accents (e.g., `ọ`, `ẹ`, `ṣ`, `ụ`, `ị`, `ń`).
- [ ] **6. No Emojis**: Verify zero emojis appear in any translation string.
- [ ] **7. UI Layout Fit**: Confirm that translated button labels and filter chips fit within standard mobile (360px) and desktop containers without clipping.

---

## 4. Enabling a Reviewed Locale

Once native review is complete and formally approved:

1. Update the header docblock of the locale file in `lang/{code}/tracker.php` to indicate `STATUS: VERIFIED BY [Reviewer Name] ON [Date]`.
2. Update the status in `config/tracker.php` `all_locales` array to `'verified'`.
3. In production `.env`, set:
   ```ini
   TRACKER_ENABLE_ALL_LOCALES=true
   ```
4. Clear configuration cache:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```
