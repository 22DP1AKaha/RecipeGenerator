<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstructionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define your recipes with names and instructions
        $recipes = [
            [
                'name' => 'Omlete ar spinātiem un sieru',
                'instructions' => [
                    "Sakarsē pannu ar augu eļļu uz vidējas uguns.",
                    "Sakult olas ar sāli un pipariem.",
                    "Pievieno spinātus sakultajām olām un samaisa.",
                    "Ielej maisījumu pannā un cep 2-3 minūtes, līdz apakša ir zeltaini brūna.",
                    "Pārkaisa ar sarīvētu sieru un turpina cept, līdz siers izkūst.",
                    "Saloka omleti uz pusēm un pasniedz siltu."
                ]
            ],
            [
                'name' => 'Banānu pankūkas',
                'instructions' => [
                    "Samaisa bānānus bļodā līdz putra.", 
                    "Pievieno olu, pienu un kokosriekstu eļļu, rūpīgi samaisa.", 
                    "Pievieno miltus, cukuru, sāli un cepamo pulveri, samaisa līdz vienmērīgai mīklai.",
                    "Uzkarsē pannu ar kokosriekstu eļļu vidējā ugunī.",
                    "Ar kausiņu ielej mīklu pannā, veidojot aplīti.", 
                    "Cep 2-3 minūtes līdz parādās burbuļi virsmā.",
                    "Apgriez pankūku un cept otru pusi 1-2 minūtes līdz zeltaini brūnai.", 
                    "Atkārto ar atlikušo mīklu.",
                    "Pasniedz karstas ar medu vai sīrupu."
                ]
            ],
            [
                'name' => 'Auzu pārslas ar āboliem un kanēli',
                'instructions' => [
                    "Sakarsē pannu vidējā ugunī.",
                    "Auzu pārslas ielej pannā un karsē 2 minūtes, nepārtraukti maisot.",
                    "Pievieno ūdeni un turpina vārīt 5 minūtes, bieži samaisot.",
                    "Ābolu nomizo, izņem serdi un sasmalcina uz rīvēm.",
                    "Pannai pievieno sasmalcināto ābolu, kanēli un kļavu sīrupu.",
                    "Maisa 3-4 minūtes līdz masā iegūst krēmīgu konsistenci.",
                    "Noņem no uguns un ļauj nostāvēties 2 minūtes.",
                    "Pasniedz siltu, papildus pārkaisot ar kanēli pa virsu."
                ]
            ],
            [
                'name' => 'Avakado tostermaize ar olu',
                'instructions' => [
                    "Apcep maizes šķēli tosterī līdz zeltaini brūnai.",
                    "Noņem maizi no tostera un pārlies ar olīveļļu.",
                    "Sagatavo avokado: nomizo, izņem kauliņu un sakasa ar dakstiņu.",
                    "Vienmērīgi uzzeķ maizes šķēli ar avokado.",
                    "Uzkarsē pannu ar atlikušo olīveļļu vidējā ugunī.",
                    "Pannā ielej olu un cept 2-3 minūtes līdz baltumi sakūst.",
                    "Uzmanīgi uzliek olu virsū uz avokado maizes.",
                    "Pārkaisa ar sāli un pasniedz tūlīt pēc pagatavošanas."
                ]
            ],
            [
                'name' => 'Augļu salāti ar jogurtu',
                'instructions' => [
                    "Nomizo un sagriez ābolus, banānus un kivi mazos gabaliņos.",
                    "Ieliec sagrieztos augļus bļodā.",
                    "Pievieno jogurtu un rūpīgi samaisa.",
                    "Pārlej ar medu un pārkaisa ar kokosriekstu eļļu.",
                    "Pasniedz uzreiz pēc pagatavošanas."
                ]
            ],
            [
                'name' => 'Tomātu zupa',
                'instructions' => [
                    "Sakarsē pannu ar olīveļļu vidējā ugunī.",
                    "Pievieno sīpolu un ķiplokus, sautē 3-4 minūtes.",
                    "Pievieno tomātus, sāli un piparus, sautē vēl 5 minūtes.",
                    "Pievieno ūdeni un vāra 15 minūtes.",
                    "Samalina zupu ar blenderi līdz viendabīgai masai.",
                    "Pievieno krējumu un vāra vēl 2 minūtes.",
                    "Pasniedz karstu ar maizes šķēli."
                ]
            ],
            [
                'name' => 'Burkānu un ingvera zupa',
                'instructions' => [
                    "Sakarsē pannu ar olīveļļu vidējā ugunī.",
                    "Pievieno sīpolu un ingveru, sautē 3-4 minūtes.",
                    "Pievieno burkānus, sāli un piparus, sautē vēl 5 minūtes.",
                    "Pievieno ūdeni un vāra 20 minūtes.",
                    "Samalina zupu ar blenderi līdz viendabīgai masai.",
                    "Pievieno krējumu un vāra vēl 2 minūtes.",
                    "Pasniedz karstu ar maizes šķēli."
                ]
            ],
            [
                'name' => 'Kāpostu un kartupeļu zupa',
                'instructions' => [
                    "Sakarsē pannu ar olīveļļu vidējā ugunī.",
                    "Pievieno sīpolu un ķiplokus, sautē 3-4 minūtes.",
                    "Pievieno kāpostus, kartupeļus, sāli, piparus un laura lapas.",
                    "Pievieno ūdeni un vāra 25 minūtes.",
                    "Pasniedz karstu ar maizes šķēli."
                ]
            ],
            [
                'name' => 'Vistas un rīsu zupa',
                'instructions' => [
                    "Nomazgā un sagriež sīpolu un burkānu gabaliņos.",
                    "Uz pannas uzkarsē nedaudz eļļas (vai vari izmantot vistas gaļas izkausēto tauku) un apcep sīpolu līdz tas kļūst caurspīdīgs.",
                    "Pievieno vistas gaļu un apcep, līdz tā viegli apbrūnējas.",
                    "Ielej ūdeni, pievieno rīsus un iepriekš sagatavoto dārzeņu maisījumu.",
                    "Vāra zupu apmēram 30 minūtes, līdz rīsi un gaļa kļūst mīksti.",
                    "Garšo ar sāli un pipariem pirms pasniegšanas."
                ]
            ],
            [
                'name' => 'Lēcu un spinātu sautējums',
                'instructions' => [
                    "Nomazgā un sagriež sīpolu, spinātus un tomātus.",
                    "Uz pannas uzkarsē olīveļļu un apcep sīpolu līdz tas kļūst caurspīdīgs.",
                    "Pievieno lēcas un cep dažas minūtes.",
                    "Ielej ūdeni, lai pārklātu sastāvdaļas, un vāra līdz lēcas ir mīkstas.",
                    "Pievieno spinātus un tomātus, vāra vēl 5 minūtes.",
                    "Garšo ar sāli un pipariem pirms pasniegšanas."
                ]
            ],
            [
                'name' => 'Spageti ar ķiploku un sviesta mērci',
                'instructions' => [
                    "Vāri makaroni saskaņā ar iepakojuma norādījumiem, līdz tie ir al dente.",
                    "Kamēr makaroni vārās, sasmalcini ķiplokus.",
                    "Pannā izkaus sviestu uz vidējas uguns, tad pievieno ķiplokus un viegli apcep, līdz tie iegūst zeltainu krāsu.",
                    "Iemaisa vārotos makaroni un uzvāra mērci vēl dažas minūtes.",
                    "Garšo ar sāli un pipariem un pasniedz karstu."
                ]
            ],
            [
                'name' => 'Vistas sautējums ar dārzeņiem',
                'instructions' => [
                    "Sagriež sīpolu un sadala brokoļus mazākos ziediņos.",
                    "Uz pannas uzkarsē olīveļļu un apcep sīpolu līdz tas kļūst mīksts.",
                    "Pievieno vistas gaļu un cep, līdz tā ir gandrīz gatava.",
                    "Iemaisa brokoļus un turpina sautēt vēl 5–7 minūtes.",
                    "Garšo ar sāli un pipariem un pasniedz karstu."
                ]
            ],
            [
                'name' => 'Tofu un dārzeņu karijs',
                'instructions' => [
                    "Nomazgā un sagriež tofu kubiņos, kā arī burkānu un cukini gabaliņos.",
                    "Uz pannas uzkarsē nelielu daudzumu eļļas un apcep tofu, līdz tas iegūst zeltainu krāsu.",
                    "Pievieno sagrieztos burkānus un cukini, cep vēl dažas minūtes.",
                    "Ielej kokosriekstu pienu un pievieno kariju.",
                    "Vāra apmēram 10–15 minūtes, lai sastāvdaļas labi sajauktos.",
                    "Pievieno sāli un piparus pēc garšas un pasniedz karstu."
                ]
            ],
            [
                'name' => 'Pildīta piprika',
                'instructions' => [
                    "Nomazgā papriku, nogaib tās augšējo daļu un iztukšo iekšpusi.",
                    "Vāri rīsus līdz gandrīz gataviem.",
                    "Atsevišķā pannā apcep maltu gaļu līdz vieglai apbrūnējumam. (Piezīme: 'Maltā gaļa' šobrīd nav DB – lūdzu, pievienojiet, ja nepieciešams.)",
                    "Sajauc vāros rīsus ar apcepto gaļu un sasmalcinātiem tomātiem.",
                    "Piepildi papriku ar šo maisījumu, pievieno olīveļļu, sāli un piparus.",
                    "Cep iepriekš uzkarsētā cepeškrāsnī 20–25 minūtes, līdz paprika kļūst mīksta.",
                    "Pasniedz karstu."
                ]
            ],
            [
                'name' => 'Grilēta zivs',
                'instructions' => [
                    "Nomazgā un nosusini zivju fileju.",
                    "Marinē zivju fileju ar olīveļļu, sāli un pipariem.",
                    "Grilē fileju uz iepriekš uzkarsēta grila vai pannas, līdz tā ir gatava.",
                    "Izkaus sviestu pannā un pievieno sasmalcinātu citrona sulu, lai izveidotu mērci.",
                    "Pārlej grilēto zivi ar mērci un pasniedz tūlīt."
                ]
            ],
            [
                'name' => 'Cepti kartupeļi ar rozmarīnu',
                'instructions' => [
                    "Nomazgā un sagriež kartupeļus šķēlēs.",
                    "Sajauc kartupeļu šķēles ar olīveļļu, sāli, pipariem un pievieno rozmarīna lapas.",
                    "Cep iepriekš uzkarsētā cepeškrāsnī 25–30 minūtes, līdz kartupeļi kļūst zeltaini un kraukšķīgi.",
                    "Pasniedz kā karstu piedevu."
                ]
            ],
            [
                'name' => 'Kāpostu salāti',
                'instructions' => [
                    "Nomazgā kāpostu un burkānus.",
                    "Smalki sagriež vai saralle kāpostu un ralle burkānus.",
                    "Sajauc maisījumu ar jogurtu.",
                    "Pievieno sāli un piparus pēc garšas.",
                    "Pasniedz kā svaigu un vieglu piedevu."
                ]
            ],
            [
                'name' => 'Gurķu un tomātu salāti',
                'instructions' => [
                    "Nomazgā gurķi un tomātus.",
                    "Sagriež gurķi šķēlēs un tomātus gabaliņos.",
                    "Sajauc dārzeņus bļodā ar olīveļļu, sāli un pipariem.",
                    "Viegls samaisiet un pasniedz svaigus."
                ]
            ],
            [
                'name' => 'Ābolu pīrāgs ar kanēli',
                'instructions' => [
                    "Nomazgā, nomizo un sagriež ābolus šķēlēs.",
                    "Sagatavo mīklu, sajaucot miltus, sviestu, olas un cukuru.",
                    "Izklāj mīklu cepamformā un kārto uz tās ābolu šķēles.",
                    "Apkaisa ar kanēli un, pēc vajadzības, papildus cukuru.",
                    "Cep iepriekš uzkarsētā krāsnī 60 minūtes, līdz mīkla kļūst zeltaina un āboli mīksti.",
                    "Atdzesē un pasniedz."
                ]
            ],
            [
                'name' => 'Mango smūtijs',
                'instructions' => [
                    "Nomazgā, nomizo un sagriež mango gabaliņos.",
                    "Ievieto mango, jogurtu un medu blenderī.",
                    "Blendē līdz iegūst gludu konsistenci.",
                    "Pasniedz atspirdzinošu smūtiju."
                ]
            ],
            
        ];

        foreach ($recipes as $recipe) {
            // Get the recipe ID using the recipe name
            $recipeId = DB::table('recipes')->where('name', $recipe['name'])->value('id');

            if (!$recipeId) {
                continue; // Skip if recipe doesn't exist
            }

            // Insert instructions for this recipe
            foreach ($recipe['instructions'] as $index => $step) {
                DB::table('instructions')->insert([
                    'recipe_id' => $recipeId,
                    'step_number' => $index + 1,
                    'description' => $step,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}