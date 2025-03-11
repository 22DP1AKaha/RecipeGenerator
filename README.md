# Recipe Generator

## Projekta apraksts
**Recipe Generator** ir tīmekļa lietotne, kas izstrādāta, izmantojot **Laravel** un **Vue** frameworkus, ar datubāzi, kas tiek pārvaldīta ar **XAMPP** un **phpMyAdmin**.  
Šobrīd sistēmā ir ieviesta recepšu meklēšanas funkcionalitāte, taču, lai paplašinātu lietotnes iespējas, tiek plānots izstrādāt **Python** mašīnmācīšanās (ML) modeli, kas ļaus ģenerēt personalizētas receptes.

Projekta mērķis ir sniegt lietotājiem ērtu veidu, kā:
1. Atrast receptes, izmantojot meklēšanu pēc ēdiena nosaukuma vai atslēgvārdiem.  
2. Ģenerēt ēdienus, pamatojoties uz pieejamajām sastāvdaļām.  
3. (Nākotnē) izmantot AI risinājumu recepšu ģenerēšanai, kas spēs adaptēties lietotāja vajadzībām un ierobežojumiem (diēta, alerģijas utt.).

---

## Funkcionalitāte

### 1. Recepšu meklēšanas lapa
- Lietotāji var ievadīt meklēšanas laukā ēdiena nosaukumu vai atslēgvārdus.  
- Pēc receptes izvēles tiek atvērta dinamiska lapa, kurā redzama informācija par ēdienu un tā pagatavošanas instrukcijas.

### 2. Nākotnes plāni
- **AI balstīta recepšu ģenerēšana**  
  Plānots izstrādāt pielāgotu **Python** mašīnmācīšanās modeli, kas spēs ģenerēt receptes no lietotāja atlasītajām sastāvdaļām un ņemt vērā diētiskos ierobežojumus vai alerģijas.
- **Lietotāju profili un personalizācija**  
  Iespēja lietotājiem saglabāt iecienītākās receptes un pielāgot ieteikumus.
- **Uzlabota backend integrācija**  
  Turpināt attīstīt **Laravel** pusi, lai apstrādātu gan recepšu datus, gan AI modeli.
