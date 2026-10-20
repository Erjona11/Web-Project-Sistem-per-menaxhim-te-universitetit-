# Erjona Asmanollaj - Sistem per menaxhim te universitetit

**Raporti 1 Teknik**

## Përshkrimi i projektit

Ky projekt ka për qëllim zhvillimin e një sistemi web aplikacioni i quajtur Student Hub i cili do të përdoret për menaxhimin dhe organizimin e informacioneve dhe shërbimeve që lidhen me jetën akademike të studentëve në Universitetin “Ukshin Hoti” në Prizren.

Ideja e këtij sistemit është që studentët të kenë një hapësirë të centralizuar ku mund të gjejnë informacione dhe funksione të ndryshme akademike pa pasur nevojë të kërkojnë në faqe ose platforma të ndryshme.

Ky sistem nuk kam dashur të jetë vetëm një kopje e website-it zyrtar të universitetit ton. Qëllimi është që të krijohet një platformë krejt ndryshe nga ajo që ne kemi për universitetin tonë, më e orientuar drejt nevojave të përditshme të studentit, me funksione si orari personal, lëndët gjatë semestrit të caktuar, detyrat, notat, provimet, njoftimet, progresi akademik dhe informacione të tjera të dobishme. Pra, ky web aplikacion synon të ndihmojë studentët, përdoruesit kryesorë të këtij website-i, në shumë pika të ndryshme.

## Përdoruesit e sistemit

Sistemi do të ketë disa kategori kryesore përdoruesish:

**Studenti**

Studenti është përdoruesi kryesor i sistemit.

**Profesori**

Profesori do të mund të menaxhojë informacionet që lidhen me lëndët e tij.

**Administratori**

Administratori do të ketë rol më të gjerë në sistem.

### Kërkesat funksionale

Sistemi duhet t'u mundësojë përdoruesve këto funksionalitete:

- Përdoruesi duhet të mund të kyçet në sistem përmes email-it dhe fjalëkalimit.
- Sistemi duhet të dallojë llojin e përdoruesit dhe t'i shfaqë funksionalitetet përkatëse studentit, profesorit ose administratorit.
- Studenti duhet të mund të shohë dhe të përditësojë të dhënat e tij personale.
- Studenti duhet të mund të shohë lëndët në të cilat është i regjistruar.
- Studenti duhet të mund të shohë orarin e ligjëratave dhe ushtrimeve, së bashku me ditën, orën dhe sallën.
- Studenti duhet të mund të shohë detyrat dhe afatet e dorëzimit për lëndët e tij.
- Studenti duhet të mund të shohë notat e marra në lëndë të ndryshme.
- Studenti duhet të mund të shohë datat dhe informacionet për provimet.
- Sistemi duhet të shfaqë njoftimet më të fundit të universitetit dhe të profesorëve.
- Studenti duhet të mund të shohë pjesëmarrjen e tij në ligjërata dhe ushtrime.
- Sistemi duhet të paraqesë progresin akademik të studentit në bazë të të dhënave të tij.
- Studenti duhet të mund të shohë informacionet për konsultimet me profesorët.
- Studenti duhet të mund të shohë aktivitetet dhe ngjarjet e universitetit.

### Kërkesat jofunksionale

- Fjalëkalimet e përdoruesve duhet të ruhen në mënyrë të sigurt dhe jo si tekst i thjeshtë.
- Funksionalitetet e administratorit duhet të jenë të qasshme vetëm për përdoruesit e autorizuar.
- Sistemi duhet të kontrollojë të dhënat e futura nga përdoruesi para se ato të ruhen në databazë.
- Faqja duhet të jetë e përshtatshme për përdorim në kompjuter, tablet dhe telefon, pra të jetë responsive.
- Informacionet kryesore, si orari, notat, provimet dhe materialet, duhet të jenë të lehta për t'u gjetur.
- Të dhënat e sistemit duhet të ruhen në mënyrë të organizuar në databazën MySQL.
- Sistemi duhet të jetë i qëndrueshëm dhe të shmangë gabimet gjatë futjes ose kërkimit të të dhënave.

### Dizajni Teknik

-Frontend: HTML, CSS dhe JavaScript – për krijimin e ndërfaqes së përdoruesit, dizajnin, navigimin dhe funksionalitetet interaktive të platformës.
-Backend: PHP – për logjikën e sistemit, autentifikimin e përdoruesve, përpunimin e të dhënave dhe komunikimin ndërmjet frontend-it dhe databazës.
-Databaza: MySQL – për ruajtjen dhe menaxhimin e të dhënave të studentëve, profesorëve, lëndëve, notave, orareve, provimeve, njoftimeve etj.

